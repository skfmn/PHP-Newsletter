<?php
session_start();
ob_start();
require("../PHPMailer/src/PHPMailer.php");
require("../PHPMailer/src/SMTP.php");
require("../PHPMailer/src/Exception.php");
include '../includes/globals.php';
include '../includes/functions.php';

$msg = $cookies = $dir = "";
$cookies = $_SESSION["nwsadminname"];

if ($cookies == "") {

    redirect($redirect . "admin/login.php");
    ob_end_flush();

}

$blnSend = $_SESSION["blnSend"];
if ($blnSend == "false") {

    $_SESSION["msg"] = "nar";
    redirect($redirect . "admin/admin.php");

    ob_end_flush();

}

if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
    if ($msg <> "") {
        displayFancyMsg(getMessage($msg));
        $_SESSION["msg"] = "";
    }
}

if (isset($_POST["save"])) {

    $templateID = test_input($_POST["tempid"]);
    $tempTitle = test_input($_POST["temptitle"]);
    $tempBody = test_input($_POST["tempbody"]);

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "newsletter WHERE newsletterID = ?");
    $stmt->bind_param("s", $templateID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $stmt = $conn->prepare("UPDATE " . DBPREFIX . "newsletter SET news_title = ?, news_body = ? WHERE newsletterID = ?");
        $stmt->bind_param("sss", $tempTitle, $tempBody, $templateID);

        if ($stmt->execute()) {
            $_SESSION["msg"] = "tus";
        } else {
            $_SESSION["msg"] = "Error";
        }

    } else {

        $stmt = $conn->prepare("INSERT INTO " . DBPREFIX . "newsletter (news_title ,news_body) VALUES (?,?)");
        $stmt->bind_param("ss", $tempTitle, $tempBody);

        if ($stmt->execute()) {
            $_SESSION["msg"] = "tc";
        } else {
            $_SESSION["msg"] = "Error";
        }
    }
    mysqli_close($conn);

    redirect($redirect . "admin/admin_send.php");
    ob_end_flush();

}

if (isset($_POST["delete"])) {

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $tempplateID = test_input($_POST["tempid"]);

    $stmt = $conn->prepare("DELETE FROM " . DBPREFIX . "newsletter WHERE newsletterID =?");
    $stmt->bind_param("s", $tempplateID);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "del";
    } else {
        $_SESSION["msg"] = "Error";
    }
    mysqli_close($conn);

    redirect($redirect . "admin/admin_send.php");
    ob_end_flush();

}

include "../includes/header.php";
?>
<script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
<div id="main" class="container">
    <header>
        <h2 style="text-align:center;">Send a Newsletter</h2>
    </header>
    <?php
    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    if (isset($_POST["sendhtml"]) or isset($_POST["sendtxt"])) {

        $attachment = "";
        echo "<div class=\"row uniform\">\n";
        echo "  <div class=\"-2u 8u$\">\n";
        echo "    <h3>Output Window</h3><br />\n";
        echo "    <textarea rows=\"5\">\n";

        $email = $subject = $emailMsg = $semails = "";
        $check = false;
        $semails = $_POST["semail"];
        $count = count($semails);

        $sendas = $attachment = $uploadfile = "";
        $subject = $emailMsg = "";

        if (isset($_POST["subject"])) {
            $subject = test_input($_POST["subject"]);
        }

        if (isset($_POST['selectattach']) && $_POST["selectattach"] !== "")  {

            $baseDir = BASEDIR;
            $dir = str_replace("\\\\", "\\", $baseDir . "newsletter\\admin\\attachs\\");

            $attachment = $dir. $_POST['selectattach'];

        }

        $sql = "SELECT * FROM " . DBPREFIX . "addresses WHERE confirm = 'yes'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                foreach ($semails as $x => $value) {
                    $token = $row["token"];
                    if ($value == $row["email"]) {
                        $emailMsg = "";
                        $sendas = "html";
                        $email = $row["email"];
                        $emailMsg = $_POST["tempbody"];
                        $emailMsg = str_replace("#YEAR#",date("Y"),$emailMsg);
                        $emailMsg = str_replace("#SITETITLE#", SITETITLE, $emailMsg);
                        $emailMsg = str_replace("#CANCELREWRITE#", "<a href=\"" . $http . "://" . $domain . "/cancel/" . $token . "/yes/\">Unsubscribe</a>", $emailMsg);
                        $emailMsg = str_replace("#CANCELNOREWRITE#", "<a href=\"" . $http . "://" . $domain . NEWSDIR . "includes/process.php?token=" . $token . "&cancel=yes\">Unsubscribe</a>", $emailMsg);
                        $emailMsg = wordwrap($emailMsg, 70);

                        if (isset($_POST["sendtxt"])) {
                            $emailMsg = strip_tags($emailMsg);
                            $emailMsg = str_replace("&copy;", "", $emailMsg);
                            $sendas = "txt";
                        }

                        send_mail($email, $subject, $emailMsg, "yes", $attachment, $uploadfile, $sendas);

                    }
                }
            }
        }
        echo "    </textarea>\n";
        echo "  </div>\n";
        echo "</div>\n";
    }

    $sql = "SELECT * FROM " . DBPREFIX . "newsletter WHERE news_save = 'draft'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $intDCount = mysqli_num_rows($result);
    }

    $sql = "SELECT * FROM " . DBPREFIX . "newsletter WHERE news_save = 'template'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $intTCount = mysqli_num_rows($result);
    }
    ?>
    <div class="row uniform">
        <div class="-1u 5u 12u$(medium)">
            <h5>You have <?php echo $intDCount; ?> Drafts and <?php echo $intTCount; ?> Templates.</h5>
            <div class="row">
                <div class="12u$">
                    <a class="urlimg button fit fancybox.ajax" href="imageurls.php">Get Image URLs</a>
                </div>
            </div>
        </div>
        <div class="5u$ 12u$(medium)">
            <form action="uploadattach.php?dla=no&p=s" method="post" enctype="multipart/form-data">
                <input type="hidden" name="pfrom" value="send" />
                <h5>Upload Attachment</h5>
                <div class="row">
                    <div class="8u 12u$(medium)">
                        <input class="button" type="file" name="attachs[]" size="20" multiple />
                    </div>
                    <div class="4u$ 12u$(medium)">
                        <input type="submit" name="submit" value="Upload" class="button fit" />
                    </div>
                </div>
            </form>
        </div>
        <div class="-1u 5u 12u$(medium)">
            <label for="loadtemp">Template Title</label>
            <div class="select-wrapper">
                <?php selectLoadTemplate(); ?>
            </div>
        </div>
        <div class="5u$ 12u$(medium)">
            <label for="tempdescr">Template Description</label>
            <input type="text" id="tempdescr" name="tempdescr" value="" size="30" required />
        </div>
    </div>
    <form action="admin_send.php" id="template" method="post" enctype="multipart/form-data">
        <input type="hidden" name="tempid" id="tempid" value="" />
        <input type="hidden" name="temptitle" id="temptitle" value="" />
        <div class="row uniform">
            <div class="-1u 10u$ 12u$(medium)">
                <div class="12u 12u$(small)" style="padding-bottom:10px;">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" value="" size="30" required />
                </div>
                <div class="12u  12u$(small)">
                    <textarea name="tempbody" id="tempbody" cols="65" rows="25" wrap="soft"></textarea>
                    <script>
                          CKEDITOR.replace( 'tempbody');
                    </script>
                </div>

                <div class="row" style="padding-top:10px;">
                    <div class="4u 12u$(small)" style="text-align:center;">
                        <select id="semail" name="semail[]" size="5" style="height:75px;" multiple>
                            <?php
                            $counter = 0;
                            $sql = "SELECT * FROM " . DBPREFIX . "addresses WHERE confirm = 'yes'";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $counter = $counter + 1;
                                    $email = $row["email"];
                                    echo "                <option value=\"" . $email . "\">" . $counter . ". " . $email . "</option>\n";
                                }
                            }
                            mysqli_close($conn);
                            ?>
                        </select>
                    </div>
                    <div class="3u 12u$(small)">
                        <input type="button" class="button fit" value="Select All Emails" onclick="selectAll('semail');" />
                    </div>
                    <div class="5u$ 12u$(medium)">
						<div class="select-wrapper">
							<select name="selectattach" id="selectattach">
<?php
    $baseDir = BASEDIR;
    $dir = str_replace("\\\\", "\\", $baseDir . "newsletter\\admin\\attachs\\");
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            echo "<option value=\"\">Select Attachment</option>";
            while (($file = readdir($dh)) !== false) {
                if ($file == '.' or $file == '..') continue;
                echo "<option value=". $file .">" . $file . "</option>";
            }
            closedir($dh);
        } else{
            echo "<option value=\"\">No Attachments</option>";
        }
    }
 ?>
							</select>
							<label for="selectattach">Add Attachment</label>
						</div>
                    </div>
                    <div class="6u 12u$(small)" style="text-align:center;">
                        <input type="submit" name="sendhtml" class="button fit" value="Send as HTML" />
                    </div>
                    <div class="6u$ 12u$(small)" style="text-align:center;">
                        <input type="submit" name="sendtxt" class="button fit" value="Send as Plain Text" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include "../includes/footer.php" ?>
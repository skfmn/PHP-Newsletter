<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$msg = $cookies = $dir = "";
$cookies = $_SESSION["nwsadminname"];

if ($cookies == "") {

    redirect($redirect . "admin/login.php");
    ob_end_flush();

}

$blnTemplates = $_SESSION["blnTemplates"];
if ($blnTemplates == "false") {

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

$templateTitle = $templateDescr = $templateBody = $templateSave = "";

if (isset($_GET["save"])) {

    $templateTitle = test_input($_POST["temptitle"]);
    $templateDescr = test_input($_POST["tempdescr"]);
    $templateBody = test_input($_POST["tempbody"]);

    if ($_GET["save"] == "template") {
        $templateSave = "template";
    } else {
        $templateSave = "draft";
    }

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "newsletter WHERE news_title = ?");
    $stmt->bind_param("s", $templateTitle);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {

        redirect($redirect . "admin/admin_create.php?msg=nt");
        ob_end_flush();

    } else {

        $param1 = $username;
        $param2 = "no";
        $stmt = $conn->prepare("INSERT INTO " . DBPREFIX . "newsletter(news_title,news_save,news_description,news_body) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $templateTitle, $templateSave, $templateDescr, $templateBody);

        if ($stmt->execute()) {
            $msg = "tc";
        } else {
            $msg = "error";
        }

        redirect($redirect . "admin/admin_create.php?msg=" . $msg);
        ob_end_flush();

    }
    mysqli_close($conn);
}

include "../includes/header.php";
?>
<script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
<div id="main" class="container">
    <div class="row">
        <div class="-3u 6u 12u(medium)">
            <span>
                <a class="picimg fancybox.ajax" href="imageurls.php">Get Image URLs</a>
            </span>
        </div>
    </div>

    <header>
        <h2 style="text-align:center;">Create a Template</h2>
    </header>
    <form method="post">
        <div class="row uniform">
            <div class="-1u 10u$ 12u$(medium)">
                <div class="row uniform">
                    <div class="6u 12u$(small)" style="padding-bottom:10px;">
                        <label for="temptitle">
                            Template Title <span style="font-size:10px;">
                                (Titles in <span style="color:#ff0000;">RED</span>
                                are taken)
                            </span>
                        </label>
                        <input type="text" id="temptitle" name="temptitle" size="30" placeholder="Title of the Template" required />
                    </div>
                    <div class="6u$ 12u$(small)" style="padding-bottom:10px;">
                        <label for="tempdescr">Template Description</label>
                        <input type="text" id="tempdescr" name="tempdescr" size="30" placeholder="Description of the Template" required />
                    </div>

                    <div class="12u$">
                        <textarea name="tempbody" id="tempbody" wrap="soft" style="height:300px;" required></textarea>
                        <script>
                            CKEDITOR.filter.allowedContentRules = true;
                            CKEDITOR.config.format_tags = 'div;h1;h2;h3;pre';
                            CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
                            CKEDITOR.config.removeButtons = 'Templates,Save,Print,Flash,NewPage';
                            CKEDITOR.replace('tempbody', {
                                extraAllowedContent: 'header; content; footer; section; article'
                            });
                        </script>
                    </div>
                </div>
                <div class="row" style="padding-top:10px;text-align:center;">
                    <div class="6u 12u$(small)">
                        <input type="submit" name="save" value="Save as Template" class="button fit" formaction="admin_create.php?save=template" />
                    </div>
                    <div class="6u 12u$(small)">
                        <input type="submit" name="save" value="Save as Draft" class="button fit" formaction="admin_create.php?save=draft" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include "../includes/footer.php" ?>
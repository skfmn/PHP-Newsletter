<?php
$siteTitle = $domain = $smtpServer = $smtpPort = $smtpEmail = $smtpPassword = "";
$smtpDebug = $smtpsecure = $rewrite = $smtpuse = $confirmemail = "";

//$version = "1.2.1";

$saveFile = "";
$saveFile = $_SERVER["APPL_PHYSICAL_PATH"] . str_replace("/", "\\", NEWSDIR) . "admin\images";
$saveFile = str_replace("\\\\", "\\", $saveFile);
define("SAVEFILE", $saveFile);

$cookieID = "";
if (isset($_SESSION["nwsadminID"])) {
    $cookieID = $_SESSION["nwsadminID"];
}

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

if (!$conn) {

    die("Connection failed: " . mysqli_connect_error());

}

$sql = "SELECT * FROM " . DBPREFIX . "settings";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    $siteTitle = $row["site_title"];
    $domain = $row["domain_name"];
    $smtpServer = $row["smtp_server"];
    $smtpPort = $row["smtpport"];

    if ($smtpPort == "465") {
        $smtpsecure = "ssl";
    } else if ($smtpPort == "587" or $smtpPort == "2525") {
        $smtpsecure = "tls";
    } else {
        $smtpsecure = "";
    }

    $smtpEmail = $row["email_address"];
    $smtpPassword = $row["smtp_password"];
    $smtpDebug = $row["smtpdebug"];
    if ($smtpDebug == "yes") {
        $smtpDebug = 2;
    } else {
        $smtpDebug = 0;
    }
    $smtpuse = $row["smtpuse"];
    $rewrite = $row["rewrite"];
    $confirmemail = $row["confirm_email"];

    define('SITETITLE', $siteTitle);
    define('DOMAIN', $domain);
    define('SMTPSERVER', $smtpServer);
    define('SMTPPORT', $smtpPort);
    define('SMTPEMAIL', $smtpEmail);
    define('SMTPPASSWORD', $smtpPassword);
    define('SMTPDEBUG', $smtpDebug);
    define('SMTPSECURE', $smtpsecure);
    define('SMTPUSE', $smtpuse);
    define('REWRITE', $rewrite);
    define('CONFIRMEMAIL', $confirmemail);

}
mysqli_close($conn);

if ($cookieID <> "") {

    $_SESSION["loggedin"] = "yes";

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM " . DBPREFIX . "admin WHERE adminID = " . $cookieID;

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $_SESSION["blnSend"] = $row["send"];
        $_SESSION["blnAddresses"] = $row["addresses"];
        $_SESSION["blnImages"] = $row["images"];
        $_SESSION["blnTemplates"] = $row["templates"];
        $_SESSION["blnOptions"] = $row["options"];
        $_SESSION["blnAdminRights"] = $row["admins_rights"];
        $_SESSION["blnARights"] = $row["arights"];

    }

    mysqli_close($conn);

}

function rrmdir($src)
{
    $dir = opendir($src);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            $full = $src . '/' . $file;
            if (is_dir($full)) {
                rrmdir($full);
            } else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    rmdir($src);
}

function trace($stxt) {
    echo $stxt . "<br />";
}

function send_mail($tMail, $tSubject, $tMsg, $tFlag, $sAttach, $tUploadfile, $tSendas)
{

    if ($sAttach !== "") {

        if (SMTPUSE == "yes") {

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP();
            $mail->CharSet = "UTF-8";
            $mail->Host = SMTPSERVER;
            $mail->SMTPDebug = SMTPDEBUG;
            $mail->Port = SMTPPORT;
            $mail->SMTPSecure = SMTPSECURE;
            $mail->SMTPAuth = true;
            $mail->SMTPKeepAlive = true;
            if ($tSendas == "txt") {
                $mail->IsHTML(false);
            } else {
                $mail->IsHTML(true);
            }
            $mail->Username = SMTPEMAIL;
            $mail->Password = SMTPPASSWORD;
            $mail->SetFrom(SMTPEMAIL);
            $mail->AddAddress($tMail);
            $mail->Subject = $tSubject;
            $mail->Body = $tMsg;

            if (!$mail->addAttachment($sAttach)) {
                if ($tFlag == "yes") {
                    echo 'Failed to attach file ' . $sAttach . "\n";
                } else {
                    return false;
                }
            }

            if (!$mail->send()) {
                if ($tFlag == "yes") {
                    echo 'Mailer Error: ' . $mail->ErrorInfo . "\n";
                } else {
                    return false;
                }

            } else {

                if ($tFlag == "yes") {
                    echo "Newsletter has been sent to " . $tMail . "\n";
                } else {
                    return true;
                }
            }

        } else {

            //Uses built in PHP mail()
            $ext = PHPMailer\PHPMailer\PHPMailer::mb_pathinfo($sAttach['userfile']['name'], PATHINFO_EXTENSION);

            $newFileName = PHPMailer\PHPMailer\PHPMailer::mb_pathinfo($sAttach['userfile']['name'], PATHINFO_BASENAME);
            $newFileName = str_replace("." . $ext, "", $newFileName);

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->setFrom(SMTPEMAIL);
            $mail->addAddress($tMail);
            $mail->Subject = $tSubject;
            $mail->Body = $tMsg;

            if (!$mail->addAttachment($tUploadfile, $newFileName)) {
                if ($tFlag == "yes") {
                    echo 'Failed to attach file ' . $tUploadfile . " - " . $newFileName . "\n";
                } else {
                    return false;
                }
            }

            if (!$mail->send()) {
                if ($tFlag == "yes") {
                    echo 'Mailer Error: ' . $mail->ErrorInfo . "\n";
                } else {
                    return false;
                }

            } else {

                if ($tFlag == "yes") {
                    echo "Newsletter has been sent to " . $tMail . "\n";
                } else {
                    return true;
                }
            }
            sleep(1);
            //Needed so PHP mail() has time to process each request.
        }

    } else {

        if (SMTPUSE == "yes") {

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP();
            $mail->CharSet = "UTF-8";
            $mail->Host = SMTPSERVER;
            $mail->SMTPDebug = SMTPDEBUG;
            $mail->Port = SMTPPORT;
            $mail->SMTPSecure = SMTPSECURE;
            $mail->SMTPAuth = true;
            $mail->SMTPKeepAlive = true;
            if ($tSendas == "txt") {
                $mail->IsHTML(false);
            } else {
                $mail->IsHTML(true);
            }
            $mail->Username = SMTPEMAIL;
            $mail->Password = SMTPPASSWORD;
            $mail->SetFrom(SMTPEMAIL);
            $mail->AddAddress($tMail);
            $mail->Subject = $tSubject;
            $mail->Body = $tMsg;

            if (!$mail->Send()) {

                if ($tFlag == "yes") {
                    echo "Mailer Error: Newsletter was not sent to " . $tMail . "\n";
                } else {
                    return false;
                }
            } else {
                if ($tFlag == "yes") {
                    echo "Newsletter has been sent to " . $tMail . "\n";
                } else {
                    return true;
                }
            }

        } else {

            //Uses built in PHP mail()
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->setFrom(SMTPEMAIL);
            $mail->addAddress($tMail);
            $mail->Subject = $tSubject;
            $mail->Body = $tMsg;

            if (!$mail->Send()) {

                if ($tFlag == "yes") {
                    echo "Mailer Error: Newsletter was not sent to " . $tMail . "\n";
                } else {
                    return false;
                }
            } else {
                if ($tFlag == "yes") {
                    echo "Newsletter has been sent to " . $tMail . "\n";
                } else {
                    return true;
                }
            }
            sleep(1);
            //Needed so PHP mail() has time to process each request.
        }
    }
}

function getEndMsg($sMsgName, $sEmail)
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $param1 = $sMsgName;
    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "endMsg WHERE endMsgName  = ?");
    $stmt->bind_param("s", $param1);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $endMsg = $row["endMsg"];
        $endMsg = str_replace("#email#", $sEmail, $endMsg);
        $endMsg = str_replace("#sitetitle#", SITETITLE, $endMsg);

        echo "    <div class=\"row\">\n";
        echo "      <div class=\"-3u 6u$ 12u$(small)\">\n";
        echo "        " . $endMsg . "\n";
        echo "      </div>\n";
        echo "    </div>\n";

    }
    mysqli_close($conn);
}

function selectDeleteEmail()
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $param1 = "yes";
    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "addresses WHERE confirm  = ?");
    $stmt->bind_param("s", $param1);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<select id=\"email\" name=\"email[]\" multiple>\n";
        while ($row = $result->fetch_assoc()) {
            echo "<option value=\"" . $row["email"] . "\">" . $row["email"] . "</option>\n";
        }
        echo "</select>\n";
    } else {
        echo "<select id=\"email\" name=\"email\"><option>No Addresses</option></select>\n";
    }
    mysqli_close($conn);

}

function getUserMessage($sMsgName)
{

    $strTemp = "";

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $param1 = $sMsgName;
    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "endMsg WHERE endMsgName  = ?");
    $stmt->bind_param("s", $param1);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $strTemp = trim($row["endMsg"]);
    } else {
        $strTemp = $sMsgName;
    }

    return $strTemp;
    mysqli_close($conn);
}

function msgTrans($sMsg)
{
    $strtmp = "";
    switch ($sMsg) {
        case "eas":
            $strtmp = "Email address added:";
            break;
        case "aid":
            $strtmp = "Address already in DB:";
            break;
        case "ds":
            $strtmp = "Delete action successful:";
            break;
        case "nea":
            $strtmp = "Forgot email address:";
            break;
        case "uls":
            $strtmp = "File(s) uploaded:";
            break;
        case "ids":
            $strtmp = "File(s) deleted:";
            break;
        case "nadmin":
            $strtmp = "Can't change Admin info:";
            break;
        case "del":
            $strtmp = "Template deleted:";
            break;
        case "das":
            $strtmp = "Deleted an Admin:";
            break;
        case "adad":
            $strtmp = "Added an Admin:";
            break;
        case "nt":
            $strtmp = "Name taken:";
            break;
        case "tc":
            $strtmp = "Template created:";
            break;
        case "car":
            $strtmp = "Changed Admin Rights:";
            break;
        case "ulf":
            $strtmp = "Upload failed:";
            break;
        case "nwst":
            $strtmp = "Newsletter sent:";
            break;
        case "ant":
            $strtmp = "Admin name taken:";
            break;
        case "confirmed":
            $strtmp = "Email confirmed:";
            break;
        case "confirmerr":
            $strtmp = "Confirm. email error:";
            break;
        case "ftna":
            $strtmp = "Allowable extensions:";
            break;
        case "fex":
            $strtmp = "File Exists:";
            break;
        case "nimg":
            $strtmp = "Not an image:";
            break;
        case "tus":
            $strtmp = "Template Updated:";
            break;
        case "adderr":
            $strtmp = "Couldn't add to list:";
            break;
        case "thanks":
            $strtmp = "Successfully added:";
            break;
        case "thankserr":
            $strtmp = "Added - problem sending email:";
            break;
        case "alreadysubbed":
            $strtmp = "Already Subscribed:";
            break;
        case "removed":
            $strtmp = "Removed from list:";
            break;
        case "notfound":
            $strtmp = "Email not found:";
            break;
        case "removederr":
            $strtmp = "Couldn't remove from list:";
            break;
        case "mus":
            $strtmp = "Messages updated:";
            break;
        case "error":
            $strtmp = "Generic error:";
            break;
        case "siu":
            $strtmp = "Site info updated:";
            break;
        case "cpwds":
            $strtmp = "Changed your password:";
            break;
        case "nar":
            $strtmp = "No Admin Rights:";
            break;
        default:
            $strtmp = "If you see this you messed with the code!";
    }

    return $strtmp;
}

function selectLoadTemplate()
{


    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $param1 = "template";
    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "newsletter WHERE news_save = ?");
    $stmt->bind_param("s", $param1);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        echo "<select name=\"loadtemp\" id=\"loadtemp\" onchange=\"return loadTemplate(this.options[this.selectedIndex].value);\">\n";
        echo "  <option value=\"0\">Load Template</option>\n";
        while ($row = $result->fetch_assoc()) {
            echo "  <option value=\"" . $row["newsletterID"] . "\">" . $row["news_title"] . "</option>\n";
        }
        echo "</select>\n";

    } else {

        echo "<select><option>No Templates</option></select>\n";

    }
    mysqli_close($conn);
}

function selectLoadDraft()
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $param1 = "draft";
    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "newsletter WHERE news_save = ?");
    $stmt->bind_param("s", $param1);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        echo "<select name=\"loadtemp\" id=\"loadtemp\" onchange=\"return loadTemplate(this.options[this.selectedIndex].value);\">\n";
        echo "  <option value=\"0\">Load Draft</option>\n";
        while ($row = $result->fetch_assoc()) {
            echo "  <option value=\"" . $row["newsletterID"] . "\">" . $row["news_title"] . "</option>\n";
        }
        echo "</select>\n";

    } else {

        echo "<select><option>No Draft</option></select>\n";

    }
    mysqli_close($conn);
}

function randChrs($num)
{

    $sWord = $rchr = "";
    $icount = 0;
    for ($x = 0; $x <= 200; $x++) {
        $rchr = chr(rand(27, 126));
        $pattern = "/[A-Z0-9]/";

        if (preg_match($pattern, $rchr)) {
            $sWord = $sWord . $rchr;
            $icount++;
            if ($icount === $num) {
                break;
            }
        }
    }
    return $sWord;
}

function deleteDir($path)
{

    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            deleteDir(realpath($path) . '/' . $file);
        }

        return rmdir($path);

    } else if (is_file($path) === true) {

        return unlink($path);
    }

    return false;
}

// Snippet from PHP Share: http://www.phpshare.org
function formatSizeUnits($bytes)
{

    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } else if ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } else if ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } else if ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } else if ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function getMessage($sMsg)
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $strTemp = "";
    $sql = "SELECT message FROM " . DBPREFIX . "messages WHERE msg = '" . trim($sMsg) . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $strTemp = $row["message"];
    } else {
        $strTemp = $sMsg;
    }
    mysqli_close($conn);

    return $strTemp;

}

function displayFancyMsg($sText)
{
?>
<div style="display: none">
    <a id="textmsg" href="#displaymsg">Message</a>
    <div id="displaymsg" style="width: 300px;">
        <h2 style="text-align: left;">Message</h2>
        <div style="text-align: center;">
            <span style="color: #FF0000;">
                <?php echo $sText; ?>
            </span>
        </div>
        <div class="left_menu_bottom"></div>
    </div>
</div>
<?php
}

function redirect($location)
{
    if ($location) {

        header('Location: ' . $location);
        exit;

    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function test_inputA($data)
{
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
}

function getNumtxt($sNum)
{

    $strTemp = "";

    switch ($sNum) {
        case 1:
            $strTemp = "11.png";
            break;
        case 2:
            $strTemp = "12.png";
            break;
        case 3:
            $strTemp = "13.png";
            break;
        case 4:
            $strTemp = "14.png";
            break;
        case 5:
            $strTemp = "15.png";
            break;
        case 6:
            $strTemp = "16.png";
            break;
        case 7:
            $strTemp = "17.png";
            break;
        case 8:
            $strTemp = "18.png";
            break;
        case 9:
            $strTemp = "19.png";
            break;
        default:
            $strTemp = "20.png";
    }

    return $strTemp;

}

?>
<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$cookies = $dir = $username = $password = $encrPassword = $chmsgs = $count = "";
$siteTitle = $urlrewrite = $smtpServer = $smtpEmail = $smtpPassword = $smtpDebug = $smtpuse = "";
$msg = $chvalue = "";

$cookies = $_SESSION["nwsadminname"];

if ($cookies == "") {

    redirect($redirect . "admin/login.php");
    ob_end_flush();

}

$blnOptions = $_SESSION["blnOptions"];
if ($blnOptions == "false") {

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

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

if (!$conn) {

    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST["chmsg"])) {

    $chmsgs = "";
    $chmsgs = $_POST["messages"];
    $count = count($chmsgs);

    foreach ($chmsgs as $x => $x_value) {

        $param1 = $param2 = "";
        $param1 = trim($x_value);
        $param2 = trim($x);
        $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "messages SET message = ? WHERE msg = ?");
        $stmt->bind_param('ss', $param1, $param2);

        if ($stmt->execute()) {
            $_SESSION["msg"] = "mus";
        } else {
            $_SESSION["msg"] = "error";
        }
    }

    redirect($redirect . "admin/admin_options.php");
    ob_end_flush();

}

if (isset($_POST["chumsg"])) {

    $chumsgs = "";
    $chumsgs = $_POST["usermessages"];
    $count = count($chumsgs);

    foreach ($chumsgs as $x => $x_value) {

        $param1 = $param2 = "";
        $param1 = trim($x_value);
        $param2 = trim($x);
        $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "endMsg SET endMsg = ? WHERE endMsgName = ?");
        $stmt->bind_param('ss', $param1, $param2);

        if ($stmt->execute()) {
            $_SESSION["msg"] = "mus";
        } else {
            $_SESSION["msg"] = "error";
        }
    }

    redirect($redirect . "admin/admin_options.php");
    ob_end_flush();

}

if (isset($_POST["chmstgs"])) {

    if (isset($_POST["sitetitle"])) {
        $siteTitle = test_input($_POST["sitetitle"]);
    }
    if (isset($_POST["smtpserver"])) {
        $smtpServer = test_input($_POST["smtpserver"]);
    }
    if (isset($_POST["smtpemail"])) {
        $smtpEmail = test_input($_POST["smtpemail"]);
    }
    if (isset($_POST["smtppassword"])) {
        $smtpPassword = test_input($_POST["smtppassword"]);
    }

    if (isset($_POST["smtpport"])) {
        $smtpPort = test_input($_POST["smtpport"]);
    }

    $urlrewrite = "no";
    if (isset($_POST["urlrewrite"])) {
        $urlrewrite = test_input($_POST["urlrewrite"]);
        if ($urlrewrite == "on") {
            $urlrewrite = "yes";
        }
    }

    $smtpDebug = "no";
    if (isset($_POST["smtpdebug"])) {
        $smtpDebug = test_input($_POST["smtpdebug"]);
        if ($smtpDebug == "on") {
            $smtpDebug = "yes";
        }
    }

    $smtpUse = "no";
    if (isset($_POST["smtpuse"])) {
        $smtpUse = test_input($_POST["smtpuse"]);
        if ($smtpUse == "on") {
            $smtpUse = "yes";
        }
    }

    $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "settings SET site_title = ?, smtp_server = ?, email_address = ?, smtp_password = ?, smtpdebug = ?, smtpuse= ?, rewrite = ?, smtpport = ?");
    $stmt->bind_param('ssssssss', $siteTitle, $smtpServer, $smtpEmail, $smtpPassword, $smtpDebug, $smtpUse, $urlrewrite, $smtpPort);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "siu";
    } else {
        $_SESSION["msg"] = "error";
    }

    redirect($redirect . "admin/admin_options.php");
    ob_end_flush();

}

if (isset($_POST["confirmemail"])) {

    $param1 = "";
    $param1 = trim($_POST["tempbody"]);
    $param1 = str_replace("\n", "", $param1);

    $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "settings SET confirm_email = ?");
    $stmt->bind_param('s', $param1);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "siu";
    } else {
        $_SESSION["msg"] = "error";
    }

    redirect($redirect . "admin/admin_options.php");
    ob_end_flush();
}

include "../includes/header.php";
?>
<div id="main" class="container">
  <header>
    <h2>Manage options</h2>
  </header>
  <div class="row uniform">
    <div class="6u 12u$(medium)">
      <h3>Admin Messages</h3>
<?php
$sql = "SELECT * FROM " . DBPREFIX . "messages";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    ?>
      <div class="12u$" style="padding-bottom:10px;">
        <div class="table-wrapper">
          <form action="admin_options.php" method="post">
          <input type="hidden" name="chmsg" value="y">
          <table>
            <tbody>
<?php
        while ($row = $result->fetch_assoc()) {
            $tempMsg = "";
            $tempMsg = $row["msg"];
            ?>
              <tr>
                <td style="width:30%;">
                  <?php echo trim(msgTrans($tempMsg)); ?>
                </td>
                <td style="width:70%;">
                  <input type="text" name="messages[<?php echo $tempMsg; ?>]" value="<?php echo trim($row["message"]); ?>">
                </td>
              </tr>
<?php
        }
        ?>
              <tfoot>
                <tr>
                  <td colspan="2"><input type=submit value="Save Admin Messages" class="button fit"></td>
                </tr>
              </tfoot>
            </tbody>
          </table>
          </form>
        </div>
<?php
}
?>
      </div>
    <div class="12u$">
        <hr class="major" style="margin: 1em 0;" />
    </div>
      <h3>User Messages</h3>
<?php

$sql = "SELECT * FROM " . DBPREFIX . "endMsg";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    ?>
      <div class="12u$" style="padding-bottom:10px;">
        <div class="table-wrapper">
          <form action="admin_options.php" method="post">
          <input type="hidden" name="chumsg" value="y">
          <table>
            <tbody>
<?php
        while ($row = $result->fetch_assoc()) {
            $tMsg = $tMsgName = "";
            $tMsg = $row["endMsg"];
            $tMsgName = $row["endMsgName"];
            ?>
              <tr>
                <td style="width:30%;">
                  <?php echo trim(msgTrans($tMsgName)); ?>
                </td>
                <td style="width:70%;">
                  <textarea name="usermessages[<?php echo $tMsgName; ?>]"><?php echo $tMsg; ?></textarea>
                </td>
              </tr>
<?php
        }
        ?>
              <tfoot>
                <tr>
                  <td colspan="2">
                    <input type=submit value="Save User Messages" class="button fit">
                    Use: #EMAIL# for users email address. Use: #SITETITLE# to insert your sites title.
                  </td>
                </tr>
              </tfoot>
            </tbody>
          </table>
          </form>
        </div>
<?php
}
mysqli_close($conn);
?>
      </div>

    </div>
    <div class="6u$ 12u$(medium)">
      <h3>Site Settings</h3>
<?php

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

if (!$conn) {

    die("Connection failed: " . mysqli_connect_error());
}

$siteTitle = $urlrewrite = $smtpServer = $smtpEmail = "";
$onoff = $smtpPassword = $smtpPort = $smtpDebug = $smtpUse = "";

$sql = "SELECT * FROM " . DBPREFIX . "settings";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $siteTitle = $row["site_title"];
    $urlrewrite = $row["rewrite"];
    $smtpServer = $row["smtp_server"];
    $smtpPort = $row["smtpport"];
    $smtpEmail = $row["email_address"];
    $smtpPassword = $row["smtp_password"];
    $smtpDebug = $row["smtpdebug"];
    $smtpUse = $row["smtpuse"];

    if ($urlrewrite == "yes") {
        $urlchecked = "checked";
        $urlonoff = "On";
    } else {
        $urlchecked = "";
        $urlonoff = "Off";
    }

    if ($smtpDebug == "yes") {
        $debchecked = "checked";
        $debonoff = "On";
    } else {
        $debchecked = "";
        $debonoff = "Off";
    }

    if ($smtpUse == "yes") {
        $usechecked = "checked";
        $useonoff = "On";
    } else {
        $usechecked = "";
        $useonoff = "Off";
    }

    $confirmemail = str_replace("\n", "", $confirmemail);
    $confirmemail = str_replace("\r", "", $confirmemail);
    ?>
      <div class="row">
        <div class="12u$" style="padding-bottom:10px;">
          <div class="table-wrapper">
            <form action="admin_options.php" method="post">
            <input type="hidden" name="chmstgs" value="y">
            <table>
              <tbody>

                <tr>
                  <td style="width:30%;">
                    Site Title
                  </td>
                  <td style="width:70%;">
                    <input type="text" name="sitetitle" value="<?php echo $siteTitle; ?>">
                  </td>
                </tr>

                <tr>
                  <td style="width:30%;">
                    URL Rewrite
                  </td>
                  <td style="width:70%;">
                    <input type="checkbox" id="urlrewrite" name="urlrewrite" <?php echo $urlchecked; ?> >
                    <label for="urlrewrite"><span style="font-size:1.2em;font-weight:bold;"><?php echo $urlonoff; ?></span></label>
                  </td>
                </tr>

                <tr>
                  <td style="width:30%;">
                    Use SMTP
                  </td>
                  <td style="width:70%;">
                    <input type="checkbox" id="smtpuse" name="smtpuse" <?php echo $usechecked; ?> >
                    <label for="smtpuse"><span style="font-size:1.2em;font-weight:bold;"><?php echo $useonoff; ?></span></label>
                  </td>
                </tr>

                <tr>
                  <td style="width:30%;">
                    SMTP Server
                  </td>
                  <td style="width:70%;">
                    <input type="text" name="smtpserver" value="<?php echo $smtpServer; ?>">
                  </td>
                </tr>

                <tr>
                  <td style="width:30%;">
                    SMTP Port
                  </td>
                  <td style="width:70%;">
                    <div class="select-wrapper" style="width:85px;">
                      <select name="smtpport" style="width:80px;">
                        <option value="25" <?php if ($smtpPort == "25") {
                            echo "selected";
                        } ?>>25</option>
                        <option value="80" <?php if ($smtpPort == "80") {
                            echo "selected";
                        } ?>>80</option>
                        <option value="465" <?php if ($smtpPort == "465") {
                            echo "selected";
                        } ?>>465</option>
                        <option value="587" <?php if ($smtpPort == "587") {
                            echo "selected";
                        } ?>>587</option>
                        <option value="2525" <?php if ($smtpPort == "2525") {
                            echo "selected";
                        } ?>>2525</option>
                      </select>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td style="width:30%;">
                    SMTP Email address
                  </td>
                  <td style="width:70%;">
                    <input type="text" name="smtpemail" value="<?php echo $smtpEmail; ?>">
                  </td>
                </tr>

                <tr>
                  <td style="width:30%;vertical-align:middle;">
                    SMTP Password
                  </td>
                  <td style="width:70%;vertical-align:middle;">
                    <div class="input-wrapper">
                      <input type="password" id="smtppwd" name="smtppassword" value="<?php echo $smtpPassword; ?>"><br />
                      <i id="shpwd" onclick="togglePass('smtppwd','shpwd')" style="cursor:pointer;" class="fa fa-eye-slash shpwd"></i>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td style="width:30%;">
                    SMTP Debug
                  </td>
                  <td style="width:70%;">
                    <input type="checkbox" id="smtpdebug" name="smtpdebug" <?php echo $debchecked; ?> >
                    <label for="smtpdebug"><span style="font-size:1.2em;font-weight:bold;"><?php echo $debonoff; ?></span></label>
                  </td>
                </tr>

                <tfoot>
                  <tr>
                    <td colspan="2"><input type=submit value="Save Settings" class="button fit"></td>
                  </tr>
                </tfoot>
              </tbody>
            </table>
            </form>
          </div>
        </div>
        <div class="12u$">
            <hr class="major" style="margin: 1em 0;" />
        </div>
        <div class="12u$">
            <div class="row">
                <div class="6u 12u$(medium)"><h3>Confirmation Email</h3></div>
                <div class="6u$ 12u$(medium)"><a class="button picimg" href="#notice" style="font-size:12px;float:right;">HELP</a></div>
            </div>

            <form action="admin_options.php" method="post">
                <input type="hidden" name="confirmemail" value="yes" />
                <textarea name="tempbody" id="tempbody" rows="25" wrap="soft"></textarea>
                <script>
                    CKEDITOR.replace( 'tempbody', {
                    height: 250,
                    customConfig: '<?php echo NEWSDIR; ?>assets/js/email-config.js'
                    });
                    CKEDITOR.instances.tempbody.setData('<?php echo $confirmemail; ?>');
                </script>
                <input class="button fit" type="submit" name="submit" value="Save Email" style="margin-top:10px;" />
            </form>
        </div>
        <div class="12u$">
            <hr class="major" style="margin: 1em 0;" />
        </div>
         <div class="12u$">
            <h3>Attachments</h3>
            <form action="uploadattach.php?dla=no&p=o" method="post" enctype="multipart/form-data">
                <h5>Upload Attachment</h5>
                <div class="row">
                    <div class="8u 12u$(medium)">
                        <input class="button fit" type="file" name="attachs[]" size="20" multiple />
                    </div>
                    <div class="4u$ 12u$(medium)">
                        <input type="submit" name="submit" value="Upload" class="button fit" />
                    </div>
                </div>
            </form>
            Delete Attachment(s)
            <form action="uploadattach.php?dla=yes&p=o" method="post">
                <div class="row">
                    <div class="7u 12u$(medium)">
                        <div class="select-wrapper">
                            <select name="selectattach[]" id="selectattach" size="4" multiple>
<?php
        $baseDir = BASEDIR;
        $dir = str_replace("\\\\", "\\", $baseDir . "newsletter\\admin\\attachs\\");
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' or $file == '..')
                        continue;
                    echo "<option value=". $file .">" . $file . "</option>";
                }
                closedir($dh);
            } else {
                echo "<option value=\"\">No Attachments</option>";
            }
        }
        ?>
							</select>
                        </div>
                    </div>
                    <div class="5u$ 12u$(medium)">
                        <input class="button fit" type="submit" name="submit" value="Delete Attachment(s)" />
                    </div>
                </div>
            </form>
         </div>
        <div class="12u$">
            <hr class="major" style="margin: 1em 0;" />
        </div>
        <div class="12u$">
          <h3>URL Rewrite</h3>
          <span>If you have modRewrite for either Windows or Unix you can add the code below to the appropriate file then upload it to the root folder of your website. if you are using a folder other than "newsletter" you will need to change it in the files.</span>
          <br /><br />
          <span><strong>Windows:</strong><br />Create a file called web.config add the code below.</span>
          <pre>
            <code>
<?php
        echo htmlentities(
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<configuration>
  <system.webServer>
    <rewrite>
      <rules>
        <rule name=\"Rewrite signup to friendly URL\">
          <match url=\"^process/([\~\-\@a-z-]+)/([a-z-]+)\" />
          <action type=\"Rewrite\" url=\"/newsletter/includes/process.php?email={R:1}&amp;mode={R:2}\" />
        </rule>
        <rule name=\"Rewrite remove to friendly URL\">
          <match url=\"^remove\" />
          <action type=\"Rewrite\" url=\"/newsletter/includes/process.php?mode=cancel\" />
        </rule>
        <rule name=\"Rewrite subscribe to friendly URL\">
          <match url=\"^subscribe\" />
          <action type=\"Rewrite\" url=\"/newsletter/includes/process.php\" />
        </rule>
        <rule name=\"Rewrite unsubscrib to friendly URL\">
          <match url=\"^unsubscribe\" />
          <action type=\"Rewrite\" url=\"/newsletter/remove.php\" />
        </rule>
        <rule name=\"Rewrite thankyou to friendly URL\">
          <match url=\"^thankyou\" />
          <action type=\"Rewrite\" url=\"/newsletter/includes/process.php?thank=you\" />
        </rule>
      </rules>
    </rewrite>
  </system.webServer>
</configuration>"
        )
            ?>
            </code>
          </pre>
          <span><strong>Unix, et al:</strong><br />Create a file called .htaccess add the code below.</span>
          <pre>
            <code>
<?php
        echo htmlentities(
            "RewriteEngine on
RewriteRule ^process/([\~\-a-z-]+)/([a-z-]+) /newsletter/includes/process.php?email=$1&amp;mode=$2
RewriteRule ^remove /newsletter/includes/process.php?mode=cancel
RewriteRule ^subscribe /newsletter/includes/process.php
RewriteRule ^unsubscribe /newsletter/includes/remove.php
RewriteRule ^thankyou /newsletter/includes/process.php?thank=you"
        )
            ?>
            </code>
          </pre>
        </div>
<?php
}
mysqli_close($conn);
?>
      </div>
    </div>
  </div>
</div>
<div style="display:none;max-width:600px;" id="notice">
    <h2>HELP</h2>
    <p>
        You can use the following snippets:
        <ul>
            <li>#SITETITLE# - Sites Title</li>
            <li>#EMAIL# - Subscribers email</li>
            <li>#CR# - For the &copy; symbol</li>
            <li>#YEAR# - For the current year</li>
            <li>#CONFIRMREWRITE# - Confirmation link if Rewrite is enabled</li>
            <li>#CONFIRMNOREWRITE# - Confirmation link if Rewrite is disabled</li>
            <li>#CANCELREWRITE# - Cancellation link if Rewrite is enabled</li>
            <li>#CANCELNOREWRITE# - Cancellation link if Rewrite is disabled</li>
        </ul>
        NOTE: Confirmation and Cancel links will be auto generated depending on the snippet.
    </p>
</div>
<?php include "../includes/footer.php" ?>
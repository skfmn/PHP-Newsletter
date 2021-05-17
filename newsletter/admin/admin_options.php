<?php
  session_start();
  ob_start();
  include '../includes/globals.php';
  include '../includes/functions.php';

  $cookies = $dir = $username = $password = $encrPassword = $chmsgs = $count = "";
  $siteTitle = $urlrewrite = $smtpServer = $smtpEmail = $smtpPassword = $smtpDebug = $smtpuse = "";
  $msg = $chvalue =  "";

	$cookies = $_SESSION["nwsadminname"];

	If ($cookies == "") {

    redirect($redirect."admin/login.php");
    ob_end_flush();
  
	}

  $blnOptions = $_SESSION["blnOptions"];
	if ($blnOptions == "false") {

    redirect($redirect."admin/admin.php?msg=nar");
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

    foreach($chmsgs as $x => $x_value) {

      $param1 = $param2 = "";
      $param1 = trim($x_value);
      $param2 = trim($x);
      $stmt = mysqli_prepare($conn,"UPDATE ".DBPREFIX."messages SET message = ? WHERE msg = ?");
      $stmt->bind_param('ss', $param1, $param2);

		  if ($stmt->execute()) {
			  $_SESSION["msg"] = "mus";
      } else {
        $_SESSION["msg"] = "error";
      }
    }

    redirect($redirect."admin/admin_options.php");
    ob_end_flush();
		
	}

	if (isset($_POST["chumsg"])) {

    $chumsgs = "";
	  $chumsgs = $_POST["usermessages"];
    $count = count($chumsgs);
    
    foreach($chumsgs as $x => $x_value) {

      $param1 = $param2 = "";
      $param1 = trim($x_value);
      $param2 = trim($x);
      $stmt = mysqli_prepare($conn,"UPDATE ".DBPREFIX."endMsg SET endMsg = ? WHERE endMsgName = ?");
      $stmt->bind_param('ss', $param1, $param2);

		  if ($stmt->execute()) {
			  $_SESSION["msg"] = "mus";
      } else {
        $_SESSION["msg"] = "error";
      }
    }

    redirect($redirect."admin/admin_options.php");
    ob_end_flush();
		
	}

	if (isset($_POST["chmstgs"])) {

 
    if (isset($_POST["sitetitle"])) { $siteTitle = test_input($_POST["sitetitle"]); }
    if (isset($_POST["smtpserver"])) { $smtpServer = test_input($_POST["smtpserver"]); }
    if (isset($_POST["smtpemail"])) { $smtpEmail = test_input($_POST["smtpemail"]); }
    if (isset($_POST["smtppassword"])) { $smtpPassword = test_input($_POST["smtppassword"]); }

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

    $stmt = mysqli_prepare($conn,"UPDATE ".DBPREFIX."settings SET site_title = ?, smtp_server = ?, email_address = ?, smtp_password = ?, smtpdebug = ?, smtpuse= ?, rewrite = ?");
    $stmt->bind_param('sssssss', $siteTitle, $smtpServer, $smtpEmail, $smtpPassword, $smtpDebug, $smtpUse, $urlrewrite);

		if ($stmt->execute()) {
			$_SESSION["msg"] = "siu";
    } else {
      $_SESSION["msg"] = "error";
    }

    redirect($redirect."admin/admin_options.php");
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
    $sql = "SELECT * FROM ".DBPREFIX."messages";
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
      while($row = $result->fetch_assoc()) {
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
      <h3>User Messages</h3>
<?php
    
    $sql = "SELECT * FROM ".DBPREFIX."endMsg";
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
      while($row = $result->fetch_assoc()) {
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
                    Use: #email# for users email address. Use: #sitetitle# to insert your sites title.
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

  $sql = "SELECT * FROM ".DBPREFIX."settings";
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
                        <option value="25" <?php if ($smtpPort == "25") {echo "selected";} ?>>25</option>
                        <option value="587" <?php if ($smtpPort == "587") {echo "selected";} ?>>587</option>
                        <option value="465" <?php if ($smtpPort == "465") {echo "selected";} ?>>465</option>
                        <option value="2525" <?php if ($smtpPort == "2525") {echo "selected";} ?>>2525</option>
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
          <action type=\"Rewrite\" url=\"/newsletter/includes/process.php?thanks=you\" />
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
RewriteRule ^unsubscribe /newsletter/includes/process.php
RewriteRule ^thankyou /newsletter/includes/process.php?thanks=you"
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
<?php include "../includes/footer.php" ?>
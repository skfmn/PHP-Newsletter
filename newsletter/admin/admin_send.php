<?php
  session_start();
  ob_start();
  require("../PHPMailer/src/PHPMailer.php");
  require("../PHPMailer/src/SMTP.php");
  require("../PHPMailer/src/Exception.php");
  include '../includes/globals.php';
  include '../includes/functions.php';



  $cookies = $dir = "";
	$cookies = $_SESSION["nwsadminname"];
	
	if ($cookies == "") {

    redirect($redirect."admin/login.php");
    ob_end_flush();
  
	}

  $blnSend = $_SESSION["blnSend"];
  if ($blnSend == "false") {

    redirect($redirect."admin/admin.php?msg=nar");
    ob_end_flush();
  
	}

  if ($msg <> "") {
    displayFancyMsg(getMessage($msg));
  }
	
	
	if (isset($_POST["save"])) {
	  
    $templateID = test_input($_POST["tempid"]);
	  $tempTitle = test_input($_POST["temptitle"]);
		$tempBody = test_input($_POST["tempbody"]);
	
		$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

		if (!$conn) {
  
			die("Connection failed: " . mysqli_connect_error());
		}

		$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."newsletter WHERE newsletterID = ?");
		$stmt->bind_param("s", $templateID);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {

			$stmt = $conn->prepare("UPDATE ".DBPREFIX."newsletter SET news_title = ?, news_body = ? WHERE newsletterID = ?");
			$stmt->bind_param("sss", $tempTitle, $tempBody, $templateID);

			if ($stmt->execute()) {
        $msg = "tus";
      } else {
        $msg = "Error";
      }

		} else {

			$stmt = $conn->prepare("INSERT INTO ".DBPREFIX."newsletter (news_title ,news_body) VALUES (?,?)");
			$stmt->bind_param("ss", $tempTitle, $tempBody);

			if ($stmt->execute()) {
        $msg = "tc";
      } else {
        $msg = "Error";
      }
		}
    mysqli_close($conn);

    redirect($redirect."admin/admin_send.php?msg=".$msg);
    ob_end_flush();
		
	}
	
	if (isset($_POST["delete"])) {

	  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

	  if (!$conn) {
  
		  die("Connection failed: " . mysqli_connect_error());
	  }

    $tempplateID = test_input($_POST["tempid"]);

		$stmt = $conn->prepare("DELETE FROM ".DBPREFIX."newsletter WHERE newsletterID =?");
		$stmt->bind_param("s", $tempplateID);

		if ($stmt->execute()) {
      $msg = "del";
    } else {
      $msg = "Error";
    }
    mysqli_close($conn);

    redirect($redirect."admin/admin_send.php?msg=".$msg);
    ob_end_flush();
		
	}

  include "../includes/header.php";
?>
<script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
<div id="main" class="container">
  <header>
    <h2>Send a Newsletter</h2>
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
    echo "    <textarea rows=\"5\">\n";

    $email = $subject = $emailMsg = $semails = "";
    $check = false;
	  $semails = $_POST["semail"];
    $count = count($semails);	

    $sendas = $attachment = $uploadfile = "";
    $subject = $emailMsg = "";

    if (isset($_POST["subject"])) { $subject = test_input($_POST["subject"]); }

    if (isset($_FILES['userfile']['name']) && $_FILES['userfile']['error'] == UPLOAD_ERR_OK) {

      $ext = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
      $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $_FILES['userfile']['name'])) . '.' . $ext;

      if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        $attachment=$_FILES;
      } else {
        echo 'Failed to move file '.$_FILES['userfile']['name'].' to file ' . $uploadfile."\n\r";
        return false;
      }

    }

    $sql = "SELECT * FROM ".DBPREFIX."addresses";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
  
        foreach($semails as $x => $value) {
          
          if ($value == $row["email"]) {
            $emailMsg = "";
            $sendas = "html";
            $email = $row["email"];
            $emailMsg = $_POST["tempbody"];
            $emailMsg = wordwrap($emailMsg,70);

            if (isset($_POST["sendtxt"])) {
              $emailMsg = strip_tags($emailMsg);
              $emailMsg = str_replace("&copy;","",$emailMsg);
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

  $sql = "SELECT * FROM ".DBPREFIX."newsletter WHERE news_save = 'draft' OR news_save = 'both'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $intDCount = mysqli_num_rows($result);
  }

  $sql = "SELECT * FROM ".DBPREFIX."newsletter WHERE news_save = 'template' OR news_save = 'both'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $intTCount = mysqli_num_rows($result);
  }

 ?>

  <div class="row uniform">
    <div class="-3u 6u$ 12u$(medium)">
      <span>You have <?php echo $intDCount; ?> Drafts and <?php echo $intTCount; ?> Templates. <a class="urlimg fancybox.ajax" href="imageurls.php">Get Image URLs</a></span>
    </div>
    <div class="-2u 4u 12u$(medium)">
      <label for="loadtemp">Template Title</label>
      <div class="select-wrapper">
        <?php selectLoadTemplate(); ?>
      </div>
    </div>
    <div class="4u$ 12u$(medium)">
      <label for="tempdescr">Template Description</label>
      <input type="text" id="tempdescr" name="tempdescr" value="" size="30" required >
    </div>
  </div>
  <form action="admin_send.php" id="template" method="post" enctype="multipart/form-data">
    <input type="hidden" name="tempid" id="tempid" value="" />
    <input type="hidden" name="temptitle" id="temptitle" value="" />
      <div class="row uniform">
        <div class="-2u 8u 12u(medium)">
          <div class="12u 12u$(small)" style="padding-bottom:10px;">
            <label for="subject">Subject</label>
            <input type="text" name="subject" id="subject" value="" size="30" required />
	        </div>
          <div class="12u  12u$(small)">
            <span>Select the <span class="icon fa-file-text-o"></span> icon to load a saved template</span>
            <textarea name="tempbody" id="tempbody" cols="65" rows="25" wrap="soft"></textarea>
            <script>
              CKEDITOR.filter.allowedContentRules = true;
              CKEDITOR.config.format_tags = 'div;h1;h2;h3;pre';
              CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
              CKEDITOR.config.templates_files = ['<?php echo GBDIR; ?>admin/loadtemp.php'];
              CKEDITOR.replace( 'tempbody', {
                extraAllowedContent: 'header; content; footer; section; article'
              });
            </script>
          </div>

          <div class="row" style="padding-top:10px;">
            <div class="4u 12u$(small)" style="text-align:center;">
             <select id="semail" name="semail[]" size="5" style="height:75px;" multiple>
<?php
    $counter = 0;
    $sql = "SELECT * FROM ".DBPREFIX."addresses";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $counter = $counter+1;
        $email = $row["email"];
        echo "                <option value=\"".$email."\">".$counter.". ".$email."</option>\n";

      }
    } 
    mysqli_close($conn);
?>
              </select>
            </div>
            <div class="3u 12u$(small)">
              <input type="button" class="button fit" value="Select All Emails" onclick="selectAll('semail');">
            </div>
            <div class="5u$ 12u$(small)" style="text-align:center;">
              <input type="file" id="userfile" name="userfile" class="button fit">
              <label for="userfile">Add Attachment</label>
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
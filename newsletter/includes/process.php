<?php
	ob_start();
	require("../PHPMailer/src/PHPMailer.php");
  require("../PHPMailer/src/SMTP.php");
  require("../PHPMailer/src/Exception.php");
	include "globals.php"; 
	include "functions.php";

  if ($msg <> "") {
    displayFancyMsg(getMessage($msg));
  }

?>
<!DOCTYPE HTML>
<html>
<head>
  <title>PHP Newsletter - Version 1.0</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
  <link type="text/css" rel="stylesheet" href="<?php echo $http."://".$httpHost.GBDIR;  ?>/assets/css/jquery.fancybox.css" />
  <link type="text/css" rel="stylesheet" href="<?php echo $http."://".$httpHost.GBDIR;  ?>/assets/css/main.css" />
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
</head>

<body>
  <div id="main" class="container">
<?PHP

	$email = $confirm = $response = $param1 = $cancel = "";
	$subject = $emailMsg = $headers = $error = $nemail = "";
	$blnmsgsent = false;

  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}

	if ((isset($_POST["email"]))) { 
	  $email = test_input($_POST["email"]);
		$email = trim($email);
	} else if ((isset($_GET["email"]))) {
	  $email = test_input($_GET["email"]);
		$email = str_replace("~","@",$email);
		$email = str_replace("-",".",$email);
		$email = trim($email);
	}

	if ((isset($_GET["mode"]))) {
	  $mode = test_input($_GET["mode"]);
	  if ($mode == "confirm") {
		 $confirm = "yes";
		} else if ($mode == "cancel") {
		 $cancel = "yes";
		}
	}

	if (isset($_POST["confirm"])) {
	  $confirm = test_input($_POST["confirm"]);
	}

  if ($confirm == "yes") {

    $stmt = $conn->prepare("UPDATE ".DBPREFIX."addresses SET confirm = ? WHERE email = ?");
    $stmt->bind_param('ss', $confirm, $email);

    if ($stmt->execute()) {

      $blnmsgsent = true;

    }

		if ($blnmsgsent) {

		  getEndMsg("confirmed",$email);

		} else {

		  getEndMsg("confirmerr",$email);

		}

  } else if ($confirm == "no") {

		$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."addresses WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();

    if ($result->num_rows > 0) {

		  getEndMsg("alreadysubbed",$email);

	  } else {

      $param1 = date("m \/ d \/ Y");
      $stmt = $conn->prepare("INSERT INTO ".DBPREFIX."addresses (email,datDate,confirm) VALUES (?,?,?)");
      $stmt->bind_param('sss', $email, $param1, $confirm);

      if ($stmt->execute()) {

			  $nemail = str_replace("@","~",$email);
		    $nemail = str_replace(".","-",$nemail);	

				$subject = $siteTitle." Newsletter confirmation";

				$emailMsg = "Thank you for subscribing to our Newsletter<br /><br />";
				$emailMsg .= "Please confirm your subscription by clicking on the link below.<br /><br />";

				if (REWRITE == "yes") {
					$emailMsg .= "<a"." href=\"".$http."://".$domain."/process/".$nemail."/confirm/\">Confirm</a><br /><br />";
				} else {
					$emailMsg .= "<a"." href=\"".$http."://".$domain.GBDIR."includes/process.php?email=".$nemail."&mode=confirm\">Confirm</a><br /><br />";
				}

				$emailMsg .= "You received this email because you submitted this email address to our mailing list.<br />";
				$emailMsg .= "If you did not subscribe or wish to be removed from our list - click on the link below<br /><br />"; 

				if (REWRITE == "yes") {
					$emailMsg .= "<a href=\"".$http."://".$domain."/process/".$nemail."/cancel/\">Cancel</a><br /><br />";
				} else {
					$emailMsg .= "<a href=\"".$http."://".$domain.GBDIR."includes/process.php?email=".$nemail."&mode=cancel\">Cancel</a><br /><br />";
				}

				$emailMsg .= "Our Thanks<br />".$siteTitle;
				$emailMsg = wordwrap($emailMsg,70);

				if (send_mail($email, $subject, $emailMsg, "", "", "", "")) {
				  
				  $blnmsgsent = true;

        }

				if ($blnmsgsent) {
				  
				  getEndMsg("thanks",$email);

        } else {

				  getEndMsg("thankserr",$email);
			
				}
	
      } else {

			  getEndMsg("adderr",$email);

      }
		}

  } else {

	  if ($cancel == "yes") {

		  $blnDelete = false;

			$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."addresses WHERE email = ?");
			$stmt->bind_param("s", $email);
			$stmt->execute();

      if ($result->num_rows > 0) {
			
			  $blnDelete = true;
	
			} else {

			  getEndMsg("notfound",$email);

			}

			if ($blnDelete) {

			  $stmt = "";
				$stmt = $conn->prepare("DELETE FROM ".DBPREFIX."addresses WHERE email = ?");
				$stmt->bind_param("s", $email);

				if ($stmt->execute()) {

				  getEndMsg("removed",$email);

				} else {

				  getEndMsg("removederr",$email);

				}
      }
	  }
  }
	mysqli_close($conn);
  echo "  </div>";
	include "footer.php";
?>
<?php
  session_start();
  ob_start();
  include '../includes/globals.php';
  include '../includes/functions.php';

  $cookies = $dir = $username = $password = $encrPassword = "";
	$msg = $lngMemberID = $strRights = $strName = "";
  $send = $addresses = $images = $templates = $dbrights = $adminrights = $arights = "";

	$cookies = $_SESSION["nwsadminname"];

	If ($cookies == "") {

    redirect($redirect."admin/login.php");
    ob_end_flush();
  
	}

	$blnARights = $_SESSION["blnARights"];
	if ($blnARights == "false") {

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

	if (isset($_GET["id"])) { $lngMemberID = test_input($_GET["id"]);}

  if (isset($_GET["as"])) {

		$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

		if (!$conn) {
  
			die("Connection failed: " . mysqli_connect_error());
		}

    if (isset($_POST["rights"])) { $strRights = trim(implode(',', $_POST["rights"]));}

		$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."admin WHERE adminID = ?");
		$stmt->bind_param("s", $lngMemberID);
    $stmt->execute();
		$result = $stmt->get_result();

    if ($result->num_rows > 0) {

			$param1 = "true";
			$param2 = "false";

			if (stripos($strRights,"send") !== false) {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET send = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param1,$lngMemberID);
				$stmt->execute();

			} else {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET send = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param2,$lngMemberID);
				$stmt->execute();

			}
      
			if (stripos($strRights, "addresses") !== false) {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET addresses = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param1,$lngMemberID);
				$stmt->execute();

			} else {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET addresses = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param2,$lngMemberID);
				$stmt->execute();

			}

			if (stripos($strRights, "images") !== false) {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET images = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param1,$lngMemberID);
				$stmt->execute();

			} else {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET images = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param2,$lngMemberID);
				$stmt->execute();

			}

			if (stripos($strRights, "templates") !== false) {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET templates = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param1,$lngMemberID);
				$stmt->execute();

			} else {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET templates = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param2,$lngMemberID);
				$stmt->execute();

			} 

			if (stripos($strRights, "options") !== false) {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET options = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param1,$lngMemberID);
				$stmt->execute();

			} else {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET options = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param2,$lngMemberID);
				$stmt->execute();

			}

			if (stripos($strRights, "admins_rights") !== false) {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET admins_rights = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param1,$lngMemberID);
				$stmt->execute();

			} else {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET admins_rights = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param2,$lngMemberID);
				$stmt->execute();

			}

			if (stripos($strRights, "arights") !== false) {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET arights = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param1,$lngMemberID);
				$stmt->execute();

			} else {

				$stmt = $conn->prepare("UPDATE ".DBPREFIX."admin SET arights = ? WHERE adminID = ?");
				$stmt->bind_param("ss", $param2,$lngMemberID);
				$stmt->execute();

			}

		}
    mysqli_close($conn);

		$_SESSION["msg"] = "car";
    redirect($redirect."admin/arights.php?id=".$lngMemberID);
    ob_end_flush();

  }

	$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

	if (!$conn) {
  
		die("Connection failed: " . mysqli_connect_error());
	}

	$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."admin WHERE adminID = ?");
	$stmt->bind_param("s", $lngMemberID);
  $stmt->execute();
	$result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $strName = $row["name"];
  }

  include "../includes/header.php";
?>
<div id="main" class="container">
  <header>
    <h2>Manage Rights for <?php echo $strName; ?></h2>
  </header>
	<div class="row uniform">
		<div class="-4u 4u 12u(medium)">
<?php

	$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."admin WHERE adminID = ?");
	$stmt->bind_param("s", $lngMemberID);
	$stmt->execute();
	$result = $stmt->get_result();

  if ($result->num_rows > 0) {
?>
	    <form method="post" name="rights" id="rights" action="arights.php?as=y&id=<?php echo $lngMemberID; ?>" >
      <div class="row uniform">
        <div class="12u 12u$(small)">
<?php
		  $row = mysqli_fetch_assoc($result); 
		  $strChecked = "";
	    $strValue = $row["send"];
	    if ($strValue == "true") {$strChecked = "checked";}
?>				
				  <div class="12u 12u$(small)">
            <input type="checkbox" id="send" name="rights[]" value="send" <?php echo $strChecked; ?> >
            <label for="send">send</label>
				  </div>
<?php
	    $strChecked = "";
	    $strValue = $row["addresses"];
	    if ($strValue == "true") {$strChecked = "checked";}
?>				
				  <div class="12u 12u$(small)">
            <input type="checkbox" id="addresses" name="rights[]"  value="addresses" <?php echo $strChecked; ?> >
            <label for="addresses">Addresses</label>
				  </div>
<?php
	    $strChecked = "";
	    $strValue = $row["images"];
	    if ($strValue == "true") {$strChecked = "checked";}
?>				
				  <div class="12u 12u$(small)">
            <input type="checkbox" id="images" name="rights[]"  value="images" <?php echo $strChecked; ?> >
            <label for="images">images</label>
				  </div>
<?php
	    $strChecked = "";
	    $strValue = $row["templates"];
	    if ($strValue == "true") {$strChecked = "checked";}
?>				
				  <div class="12u 12u$(small)">
            <input type="checkbox" id="templates" name="rights[]"  value="templates" <?php echo $strChecked; ?> >
            <label for="templates">Templates</label>
				  </div>
<?php
	    $strChecked = "";
	    $strValue = $row["options"];
	    if ($strValue == "true") {$strChecked = "checked";}
?>				
				  <div class="12u 12u$(small)">
            <input type="checkbox" id="db_rights" name="rights[]"  value="options" <?php echo $strChecked; ?> >
            <label for="db_rights">Options</label>
				  </div>
<?php
	    $strChecked = "";
	    $strValue = $row["admins_rights"];
	    if ($strValue == "true") {$strChecked = "checked";}
?>				
				  <div class="12u 12u$(small)">
            <input type="checkbox" id="admins_rights" name="rights[]"  value="admins_rights" <?php echo $strChecked; ?> >
            <label for="admins_rights">Admins</label>
				  </div>
<?php
	    $strChecked = "";
	    $strValue = $row["arights"];
	    if ($strValue == "true") {$strChecked = "checked";}
?>				
				  <div class="12u 12u$(small)">
            <input type="checkbox" id="arights" name="rights[]"  value="arights" <?php echo $strChecked; ?> >
            <label for="arights">arights</label>
				  </div>
		      <div class="12u 12u$(small)">
            <input type="submit" name="submit" value="Submit" />
		      </div>
        </div>
      </div>
		  </form>
<?php } else { ?>
      <div class="table-wrapper">
	      <table>
	        <tr>
	          <td style="width:75%;text-align:left"><span>That person is not an Admin.</span></td>
		      </tr>
		    </table>
      </div>
<?php
	}
	mysqli_close($conn);
?>
    </div>
  </div>
</div>
<?php include "../includes/footer.php" ?>
<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$msg = $cookies = $dir = "";

$cookies = $_SESSION["nwsadminname"];
if($cookies == "") {
	redirect($redirect."admin/login.php");
	ob_end_flush();
}

$blnAddresses = $_SESSION["blnAddresses"];
if($blnAddresses == "false") {
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

if(isset($_GET["ae"])) {
	if(isset($_POST["email"])) {
		$email = test_input($_POST["email"]);
		$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
		if(!$conn) {
			die("Connection failed: ".mysqli_connect_error());
		}
		$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."addresses WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0) {
			$_SESSION["msg"] = "aid";
		} else {
			$param1 = date("Y-m-d");
			$param2 = "yes";
			$stmt = $conn->prepare("INSERT INTO ".DBPREFIX."addresses (email,datDate,confirm) VALUES (?,?,?)");
			$stmt->bind_param("sss", $email, $param1, $param2);
			if($stmt->execute()) {
				$_SESSION["msg"] = "eas";
			} else {
				$_SESSION["msg"] = "Error";
			}
		}
		mysqli_close($conn);
	} else {
		$_SESSION["msg"] = "nea";
	}

	redirect($redirect."admin/admin_addresses.php");
	ob_end_flush();

}
if(isset($_GET["da"])) {

	$emails = $_POST["email"];
	$count = count($emails);
	$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	if(!$conn) {
		die("Connection failed: ".mysqli_connect_error());
	}
	for($x = 0;
	$x < $count;
	$x ++ ) {
		$param1 = $emails[$x];
		$stmt = $conn->prepare("DELETE FROM ".DBPREFIX."addresses WHERE email =?");
		$stmt->bind_param("s", $param1);
		if($stmt->execute()) {
			$_SESSION["msg"] = "ds";
		} else {
			$_SESSION["msg"] = "error";
		}
	}
	mysqli_close($conn);

	redirect($redirect."admin/admin_addresses.php");
	ob_end_flush();

}

if(isset($_GET["p"])) {

	$strDate = strtotime("last week");
	$todaysDate = date("Y-m-d",$strDate);
	$purge = test_input($_GET["p"]);

	$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	if(!$conn) {
		die("Connection failed: ".mysqli_connect_error());
	}

  if ($purge == "s") {

		$param1 = "no";
		$stmt = $conn->prepare("DELETE FROM ".DBPREFIX."addresses WHERE datDate < ? and confirm = ?");
		$stmt->bind_param("ss", $todaysDate, $param1);

		if($stmt->execute()) {
			$_SESSION["msg"] = "ds";
		} else {
			$_SESSION["msg"] = "error";
		}

  } else {

		$param1 = "no";
		$stmt = $conn->prepare("DELETE FROM ".DBPREFIX."addresses WHERE confirm = ?");
		$stmt->bind_param("s", $param1);

		if($stmt->execute()) {
			$_SESSION["msg"] = "ds";
		} else {
			$_SESSION["msg"] = "error";
		}
	}
	mysqli_close($conn);

	redirect($redirect."admin/admin_addresses.php");
	ob_end_flush();
}

include "../includes/header.php";

?>
  <div id="main" class="container">
    <header>
      <h2>Manage Addresses</h2>
    </header>
    <div class="row">
<?php
$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
if(!$conn) {
	die("Connection failed: ".mysqli_connect_error());
}
$param1 = "yes";
$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."addresses WHERE confirm  = ?");
$stmt->bind_param("s", $param1);
$stmt->execute();
$result = $stmt->get_result();
$count = 0;
if($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {
		$count = mysqli_num_rows($result);
	}

}
mysqli_close($conn);

?>
      <div class="6u 12u$(medium)">
        <div class="12u$" style="padding-bottom:10px;">
          <label for="viewemail">View Addresses in Your List</label>
          <textarea rows="2">
<?php
$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
if(!$conn) {
	die("Connection failed: ".mysqli_connect_error());
}
$param1 = "yes";
$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."addresses WHERE confirm  = ?");
$stmt->bind_param("s", $param1);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {
		echo $row["email"]."\n";
	}
}
mysqli_close($conn);

?>
          </textarea>
        </div>
        <div class="12u$" style="padding-bottom:10px;">
          <span>There are <?php echo $count; ?> members in your mailing list.</span>
        </div>
      </div>
      <div class="6u$ 12u$(medium)">
        <form action="admin_addresses.php?ae=y" method="post">
        <div class="row uniform">
          <div class="12u$" style="padding-bottom:10px;">
            <label for="email">Add an Email Address</label>
            <input id="email" name="email" type="text" required>
          </div>
          <div class="12u$" style="padding-bottom:10px;text-align:center;">
            <input name="submit" class="button fit" type="submit" value="Add Address">
          </div>
        </div>
        </form>
      </div>
      <div class="6u 12u$(medium)">
        <form action="admin_addresses.php?da=y" method="post">
        <div class="row uniform">
          <div class="12u$" style="padding-bottom:10px;">
            <label for="email">Select address(es) to delete</label>
            <?php selectDeleteEmail(); ?>
          </div>
          <div class="12u$" style="padding-bottom:10px;text-align:center;">
            <input type="submit" class="button fit" value="Delete Addresses" onclick="return confirm('WARNING!!\n Are you sure you want to delete these addresses?\n This cannot be undone!')">
          </div>
        </div>
        </form>
      </div>
      <div class="6u$ 12u$(medium)">
        <div class="row uniform">
          <div class="12u$" style="padding-bottom:10px;">
            <label>Purge Unconfirmed email addresses</label>
            <input type="button" onclick="return confirmSubmit('WARNING!!\n Are you sure you want to delete these unconfirmed addresses older than a week?\n This cannot be undone!','admin_addresses.php?p=s')" class="button fit" value="Purge Unconfirmed > 7 days" >
          </div>
          <div class="12u$" style="padding-bottom:10px;text-align:center;">
           <input type="button" onclick="return confirmSubmit('WARNING!!\n Are you sure you want to delete all unconfirmed addresses?\n This cannot be undone!','admin_addresses.php?p=a')" class="button fit" value="Purge all Unconfirmed" >
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
include "../includes/footer.php";

?>
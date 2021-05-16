<?php
  session_start();
  ob_start();
  include '../includes/globals.php';
  include '../includes/functions.php';

  $cookies = $dir = "";
	$cookies = $_SESSION["nwsadminname"];
	
	if ($cookies == "") {

    redirect($redirect."admin/login.php");
    ob_end_flush();
  
	}

  $blnAddresses = $_SESSION["blnAddresses"];
	if ($blnAddresses == "false") {

    redirect($redirect."admin/admin.php?msg=nar");
    ob_end_flush();
  
	}

  if ($msg <> "") {
    displayFancyMsg(getMessage($msg));
  }

	if (isset($_GET["ae"])) {

	  if (isset($_POST["email"])) {

      $email = test_input($_POST["email"]);
		
			$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

			if (!$conn) {
  
				die("Connection failed: " . mysqli_connect_error());
			}
	
			$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."addresses WHERE email = ?");
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows > 0) {
				$msg="aid";
			} else { 
        
        $param1 = date("m \/ d \/ Y");
        $param2 = "yes";
				$stmt = $conn->prepare("INSERT INTO ".DBPREFIX."addresses (email,datDate,confirm) VALUES (?,?,?)");
				$stmt->bind_param("sss", $email, $param1, $param2);

				if ($stmt->execute()) {
					$msg = "eas";
				} else {
					$msg = "Error";
				}

			}
      mysqli_close($conn);

		} else {
		  $msg = "nea";
		}

    redirect($redirect."admin/admin_addresses.php?msg=".$msg);
    ob_end_flush();

	}
	
	if (isset($_GET["da"])) {

	  $emails = $_POST["email"];
    $count = count($emails);

	  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

	  if (!$conn) {
  
		  die("Connection failed: " . mysqli_connect_error());
	  }

		for ($x = 0; $x < $count; $x++) {

      $param1 = $emails[$x];
			$stmt = $conn->prepare("DELETE FROM ".DBPREFIX."addresses WHERE email =?");
			$stmt->bind_param("s", $param1);

			if ($stmt->execute()) {
			  $msg = "ds";
      } else {
        $msg = "error";
      }

		}
    mysqli_close($conn);

    redirect($redirect."admin/admin_addresses.php?msg=".$msg);
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

	if (!$conn) {
  
		die("Connection failed: " . mysqli_connect_error());
	}

  $param1 = "yes";
	$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."addresses WHERE confirm  = ?");
	$stmt->bind_param("s", $param1);
	$stmt->execute();
	$result = $stmt->get_result();
  $count = 0;
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$count = mysqli_num_rows($result);
		}

	}
	mysqli_close($conn);
?>
      <div class="4u 12u(medium)">
        <div class="12u$" style="padding-bottom:10px;">
          <label for="viewemail">View Addresses in Your List</label>
          <select multiple size="5">
<?php 

  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

	if (!$conn) {
  
		die("Connection failed: " . mysqli_connect_error());
	}

  $param1 = "yes";
	$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."addresses WHERE confirm  = ?");
	$stmt->bind_param("s", $param1);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
      echo "            <option>".$row["email"]."</option>\n";
		}
	}
  mysqli_close($conn);
?>
          </select>
        </div>
        <div class="12u$" style="padding-bottom:10px;">
          <span>There are <?php echo $count; ?> members in your mailing list.</span>
        </div>
      </div>
      <div class="4u 12u$(medium)">
        <form action="admin_addresses.php?ae=y" method="post">
        <div class="row uniform">
          <div class="12u$" style="padding-bottom:10px;">
            <label for="email">Add an Email Address</label>
            <input id="email" name="email" type="text">
          </div>
          <div class="12u$" style="padding-bottom:10px;text-align:center;">
            <input name="submit" class="button fit" type="submit" value="Add Address">
          </div>
        </div>
        </form>
      </div>
      <div class="4u$ 12u$(medium)">
        <form action="admin_addresses.php?da=y" method="post">
        <div class="row uniform">
          <div class="12u$" style="padding-bottom:10px;">
            <label for="email">Select addresses to delete</label>
            <?php selectDeleteEmail(); ?>
          </div>
          <div class="12u$" style="padding-bottom:10px;text-align:center;">
            <input type="submit" class="button fit" value="Delete Addresses" onclick="return confirm('WARNING!!\n Are you sure you want to delete these addresses?\n This cannot be undone!')">
          </div>
        </div>
        </form>
      </div>

    </div>
  </div>
<?php include "../includes/footer.php"; ?>
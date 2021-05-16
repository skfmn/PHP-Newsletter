<?php

  include '../includes/globals.php';
  include '../includes/functions.php';

	$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

	if (!$conn) {
  
		die("Connection failed: " . mysqli_connect_error());
	}

	$strTempTitle = "";
	$strTempDecsr = "";
	$strTempBody = "";
	$lngTempID = 0;
	$intTid = 0;
	if (isset($_GET["tid"])) { $intTid = test_input($_GET["tid"]); }

	$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."newsletter WHERE newsletterID = ?");
	$stmt->bind_param("s", $intTid);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

		$strTempTitle = $row["news_title"];
		$strTempDecsr = $row["news_description"];
		$strTempBody = htmlspecialchars_decode($row["news_body"]);
		$lngTempID = $row["newsletterID"];

	}
	mysqli_close($conn);
	$strTempBody = str_replace(",","~",$strTempBody);
	echo $strTempTitle.",".$strTempDecsr.",".$strTempBody.",".$lngTempID;
 
?>
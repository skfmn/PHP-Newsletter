<?php
  session_start();
  ob_start();
  include '../includes/globals.php';
  include '../includes/functions.php';

	$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

	if (!$conn) {
  
		die("Connection failed: " . mysqli_connect_error());
	}

  $param1 = "template";
  $param2 = "both";
	$stmt = $conn->prepare("SELECT * FROM ".DBPREFIX."newsletter WHERE news_save = ? OR news_save =?");
	$stmt->bind_param("ss", $param1,$param2);
	$stmt->execute();
	$result = $stmt->get_result();

  $intCount = 0;
	if ($result->num_rows > 0) {
    $intRecordCount = mysqli_num_rows($result);
?>
CKEDITOR.addTemplates( 'default',
{
	templates :
		[
<?php
    while ($row = $result->fetch_assoc()) {
      $strNewsBody = $row["news_body"];
      $intCount = $intCount+1; 
?>
			{
        title: '<?php echo $row["news_title"]; ?>',
        description: '<?php echo $row["news_description"]; ?>',
        html:
					'<?php echo htmlspecialchars_decode($strNewsBody); ?>'
			}
<?php
      if ($intCount <> $intRecordCount) {
        echo ",";
      }
    } 
?>
		]
});
<?php 
  } 
  mysqli_close($conn);
?>
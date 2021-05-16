<?php
  session_start();
  ob_start();
  include '../includes/globals.php';
  include '../includes/functions.php';

  $cookies = $dir = $username = $password = $encrPassword = "";
	$lngMemberID = $strRights = "";
  $send = $addresses = $images = $templates = $dbrights = $adminrights = $arights = "";

	$cookies = $_SESSION["nwsadminname"];
	If ($cookies == "") {

    redirect($redirect."admin/login.php");
    ob_end_flush();
  
	}

  $blnImages = $_SESSION["blnImages"];
	if ($blnImages == "false") {

   redirect($redirect."admin/admin.php?msg=nar");
   ob_end_flush();
  
	}

  if ($msg <> "") {
    displayFancyMsg(getMessage($msg));
  }	

  $dir = SAVEFILE;
  if (!is_dir($dir)) {
   mkdir($dir); 
  }

	$counter = 0;
	$intCounter = 0;

  include "../includes/header.php";
?>
<div id="main" class="container">
  <div class="row">
		<div class="-2u 8u 12u(medium)">
      <form action="upload.php?deleteimg=yes" method="post">
        <h4>Images</h4>
        <div class="table-wrapper">
	        <table>
		        <thead>
			        <tr>
				        <th>&nbsp;</th>
				        <th>Image</th>
				        <th>Size</th>
                <th>Delete</th>
			        </tr>
		        </thead>
		        <tbody>
<?php

 if (is_dir($dir)){

  if ($dh = opendir($dir)){
    $counter = 0;
    $Bgcolor = "gray";
    while (($file = readdir($dh)) !== false){
  
      if (!is_dir($file)){
 
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        
        $counter++;
        if (fmod($counter, 4) == 0) {
          echo "<div class='3u$ 4u$(large) 6u$(medium) 12u$(small)'>";
        }else{
          echo "<div class='3u 4u(large) 6u(medium) 12u$(small)'>";
        }
			
			  if ($Bgcolor == "silver") {
				  $Bgcolor = "gray";
			  } else {
				  $Bgcolor = "silver";
			  }
?>
              <tr>
                <td><?php echo $counter; ?>.</td>
                <td><a class="picimg" href="<?php echo $http."://".$httpHost.GBDIR; ?>admin/images/<?php echo $file; ?>"><?php echo $file; ?></a></td>
                <td><span style="font-size:12px"><?php echo formatSizeUnits(filesize($dir."\\".$file)); ?></span></td>
                <td>
                  <input type="checkbox" id="file<?php echo $counter; ?>" name="file<?php echo $counter; ?>" value="<?php echo $file; ?>" style="z-index:1000"/>
                  <label for="file<?php echo $counter; ?>">Yes</label>
                </td>
              </tr>
<?php
      }
    }
    closedir($dh);
  }
} 

?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" style="text-align:center;">
                  <input type="submit" value="Delete Selected Images">
                </td>
              </tr>
			      </tfoot>
          </table>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
		<div class="-2u 8u 12u(medium)">
      <h4>Upload Files</h4>
      <form name="upldfile" action="upload.php" method="post" enctype="multipart/form-data">
      <input type="file" name="images[]" size="20" multiple>
      <input type="submit" value="Upload">
      </form>
    </div>
  </div>
</div>
<?php include "../includes/footer.php"; ?>
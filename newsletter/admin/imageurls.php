<?php
  session_start();
  ob_start();
  include '../includes/globals.php';
  include '../includes/functions.php';

  $cookies = "";
	$cookies = $_SESSION["nwsadminname"];

	If ($cookies == "") {

    redirect($redirect."admin/login.php");
    ob_end_flush();
  
	}

  $dir = $bkcolor = $ext = $file = $dh = "";
  $allowExt = array("png","jpg","gif");

  $dir = BASEDIR.GBDIR."admin\images";
  $dir = str_replace("/","\\",$dir);
  $dir = str_replace("\\\\","\\",$dir);

  $bkcolor = "#e2effc";

  if (is_dir($dir)){

    if ($dh = opendir($dir)){
      $counter = 0;
      while (($file = readdir($dh)) !== false){
 
        if (!is_dir($file)) {
          
          $ext = pathinfo($file, PATHINFO_EXTENSION);

          $arrlength = count($allowExt);

          for($x = 0; $x < $arrlength; $x++) {

            if ($allowExt[$x] == $ext) {

              $counter++;

              if ($bkcolor == "#e2effc") {
                $bkcolor = "#ffffff";
              } else {

                $bkcolor = "#e2effc";
              }

              echo "<span onmouseover=\"document.getElementById('place-holder-1').src='".$http."://".$httpHost.GBDIR."admin/images/".$file."'\";";
              echo "onmouseout=\"document.getElementById('place-holder-1').src=''\"; style=\"background-color:".$bkcolor.";\">".$http."://".$httpHost.GBDIR."admin/images/".$file."</span>";
              echo "<img src=\"\" id=\"place-holder-1\" style=\"zindex: 100; position: absolute;\" /><br />";


              clearstatcache();
            }         
          }
        }
      }
      closedir($dh);
    }
  }
?>

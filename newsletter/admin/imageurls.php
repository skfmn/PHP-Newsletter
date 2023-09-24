<script>
    function selectText(containerid) {
        if (document.selection) { // IE
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));
            range.select();
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(document.getElementById(containerid));
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
        }
    }
</script>
<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$cookies = "";
$cookies = $_SESSION["nwsadminname"];

if ($cookies == "") {

    redirect($redirect . "admin/login.php");
    ob_end_flush();

}

$dir = $bkcolor = $ext = $file = $dh = "";
$allowExt = array("png", "jpg", "gif");

$dir = BASEDIR . NEWSDIR . "admin\images";
$dir = str_replace("/", "\\", $dir);
$dir = str_replace("\\\\", "\\", $dir);

$bkcolor = "#e2effc";

if (is_dir($dir)) {

    if ($dh = opendir($dir)) {
        $counter = 0;
        echo "<div style=\"min-width:600px\">";
        echo "   <div style=\"position:relative;display:block;float:left;\">";
        while (($file = readdir($dh)) !== false) {

            if (!is_dir($file)) {

                $ext = pathinfo($file, PATHINFO_EXTENSION);

                $arrlength = count($allowExt);

                for ($x = 0; $x < $arrlength; $x++) {

                    if ($allowExt[$x] == $ext) {

                        $counter++;

                        if ($bkcolor == "#e2effc") {
                            $bkcolor = "#ffffff";
                        } else {

                            $bkcolor = "#e2effc";
                        }

                        echo "<span id=\"img-" . $counter . "\" onclick=\"selectText('img-" . $counter . "')\" onmouseover=\"document.getElementById('place-holder-1').src='" . $http . "://" . $httpHost . NEWSDIR . "admin/images/" . $file . "'\";";
                        echo "onmouseout=\"document.getElementById('place-holder-1').src=''\"; style=\"background-color:" . $bkcolor . ";\">" . $http . "://" . $httpHost . NEWSDIR . "admin/images/" . $file . "</span><br>";

                        clearstatcache();
                    }
                }
            }
        }
        echo "   </div>";
        echo "   <div style=\"position:relative;display:block;float:left;margin-left:10px;\">";
        echo "      <img src=\"\" id=\"place-holder-1\" />";
        echo "   </div>";
        echo "<div>";
        closedir($dh);
    }
}
?>
<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$cookies = $_SESSION["nwsadminname"];

if ($cookies == "") {

    redirect($redirect . "admin/login.php");
    ob_end_flush();

}

if (isset($_GET["deleteimg"])) {
    $dir = BASEDIR . NEWSDIR . "admin\\images\\";
    $dir = str_replace("/", "\\", $dir);
    $dir = str_replace("\\\\", "\\", $dir);

    $strDelFile = "";
    $strDelFile = "admin/images/";

    foreach ($_POST as $key => $value) {
        $x = str_replace('item', '', $key);
        unlink($dir . $value);
    }

    redirect($redirect . "admin/admin_images.php?msg=ids");
    ob_end_flush();

}

$uploadedFile = "";
$target_dir = "images/";
$files = $_FILES["images"]["name"];
foreach ($files as $file) {

    $target_file = $target_dir . basename($file);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $uploadedFile = $_FILES["images"]["tmp_name"];

    if (isset($_POST["Upload"])) {

        $check = getimagesize($uploadedFile);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $msg = "nimg";
            $uploadOk = 0;
        }
    }

    if (file_exists($target_file)) {
        $msg = " fex";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "gif") {
        $msg = "ftna";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        $msg = "ulf";
    } else {
        $targetFileA = "";
        $ufiles = $_FILES["images"]["tmp_name"];
        foreach ($ufiles as $ufile) {
            if ($targetFileA <> $target_file) {
                if (move_uploaded_file($ufile, $target_file)) {
                    $msg = "uls";
                    $targetFileA = $target_file;
                } else {
                    $msg = "ulff";
                }
            }
        }
    }
}

redirect($redirect . "admin/admin_images.php?msg=" . $msg);
ob_end_flush();

?>
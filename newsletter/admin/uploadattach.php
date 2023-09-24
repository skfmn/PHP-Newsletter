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

if ($_GET["dla"] === "yes") {

    $dir = BASEDIR . NEWSDIR . "admin\\attachs\\";
    $dir = str_replace("/", "\\", $dir);
    $dir = str_replace("\\\\", "\\", $dir);

    foreach ($_POST["selectattach"] as $value) {
        unlink($dir . $value);
    }

    $msg = "ids";

} else {

    $uploadedFile = "";
    $target_dir = "attachs/";
    $files = $_FILES["attachs"]["name"];
    foreach ($files as $file) {

        $target_file = $target_dir . basename($file);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadedFile = $_FILES["attachs"]["tmp_name"];

        if (file_exists($target_file)) {
            $msg = " fex";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            $msg = "ulf";
        } else {
            $targetFileA = "";
            $ufiles = $_FILES["attachs"]["tmp_name"];
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

}

$_SESSION["msg"] = $msg;
if ($_GET["p"] == "o") {
    redirect($redirect . "admin/admin_options.php");
} else{
    redirect($redirect . "admin/admin_send.php");
}

ob_end_flush();

?>
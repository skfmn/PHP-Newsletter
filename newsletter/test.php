<?php
  require("PHPMailer/src/PHPMailer.php");
  require("PHPMailer/src/Exception.php");
	include "includes/globals.php"; 
	include "includes/functions.php";
$msg = '';
if (array_key_exists('userfile', $_FILES)) {
    //First handle the upload
    //Don't trust provided filename - same goes for MIME types
    //See http://php.net/manual/en/features.file-upload.php#114004 for more thorough upload validation
    //Extract an extension from the provided filename
    $ext = PHPMailer\PHPMailer\PHPMailer::mb_pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
    //Define a safe location to move the uploaded file to, preserving the extension
    $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $_FILES['userfile']['name'])) . '.' . $ext;

    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        //Upload handled successfully
        //Now create a message
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->setFrom('admin@htmljunction.com');
        $mail->addAddress('steeve@htmljunction.com');
        $mail->Subject = 'PHPMailer file sender';
        $mail->Body = 'My message body';
        //Attach the uploaded file
        if (!$mail->addAttachment($uploadfile, 'My uploaded file')) {
            $msg .= 'Failed to attach file ' . $_FILES['userfile']['name'];
        }
        if (!$mail->send()) {
            $msg .= 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $msg .= 'Message sent!';
        }
    } else {
        $msg .= 'Failed to move file to ' . $uploadfile;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>PHPMailer Upload</title>
</head>
<body>
<?php if (empty($msg)) { ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000"> Send this file: <input name="userfile" type="file">
        <input type="submit" value="Send File">
    </form>
<?php } else {
    echo htmlspecialchars($msg);
} ?>
</body>
</html>

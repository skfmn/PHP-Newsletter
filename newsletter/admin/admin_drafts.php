<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$cookies = $dir = $username = $password = $encrPassword = "";
$msg = $lngMemberID = $strRights = "";
$send = $addresses = $images = $templates = $dbrights = $adminrights = $arights = "";
$intTemplateID = $strTempTitle = $strTempDescr = $strTempBody = $strTempBody = "";

$cookies = $_SESSION["nwsadminname"];

if ($cookies == "") {

    redirect($redirect . "admin/login.php");
    ob_end_flush();

}

$blnTemplates = $_SESSION["blnTemplates"];
if ($blnTemplates == "false") {

    $_SESSION["msg"] = "nar";
    redirect($redirect . "admin/admin.php");

    ob_end_flush();

}

if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
    if ($msg <> "") {
        displayFancyMsg(getMessage($msg));
        $_SESSION["msg"] = "";
    }
}

if (isset($_POST["save"])) {

    $blnFound = false;
    $intTemplateID = test_input($_POST["tempid"]);
    $strTempTitle = htmlentities(test_input($_POST["temptitle"]));
    $strTempDescr = test_input($_POST["tempdescr"]);
    $strTempBody = htmlentities($_POST["tempbody"]);
    $strTempBody = str_replace(",", "~", $strTempBody);

    if (test_input($_POST["save"]) == "Save as Template") {
        $templateSave = "template";
    } else {
        $templateSave = "draft";
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM " . DBPREFIX . "newsletter WHERE newsletterID = ?");
    mysqli_stmt_bind_param($stmt, "s", $intTemplateID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $blnFound = true;
    } else {
        $blnFound = false;
    }

    if ($blnFound) {

        $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "newsletter SET news_title = ?, news_body = ?, news_description = ?, news_save = ? WHERE newsletterID = ?");
        mysqli_stmt_bind_param($stmt, "sssss", $strTempTitle, $strTempBody, $strTempDescr, $templateSave, $intTemplateID);

        if ($stmt->execute()) {
            $_SESSION["msg"] = "Updated";
        } else {
            $_SESSION["msg"] = "Error";
        }

    } else {

        $stmt = mysqli_prepare($conn, "INSERT INTO " . DBPREFIX . "newsletter (news_title ,news_body,news_description, news_save) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssss", $strTempTitle, $strTempBody, $strTempDescr, $templateSave);

        if ($stmt->execute()) {
            $_SESSION["msg"] = "Added";
        } else {
            $_SESSION["msg"] = "Error";
        }

    }
    mysqli_close($conn);

    redirect($redirect . "admin/admin_drafts.php");
    ob_end_flush();

}

if (isset($_POST["delete"])) {

    $lngTempplateID = "";
    $lngTempplateID = test_input($_POST["tempid"]);

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM " . DBPREFIX . "newsletter WHERE newsletterID =?");
    $stmt->bind_param("s", $lngTempplateID);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "del";
    } else {
        $_SESSION["msg"] = "Error";
    }
    mysqli_close($conn);

    redirect($redirect . "admin/admin_drafts.php");
    ob_end_flush();

}

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

if (!$conn) {

    die("Connection failed: " . mysqli_connect_error());
}

$param1 = "draft";
$stmt = mysqli_prepare($conn, "SELECT * FROM " . DBPREFIX . "newsletter WHERE news_save = ?");
$stmt->bind_param("s", $param1);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $intDCount = mysqli_num_rows($result);
}

$param1 = "template";
$stmt = mysqli_prepare($conn, "SELECT * FROM " . DBPREFIX . "newsletter WHERE news_save = ?");
$stmt->bind_param("s", $param1);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $intTCount = mysqli_num_rows($result);
}
mysqli_close($conn);

include "../includes/header.php";
?>
<script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
<div id="main" class="container">
    <header>
        <h2 style="text-align:center;">Manage Drafts</h2>
    </header>
    <div class="row">
        <div class="-4u 4u$ 12u$(medium)">
            <h5>You have <?php echo $intDCount; ?> Drafts and <?php echo $intTCount; ?> Templates.</h5>
            <div class="row">
                <div class="12u$">
                    <a class="picimg button fit fancybox.ajax" href="imageurls.php">Get Image URLs</a>
                </div>
                <div class="12u$">
                    <div class="select-wrapper">
                        <?php selectLoadDraft(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="admin_drafts.php" id="template" method="post">
        <input type="hidden" name="tempid" id="tempid" value="" />
        <div class="row uniform">
            <div class="-1u 10u$ 12u$(medium)">
                <div class="row">
                    <div class="6u 12u$(small)" style="padding-bottom:10px;">
                        <label for="temptitle">Title</label>
                        <input type="text" name="temptitle" id="temptitle" value="" size="30" />
                    </div>
                    <div class="6u$ 12u$(medium)">
                        <label for="tempdescr">Draft Description</label>
                        <input type="text" id="tempdescr" name="tempdescr" value="" form="template" size="30" required />
                    </div>
                    <div class="12u$  12u$(small)">
                        <textarea name="tempbody" id="tempbody" cols="65" rows="25" wrap="soft"></textarea>
                        <script>
                            CKEDITOR.replace( 'tempbody');
                        </script>
                    </div>
                </div>
                <div class="row" style="padding-top:10px;">
                    <div class="4u 12u$(small)">
                        <input type="submit" name="save" class="button fit" value="Save as Draft" />
                    </div>
                    <div class="4u 12u$(small)">
                        <input type="submit" name="save" class="button fit" value="Save as Template" />
                    </div>
                    <div class="4u$ 12u$(small)">
                        <input type="submit" name="delete" class="button fit" value="Delete" onclick="return confirm('WARNING!\n Are you SURE you want to delete this template?')" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php include "../includes/footer.php" ?>
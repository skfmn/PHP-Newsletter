<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$msg = $cookies = $dir = "";
$cookies = $_SESSION["nwsadminname"];

if ($cookies == "") {

    redirect($redirect . "admin/login.php");
    ob_end_flush();

}

if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
    if ($msg <> "") {
        displayFancyMsg(getMessage($msg));
        $_SESSION["msg"] = "";
    }
}

$baseDir = BASEDIR;
$baseDir = str_replace("\\\\", "\\", $baseDir . "newsleeter\\install\\");
$dir = $baseDir;
if (is_dir($dir)) {
    deleteDir($dir);
}

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

if (!$conn) {

    die("Connection failed: " . mysqli_connect_error());
}

$param1 = "draft";
$stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "newsletter WHERE news_save = ?");
$stmt->bind_param("s", $param1);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $intDCount = mysqli_num_rows($result);
}

$param1 = "template";
$stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "newsletter WHERE news_save = ?");
$stmt->bind_param("s", $param1);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $intTCount = mysqli_num_rows($result);
}
mysqli_close($conn);

include "../includes/header.php";
?>
<div id="main" class="container">
    <header>
        <h4 style="text-align:center;">Choose an Option below</h4>
    </header>
    <div class="row">
        <div class="-3u 3u 12u(medium)">
            <ul class="alt">
                <li>
                    <a href="admin_send.php" class="button fit">Send a Newsletter</a>
                </li>
                <li>
                    <a href="admin_manage.php" class="button fit">Manage Admins</a>
                </li>
                <li>
                    <a href="admin_images.php" class="button fit">Manage Images</a>
                </li>
                <li>
                    <a href="admin_options.php" class="button fit">Manage Options</a>
                </li>
            </ul>
        </div>
        <div class="3u$ 12u$(medium)">
            <ul class="alt">
                <li>
                    <a href="admin_create.php" class="button fit">Create Template/Draft</a>
                </li>
                <li>
                    <a href="admin_template.php" class="button fit">
                        Manage Templates (<?php echo $intTCount; ?>)
                    </a>
                </li>
                <li>
                    <a href="admin_drafts.php" class="button fit">
                        Manage Drafts (<?php echo $intDCount; ?>)
                    </a>
                </li>
                <li>
                    <a href="admin_addresses.php" class="button fit">Manage Addresses</a>
                </li>
            </ul>
        </div>
        <div class="-3u 6u$ 12u$(medium)">
            <?php echo file_get_contents("http://www.phpjunction.com/gnews.php?ref=y&pnl=" . $version . ""); ?>
        </div>

    </div>
</div>
<?php include "../includes/footer.php"; ?>
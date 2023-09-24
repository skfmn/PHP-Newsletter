<?php
ob_start();
require("../PHPMailer/src/PHPMailer.php");
require("../PHPMailer/src/SMTP.php");
require("../PHPMailer/src/Exception.php");
include "globals.php";
include "functions.php";
$msg = "";
if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
    if ($msg <> "") {
        displayFancyMsg(getMessage($msg));
        $_SESSION["msg"] = "";
    }
}

?>
<!DOCTYPE HTML>
<html>
<head>
  <title>PHP Newsletter - Version 1.0</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link type="text/css" rel="stylesheet" href="<?php echo $http . "://" . $httpHost . NEWSDIR; ?>/assets/css/jquery.fancybox.css" />
  <link type="text/css" rel="stylesheet" href="<?php echo $http . "://" . $httpHost . NEWSDIR; ?>/assets/css/main.css" />
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
</head>

<body>
  <div id="main" class="container">
<?php
$email = $confirm = $response = $param1 = $cancel = "";
$msg = $subject = $emailMsg = $headers = $error = $nemail = "";
$blnmsgsent = false;

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ((isset($_POST["email"]))) {
    $email = test_input($_POST["email"]);
    $email = trim($email);
} else if ((isset($_GET["email"]))) {
    $email = test_input($_GET["email"]);
    $email = str_replace("~", "@", $email);
    $email = str_replace("-", ".", $email);
    $email = trim($email);
}

if ((isset($_GET["mode"]))) {
    $mode = test_input($_GET["mode"]);
    if ($mode == "confirm") {
        $confirm = "yes";
    } else if ($mode == "cancel") {
        $cancel = "yes";
    }
}

if (isset($_POST["confirm"])) {
    $confirm = test_input($_POST["confirm"]);
}

if (isset($_POST["cancel"])) {
    $cancel = test_input($_POST["cancel"]);
}

if ($confirm == "yes") {

    $token = "";
    $token = $_GET["token"];
    $stmt = $conn->prepare("UPDATE " . DBPREFIX . "addresses SET confirm = ? WHERE token = ?");
    $stmt->bind_param('ss', $confirm, $token);

    if ($stmt->execute()) {

        $blnmsgsent = true;

    }

    if ($blnmsgsent) {

        $stmt = $conn->prepare("SELECT email FROM " . DBPREFIX . "addresses WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row["email"];
        }

        $endmsg = "";
        $endmsg = getUserMessage("confirmed");
        $endmsg = str_replace("#EMAIL#", $email, $endmsg);
        $endmsg = str_replace("#SITETITLE#", $siteTitle, $endmsg);
        echo $endmsg;

    } else {

        $endmsg = "";
        $endmsg = getUserMessage("confirmerr");
        $endmsg = str_replace("#EMAIL#", $email, $endmsg);
        $endmsg = str_replace("#SITETITLE#", $siteTitle, $endmsg);
        echo $endmsg;

    }

} else if ($confirm == "no") {

    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "addresses WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $endmsg = "";
        $endmsg = getUserMessage("alreadysubbed");
        $endmsg = str_replace("#EMAIL#", $email, $endmsg);
        $endmsg = str_replace("#SITETITLE#", $siteTitle, $endmsg);
        echo $endmsg;

    } else {

        $emailMsg = $token = "";
        $token = randChrs(15);

        $param1 = date("Y-m-d");
        $stmt = $conn->prepare("INSERT INTO " . DBPREFIX . "addresses (email,datDate,confirm,token) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $email, $param1, $confirm, $token);

        if ($stmt->execute()) {

            $subject = $siteTitle . " Newsletter confirmation";
            $emailMsg = str_replace("#SITETITLE#",$siteTitle, $confirmemail);
            $emailMsg = str_replace("#EMAIL#", $email, $emailMsg);
            $emailMsg = str_replace("#CR#", "&copy;", $emailMsg);
            $emailMsg = str_replace("#YEAR#", date("Y"), $emailMsg);
            $emailMsg = str_replace("#CONFIRMREWRITE#", "<a href=\"" . $http . "://" . $domain . "/confirm/" . $token . "/yes/\">Confirm</a>", $emailMsg);
            $emailMsg = str_replace("#CONFIRMNOREWRITE#", "<a href=\"" . $http . "://" . $domain . NEWSDIR . "includes/process.php?token=" . $token . "&confirm=yes\">Confirm</a>", $emailMsg);
            $emailMsg = str_replace("#CANCELREWRITE#", "<a href=\"" . $http . "://" . $domain . "/cancel/" . $token . "/yes/\">Unsubscribe</a>", $emailMsg);
            $emailMsg = str_replace("#CANCELNOREWRITE#", "<a href=\"" . $http . "://" . $domain . NEWSDIR . "includes/process.php?token=" . $token . "&cancel=yes\">Unsubscribe</a>", $emailMsg);

            $emailMsg = wordwrap($emailMsg, 70);

            if (send_mail($email, $subject, $emailMsg, "", "", "", "")) {

                $blnmsgsent = true;

            }

            if ($blnmsgsent) {

                $endmsg = "";
                $endmsg = getUserMessage("thanks");
                $endmsg = str_replace("#EMAIL#", $email, $endmsg);
                $endmsg = str_replace("#SITETITLE#", $siteTitle, $endmsg);
                echo $endmsg;

            } else {

                $endmsg = "";
                $endmsg = getUserMessage("thankserr");
                $endmsg = str_replace("#EMAIL#", $email, $endmsg);
                $endmsg = str_replace("#SITETITLE#", $siteTitle, $endmsg);
                echo $endmsg;

            }

        } else {

            $endmsg = "";
            $endmsg = getUserMessage("adderr");
            $endmsg = str_replace("#EMAIL#", $email, $endmsg);
            $endmsg = str_replace("#SITETITLE#", $siteTitle, $endmsg);
            echo $endmsg;

        }
    }

} else {

    if ($cancel == "yes") {

        $blnDelete = false;
        $stmt = $email = $token = "";

        $token = $_GET["token"];
        $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "addresses WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();
            $email = $row["email"];

            $blnDelete = true;

        } else {

            $endmsg = "";
            $endmsg = getUserMessage("notfound");
            $endmsg = str_replace("#EMAIL#", $email, $endmsg);
            $endmsg = str_replace("#SITETITLE#", $siteTitle, $endmsg);
            echo $endmsg;

        }

        if ($blnDelete) {

            $stmt = "";
            $stmt = $conn->prepare("DELETE FROM " . DBPREFIX . "addresses WHERE token = ?");
            $stmt->bind_param("s", $token);

            if ($stmt->execute()) {

                $endmsg = "";
                $endmsg = getUserMessage("removed");
                $endmsg = str_replace("#EMAIL#", $email, $endmsg);
                $endmsg = str_replace("#SITETITLE#", $siteTitle, $endmsg);
                echo $endmsg;

            } else {

                $endmsg = "";
                $endmsg = getUserMessage("removederr");
                $endmsg = str_replace("#EMAIL#", $email, $endmsg);
                $endmsg = str_replace("#SITETITLE#", $siteTitle, $endmsg);
                echo $endmsg;

            }
        }
    }
}
mysqli_close($conn);
echo "  </div>";
include "footer.php";
?>
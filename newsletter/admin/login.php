<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$cookies = $_SESSION["nwsadminname"];

if ($cookies != "") {

    redirect($redirect . "admin/admin.php");
    ob_end_flush();

}

$userName = $password = "";

if (isset($_POST["name"])) {
    $userName = test_input($_POST["name"]);
}
if (isset($_POST["pwd"])) {
    $password = test_input($_POST["pwd"]);
}

if ($userName <> "") {

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT adminID, name, pwd FROM " . DBPREFIX . "admin WHERE name = '" . $userName . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['pwd'])) {

            $_SESSION["nwsadminID"] = $row["adminID"];
            $_SESSION["nwsadminname"] = $userName;

            redirect($redirect . "admin/admin.php");
            ob_end_flush();
        }
    } else {

        redirect($redirect . "admin/login.php");
        ob_end_flush();
    }
    $conn->close();
}
include "../includes/header.php";
?>
<div id="main" class="container" align="center">
    <div class="row 50%">
        <div class="12u 12u$(medium)">
            <header>
                <h2>PHP Newsletter Admin Login</h2>
            </header>
        </div>
    </div>
</div>
<div id="main" class="container" align="center" style="margin-top:-75px;">
    <div class="row 50%">
        <div class="12u 12u$(medium)">

            <form action="login.php" method="POST">
                <div class="row">
                    <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required />
                    </div>
                    <div class="4u 1u$">
                        <span></span>
                    </div>

                    <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                        <label for="pwd">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="pwd" name="pwd" />
                            <br />
                            <i id="shpwd" onclick="togglePass('pwd','shpwd')" style="cursor:pointer;" class="fa fa-eye-slash shpwd"></i>
                        </div>
                    </div>
                    <div class="4u 1u$">
                        <span></span>
                    </div>

                    <div class="12u 12u$(medium)">
                        <input class="button" type="submit" value="Let me in!" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include "../includes/footer.php" ?>
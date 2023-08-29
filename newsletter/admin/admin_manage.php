<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$msg = $cookies = "";
$cookies = $_SESSION["nwsadminname"];

if ($cookies == "") {

    redirect($redirect . "admin/login.php");
    ob_end_flush();

}

$blnAdminRights = $_SESSION["blnAdminRights"];
$blnARights = $_SESSION["blnARights"];

if ($blnAdminRights == "false") {

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

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$dir = $username = $password = $encrPassword = "";

if (isset($_POST["newadmin"])) {

    $blnSelect = false;
    $username = test_input($_POST["adminname"]);
    $password = test_input($_POST["adpwd"]);

    $password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "admin WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $blnSelect = false;
    } else {
        $blnSelect = true;
    }

    if ($blnSelect) {

        $param1 = $username;
        $param2 = "no";
        $stmt = $conn->prepare("INSERT INTO " . DBPREFIX . "admin (name,pwd,send,addresses,images,templates,options,admins_rights,arights) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssssss', $param1, $password, $param2, $param2, $param2, $param2, $param2, $param2, $param2);

        if ($stmt->execute()) {
            $_SESSION["msg"] = "adad";
        } else {
            $_SESSION["msg"] = "error";
        }

    } else {
        $_SESSION["msg"] = "ant";
    }

    redirect($redirect . "admin/admin_manage.php");
    ob_end_flush();

}

if (isset($_POST["chypwd"])) {

    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "admin WHERE name = ?");
    $stmt->bind_param("s", $cookies);
    $stmt->execute();

    if ($result->num_rows > 0) {

        $password = test_input($_POST["cypwd"]);

    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = "";
    $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET pwd = ? WHERE name = ?");
    $stmt->bind_param("ss", $password, $cookies);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "cpwds";
    } else {
        $_SESSION["msg"] = "error";
    }

    redirect($redirect . "admin/admin_manage.php");
    ob_end_flush();

}

if (isset($_POST["chapwd"])) {

    $param1 = test_input($_POST["cname"]);

    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "admin WHERE name = ?");
    $stmt->bind_param("s", $param1);
    $stmt->execute();

    if ($result->num_rows > 0) {

        if ($row["name"] <> "admin" and strtolower($param1) <> "admin") {
            $password = test_input($_POST["capwd"]);
        }

    } else {

        $_SESSION["msg"] = "nadmin";
        redirect($redirect . "admin/admin.php");
        ob_end_flush();

    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = "";
    $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET pwd = ? WHERE name = ?");
    $stmt->bind_param('ss', $password, $param1);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "capwds";
    } else {
        $_SESSION["msg"] = "error";
    }

    redirect($redirect . "admin/admin.php");
    ob_end_flush();

}

if (isset($_GET["delad"])) {

    $param1 = test_input($_GET["id"]);
    $stmt = $conn->prepare("DELETE FROM " . DBPREFIX . "admin WHERE adminID = ?");
    $stmt->bind_param("s", $param1);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "das";
    } else {
        $_SESSION["msg"] = "error";
    }

    redirect($redirect . "admin/admin.php");
    ob_end_flush();

}

include "../includes/header.php";
?>
<div id="main" class="container">
    <header>
        <h2>Manage Admins</h2>
    </header>
    <div class="row">
        <div class="-1u 5u 12u$(medium)">
            <form action="admin_manage.php" method="post">
                <input type="hidden" name="chypwd" value="yes" />
                <div class="row uniform">
                    <div class="12u$">
                        <h3>Change YOUR Password</h3>
                        <div class="12u 12u$(small)" style="padding-bottom:10px;">
                            <label for="cname">Login Name</label>
                            <input type="text" name="cname" value="<?php echo $cookies; ?>" disabled />
                        </div>
                        <div class="12u 12u$(small)" style="padding-bottom:10px;">
                            <label for="cypwd">Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="cypwd" name="cypwd" required />
                                <br />
                                <i id="shpwd1" onclick="togglePass('cypwd','shpwd1')" style="cursor:pointer;" class="fa fa-eye-slash shpwd"></i>
                            </div>
                            <br />
                        </div>
                        <div class="12u 12u$(small)" style="padding:10px 0px;text-align:center;">
                            <input type="submit" class="button fit" value="Change Password" />
                        </div>
                    </div>
                </div>
            </form>

            <form action="admin_manage.php" method="post">
                <input type="hidden" name="chapwd" value="yes" />
                <div class="row uniform">
                    <div class="12u$">
                        <h3>Change an Admins Password</h3>
                        <div class="12u 12u$(small)" style="padding-bottom:10px;">
                            <label for="cname">Login Name</label>
                            <div class="select-wrapper">
                                <select id="cname" name="cname">
                                    <?php

                                    $sql = "SELECT * FROM " . DBPREFIX . "admin";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {

                                            $name = $row["name"];
                                            if ($name <> "admin") {
                                                echo "            <option value=\"" . $name . "\">" . $name . "</option>";
                                            }

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="12u 12u$(small)" style="padding:10px 0px;">

                            <label for="capwd">Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="capwd" name="capwd" required />
                                <br />
                                <i id="shpwd2" onclick="togglePass('capwd','shpwd2')" style="cursor:pointer;" class="fa fa-eye-slash shpwd"></i>
                            </div>
                            <br />
                        </div>
                        <div class="12u 12u$(small)" style="padding:10px 0px;text-align:center;">
                            <input type="submit" class="button fit" value="Change Password" />
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <div class="5u$ 12u$(medium)">
            <form action="admin_manage.php" method="post">
                <input type="hidden" name="newadmin" value="yes" />
                <div class="row uniform">
                    <div class="12u$">
                        <h3>Add an Admin</h3>
                        <div class="12u 12u$(small)" style="padding-bottom:10px;">
                            <label for="adminname">Admin Name</label>
                            <input type="text" name="adminname" required />
                        </div>
                        <div class="12u 12u$(small)" style="padding-bottom:10px;">
                            <label for="adpwd">Admin Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="adpwd" name="adpwd" required />
                                <br />
                                <i id="shpwd3" onclick="togglePass('adpwd','shpwd3')" style="cursor:pointer;" class="fa fa-eye-slash shpwd"></i>
                            </div>
                            <br />
                        </div>
                        <div class="12u 12u$(small)" style="padding:10px 0px;text-align:center;">
                            <input type="submit" class="button fit" value="Add An Admin" />
                        </div>
                    </div>
                </div>
            </form>

            <div class="row uniform">
                <div class="12u$">
                    <h3>List of Admins</h3>
                    <div class="12u 12u$(small)" style="padding-bottom:10px;">
                        <hr />
                        NOTE: Main Admin is not listed so you won't delete it!
                        <hr />
                    </div>
                    <?php
                    $sql = "SELECT * FROM " . DBPREFIX . "admin";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        ?>
                        <div class="12u 12u$(small)" style="padding-bottom:10px;">
                            <div class="table-wrapper">
                                <table>
                                    <tbody>
                                        <?php
                                        while ($row = $result->fetch_assoc()) {
                                            if ($row["adminID"] <> 1) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $row["name"]; ?>&nbsp;&nbsp;<a onclick="return confirmSubmit('Are you SURE you want to delete this admin?','admin_manage.php?delad=yes&id=<?php echo $row["adminID"] ?>')" style="cursor:pointer; text-decoration:underline;">Delete</a>
                                                        <?php if ($blnARights) { ?>
                                                            &nbsp;&nbsp;<a href="arights.php?id=<?php echo $row["adminID"]; ?>" title="<?php echo $row["name"]; ?>">Assign Rights</a>
                                                  <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
                    }
                    mysqli_close($conn);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../includes/footer.php" ?>
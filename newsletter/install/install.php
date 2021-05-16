<?php 
  ob_start(); 

  function redirect($location) {
    if ($location) {
 
      header('Location: ' . $location);
      exit;

    }
  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function test_inputA($data) {
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  $step = "";
  $step = isset($_GET["step"]) ? $_GET['step'] : "";

  $servname = $username = $dbpassword = $dbname = $dbprefix = $basedir = $gbdir = "";
  $param1 = $param2 = $param3 = $param4 = $param5 = "";
?>
<!DOCTYPE HTML>
<html>
<head>
<title>PHP Newsletter Installation</title>
<link type="text/css" rel="stylesheet" href="../assets/css/main.css" />
</head>
<body>
  <div id="main" class="container" align="center" style="margin-top:-75px;">
    <div class="row 50%">
      <div class="12u 12u$(medium)">
        <header><h2>PHP Newsletter Installation</h2></header>
      </div>
    </div>
  </div>
<?php
  
  if ($step == "one") { 

?>
  <div id="main" class="container" style="margin-top:-100px;text-align:center;">
    <div class="row 50%">
      <div class="12u 12u$(medium)">
        <form action="install.php?step=two" method="post">
        
        <header>
          <h2>MySQL Database</h2>
        </header>
        <div class="row">
          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="servername" style="text-align:left;">Server Host Name or IP Address
              <input type="text" name="servername" required>
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="dbname" style="text-align:left;">Database Name
              <input type="text" name="dbname" required>
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="username" style="text-align:left;">Database Login
              <input type="text" name="username" required>
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="dbpassword" style="text-align:left;">Database Password
              <input type="password" name="dbpassword" required>
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="dbprefix" style="text-align:left;">Table Prefix
              <input type="text" name="dbprefix" value="nwsltr_" required>
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="12u 12u$(medium)">
           <input class="button" type="submit" name="submit" value="Continue">
          </div>
        </div>
        </form>     
      </div>
    </div>
  </div>
<?php 
  } else if ($step == "two") {

?>
  <div id="main" class="container" align="center">
    <div class="row 50%">
      <div class="12u 12u$(medium)">
<?php

    $servername = test_input($_POST["servername"]);
	  $dbname = test_input($_POST["dbname"]);
	  $username = test_input($_POST["username"]);
    $dbpassword = test_input($_POST["dbpassword"]);
    $dbprefix = test_input($_POST["dbprefix"]);

    $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
		
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }

    echo "Creating Database Tables<br /><br />";

		echo "Creating Admin table...<br />";
	
    $sql = "CREATE TABLE IF NOT EXISTS ".$dbprefix."admin ( 
            adminID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	          name VARCHAR(255) NOT NULL ,
	          pwd VARCHAR(255) NOT NULL ,
            send VARCHAR(5) ,
            addresses VARCHAR(5) ,
            images VARCHAR(5),
            templates VARCHAR(5) ,
            db_rights VARCHAR(5) ,
            admins_rights VARCHAR(5) ,
            arights  VARCHAR(5) 
            )";
	 
    if ($conn->query($sql)) {
      echo "Admin table created successfully<br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

    echo "Populating admin table...<br />";

	  $tempPassword = password_hash("admin", PASSWORD_DEFAULT);

    $param1 = "admin";
    $param2 = "yes";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."admin (name,pwd,send,addresses,images,templates,db_rights,admins_rights,arights) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssssss', $param1, $tempPassword, $param2, $param2, $param2, $param2, $param2, $param2, $param2);

    if ($stmt->execute()) {
      echo "Admin table populated successfully<br /><br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    } 

    echo "Creating settings table...<br />";

    $sql = "CREATE TABLE IF NOT EXISTS ".$dbprefix."settings ( 
            settingID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	          site_title VARCHAR(255) ,
	          domain_name VARCHAR(255) ,
            smtp_server VARCHAR(255) ,
            email_address VARCHAR(255) ,
            smtp_password VARCHAR(255)
            )";

    if ($conn->query($sql)) {
      echo "Settings created successfully<br /><br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

		echo "Creating Messages table...<br />";
				  
    $sql = "CREATE TABLE IF NOT EXISTS ".$dbprefix."messages (
	          messageID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	          msg VARCHAR(50) NOT NULL ,
            message VARCHAR(50) NOT NULL
            )";

    if ($conn->query($sql)) {
      echo "Messages table created successfully<br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  
    echo "Populating Messages table...<br />";
  
    echo "Populating Messages table...<br />";

	  $param1 = "eas";
    $param2 ="The email was successfully added  to the database!";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "aid";
    $param2 ="That Email Address is already in the database!";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "ds";
    $param2 ="The delete action was successful!";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "nea";
    $param2 ="You forgot to enter an email address!";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "uls";
    $param2 ="Image(s) uploaded successful!";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "ids";
    $param2 ="Image(s) deleted successful!";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "nadmin";
    $param2 ="You can not change Admins info.";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "del";
    $param2 ="Template deleted!";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "das";
    $param2 ="You successfully deleted the Admin.";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "adad";
    $param2 ="You have successfully added an Admin.";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "nt";
    $param2 ="That name has been taken.";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "tc";
    $param2 ="Template Created!";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "car";
    $param2 ="You have successfully modified Admin Rights";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    $param1 = "nwst";
    $param2 ="Newsletter Sent!";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."messages (msg,message) VALUES (?,?)");
    $stmt->bind_param("ss", $param1, $param2);
    $stmt->execute();

    if ($stmt->execute()) {
      echo "Messages table populated successfully<br /><br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  
		echo "Creating Newsletter Addresses table...<br />";

    $sql = "CREATE TABLE IF NOT EXISTS ".$dbprefix."addresses (
	          NewsID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	          email VARCHAR(50) ,
            datDate VARCHAR(50) ,
            confirm VARCHAR(50)
            )";

    if ($conn->query($sql)) {
      echo "Newsletter Addresses table created successfully<br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
				  
		
		echo "Creating Newsletter table...<br />";

    $sql = "CREATE TABLE IF NOT EXISTS ".$dbprefix."newsletter  (
	          newsletterID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	          news_title VARCHAR(50) ,
            news_save VARCHAR(50) ,
            news_description VARCHAR(255) ,
            news_body VARCHAR(8000)
            )";

    if ($conn->query($sql)) {
      echo "Newsletter table created successfully<br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
				  
    echo "Populating Newsletter table...<br />";

    $param1 = "Template One";
    $param2 ="both";
    $param3 = "A simple starting point!";
    $param4 = '<header><h2 style="text-align:center;">Template One</h2></header><content><div style="text-align:center"><article><span style="font-size:16px;"> Hello World!</span></article></div></content><footer><div style="text-align:center">&copy; 2021 All Rights Reserved</div></footer>';
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."newsletter (news_title, news_save, news_description, news_body) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $param1, $param2, $param3, $param4);
    $stmt->execute();
	
    $param1 = "Template Two";
    $param2 ="both";
    $param3 = "Another Simple Starting Point.";
    $param4 = '<h2>Template Two</h2><table border="0" cellpadding="1" cellspacing="1" style="width:500px;"><tbody><tr><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr></tbody></table>';
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."newsletter (news_title,news_save,news_description,news_body) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $param1, $param2, $param3, $param4);

    if ($stmt->execute()) {
      echo "Newsletter  table populated successfully<br /><br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }		

		echo "Creating database tables...Complete!<br /><br /><br />";

    mysqli_close($conn);
?>
      </div>
    </div>
  </div>
  <div id="main" class="container" align="center">
    <div class="row 50%">
      <div class="12u 12u$(medium)">
        <form action="install.php?step=three" method="post">
        <input type="hidden" name="servername" value="<?php echo $servername; ?>">
        <input type="hidden" name="dbname" value="<?php echo $dbname; ?>">
        <input type="hidden" name="username" value="<?php echo $username; ?>">
        <input type="hidden" name="dbpassword" value="<?php echo $dbpassword; ?>">
        <input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>">
        <header>
          <h3><span class="first">You have successfully installed the MySQL Database<br />Please click the button below to continue</span></h3>
        </header>
        <div class="row">
          <div class="12u 12u$(medium)">
           <input class="button" type="submit" name="submit" value="Continue">
          </div>
        </div>
        </form>     
      </div>
    </div>
  </div>
<?php  
  } else if ($step == "three") { 

  $absPath = "";
  $absPath = $_SERVER['DOCUMENT_ROOT']."\\";
?>
  <div id="main" class="container" align="center">
    <div class="row 50%">
      <div class="12u 12u$(medium)">
        <form action="install.php?step=four" method="post">
        <input type="hidden" name="servername" value="<?php echo test_input($_POST["servername"]); ?>">
        <input type="hidden" name="dbname" value="<?php echo test_input($_POST["dbname"]); ?>">
        <input type="hidden" name="username" value="<?php echo test_input($_POST["username"]); ?>">
        <input type="hidden" name="dbpassword" value="<?php echo test_input($_POST["dbpassword"]); ?>">
        <input type="hidden" name="dbprefix" value="<?php echo test_input($_POST["dbprefix"]); ?>">
        <header>
          <h2>Path Settings</h2>
        </header>
        <div class="row">
          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="dbid" style="text-align:left;">Base Directory
              <input type="text" name="basedir" value="<?php echo $absPath; ?>" />
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="dir" style="text-align:left;">PHP Newsletter Directory
              <input type="text" name="gbdir" value="/newsletter/" size="40" />
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>
          <div class="12u 12u$(medium)">
           <input class="button" type="submit" name="submit" value="Continue">
          </div>
        </div>
        </form>
      </div>
    </div>
  </div>
<?php
  } else if ($step == "four") {

    $file = $fileA = "";

    $servername = test_input($_POST["servername"]);
    $username = test_input($_POST["username"]);
    $dbpassword = test_input($_POST["dbpassword"]);
    $dbname = test_input($_POST["dbname"]);
    $dbprefix = test_input($_POST["dbprefix"]);
    $basedir = test_inputA($_POST["basedir"]);
    $gbdir = test_input($_POST["gbdir"]);

    $basedir = preg_replace("/([\\\])/", '${1}${1}', $basedir);

    $file = fopen('../includes/globals.php',"r");
    $fileA = fread($file,filesize('../includes/globals.php'));
    fclose($file);

    $file = fopen('../includes/globals.php',"w");

    $fileA = str_replace("{#servername#}",$servername,$fileA);
    $fileA = str_replace("{#username#}",$username,$fileA);
    $fileA = str_replace("{#dbpassword#}",$dbpassword,$fileA);
    $fileA = str_replace("{#dbname#}",$dbname,$fileA);
    $fileA = str_replace("{#dbprefix#}",$dbprefix,$fileA);
    $fileA = str_replace("{#basedir#}",$basedir,$fileA);
    $fileA = str_replace("{#gbdir#}",$gbdir,$fileA);

    fwrite($file,$fileA);

    fclose($file);

?>
  <div id="main" class="container" align="center">
    <div class="row 50%">
      <div class="12u 12u$(medium)">
        <form action="install.php?step=five" method="post">
        <input type="hidden" name="servername" value="<?php echo $servername; ?>">
        <input type="hidden" name="dbname" value="<?php echo $dbname; ?>">
        <input type="hidden" name="username" value="<?php echo $username; ?>">
        <input type="hidden" name="dbpassword" value="<?php echo $dbpassword; ?>">
        <input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>">
        <input type="hidden" name="gbdir" value="<?php echo $gbdir; ?>">
        <header>
          <h3><span class="first">You have successfully set the configuration file<br />Please click the button below to continue</span></h3>
        </header>
        <div class="row">
          <div class="12u 12u$(medium)">
           <input class="button" type="submit" name="submit" value="Continue">
          </div>
        </div>
        </form>     
      </div>
    </div>
  </div>
<?php

  } else if ($step == "five") {

    $servername = test_input($_POST["servername"]);
    $username = test_input($_POST["username"]);
    $dbpassword = test_input($_POST["dbpassword"]);
    $dbname = test_input($_POST["dbname"]);
    $dbprefix = test_input($_POST["dbprefix"]);
    $gbdir = test_input($_POST["gbdir"]);
?>
  <div id="main" class="container" style="margin-top:-100px;">
    <div class="row">
      <div class="12u 12u$(medium)" style="text-align:center;">
        <form action="install.php?step=six" method="post">
        <input type="hidden" name="servername" value="<?php echo $servername; ?>">
        <input type="hidden" name="dbname" value="<?php echo $dbname; ?>">
        <input type="hidden" name="username" value="<?php echo $username; ?>">
        <input type="hidden" name="dbpassword" value="<?php echo $dbpassword; ?>">
        <input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>">
        <input type="hidden" name="gbdir" value="<?php echo $gbdir; ?>">
        <header>
          <h2>Other stuff</h2>
        </header>
        <div class="row">

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="sitetitle" style="text-align:left;">Site title
              <input type="text" name="sitetitle" />
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="domainname" style="text-align:left;">Domain name
              <input type="text" name="domainname" value="<?php echo $_SERVER["SERVER_NAME"]; ?>" />
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="smtpserver" style="text-align:left;">SMTP Server <span style="font-size:12px;">(If your not sure leave it blank)</span>
              <input type="text" name="smtpserver" />
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="emailaddress" style="text-align:left;">Your Email Address <span style="font-size:12px;">(If your not sure leave it blank)</span>
              <input type="text" name="emailaddress" />
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
            <label for="smtppwd" style="text-align:left;">SMTP Password <span style="font-size:12px;">(If your not sure leave it blank)</span>
              <input type="password" name="smtppwd" required>
            </label>
          </div>
          <div class="4u 1u$"><span></span></div>

          <div class="12u 12u$(medium)">
           <input class="button" type="submit" name="submit" value="Continue">
          </div>
        </div>
        </form>      
      </div>
    </div>
  </div>
<?php
  } else if ($step == "six") {

    $servername = test_input($_POST["servername"]);
    $username = test_input($_POST["username"]);
    $dbpassword = test_input($_POST["dbpassword"]);
    $dbname = test_input($_POST["dbname"]);
    $dbprefix = test_input($_POST["dbprefix"]);
    $gbdir = test_input($_POST["gbdir"]);

    $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
		
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }

    $param1 = test_input($_POST["sitetitle"]);
    $param2 = test_input($_POST["domainname"]);
    $param3 = test_input($_POST["smtpserver"]);
    $param4 = test_input($_POST["emailaddress"]);
    $param5 = test_input($_POST["smtppwd"]);
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."settings (site_title,domain_name,smtp_server,email_address,smtp_password) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $param1, $param2, $param3, $param4, $param5);


    if ($stmt->execute() === TRUE) {

      if ($_SERVER[HTTPS] == "off") {
        $http = "http";
      }else{
        $http = "https";
      };

      $httpHost = $_SERVER["HTTP_HOST"];
      $redirect = $http."://".$httpHost.$gbdir;
      redirect($redirect."install/install.php?step=done");
      ob_end_flush();

    }
    $conn->close();

  } else if ($step == "done") {
?>
  <div id="main" class="container">
    <div class="row">
      <div class="12u 12u$(medium)" style="text-align:center;">
        <span class="first">
          Success!
          <br />
          You have successfully configured PHPNewsletter!
          <br />
          The next step is to change your password.
          <br />
          Click on the link below and login to admin.
          <br />
          Click on "Password" in the left options menu and change your password.
          <br /><br />
          <a class="first" href="../admin/login.php">Login</a>
        </span>
      </div>
    </div>
  </div>
<?php } else { ?>
  <div id="main" class="container" style="margin-top:-75px;">
    <div class="row">
      <div class="-4u 4u$ 12u$(medium)" style="text-align:center;">
        <span class="first">
	      You are about to install PHPNewsletter.
	      <br>
	      Please follow the instructions carefully!
	      <br><br>
        Before you start:
        <ul style="text-align:left;">
          <li>Create the MySQL database on your server.</li>
          <li>Take note of the Server Name. "localhost" will almost always work, in not contact your provider.</li>
          <li>Other examples would be "mysql.example.com" or an IP address.</li>
          <li>Also take note of the Database Name, User Name, and Password.</li>
          <li>Also make sure you have "write" permissions to the folder.</li>
        </ul>
	      <br><br>
	      <input class="button" type="button" onClick="parent.location='install.php?step=one'" value="Continue">
	      <br><br>
	      </span>      
      </div>
    </div>
  </div>
<?php } ?>
<br />
</body>
</html>
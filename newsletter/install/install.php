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
  $param1 = $param2 = $param3 = $param4 = $param5 = $param6 = $param7 = $param8 = $param9 = "";
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
            adminID int(11) NOT NULL AUTO_INCREMENT ,
	          name VARCHAR(255) NOT NULL ,
	          pwd VARCHAR(255) NOT NULL ,
            send VARCHAR(5) DEFAULT NULL,
            addresses VARCHAR(5) DEFAULT NULL,
            images VARCHAR(5) DEFAULT NULL,
            templates VARCHAR(5) DEFAULT NULL,
            options VARCHAR(5) DEFAULT NULL,
            admins_rights VARCHAR(5) DEFAULT NULL,
            arights  VARCHAR(5) DEFAULT NULL,
            PRIMARY KEY (`adminID`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
	 
    if ($conn->query($sql)) {
      echo "Admin table created successfully<br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

    echo "Populating admin table...<br />";

	  $tempPassword = password_hash("admin", PASSWORD_DEFAULT);

    $param1 = "admin";
    $param2 = "true";
    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."admin (name,pwd,send,addresses,images,templates,options,admins_rights,arights) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssssss', $param1, $tempPassword, $param2, $param2, $param2, $param2, $param2, $param2, $param2);

    if ($stmt->execute()) {
      echo "Admin table populated successfully<br /><br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    } 

    echo "Creating settings table...<br />";

    $sql = "CREATE TABLE IF NOT EXISTS ".$dbprefix."settings ( 
            settingID int(11) NOT NULL AUTO_INCREMENT ,
	          site_title VARCHAR(255) DEFAULT NULL,
	          domain_name VARCHAR(255) DEFAULT NULL,
            smtp_server VARCHAR(255) DEFAULT NULL,
            smtpport varchar(10) NOT NULL,
            email_address VARCHAR(255) DEFAULT NULL,
            smtp_password VARCHAR(255) DEFAULT NULL,
            smtpdebug varchar(10) NOT NULL,
            smtpuse varchar(10) NOT NULL,
            rewrite varchar(10) NOT NULL,
            PRIMARY KEY (`settingID`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

    if ($conn->query($sql)) {
      echo "Settings created successfully<br /><br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

		echo "Creating Messages table...<br />";
				  
    $sql = "CREATE TABLE IF NOT EXISTS ".$dbprefix."messages (
	          messageID int(11) NOT NULL AUTO_INCREMENT ,
	          msg VARCHAR(50) NOT NULL ,
            message VARCHAR(150) NOT NULL,
            PRIMARY KEY (`messageID`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

    if ($conn->query($sql)) {
      echo "Messages table created successfully<br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  
    echo "Populating Messages table...<br />";
  
    echo "Populating Messages table...<br />";

    $sql = "INSERT INTO ".$dbprefix."messages (msg, message) VALUES
    ('ds', 'The delete action was successful!'),
    ('nea', 'You forgot to enter an email address!'),
    ('uls', 'Image(s) uploaded successfully!'),
    ('ids', 'Image(s) deleted successfully!'),
    ('nadmin', 'You can not change Admins info.'),
    ('del', 'Template deleted!'),
    ('das', 'You successfully deleted the Admin.'),
    ('adad', 'You have successfully added an Admin.'),
    ('nt', 'That name has been taken.'),
    ('tc', 'Template Created!'),
    ('car', 'You have successfully modified Admin Rights'),
    ('ulf', 'Upload failed!'),
    ('nwst', 'Newsletter Sent!'),
    ('ant', 'Admin name taken'),
    ('ftna', 'Sorry, only JPG, PNG & GIF files are allowed.'),
    ('fex', 'File already exists!'),
    ('nimg', 'File is not an image.'),
    ('tus', 'Template updated!'),
    ('mus', 'Messages updated successfully!'),
    ('error', 'An unknown error has occurred.<br />Please contact support.'),
    ('siu', 'Site info updated successfully!'),
    ('cpwds', 'You changed your password successfully!'),
    ('nar', 'You have not been assigned rights to view this page.'),
    ('eas', 'Email address added successfully!')";

    if ($conn->query($sql)) {
      echo "Messages table populated successfully<br /><br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  
		echo "Creating Newsletter Addresses table...<br />";

    $sql = "CREATE TABLE IF NOT EXISTS ".$dbprefix."addresses (
	          NewsID int(11) NOT NULL AUTO_INCREMENT ,
	          email VARCHAR(50) DEFAULT NULL,
            datDate VARCHAR(50) DEFAULT NULL,
            confirm VARCHAR(50) DEFAULT NULL,
            PRIMARY KEY (`NewsID`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

    if ($conn->query($sql)) {
      echo "Newsletter Addresses table created successfully<br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
				  	
		echo "Creating Newsletter table...<br />";

    $sql = "CREATE TABLE IF NOT EXISTS ".$dbprefix."newsletter  (
	          newsletterID int(11) NOT NULL AUTO_INCREMENT ,
	          news_title VARCHAR(50) DEFAULT NULL,
            news_save VARCHAR(50) DEFAULT NULL,
            news_description VARCHAR(255) DEFAULT NULL,
            news_body text,
            PRIMARY KEY (`newsletterID`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

    if ($conn->query($sql)) {
      echo "Newsletter table created successfully<br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
				  
    echo "Populating Newsletter table...<br />";

    $sql =  "INSERT INTO ".$dbprefix."newsletter (news_title, news_save, news_description, news_body) VALUES
            ('Template One', 'template', 'A simple starting point!', '&lt;header&gt;\r\n&lt;h2 style=&quot;text-align:center&quot;&gt;Template One&lt;/h2&gt;\r\n&lt;/header&gt;\r\n&lt;content&gt;\r\n&lt;div style=&quot;text-align:center&quot;&gt;\r\n&lt;article&gt;&lt;span style=&quot;font-size:16px&quot;&gt;Hello World!&lt;/span&gt;&lt;/article&gt;\r\n&lt;/div&gt;\r\n&lt;/content&gt;\r\n\r\n&lt;footer&gt;\r\n&lt;div style=&quot;text-align:center&quot;&gt;&amp;copy; 2021 All Rights Reserved&lt;/div&gt;\r\n&lt;/footer&gt;\r\n'),
            ('Template Two', 'template', 'Another simple starting point!', '&lt;h2&gt;Template Two&lt;/h2&gt;\r\n\r\n&lt;table border=&quot;0&quot; cellpadding=&quot;1&quot; cellspacing=&quot;1&quot; style=&quot;width:500px&quot;&gt;\r\n	&lt;tbody&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n			&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n			&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n			&lt;td&gt;&amp;nbsp;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n	&lt;/tbody&gt;\r\n&lt;/table&gt;\r\n'),
            ('Draft One', 'draft', 'A Rough Draft!', '&lt;h1&gt;This is an unfinished Draft!&lt;/h1&gt;\r\n&lt;span style=&quot;font-size:16px&quot;&gt;&lt;span style=&quot;font-family:Comic Sans MS~cursive&quot;&gt;&lt;span style=&quot;color:#1abc9c&quot;&gt;Finish it as you like!&lt;/span&gt;&lt;br /&gt;\r\n&lt;span style=&quot;color:#f1c40f&quot;&gt;Finish it as you like!&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;'),
            ('Draft Two', 'draft', 'Another Rough Draft', '&lt;h1&gt;This is another Rough Draft!&lt;/h1&gt;\r\n')";


    if ($conn->query($sql)) {
      echo "Newsletter  table populated successfully<br /><br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }	
    
    echo "Creating End Messages table...<br />";

    $sql = "CREATE TABLE IF NOT EXISTS ".$dbprefix."endMsg  (
            endMsgID int(11) NOT NULL AUTO_INCREMENT ,
            endMsgName varchar(15) NOT NULL ,
            endMsg text NOT NULL ,
            PRIMARY KEY (`endMsgID`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

    if ($conn->query($sql)) {
      echo "End Messages table created successfully<br />";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

    echo "Populating End Messages table...<br />";

    $sql = "INSERT INTO ".$dbprefix."endMsg (endMsgName, endMsg) VALUES
            ('thanks', '<h2>Thank you!</h2>\r\n#email# was added to our list<br />A confirmation email was sent please follow the instructions in it.<br />Be sure to check your Junk/Spam folder.'),
            ('confirmed', '<h2>Success!</h2><br />#email# has been confirmed.<br /><br />Thank you for subscribing to the #sitetitle# newsletter.'),
            ('confirmerr', '<h2>We''re Sorry</h2><br />There was a problem and we could not confirm #email#<br />Please try again or contact support.'),
            ('alreadysubbed', '<h2>OOPS!</h2><br />It seems that #email# is already subscribed!<br />While we appreciate your enthusiasm you can only subscribe once!'),
            ('thankserr', '<h2>Sorry?</h2><br />We were able to add #email# to our list. But could not send the confirmation email.<br />Please contact support!'),
            ('adderr', '<h2>Sorry!</h2><br />There was a problem and we could not add #email# to our list.<br />Please try again or contact support.'),
            ('notfound', '<h2>Sorry!</h2><br />We could not find #email# in our database.<br />Please contact support.'),
            ('removed', '<h2>Success!</h2>#email# has been removed from our list.<br />We are sorry to see you go!'),
            ('removederr', '<h2>Sorry!</h2>There was a problem and we could not remove #email# from our list.<br />Please contact support!')";

    if ($conn->query($sql)) {
      echo "End Messages  table populated successfully<br /><br />";
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
          <h2>Settings</h2>
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
            <label for="smtpport" style="text-align:left;">SMTP Server <span style="font-size:12px;">(If your not sure leave it as is)</span>
              <input type="text" name="smtpport" value="587" />
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
    $param4 = "587";
    $param5 = test_input($_POST["emailaddress"]);
    $param6 = test_input($_POST["smtppwd"]);
    $param7 = "no";
    $param8 = "yes";
    $param9 = "no";

    $stmt = $conn->prepare("INSERT INTO ".$dbprefix."settings (site_title, domain_name, smtp_server, smtpport, email_address, smtp_password, smtpdebug, smtpuse, rewrite) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssss", $param1, $param2, $param3, $param4, $param5, $param6, $param7, $param8, $param9);

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
      <div class="-3u 6u$ 12u$(medium)" style="text-align:center;">
        <span class="first">
	      You are about to install PHPNewsletter.
	      <br>
	      Please follow the instructions carefully!
	      <br><br>
        Before you start:
        <ul style="text-align:left;">
          <li>Read the readme.txt carefully!</li>
          <li>Create the MySQL database on your server.</li>
          <li>Read the readme.txt carefully!</li>
          <li>Make sure you have "write" permissions to the newsletter folder.</li>
          <li>Read the readme.txt carefully!</li>
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
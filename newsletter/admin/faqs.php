<?php
  session_start();
  ob_start();
  include '../includes/globals.php';
  include '../includes/functions.php';
  

  $userName = $password = "";

  if (isset($_POST["name"])) { $userName = test_input($_POST["name"]); }
  if (isset($_POST["pwd"])) { $password = test_input($_POST["pwd"]); }

  if ($userName <> "") {

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {
  
      die("Connection failed: " . mysqli_connect_error());
    }
  
    $sql = "SELECT adminID, name, pwd FROM ".DBPREFIX."admin WHERE name = '".$userName."'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      if (password_verify($password, $row['pwd'])) {

        $_SESSION["nwsadminID"] = $row["adminID"];
        $_SESSION["nwsadminname"] = $userName;

        redirect($redirect."admin/admin.php");
        ob_end_flush();
      }
    }else{

      redirect($redirect."admin/login.php");
      ob_end_flush();
    }
    $conn->close();
  }
  include "../includes/header.php";
?>
  <div id="main" class="container" align="center">
    <div class="row 50%">
      <div class="12u 12u$(medium)">
        <header><h2>PHP Newsletter FAQS</h2></header>
      </div>
    </div>
  </div>
  <div id="main" class="container" align="center" style="margin-top:-75px;">
    <div class="row 50%">
      <div class="12u 12u$(medium)">
        <div id="accordion">
          <h3>Whats the difference between a Template and a Draft?</h3>
          <div style="text-align:left;">A Template is a finished Draft that can be used for sending Newsletters.<br />A Draft is an unfinished Template not suitable for sending Newsletters.<br />In fact, you can't send a Draft. You have to save it as a Template first.<br /><br />If you're still confused sacrifice an iPhone to the APP gods!</div>
          <h3>What does modRewrite do?</h3>
          <div style="text-align:left;">modRewrite cleans URLS.<br /><br />In this case it changes all the forward facing URLs (Sign Up, Confirm, Cancel)<br />from for example "../includes/process.php" to "/thankyou/" for the Sign Up Thank You page<br /><br />If this makes no sense, sacrifice two iPhones to the APP gods!</div>
          <h3>Why do some people get the emails and others don't?</h3>
          <div style="text-align:left;">
            There could be a few reasons this is happening.
            <br /><br />
            <ol>
              <li style="padding-bottom:10px;">Your SMTP Server may be blocking some addresses from free accounts like hotmail.com, gmail.com and others. Check with your hosting provider to see if they can white list them.</li>
              <li style="padding-bottom:10px;">Your Settings may be set wrong. The SMTP Email address domain should match the SMTP Server domain name. Some shared hosting providers use aliases for Server Names. Contact your provider to see if they can help you in that regard.</li>
              <li>
                Your php.ini or .user.ini may be set wrong. Below is an example of a .user.ini that can be placed in the root folder of your website.<br />These settings should match the settings on the options page.<br />All domain names should match and you can remove the comments "//" and everything after.
                <pre>
                  <code>
sendmail_from=email@example.com // Address for the 'from' field of the Newsletter
SMTP=mail.example.com // SMTP Mail Server
smtp_port=587 // Recommended SMTP Port
username=email@example.com // SMTP User Name, should = sendmail_from
password=password // SMTP Password
                  </code>
                </pre>
              </li>
              <li style="padding-bottom:10px;">You may be trying to send too many emails at one time. Try sending fewer emails at a time.</li>
              <li>The APP gods are displeased! You must sacrifice three iPhones to appease them!</li>
            </ol>
            NOTE: if you turn on SMTP Debug it will display detailed information on each email sent in the output window.
          </div>
          <h3>What SMTP Port should I use?</h3>
          <div style="text-align:left;">
            There are four choices:
            <ol>
              <li>25 = Default port. Not secure, Most providers block port 25 for incoming mail. Should only be used as a last option.</li>
              <li>587 = TLS (Transport layer security) Recommended! Secure, most providers require this port for SMTP.</li>
              <li>465 = SSL (Secure Socket Layer) Use this if your mail server uses a SSL.</li>
              <li>2525 = Mirror of 587. Use this if port 587 is blocked.</li>
            </ol>
            If none of these work sacrifice five iPhones to the APP gods!
          </div>
          <h3>Can I use PHP Newsletter without an SMTP Server?</h3>
          <div style="text-align:left;">
            The short answer is yes.<br /><br />
            The long answer is you can but is not recommended!<br /><br />PHP Newsletter uses PHPMailer which is designed primarily for SMTP. If you turn off SMTP then PHPMailer will use the built-in PHP mail() function which is notoriously sketchy and may not work as intended and you will need to sacrifice ten iPhones to the APP gods for each email sent!  
          </div>
          <h3>Credits</h3>
          <div style="text-align:left;">
            <ul>
              <li>Mail --------> <a target="_blank" href="https://github.com/PHPMailer/PHPMailer">PHPMailer</a></li>
              <li>Editor ------> <a target="_blank" href="https://ckeditor.com/">CKEditor</a></li>
              <li>JQuery ----> <a target="_blank" href="https://api.jqueryui.com/">JQuery UI</a></li>
              <li>Pop-Ups --> <a target="_blank" href="http://fancyapps.com/fancybox/">Fancybox Version 2</a></li>
              <li>Icons ------> <a target="_blank" href="https://fontawesome.com/">Font-awesome</a></li>
            </ul>
            This APP was built with <a href="https://github.com/ajlkn/baseline"><strong>Baseline</strong></a> a boilerplate for creating new projects.
            <br /><br />
            The APP gods are pleased with this!
          </div>
        </div>
      </div>
    </div>
  </div>
<?php include "../includes/footer.php" ?>
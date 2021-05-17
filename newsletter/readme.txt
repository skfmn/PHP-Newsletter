*****************************************************
* PHP Newsletter V1                                 *
* copyright 2021 Steve Frazier                      *
* www.phpjunction.com                               *
*                                                   *
* You may modify and distribute this script free of * 
* charge as long as this readme.txt with the        *
* copyright header remains with it                  *
* or it is mentioned where you display your credits * 
* YOU MAY NOT SELL THIS SCRIPT                      *
*****************************************************

Before you start you must create an MySQL Database, if your not sure how ask your hosting provider.

Be sure to copy the information they give you as you will need it during installation.

You will need to have your SMTP information handy: Server name, User Name, Password, Port Number(if you know it)

Once you have created the Database, Upload the "newsletter" folder and all of it contents to the Root folder of your website.

:IMPORTANT: Make sure you have 'write' permissions on the newsletter folder after you upload it! :IMPORTANT:

Navigate to http://www.yourwebsite.com/newsletter/install/install.php and follow the instructions.

Once installation is done you can login using "admin" for both user name and password

Be sure to change your password once you login.

The newsletter.php and remove.php files contain the forms for people to subscribe and unsubscribe. They may be formatted for your website.
/newsletter/includes/process.php can be formatted to fit your website.

IMPORTANT NOTES:
1. This APP uses PHPMailer which is meant primarily for SMTP. Although it will work without an SMTP server it is not recommended. 
   PHPMailer will use the built-in PHP mail() function as a fall-back which does not work as expected all the time.

2. You should check with your hosting provider to see if they have modRewrite installed on the server before installation.
   You can set it after installation if you don't know.

3. For a smoother install; Appease the APP gods before installation and sacrifice one iPhone!

   
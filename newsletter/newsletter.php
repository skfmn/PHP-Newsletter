<!DOCTYPE HTML>
<html>
<head>
  <title>NewsLetter Registration</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>

<body>
<div align="center">
<br /><br />
<span style="font-family:arial;font-size:12px;color:#000080;font-weight:bold">Enter your Email address below to subscribe to our Newsletter</span><br />
<!-- if you have modRewrite enabled use this line otherwise use the other line -->
<form action="/subscribe/" method="post">
<!-- <form action="./includes/process.php" method="post"> -->
  <input type="hidden" name="confirm" value="no" />
    <table width="75%" align="center">
      <tr>
        <td align="center"><input type="email" name="email" size="20" required /></td>
      </tr>
	  <tr>
	    <td align="center"><input type="submit" value="Subscribe" /></td>
	  </tr>
    </table>
  </form>
</div>
</body>
</html>

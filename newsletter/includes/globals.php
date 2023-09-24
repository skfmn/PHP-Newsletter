<?php
define('HOST', '{#servername#}');
define('USER', '{#username#}');
define('PASSWORD', '{#dbpassword#}');
define('DATABASE', '{#dbname#}');
define('DBPREFIX', '{#dbprefix#}');
define('BASEDIR', '{#basedir#}');
define('NEWSDIR', '{#newsdir#}');

if ($_SERVER["HTTPS"] == "off") {
    $http = "http";
} else {
    $http = "https";
}

$httpHost = $_SERVER["HTTP_HOST"];
$redirect = $http . "://" . $httpHost . NEWSDIR;

$version = "1.2.1";
?>

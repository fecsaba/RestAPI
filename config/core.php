<?php
// show error reporting
error_reporting(E_ALL);

 
// set your default time-zone
date_default_timezone_set('Europe/Budapest');
 
// variables used for jwt
$key = "sg4tKey";
$iss = "http://sg4t.lebeny.eu";
$aud = "http://sg4t.lebeny.eu";
$iat = time();
// $nbf = $iat+1160;
$exp = $iat+60*60*24;


?>


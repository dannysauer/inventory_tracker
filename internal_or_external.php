<?php
$internal = false;
if( 0 === substr_compare(
  sprintf("%032b",ip2long($_SERVER['REMOTE_ADDR'])), // remote address
  sprintf("%032b",ip2long('192.168.0.0')),           // network block
  0, // must be 0 (start of string)
  24 // This is the "/24" part of a CIDR netmask
  ) ){
    $internal = true;
}
?>

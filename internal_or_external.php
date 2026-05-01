<?php
require_once 'parse_config.php';
$internal = false;
list($network, $bits) = explode('/', $config->internal_network);
if( 0 === substr_compare(
  sprintf("%032b",ip2long($_SERVER['REMOTE_ADDR'])), // remote address
  sprintf("%032b",ip2long($network)),                // network block
  0,       // must be 0 (start of string)
  (int)$bits // CIDR prefix length
  ) ){
    $internal = true;
}
?>

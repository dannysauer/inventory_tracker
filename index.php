<?php
// headers here
// then common stuff
require_once 'parse_config.php';
require_once 'internal_or_external.php';
?><!DOCTYPE html>
<html>
<head>
<?php // include mobile zoon meta tags
  readfile( "zoom.html" )
?>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
print "Remote host '$_SERVER[REMOTE_ADDR]' is probably "
    . ($internal ? '' : 'not')
    . " internal";
if( $internal ){
    print "<div id='newcontainer'>Create a new container: "
        . "<form action='create.php'>"
        . "<input type='hidden' name='container' value='/'>"
        . "<input type='text' name='new' value='new'>"
        . "<input type='submit' value='Create'>"
        . "</form></div>";
}
if( ! $containers = opendir( $config->image_base ) ){
    die( "Error opening base dir" );
}
while( false !== ($container = readdir($containers)) ){
    if( $container == '.' or $container == '..' ){
        continue;
    }
    print "<div class='container'>
           Container: <a class='container' href='container.php?container="
        . urlencode($container)
        . "'>$container</a></div>\n";
}
?>
</body>
</html>

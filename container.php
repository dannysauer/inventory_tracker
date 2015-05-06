<?php
// headers here
// then common stuff
require_once 'parse_config.php';
require_once 'internal_or_external.php';
require_once 'find.php';

/* candidate for validate_container.php */
$container = filter_input( INPUT_GET, 'container',
    FILTER_VALIDATE_REGEXP,
    array('options'=>array('regexp'=>$config->dir_regex))
);
if( $container === null ){
    $container = filter_input( INPUT_POST, 'container',
        FILTER_VALIDATE_REGEXP,
        array('options'=>array('regexp'=>$config->dir_regex))
    );
}
if( $container === false ){
    die( "container validation failed." );
}
$path='';
if( 0 !== substr_compare(
    $path=absolutepath( $config->image_base . "/$container" ),
    $config->image_base,
    0,
    strlen($config->image_base)
    )
){
    die( "Path '$path' is not under base dir." );
}
/* end candidate */

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
chdir( $config->image_base . '/' . $container )
    or die( "Failed to change to container directory." );
print "<div>In container '$container'</div>";
print "Remote host '$_SERVER[REMOTE_ADDR]' is probably "
    . ($internal ? '' : 'not')
    . " internal";
if( $internal ){
    print "<div id='newcontainer'>Create a new container: "
        . "<form action='create.php'>"
        . "<input type='hidden' name='container' value='".addslashes($container)."'>"
        . "<input type='text' name='new' value='new'>"
        . "<input type='submit' value='Create'>"
        . "</form></div>";
}
if( ! $containers = opendir( $config->image_base . "/$container" ) ){
    die( "Error opening base dir" );
}
while( false !== ($container = readdir($containers)) ){
    if( $container == '.' or $container == '..' ){
        continue;
    }
    print "<div class='container'>
           Container: <a class='container' href='container.php?"
        . urlencode($container)
        . "'>$container</a></div>\n";
}
?>
</body>
</html>

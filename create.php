<?php
// headers here
// then common stuff
require_once 'parse_config.php';
require_once 'internal_or_external.php';
require_once 'find.php';

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
$new = filter_input( INPUT_GET, 'new',
    FILTER_VALIDATE_REGEXP,
    array('options'=>array('regexp'=>$config->file_regex))
);
if( $new === null ){
    $new = filter_input( INPUT_POST, 'nwq',
        FILTER_VALIDATE_REGEXP,
        array('options'=>array('regexp'=>$config->file_regex))
    );
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

if( ! find_container($new) ){
    chdir( $path = $config->image_base . "/$container" )
        or die( "Failed to change to container directory '$path'." );
    mkdir( $new, 0775)
        or die( "Failed to create directory." );
    if( ! http_redirect( "container.php",
                       array( "container" => "$container/$new" ) )
    ){
        print "Redirect failed. :(";
    }
}

?><!DOCTYPE html>
<html>
<head>
<?php // include mobile zoom meta tags
  readfile( "zoom.html" )
?>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<p>Something clearly went wrong.  You should not see this.</p>
</body>
</html>

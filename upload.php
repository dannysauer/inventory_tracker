<?php
// headers here
// then common stuff
require_once 'parse_config.php';
require_once 'internal_or_external.php';
require_once 'find.php';

// gather input
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
$picture = filter_input( INPUT_POST, 'picture', FILTER_SANITIZE_STRING,
 FILTER_FLAG_STRIP_LOW & FILTER_FLAG_STRIP_HIGH);

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

$target = '';
if( FALSE === ($target = find_container($container)) ){
    die( "Failed to locate directory '$container'." );
}

foreach( $_FILES as $F ){
    // should probably compare mime type to image/* or something
    // print "mime is " . $F['type'];
    $newfile = uniqid(basename($F['name']), false);
    if( $F['error'] == UPLOAD_ERR_OK ){
        move_uploaded_file( $F['tmp_name'], $target.'/'.$newfile );
    }
    else{
        die( 'upload failed ' + $F['error'] );
    }
}
header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
die( "Something went wrong" );

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

<?php
require_once 'parse_config.php';
require_once 'internal_or_external.php';
require_once 'find.php';

if( ! $internal ){
    die( "You should not be here" );
}

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

$object = filter_input( INPUT_GET, 'object',
    FILTER_VALIDATE_REGEXP,
    array('options'=>array('regexp'=>$config->dir_regex))
);
if( $object === null ){
    $object = filter_input( INPUT_POST, 'object',
        FILTER_VALIDATE_REGEXP,
        array('options'=>array('regexp'=>$config->dir_regex))
    );
}

$container_path = '';
if( 0 !== substr_compare(
    $container_path = absolutepath( $config->image_base . "/$container" ),
    $config->image_base,
    0,
    strlen($config->image_base)
    )
){
    die( "Path '$container_path' is not under base dir." );
}

$obj_path = "$container_path/$object";
if( ! is_file( $obj_path ) ){
    die( "Object '$object' not found in container '$container'." );
}

$attrs = array();
foreach( $config->object_attrs as $attr ){
    $val = filter_input( INPUT_GET, "attr_$attr",
        FILTER_SANITIZE_STRING,
        FILTER_FLAG_STRIP_LOW & FILTER_FLAG_STRIP_HIGH
    );
    if( $val !== null ){
        $attrs[$attr] = $val;
        continue;
    }
    $val = filter_input( INPUT_POST, "attr_$attr",
        FILTER_SANITIZE_STRING,
        FILTER_FLAG_STRIP_LOW & FILTER_FLAG_STRIP_HIGH
    );
    if( $val !== null ){
        $attrs[$attr] = $val;
        continue;
    }
}

if( count($attrs) == 0 ){
    die( "No known attribute provided." );
}

foreach( $attrs as $attr => $val ){
    if( ! xattr_set( $obj_path, $attr, $val ) ){
        die( "Failed to set attribute '$attr' to value '$val' on '$obj_path'." );
    }
}

header( 'Location: object.php?container=' . urlencode($container) . '&object=' . urlencode($object) );
die( "Something went wrong" );
?>

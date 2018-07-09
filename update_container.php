<?php
require_once 'parse_config.php';
require_once 'internal_or_external.php';
require_once 'find.php';

if( ! $internal ){
    die( "You should not be here" );
}

// find container being updated
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
$target = '';
if( FALSE === ($target = find_container($container)) ){
    die( "Failed to locate directory '$container'." );
}

// identify attr to update
$attrs = array();
foreach( $config->container_attrs as $attr ){
    $val = filter_input( INPUT_GET, "attr_$attr", 
        FILTER_SANITIZE_STRING,
        FILTER_FLAG_STRIP_LOW & FILTER_FLAG_STRIP_HIGH
    );
    if( $val !== null ){
        $attrs[$attr] = $val;
        next;
    }
    $val = filter_input( INPUT_POST, "attr_$attr", 
        FILTER_SANITIZE_STRING,
        FILTER_FLAG_STRIP_LOW & FILTER_FLAG_STRIP_HIGH
    );
    if( $val !== null ){
        $attrs[$attr] = $val;
        next;
    }
}

if( count($attrs) == 0 ){
  die( "No known attribute provided." );
}

// update xattr on object
foreach( $attrs as $attr => $val ){
    //die( "would set attribute '$attr' to value '$val' on '$target'." );
    if( ! xattr_set( $target, $attr, $val ) ){
        die( "Failed to set attribute '$attr' to value '$val' on '$target'." );
    }
}

header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
die( "Something went wrong" );


if( ! $containers = opendir( $config->image_base ) ){
    die( "Error opening base dir" );
}
?>

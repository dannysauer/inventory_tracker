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
$container_path='';
if( 0 !== substr_compare(
    $container_path=absolutepath( $config->image_base . "/$container" ),
    $config->image_base,
    0,
    strlen($config->image_base)
    )
){
    die( "Path '$container_path' is not under base dir." );
}
/* end candidate */
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
if(  $object !== null
 and $obj_path = "$container_path/$object"
 and is_file( $obj_path )
){
    // This is easier to read than negating the conditions
}
else{
    print "Can't find '$container_path/$object'";
//    header( "Location: container.php?container=$container" );
    die();
}


?><!DOCTYPE html>
<html>
<head>
<?php // include mobile zoom meta tags
  readfile( "zoom.html" )
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="common.js"></script>
</head>
<body>
<?php
chdir( $container_path )
    or die( "Failed to change to container directory." );
print "<div>In container '$container'</div>";
print "Remote host '$_SERVER[REMOTE_ADDR]' is probably "
    . ($internal ? '' : 'not')
    . " internal";

if( $internal ){
    // object details
    print "<div id='object_details'>"
        . "<form action='update_object.php'>"
        ;
    #var_dump($config);
    $attrs = xattr_list( $obj_path );
    //$attrs = array();
    foreach( $attrs as $attr ){
        $val = xattr_get( $obj_path, $attr );
        if( $val === FALSE ){
            $val = '';
        }
        $val = 'pie';
        # TODO: escape $atr for input naming purposes
        print "<div id='attr_$attr' class='table_row'>"
            . "<label for='attr_$attr' class='container_attr'>$attr</label>"
            #. "<div class='container_val'>"
            . "<input type='text' name='attr_$attr' value='$val' />"
            . "</div>"
            ;
    }
    print ""
        . "</form>"
        . "</div>"
        ;
}
print "<div>$obj_path</div>";
print "<div class='object'><img src='/inventory_images/$container/$object' /></div>";


if( ! $container_handle = opendir( $container_path ) ){
    die( "Error opening container dir" );
}
while( false !== ($subcontainer = readdir($container_handle)) ){
    if( $subcontainer == '.' or $subcontainer == '..' ){
        continue;
    }
    $containers[] = $subcontainer;
}
foreach( $containers as $subcontainer ){
    if( is_dir( "$container_path/$subcontainer" ) ){
        print "<div class='container'>
               Container: <a class='container' href='container.php?"
            . "container=" . urlencode($container.'/'.$subcontainer)
            . "'>$subcontainer</a></div>\n";
    }
}
foreach( $containers as $subcontainer ){
    if( ! is_dir( "$container_path/$subcontainer" ) ){
        print "<div class='container'>
               Object: <a class='object' href='object.php?"
            . "container=" . urlencode($container.'/'.$subcontainer)
            . "'>$subcontainer</a></div>\n";
    }
}
?>
</body>
</html>

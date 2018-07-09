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
<?php // include mobile zoom meta tags
  readfile( "zoom.html" )
?>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="common.js"></script>
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
    // container details
    #print "<div id='container_detail' class='two_column'>"
    print "<div id='container_detail'>"
        . "<form action='update_container.php' method='POST'>"
        ;
    #var_dump($config);
    foreach( $config->container_attrs as $attr ){
        $val = xattr_get( '.', $attr );
        if( $val === FALSE ){
            $val = '';
        }
        #$val = 'pie';
        # TODO: escape $atr for input naming purposes
        print "<div id='attr_$attr' class='table_row'>"
            . "<label for='attr_$attr' class='container_attr'>$attr</label>"
            #. "<div class='container_val'>"
            . "<input type='text' name='attr_$attr' value='$val'
               onChange='this.form.elements.namedItem(\"s\").disabled=false'
               />"
            . "</div>"
            ;
    }
    print ""
        . "<input type='hidden' name='container' value='$container' />"
        . "<input type='submit' id='s' disabled='true' />"
        . "</form>"
        . "</div>"
        ;

    // new container
    print "<div id='newcontainer'>Create a new container: "
        . "<form action='create.php'>"
        . "<input type='hidden' name='container' value='".addslashes($container)."'>"
        . "<input type='text' name='new' value='new'>"
        . "<input type='submit' value='Create'>"
        . "</form></div>";

    // upload
    print "<div id='newimage'>Add an image: "
        . "<form action='upload.php' enctype='multipart/form-data' method='POST'>"
        . "<input type='hidden' name='container' value='".addslashes($container)."'>"
        // add MAX_FILE_SIZE at some point
        // http://php.net/manual/en/features.file-upload.post-method.php
        . "<input type='file' name='picture' accept = 'image/*' onChange='picture_added(event)' />"
        . "<br /><img src='about:blank' alt='' id='picture-preview' /><br />"
        . "<input type='submit' id='image_upload' disabled='true' value='Upload' />"
        . "</form></div>";
}

$container_path = $config->image_base . "/$container" ;
if( ! $container_handle = opendir( $config->image_base . "/$container" ) ){
    die( "Error opening base dir" );
}
while( false !== ($subcontainer = readdir($container_handle)) ){
    if( $subcontainer == '.' or $subcontainer == '..' ){
        continue;
    }
    $containers[] = $subcontainer;
}
foreach( $containers as $subcontainer ){
    if( is_dir( $config->image_base . "/$container/$subcontainer" ) ){
        print "<div class='container'>
               Container: <a class='container' href='container.php?"
            . "container=" . urlencode($container.'/'.$subcontainer)
            . "'>$subcontainer</a></div>\n";
    }
}
foreach( $containers as $subcontainer ){
    if( ! is_dir( $config->image_base . "/$container/$subcontainer" ) ){
        print "<div class='container'>
               Object: <a class='object' href='object.php?"
               . join( '&', array(
                   "container=" . urlencode($container) ,
                   "object="    . urlencode($subcontainer) ,
               ))
               . "'>$subcontainer</a></div>\n";

    }
}
?>
</body>
</html>

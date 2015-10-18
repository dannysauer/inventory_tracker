<?php
require_once 'parse_config.php';
require_once 'internal_or_external.php';

function find_container($d){
    global $config;
    return( dir_search($d, $config->image_base) );
}

function dir_search($name, $dir){
    if( false === ($d = opendir($dir)) ){
        return( FALSE );
    }
    // should really only do this if $dir contains a '/'
    if( opendir( "$dir/$name" ) ){
        return( "$dir/$name" );
    }
    while( false !== ($e = readdir($d)) ){
        if( $e == '.' || $e == '..' ){
            continue;
        }
        if( is_dir( "$dir/$e" ) ){
            if( $e == $name ){
                return( "$dir/$e" );
            }
            else{
                if( false !== ($r = dir_search( $name, "$dir/$e" )) ){
                    return( $r );
                };
            }
        }
    }
    // if we get here, we did not find anything
    return( FALSE );
}

// apparently realpath sucks.
// http://stackoverflow.com/questions/4049856/replace-phps-realpath
function absolutePath($path) {
    $isEmptyPath    = (strlen($path) == 0);
    $isRelativePath = ($path{0} != '/');
    $isWindowsPath  = !(strpos($path, ':') === false);

    if (($isEmptyPath || $isRelativePath) && !$isWindowsPath)
        $path= getcwd().DIRECTORY_SEPARATOR.$path;

    // resolve path parts (single dot, double dot and double delimiters)
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $pathParts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutePathParts = array();
    foreach ($pathParts as $part) {
        if ($part == '.')
            continue;

        if ($part == '..') {
            array_pop($absolutePathParts);
        } else {
            $absolutePathParts[] = $part;
        }
    }
    $path = implode(DIRECTORY_SEPARATOR, $absolutePathParts);

    // resolve any symlinks
    if (file_exists($path) && linkinfo($path)>0)
        $path = readlink($path);

    // put initial separator that could have been lost
    $path= (!$isWindowsPath ? '/'.$path : $path);

    return $path;
}
?>

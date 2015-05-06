<?php 
$config_file = './config.json';
if( ! $config_contents = file_get_contents( $config_file ) ){
    die( "Failed to read config_file" );
}
$config = json_decode( $config_contents );
if( ($error = json_last_error()) != JSON_ERROR_NONE ){
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            $errstr = ' - No errors';
        break;
        case JSON_ERROR_DEPTH:
            $errstr = ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            $errstr = ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            $errstr = ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            $errstr = ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            $errstr = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        /*
         * 5.5 also has:
         * JSON_ERROR_RECURSION
         *   One or more recursive references in the value to be encoded
         * JSON_ERROR_INF_OR_NAN
         *   One or more NAN or INF values in the value to be encoded
         * JSON_ERROR_UNSUPPORTED_TYPE
         *   A value of a type that cannot be encoded was given
         */
        default:
            $errstr = ' - Unknown error';
        break;
    }
// if( $errstr = json_last_error_msg() ){ // PHP > 5.5.0
  die( "JSON error: " . $errstr );
}
?>

<?php

declare(strict_types=1);

require 'env.php';
//import database class
require 'class_db.php';
//import mail file
require 'mail.php';

//instantiate class
$db = new DatabaseClass();

//set configuration
define(
    'OV_OPTIONS',
    array(
        "stripTags" => true,
        "strictMode" => true,
        "strictWords" => ["tailorskit", "object", "null", "undefined", "admin", "array", "webmaster", "sql", "php", "javascript"]
    )
);

/**
 *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
 *  origin.
 *
 *  In a production environment, you probably want to be more restrictive, but this gives you
 *  the general idea of what is involved.  For the nitty-gritty low-down, read:
 *
 *  - https://developer.mozilla.org/en/HTTP_access_control
 *  - https://fetch.spec.whatwg.org/#http-cors-protocol
 *
 */
function cors()
{

    // Allow from any origin
    if (true) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: " . ORIGIN);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // cache preflight requests for 1 day
        //Dont cache response
        header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
        header("Pragma: no-cache"); //HTTP 1.0
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}
function doError(int $status = 400, $error)
{
    //Easily print out errors to the user
    $retval = array(
        "success" => false,
        "data" => $error
    );
    http_response_code($status);
    return (print_r(json_encode($retval)) . exit());
}

function doReturn(int $status = 400, bool $success = false, $data)
{
    //Easily print out errors to the user
    $retval = array(
        "success" => $success,
        "data" => $data
    );
    http_response_code($status);
    return (print_r(json_encode($retval)) . exit());
}
function verifyToken(string $Token)
{

    if (strpos($Token, "::")) {
        //check if token is valid
        $token = explode("::", $Token);
        $user_id = base64_decode($token[0]);
        $time = $token[1];
        // $premium = base64_decode( $token[2] );
        if (!$user_id || !$time || (time() > intval($time))) {
            doReturn(401, false, ["message" => "Please re-login to your account"]);
        } else {
            //if successful, return the user_id
            return $user_id;
        }
    }
}

//handle dynamic email
//replaces email placeholders with actual values
function doDynamicEmail($replaceWith, $body)
{

    //return false if it isn't an array
    if (!is_array($replaceWith))
        return;

    //loop through
    foreach ($replaceWith as $key => $val) {
        $body = str_replace('{' . strtoupper($key) . '}', $val, $body);
    }

    return $body;
}

//this function will only be called internally by scripts and not through APIs
//scripts such as in the clients folder will have session started already
function setFormResponse(bool $success, string $message, string $redirectTo)
{
    if ($success && $message) {
        $_SESSION['formResponse'] = ["success" => $success, "message" => $message];
        return header("Location: $redirectTo") . exit();
    }
}

//generate random string
//https://stackoverflow.com/questions/4356289/php-random-string-generator
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

//check if user is signed up
function checkAccount(){
    global $db;

    $res = $db->SelectOne("SELECT * FROM user LIMIT 1", []);

    if(empty($res)) return false;
    
    return true;
}
?>
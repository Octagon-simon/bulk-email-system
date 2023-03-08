<?php

require '../../core/functions.php';
cors();

//use octavalidate
use Validate\octaValidate;

//create new instance
$myForm = new octaValidate('form_upd_profile', OV_OPTIONS);
//define rules for each form input name
$valRules = array(
    "fname" => array(
        ["R", "Your First Name is required"],
        ["APLHA_SPACES", "Your First name must have letters or spaces"]
    ),
    "lname" => array(
        ["R", "Your Last Name is required"],
        ["APLHA_SPACES", "Your Last Name must have letters or spaces"]
    ),
    "state" => array(
        ["R", "Your state of origin is required"],
        ["APLHA_SPACES", "Your state of origin must have letters or spaces"]
    ),
    "phone" => array(
        ["R", "Your Primary Phone is required"],
        ["DIGITS"]
    ),
    "alt_phone" => array(
        ["DIGITS"]
    )
);

$fileRules = array(
    "image" => array(
        ["ACCEPT-MIME", "image/jpg, image/jpeg, image/png"]
    ));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {

    } catch (Exception $e) {
        error_log($e);
        doReturn(500, false, ["message" => "A server error has occured"]);
    }
}else{
    doReturn(400, false, ["message" => "Invalid request method"]);
}
?>
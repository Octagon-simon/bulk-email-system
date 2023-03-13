<?php

declare(strict_types=1);

require 'env.php';
//import database class
require 'class_db.php';
//import mail file
require 'mail.php';

//instantiate class
$db = new DatabaseClass();

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
//https://stackoverflow.com/questions/33865113/extract-email-address-from-string-php/33865191#33865191
function extract_emails_from($string){
    preg_match_all( '/([\w+\.]*\w+@[\w+\.]*\w+[\w+\-\w+]*\.\w+)/is', $string, $matches );
    // var_dump($matches);
    return $matches[0];
}

//check if string is a valid email and then return it
function checkIfStringIsEmail($str){
    if(filter_var($str, FILTER_VALIDATE_EMAIL)){
        return $str;
    }
}

//system status
function checkSystemStatus(){
    //check env
    return(function(){
        //check for constants
        $envs = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME', 'ORIGIN', 'VERSION', 'MAIL_HOST', 'MAIL_CC', 'MAIL_BCC', 'MAIL_UNAME', 'MAIL_PASS', 'MAIL_SENDER', 'MAIL_SMTP_AUTH', 'MAIL_SMTP_PORT'];
        //loop through
        foreach($envs as $e){
            if(!defined($e)){
                return [
                    "success" => false,
                    "msg" => "The constant <strong>$e</strong> is not defined"
                ];
            }
        }
        //check for cron script
        if(!file_exists('cron/run_script.php')){
            return [
                "success" => false,
                "msg" => "This System will not function without the cron script"
            ];
        }
        //if all else
        return [
            "success" => true,
            "msg" => "This System is operational"
        ];
    })();
}
?>
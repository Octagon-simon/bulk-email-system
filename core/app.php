<?php

require('functions.php');

if (session_status() === PHP_SESSION_NONE) session_start();

if(!isset($_SESSION['bes_user']) || empty($_SESSION['bes_user'])){
    //check if account exists
    if(!checkAccount()){
        header("Location:".ORIGIN."setup.php?an=".base64_encode("reg"));
        exit();
    }else{
        header("Location:".ORIGIN."setup.php?an=".base64_encode("log"));
        exit();
    }
}

if(isset($_SESSION['bes_user']) && !empty($_SESSION['bes_user']) && (time() > intval($_SESSION['bes_user']))){
    header("Location:".ORIGIN."setup.php?an=".base64_encode("log"));
    exit();
}

?>
<?php 

    session_start();

    require_once("SwiftCSRF.php");
    $csrf = new SwiftCSRF();

    $dd = $csrf->CheckCsrfInput("hi");
    if($dd){
        echo "found";
    }
    else{
        echo "not found";
    }

    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
    
?>
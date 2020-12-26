<?php 

    //require_once("SwiftDatetime.php");
   
    //http://localhost/SwiftFramework/PHP/Datetime/test.php
    
   // $dt = new SwiftDatetime(true);
   
    try{
       // $test = $dt->takeDate("12-11-2010")->takeDate("12-11-2010")->calculateAge();

       
       $strToTime = new Datetime("now");
        $date = $strToTime->format("Y-m-d");
        echo $date;
    }
    catch(Exception $exp){
        die($exp->getMessage());
    }

   



   
?>
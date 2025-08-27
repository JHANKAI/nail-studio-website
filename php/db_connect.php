<?php
     header("Content-Type:text/html; charset=utf-8");
     $serverName="DESKTOP-G6PM471\SQLEXPRESS";
     $connectionInfo=array("Database"=>"nail_studio","UID"=>"Emma","PWD"=>"1234","CharacterSet" => "UTF-8");
     $conn=sqlsrv_connect($serverName,$connectionInfo);
         if($conn){
            //  echo "Success!!!<br />";
         }else{
             echo "Error!!!<br />";
             die(print_r(sqlsrv_errors(),true));
         }            
?>


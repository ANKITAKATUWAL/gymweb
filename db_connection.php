<?php
    $servername ="localhost";
    $username ="root";
    $password ="";
    $dbname ="project";

    //database connection
    $con = mysqli_connect($servername, $username, $password, $dbname);
    //echo "connection Sucesful";
    
    /*if($con){
        echo "Database connection is sucessful !!";
    }else{
        die(mysqli_error($con));
        //optional no parameter: mysqli_connect_error()
    } */

    
    if(!$con){
        die(mysqli_error($con));
    } 
?>
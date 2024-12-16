<?php
    $servername ="localhost";
    $username ="root";
    $password ="";
    $dbname ="project";

    //database connection
    $con = mysqli_connect($servername, $username, $password, $dbname);

    if(!$con){
        die(mysqli_error($con));
    } 
?>

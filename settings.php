<?php
    $host = "feenix-mariadb.swin.edu.au";
    $user = "s104817068";
    $pswd = "qwertyuiop.123";
    $dbnm = "s104817068_db";

    $table1 = "friends";
    $table2 = "myfriends";

    //connecting to the database
    $conn = @mysqli_connect($host , $user , $pswd , $dbnm);
    if(!$conn){
        echo "Error in connecting to the database";
    }
?>
<?php
    $host = '209.172.60.196';
    $username = 'f1293009';
    $password = 'Gbc28112002';
    $db_name = 'f1293009_assignment4';

    $db_connection = new mysqli($host, $username, $password, $db_name);
    if($db_connection->connect_errno){
        die("MySQLI not connected. Error number: $db_connection->connect_errno");
    }
?>
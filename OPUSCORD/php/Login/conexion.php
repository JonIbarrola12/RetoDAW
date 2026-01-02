<?php
    $hostname = "localhost";
    $username = "root";
    $password = "admin";
    $database = "GAME";
    $conexion = mysqli_connect($hostname,$username,$password,$database)
            or die("Problemas al establecer conexion");
?>
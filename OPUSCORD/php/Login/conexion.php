<?php
    $hostname = "localhost";
    $username = "root";
    $password = "admin";
    $database = "opuscord";
    $conexion = mysqli_connect($hostname,$username,$password,$database)
            or die("Problemas al establecer conexion");
?>
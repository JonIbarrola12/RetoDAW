<?php
require_once('LoginClass.php');

if(isset($_POST['usuario']) && isset($_POST['contrasena'])){
    $login = new LoginClass($_POST['usuario'], $_POST['contrasena']);
} else {
    header('Location: index.php');
}
?>

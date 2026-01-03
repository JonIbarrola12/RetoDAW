<?php
    ob_start();
    session_start();

    if(!isset($_SESSION['valid'])){
        header('Location: index.php');
    }

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
        <link href="../../css/estilos.css" rel="stylesheet"/>
    <title>Login con PHP y Sessions</title>
</head>
<body>
    <header>
        <div id="menu-user">
            <p>Usuario: <?php echo $_SESSION['nombreUsuario'];?> </p>
            <p>Id: <?php echo $_SESSION['id'];?> </p>
        </div>
    </header>
<?php
session_start();
require_once("../conexion.php"); 

// Marcar usuario como OFFLINE ANTES de destruir sesión
if (isset($_SESSION['id_usuario'])) {
    $stmt = $pdo->prepare(
        "UPDATE usuarios SET estado = 'Offline' WHERE id_usuario = ?"
    );
    $stmt->execute([$_SESSION['id_usuario']]);
}

// Limpiar sesión
session_unset();

// Borrar cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header("Location: ../paginas/index.php");
exit;

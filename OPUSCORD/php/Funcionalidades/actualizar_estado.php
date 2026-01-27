<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    exit;
}

$stmt = $pdo->prepare("
    UPDATE usuarios 
    SET Estado = 'Online', last_activity = NOW()
    WHERE id_usuario = ?
");
$stmt->execute([$_SESSION['id_usuario']]);
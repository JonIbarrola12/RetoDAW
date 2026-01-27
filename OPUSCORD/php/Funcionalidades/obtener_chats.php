<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([]);
    exit;
}

$idUsuario = $_SESSION['id_usuario'];

$stmt = $pdo->prepare("
    SELECT u.id_usuario, u.Username, u.Pfp, u.estado
    FROM amigos a
    JOIN usuarios u 
        ON (u.id_usuario = a.id_usuario OR u.id_usuario = a.id_amigo_usuario)
    WHERE a.Estado = 'aceptado'
      AND u.id_usuario != ?
");
$stmt->execute([$idUsuario]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

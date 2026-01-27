<?php
session_start();
require_once '../conexion.php';
require_once '../Clases/Amigo.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([]);
    exit;
}

$idUsuario = $_SESSION['id_usuario'];

$stmt = $pdo->prepare("
    SELECT 
        a.id_amigo,
        u.id_usuario,
        u.Username,
        u.Pfp,
        u.estado
    FROM amigos a
    JOIN usuarios u ON u.id_usuario = a.id_usuario
    WHERE a.Estado = ?
    AND a.id_amigo_usuario = ?
");
$stmt->execute([Amigo::ESTADO_PENDIENTE, $idUsuario]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

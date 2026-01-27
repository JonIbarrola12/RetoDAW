<?php
session_start();
require_once '../conexion.php';

$idUsuario = $_SESSION['id_usuario'];

$stmt = $pdo->prepare("
    SELECT 
        u.id_usuario,
        u.Username,
        u.Pfp,
        CASE 
            WHEN u.last_activity > NOW() - INTERVAL 15 SECOND 
            THEN 'Online'
            ELSE 'Offline'
        END AS estado
    FROM amigos a
    JOIN usuarios u ON (
        u.id_usuario = a.id_usuario OR u.id_usuario = a.id_amigo_usuario
    )
    WHERE 
        a.Estado = 'aceptado'
        AND u.id_usuario != ?
");
$stmt->execute([$idUsuario]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
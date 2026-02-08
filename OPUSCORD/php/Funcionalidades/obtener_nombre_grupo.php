<?php
require 'conexion.php';

$idGrupo = $_GET['id_grupo'] ?? null;

if (!$idGrupo) {
    echo json_encode(['error' => true]);
    exit;
}



$stmt = $pdo->prepare("SELECT Nombre FROM grupos WHERE id_grupo = ?");
$stmt->execute([$idGrupo]);
$grupo = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'nombre' => $grupo['Nombre'] ?? ''
]);

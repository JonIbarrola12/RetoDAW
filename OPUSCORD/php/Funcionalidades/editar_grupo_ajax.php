<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status'=>'error']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$idGrupo = (int)$data['id_grupo'];
$campo = $data['campo'];
$valor = trim($data['valor']);
$idUsuario = $_SESSION['id_usuario'];

if (!in_array($campo, ['Nombre','Descripcion'])) {
    echo json_encode(['status'=>'error']);
    exit;
}

/* comprobar admin */
$stmt = $pdo->prepare("
    SELECT 1 FROM miembros 
    WHERE id_usuario = ? AND GrupoId = ? AND Rol = 'admin'
");
$stmt->execute([$idUsuario, $idGrupo]);

if (!$stmt->fetch()) {
    echo json_encode(['status'=>'error']);
    exit;
}

$stmt = $pdo->prepare("UPDATE grupos SET $campo = ? WHERE id_grupo = ?");
$stmt->execute([$valor, $idGrupo]);

echo json_encode(['status'=>'ok']);

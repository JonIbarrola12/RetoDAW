<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'], $_POST['id_grupo'])) {
    echo json_encode(['status'=>'error','msg'=>'No autorizado']);
    exit;
}

$idUsuario = $_SESSION['id_usuario'];
$idGrupo = (int)$_POST['id_grupo'];

if (!isset($_FILES['foto'])) {
    echo json_encode(['status'=>'error','msg'=>'No se recibió archivo']);
    exit;
}

/* comprobar admin */
$stmt = $pdo->prepare("
    SELECT Rol FROM miembros 
    WHERE id_usuario = ? AND GrupoId = ? AND Rol = 'admin'
");
$stmt->execute([$idUsuario, $idGrupo]);

if (!$stmt->fetch()) {
    echo json_encode(['status'=>'error','msg'=>'No eres admin']);
    exit;
}

/* validar imagen */
$foto = $_FILES['foto'];
$allowed = ['image/jpeg','image/png','image/gif','image/webp'];

if (!in_array($foto['type'], $allowed)) {
    echo json_encode(['status'=>'error','msg'=>'Formato no permitido']);
    exit;
}

/* carpeta */
$dir = '../Uploads/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

/* guardar */
$ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
$nombre = 'grupo_' . $idGrupo . '_' . time() . '.' . $ext;
$ruta = $dir . $nombre;
$pathDB = '../Uploads/' . $nombre;

if (!move_uploaded_file($foto['tmp_name'], $ruta)) {
    echo json_encode(['status'=>'error','msg'=>'Error al guardar imagen']);
    exit;
}

/* actualizar BD */
$stmt = $pdo->prepare("UPDATE grupos SET Pfp = ? WHERE id_grupo = ?");
$stmt->execute([$pathDB, $idGrupo]);

echo json_encode([
    'status' => 'ok',
    'nuevaFoto' => $pathDB
]);

<?php
session_start();
require_once '../conexion.php';

header('Content-Type: application/json');

if (!isset($pdo)) {
    echo json_encode(['status'=>'error','msg'=>'Error conexión BD']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status'=>'error','msg'=>'No autenticado']);
    exit;
}

if (empty($data['username'])) {
    echo json_encode(['status'=>'error','msg'=>'Username vacío']);
    exit;
}

$nuevoUsername = trim($data['username']);

if (strlen($nuevoUsername) < 3) {
    echo json_encode(['status'=>'error','msg'=>'Mínimo 3 caracteres']);
    exit;
}

// comprobar si existe (excepto el propio)
$stmt = $pdo->prepare(
    "SELECT id_usuario FROM usuarios 
     WHERE Username = ? AND id_usuario != ?"
);
$stmt->execute([$nuevoUsername, $_SESSION['id_usuario']]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['status'=>'error','msg'=>'Ese nombre ya existe']);
    exit;
}

// actualizar
$stmt = $pdo->prepare(
    "UPDATE usuarios SET Username = ? WHERE id_usuario = ?"
);
$stmt->execute([$nuevoUsername, $_SESSION['id_usuario']]);

$_SESSION['Usuario'] = $nuevoUsername;

echo json_encode(['status'=>'ok']);

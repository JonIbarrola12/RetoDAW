<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['Usuario'])){
    echo json_encode(['status'=>'error','msg'=>'No autorizado']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$bio = trim($data['bio'] ?? '');

$usuario = $_SESSION['Usuario'];

$mysqli = new mysqli('localhost','root','admin','opuscord');
if($mysqli->connect_error){
    echo json_encode(['status'=>'error','msg'=>'Error BD']);
    exit;
}

$stmt = $mysqli->prepare("UPDATE usuarios SET Bio = ? WHERE Username = ?");
$stmt->bind_param("ss", $bio, $usuario);

if($stmt->execute()){
    $_SESSION['Biografia'] = $bio; // actualizar sesión
    echo json_encode(['status'=>'ok']);
} else {
    echo json_encode(['status'=>'error','msg'=>'No se pudo guardar']);
}
$bio = substr(trim($data['bio'] ?? ''), 0, 200);

$stmt->close();
$mysqli->close();
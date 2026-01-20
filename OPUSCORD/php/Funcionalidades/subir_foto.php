<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['Usuario'])){
    echo json_encode(['status'=>'error','msg'=>'No has iniciado sesión']);
    exit;
}

if(!isset($_FILES['nuevaFoto'])){
    echo json_encode(['status'=>'error','msg'=>'No se recibió archivo']);
    exit;
}

$usuario = $_SESSION['Usuario'];
$foto = $_FILES['nuevaFoto'];

$allowedTypes = ['image/jpeg','image/png','image/gif','image/webp'];
if(!in_array($foto['type'], $allowedTypes)){
    echo json_encode(['status'=>'error','msg'=>'Tipo de archivo no permitido']);
    exit;
}

$dir = '../Funcionalidades/uploads/';
if(!is_dir($dir)){
    if(!mkdir($dir, 0755, true)){
        echo json_encode(['status'=>'error','msg'=>'No se pudo crear la carpeta uploads']);
        exit;
    }
}

$ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
$nombreArchivo = $dir . $usuario . '_' . time() . '.' . $ext;

if(!move_uploaded_file($foto['tmp_name'], $nombreArchivo)){
    echo json_encode(['status'=>'error','msg'=>'No se pudo mover el archivo']);
    exit;
}

$mysqli = new mysqli('localhost','root','admin','opuscord');
if($mysqli->connect_error){
    echo json_encode(['status'=>'error','msg'=>'Error de conexión a la BD']);
    exit;
}

$stmt = $mysqli->prepare("UPDATE usuarios SET Pfp = ? WHERE Username = ?");
if(!$stmt){
    echo json_encode(['status'=>'error','msg'=>'Error al preparar la consulta']);
    exit;
}

$stmt->bind_param("ss", $nombreArchivo, $usuario);

if($stmt->execute()){
    $_SESSION['Foto'] = $nombreArchivo;
    echo json_encode([
        'status' => 'ok',
        'msg' => 'Foto actualizada',
        'nuevaFoto' => $nombreArchivo
    ]);
} else {
    echo json_encode(['status'=>'error','msg'=>'Error al actualizar BD']);
}

$stmt->close();
$mysqli->close();
?>

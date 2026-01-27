<?php
session_start();
require_once '../conexion.php';
require_once '../CRUD/AmigosCRUD.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([]);
    exit;
}

$idUsuario = $_SESSION['id_usuario'];

// Obtener todos los amigos aceptados
$amigos = AmigosCRUD::obtenerAmigos($idUsuario); // Debe devolver un array con id_usuario, Username, Pfp, estado

echo json_encode($amigos);

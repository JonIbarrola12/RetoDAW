<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'], $_POST['id_grupo'])) {
    exit;
}

$idUsuario = $_SESSION['id_usuario'];
$idGrupo = (int)$_POST['id_grupo'];

/* NO permitir que un admin abandone (decisión de diseño) */
$stmt = $pdo->prepare("
    SELECT Rol FROM miembros 
    WHERE id_usuario = ? AND GrupoId = ?
");
$stmt->execute([$idUsuario, $idGrupo]);
$rol = $stmt->fetchColumn();

if ($rol === 'admin') {
    exit('Un admin no puede abandonar el grupo');
}

/* borrar membresía */
$stmt = $pdo->prepare("
    UPDATE miembros 
    SET activo = 0
    WHERE id_usuario = ? AND GrupoId = ?
");
$stmt->execute([$idUsuario, $idGrupo]);


header('Location: ../paginas/grupos.php');

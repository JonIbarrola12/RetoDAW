<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'], $_POST['id_usuario'], $_POST['id_grupo'])) {
    exit;
}

$idAdmin = $_SESSION['id_usuario'];
$idUsuario = (int)$_POST['id_usuario'];
$idGrupo = (int)$_POST['id_grupo'];

/* comprobar admin */
$stmt = $pdo->prepare("
    SELECT Rol FROM miembros
    WHERE id_usuario = ? AND GrupoId = ? AND Rol = 'admin'
");
$stmt->execute([$idAdmin, $idGrupo]);

if (!$stmt->fetch()) {
    exit('No autorizado');
}

/* expulsar */
$stmt = $pdo->prepare("
    DELETE FROM miembros
    WHERE id_usuario = ? AND GrupoId = ?
");
$stmt->execute([$idUsuario, $idGrupo]);

header("Location: ../paginas/grupos.php?grupo=$idGrupo");
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

// Borrar mensajes del usuario en este grupo
$stmt = $pdo->prepare("
    DELETE mg FROM mensajesGrupos mg
    INNER JOIN miembros m ON mg.id_emisor = m.id_miembro
    WHERE m.id_usuario = ? AND m.GrupoId = ?
");
$stmt->execute([$idUsuario, $idGrupo]);

// Luego expulsar al miembro
$stmt = $pdo->prepare("
    DELETE FROM miembros
    WHERE id_usuario = ? AND GrupoId = ?
");
$stmt->execute([$idUsuario, $idGrupo]);

header("Location: ../paginas/grupos.php?grupo=$idGrupo");
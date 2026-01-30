<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'], $_POST['id_grupo'])) {
    exit;
}

$idUsuario = $_SESSION['id_usuario'];
$idGrupo = (int)$_POST['id_grupo'];

/* comprobar admin */
$stmt = $pdo->prepare("
    SELECT 1 FROM miembros 
    WHERE id_usuario = ? AND GrupoId = ? AND Rol = 'admin'
");
$stmt->execute([$idUsuario, $idGrupo]);

if (!$stmt->fetch()) {
    exit('No autorizado');
}

/* borrar miembros */
$pdo->prepare("DELETE FROM miembros WHERE GrupoId = ?")
    ->execute([$idGrupo]);

/* borrar grupo */
$pdo->prepare("DELETE FROM grupos WHERE id_grupo = ?")
    ->execute([$idGrupo]);

header('Location: ../paginas/grupos.php');

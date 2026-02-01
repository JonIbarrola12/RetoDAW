<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'], $_POST['id_grupo'], $_POST['username'])) {
    exit;
}

$idAdmin = $_SESSION['id_usuario'];
$idGrupo = (int)$_POST['id_grupo'];
$username = trim($_POST['username']);

/* comprobar admin */
$stmt = $pdo->prepare("
    SELECT 1 FROM miembros
    WHERE id_usuario = ? AND GrupoId = ? AND Rol = 'admin'
");
$stmt->execute([$idAdmin, $idGrupo]);

if (!$stmt->fetch()) {
    exit('No autorizado');
}

/* CONTAR MIEMBROS DEL GRUPO */
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM miembros WHERE GrupoId = ?
");
$stmt->execute([$idGrupo]);
$totalMiembros = $stmt->fetchColumn();

if ($totalMiembros >= 15) {
    $_SESSION['mensaje'] = "❌ No se pueden tener grupos con más de 15 Miembros";
    header("Location: ../paginas/grupos.php?grupo=$idGrupo");
    exit;
}

/* obtener usuario */
$stmt = $pdo->prepare("
    SELECT id_usuario FROM usuarios WHERE Username = ?
");
$stmt->execute([$username]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $_SESSION['mensaje'] = "❌ Usuario no encontrado";
    header("Location: ../paginas/grupos.php?grupo=$idGrupo");
    exit;
}

$idUsuario = $usuario['id_usuario'];

/* insertar si no existe */
$stmt = $pdo->prepare("
    INSERT IGNORE INTO miembros (id_usuario, GrupoId)
    VALUES (?, ?)
");
$stmt->execute([$idUsuario, $idGrupo]);

$_SESSION['mensaje'] = "✅ Usuario añadido al grupo";
header("Location: ../paginas/grupos.php?grupo=$idGrupo");

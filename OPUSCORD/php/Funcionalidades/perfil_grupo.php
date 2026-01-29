<?php
session_start();
require_once '../conexion.php';
require_once '../CRUD/MiembrosCRUD.php';

if (!isset($_SESSION['id_usuario'])) {
    exit('No has iniciado sesión');
}

if (!isset($_GET['id'])) {
    exit('Grupo no especificado');
}

$idUsuario = $_SESSION['id_usuario'];
$idGrupo = (int) $_GET['id'];

/* obtener datos del grupo */
$stmt = $pdo->prepare("
    SELECT g.Nombre, g.Descripcion, g.Pfp, g.FechaCreacion, u.Username AS creador
    FROM grupos g
    JOIN usuarios u ON g.id_creador = u.id_usuario
    WHERE g.id_grupo = ?
");
$stmt->execute([$idGrupo]);
$grupo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$grupo) {
    exit('Grupo no encontrado');
}

/* comprobar rol del usuario */
$stmt = $pdo->prepare("
    SELECT Rol FROM miembros
    WHERE id_usuario = ? AND GrupoId = ?
");
$stmt->execute([$idUsuario, $idGrupo]);
$miembro = $stmt->fetch(PDO::FETCH_ASSOC);

$esAdmin = $miembro && $miembro['Rol'] === 'admin';

$foto = !empty($grupo['Pfp'])
    ? $grupo['Pfp']
    : '/Recursos/grupos/default.png';
?>

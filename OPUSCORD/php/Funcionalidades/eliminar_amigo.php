<?php
session_start();
require_once '../CRUD/AmigosCRUD.php';
require_once '../conexion.php'; // si lo necesitas

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_amigo'])) {
    $idAmigoUsuario = (int)$_POST['eliminar_amigo']; // ID del amigo
    $idUsuario = $_SESSION['id_usuario']; // tu ID

    // Buscar el registro de amistad
    $stmt = $pdo->prepare("
        SELECT id_amigo 
        FROM amigos 
        WHERE (id_usuario = :yo AND id_amigo_usuario = :amigo)
           OR (id_usuario = :amigo AND id_amigo_usuario = :yo)
    ");
    $stmt->execute([
        ':yo' => $idUsuario,
        ':amigo' => $idAmigoUsuario
    ]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        // Eliminar usando el ID real del registro
        AmigosCRUD::eliminarAmigo($registro['id_amigo']);
    }

    header("Location: ../paginas/amigos.php");
    exit;
}
?>

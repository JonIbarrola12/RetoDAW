<?php
require_once("../conexion.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Recibir datos del formulario
    $nombre     = trim($_POST["nombre"]);
    $apellido   = trim($_POST["apellido"]);
    $email      = trim($_POST["email"]);
    $usuario    = trim($_POST["usuario"]);
    $contrasena = $_POST["contrasena"];

    // 4. Encriptar contraseña
    $passwordHash = password_hash($contrasena, PASSWORD_DEFAULT);

    // 5. Preparar consulta SQL (segura)
    $sql = "INSERT INTO usuarios (Nombre, Apellido, Email, Username, Password)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $apellido, $email, $usuario, $passwordHash);

    // 6. Ejecutar
    if ($stmt->execute()) {
        echo "<script>
                alert('Usuario registrado correctamente');
                window.location.href = 'login.php';
              </script>";
    } else {
        echo "Error al registrar usuario: " . $stmt->error;
    }

    $stmt->close();
}

$conexion->close();
?>
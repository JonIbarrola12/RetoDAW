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

    $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: registrarse.php?error=email");
        exit;
    }
    $stmt->close();


    $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE Username = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: registrarse.php?error=usuario");
        exit;
    }
    $stmt->close();


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
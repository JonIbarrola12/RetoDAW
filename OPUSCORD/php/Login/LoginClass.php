<?php
class LoginClass {
    private $usuario;
    private $contrasena;
    private $conexion;

    public function __construct($usuario, $contrasena) {
        $this->usuario = trim($usuario);
        $this->contrasena = trim($contrasena);

        require_once("conexion.php");
        $this->conexion = $conexion;

        $this->verificarUsuario();
    }

    private function verificarUsuario() {
        // Preparar la consulta segura
        $stmt = $this->conexion->prepare("SELECT TrabajadorId, Usuario, Contrasena FROM trabajadores WHERE Usuario = ?");
        $stmt->bind_param("s", $this->usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();

            // Comparación directa sin hash
            if (password_verify($this->contrasena, $fila['Contrasena'])) {
                session_start();
                $_SESSION['Usuario'] = $this->usuario;
                $_SESSION['valid'] = true;
                $_SESSION['id'] = $fila['TrabajadorId'];         // Guardar el ID
                $_SESSION['nombreUsuario'] = $fila['Usuario']; // Guardar el nombre de usuario
                header("Location: ../index.php");
                exit();
            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "El usuario no existe.";
        }

        $stmt->close();
        $this->conexion->close();
    }
}
?>


<?php
class LoginClass {
    private $usuario;
    private $contrasena;
    private $conexion;

    public function __construct($usuario, $contrasena) {
        $this->usuario = trim($usuario);
        $this->contrasena = trim($contrasena);

        require_once("../conexion.php");
        $this->conexion = $conexion;

        $this->verificarUsuario();
    }

    private function verificarUsuario() {
        // Preparar la consulta segura
        $stmt = $this->conexion->prepare("SELECT id_usuario, Username, Password FROM usuarios WHERE Username = ?");
        $stmt->bind_param("s", $this->usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();

            // Comparación directa sin hash
            if (password_verify($this->contrasena, $fila['Password'])) {
                session_start(); // inicia sesión
                $_SESSION['Usuario'] = $fila['Username'];       // Nombre de usuario
                $_SESSION['id_usuario'] = $fila['id_usuario'];  // ID de usuario
                $_SESSION['valid'] = true;                      // login válido
                header("Location: ../paginas/index.php");
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


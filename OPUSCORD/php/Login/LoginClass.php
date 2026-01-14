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
        $stmt = $this->conexion->prepare("SELECT id_usuario, Username, Password, Pfp FROM usuarios WHERE Username = ?");
        $stmt->bind_param("s", $this->usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();

            // Comparación directa sin hash
            if (password_verify($this->contrasena, $fila['Password'])) {
                session_start();

                $_SESSION['Usuario'] = $fila['Username'];
                $_SESSION['id_usuario'] = $fila['id_usuario'];
                $_SESSION['valid'] = true;

                // 📌 FOTO GARANTIZADA
                $_SESSION['Foto'] = !empty($fila['Pfp']) 
                    ? $fila['Pfp'] 
                    : '../img/default-avatar.png';

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


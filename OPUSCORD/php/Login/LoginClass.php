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

        $stmt = $this->conexion->prepare(
            "SELECT id_usuario, Username, Password, Bio, Pfp, FechaRegistro 
             FROM usuarios 
             WHERE Username = ?"
        );

        $stmt->bind_param("s", $this->usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            $fila = $resultado->fetch_assoc();

            if (password_verify($this->contrasena, $fila['Password'])) {

                session_start();
                

                $_SESSION['Usuario'] = $fila['Username'];
                $_SESSION['id_usuario'] = $fila['id_usuario'];
                $_SESSION['Biografia'] = $fila['Bio'];
                $_SESSION['FechaReg'] = $fila['FechaRegistro'];
                $_SESSION['valid'] = true;

                $_SESSION['Foto'] = !empty($fila['Pfp']) 
                    ? $fila['Pfp'] 
                    : '../img/default-avatar.png';

                $update = $this->conexion->prepare(
                    "UPDATE usuarios SET estado = 'Online' WHERE id_usuario = ?"
                );
                $update->bind_param("i", $fila['id_usuario']);
                $update->execute();
                $update->close();

                header("Location: ../paginas/index.php");
                exit();

            } else {
                    session_start();
                    $_SESSION['error_login'] = "Contraseña incorrecta.";
                    header("Location: index.php");
                    exit();
            }

        } else {
                session_start();
                $_SESSION['error_login'] = "El usuario no existe.";
                header("Location: index.php");
                exit();
        }

        $stmt->close();
        $this->conexion->close();
    }
    
}
?>

<?php
require_once '../conexion.php';
require_once '../Clases/Usuario.php';

class UsuariosCRUD {

    /* obtener todos los usuarios */
    public static function recibirRegistros(): array {
        global $conexion;

        $sql = "SELECT * FROM usuarios";
        $query = mysqli_query($conexion, $sql);

        if (!$query) {
            return [];
        }

        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    /* añadir usuarios */
    public static function añadirUsuario(Usuario $usuario): void {
        global $conexion;

        $sql = "INSERT INTO usuarios 
                (Nombre, Apellido, Username, Email, Password, Pfp, Bio, FechaRegistro)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return;

        $nombre = $usuario->getNombre();
        $apellido = $usuario->getApellido();
        $username = $usuario->getUsername();
        $email = $usuario->getEmail();
        $password = password_hash($usuario->getPassword(), PASSWORD_DEFAULT);
        $pfp = $usuario->getPfp();
        $bio = $usuario->getBio();
        $fecha = $usuario->getFechaRegistro()->format('Y-m-d H:i:s');

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssss",
            $nombre,
            $apellido,
            $username,
            $email,
            $password,
            $pfp,
            $bio,
            $fecha
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /* eliminar usuarios */
    public static function eliminarUsuario(string $username): void {
        global $conexion;

        $sql = "DELETE FROM usuarios WHERE Username = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return;

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /* modificar usuario */
    public static function modificarUsuario(Usuario $usuario, string $usernameOriginal): void {
        global $conexion;

        $sql = "UPDATE usuarios 
                SET Nombre = ?, Apellido = ?, Username = ?, Email = ?, Pfp = ?, Bio = ?
                WHERE Username = ?";

        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return;

        mysqli_stmt_bind_param(
            $stmt,
            "sssssss",
            $usuario->getNombre(),
            $usuario->getApellido(),
            $usuario->getUsername(),
            $usuario->getEmail(),
            $usuario->getPfp(),
            $usuario->getBio(),
            $usernameOriginal
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /* contar usuarios */
    public static function cuantosUsuarios(): int {
        return count(self::recibirRegistros());
    }

    /* obtener usuarios por su user name */
    public static function obtenerPorUsername(string $username): ?array {
        global $conexion;

        $sql = "SELECT * FROM usuarios WHERE Username = ?";
        $stmt = mysqli_prepare($conexion, $sql);

        if (!$stmt) return null;

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);
        $usuario = mysqli_fetch_assoc($resultado);

        mysqli_stmt_close($stmt);

        return $usuario ?: null;
    }

    /* obtener usuarios por id */
    public static function obtenerPorId(int $id): ?array {
        global $conexion;

        $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conexion, $sql);

        if (!$stmt) return null;

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);
        $usuario = mysqli_fetch_assoc($resultado);

        mysqli_stmt_close($stmt);

        return $usuario ?: null;
    }
}

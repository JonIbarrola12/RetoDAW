<?php
require_once '../conexion.php';
require_once '../Clases/Miembro.php';

class MiembrosCRUD {

    // obtener todos los miembros
    public static function recibirRegistros(): array {
        global $conexion;
        $sql = "SELECT * FROM miembros";
        $query = mysqli_query($conexion, $sql);

        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    // añadir un miembro a un grupo
    public static function añadirMiembro(Miembro $miembro): bool {
        global $conexion;

        $sql = "INSERT INTO miembros (id_usuario, GrupoId, Rol, FechaIngreso)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return false;

        $usuarioId = $miembro->getUsuarioId();
        $grupoId   = $miembro->getGrupoId();
        $rol       = $miembro->getRol();
        $fecha     = $miembro->getFechaIngreso()->format('Y-m-d H:i:s');

        mysqli_stmt_bind_param($stmt, "iiss", $usuarioId, $grupoId, $rol, $fecha);

        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $ok;
    }

    // eliminar un miembro
    public static function eliminarMiembro(int $idMiembro): bool {
        global $conexion;

        $sql = "DELETE FROM miembros WHERE id_miembro = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "i", $idMiembro);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $ok;
    }

    // modificar rol de un miembro
    public static function modificarRol(int $idMiembro, string $rol): bool {
        global $conexion;

        $sql = "UPDATE miembros SET Rol = ? WHERE id_miembro = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "si", $rol, $idMiembro);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $ok;
    }

    // obtener grupos a los que pertenece un usuario
    public static function obtenerGruposUsuario(int $idUsuario): array {
        global $conexion;

        $sql = "SELECT * FROM miembros WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return [];

        mysqli_stmt_bind_param($stmt, "i", $idUsuario);
        mysqli_stmt_execute($stmt);

        $res = mysqli_stmt_get_result($stmt);
        $grupos = mysqli_fetch_all($res, MYSQLI_ASSOC);

        mysqli_stmt_close($stmt);
        return $grupos;
    }

    // comprobar si el usuario es admin del grupo
    public static function esAdmin(int $idUsuario, int $idGrupo): bool {
        global $conexion;

        $sql = "SELECT 1 FROM miembros 
                WHERE id_usuario = ? AND GrupoId = ? AND Rol = 'admin'";

        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "ii", $idUsuario, $idGrupo);
        mysqli_stmt_execute($stmt);

        $res = mysqli_stmt_get_result($stmt);
        $esAdmin = mysqli_num_rows($res) > 0;

        mysqli_stmt_close($stmt);
        return $esAdmin;
    }

    // comprobar si un usuario ya pertenece a un grupo
    public static function existeMiembro(int $idUsuario, int $idGrupo): bool {
        global $conexion;

        $sql = "SELECT 1 FROM miembros WHERE id_usuario = ? AND GrupoId = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, "ii", $idUsuario, $idGrupo);
        mysqli_stmt_execute($stmt);

        $res = mysqli_stmt_get_result($stmt);
        $existe = mysqli_num_rows($res) > 0;

        mysqli_stmt_close($stmt);
        return $existe;
    }

    // obtener un miembro por usuario y grupo
    public static function obtenerPorUsuarioYGrupo(int $idUsuario, int $idGrupo): ?array {
        global $conexion;

        $sql = "SELECT * FROM miembros WHERE id_usuario = ? AND GrupoId = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return null;

        mysqli_stmt_bind_param($stmt, "ii", $idUsuario, $idGrupo);
        mysqli_stmt_execute($stmt);

        $res = mysqli_stmt_get_result($stmt);
        $fila = mysqli_fetch_assoc($res);

        mysqli_stmt_close($stmt);
        return $fila ?: null;
    }

    // obtener un miembro por su id
    public static function obtenerPorId(int $idMiembro): ?array {
        global $conexion;

        $sql = "SELECT * FROM miembros WHERE id_miembro = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) return null;

        mysqli_stmt_bind_param($stmt, "i", $idMiembro);
        mysqli_stmt_execute($stmt);

        $res = mysqli_stmt_get_result($stmt);
        $fila = mysqli_fetch_assoc($res);

        mysqli_stmt_close($stmt);
        return $fila ?: null;
    }

}

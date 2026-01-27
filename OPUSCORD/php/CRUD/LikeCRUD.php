<?php
require_once '../conexion.php';
require_once '../clases/Like.php';

class LikesCRUD {

    // Traer todos los likes
    public static function recibirRegistros() {
        global $conexion;
        $selectSql = "SELECT * from likes";

        $resultados = [];
        $query = mysqli_query($conexion, $selectSql);
        if ($query) {
            while ($fila = mysqli_fetch_assoc($query)) {
                $resultados[] = $fila;
            }
        }
        return $resultados;
    }

    // Añadir like
    public static function añadirLike(Like $like) {
        global $conexion;
        $insertSql = "INSERT INTO likes (id_usuario, id_publicacion) VALUES (?, ?)";

        $stmt = mysqli_prepare($conexion, $insertSql);
        if ($stmt) {
            $usuarioId = $like->getUsuarioId();
            $publicacionId = $like->getPublicacionId();
            mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $publicacionId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Eliminar like
    public static function eliminarLike(int $likeId) {
        global $conexion;
        $deleteSql = "DELETE FROM likes WHERE id_like = ?";

        $stmt = mysqli_prepare($conexion, $deleteSql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $likeId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // ✅ Ver si un usuario ya dio like a una publicación
    public static function obtenerLikeUsuario(int $usuarioId, int $publicacionId) {
        global $conexion;
        $selectSql = "SELECT * FROM likes WHERE id_usuario = ? AND id_publicacion = ? LIMIT 1";
        $stmt = mysqli_prepare($conexion, $selectSql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $publicacionId);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $like = mysqli_fetch_assoc($resultado);
            mysqli_stmt_close($stmt);
            return $like ?: false;
        }
        return false;
    }

    // ✅ Verificar si existe un like (para toggle)
    public static function existeLike(int $usuarioId, int $publicacionId) {
        return self::obtenerLikeUsuario($usuarioId, $publicacionId);
    }

    public static function contarLikes(int $publicacionId): int {
        global $conexion;
        $sql = "SELECT COUNT(*) as total FROM likes WHERE id_publicacion = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $publicacionId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $fila = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
        return (int)$fila['total'];
    }

}
?>

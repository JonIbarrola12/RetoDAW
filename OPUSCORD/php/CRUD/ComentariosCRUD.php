<?php
require_once '../conexion.php';

class ComentariosCRUD {

    public static function recibirRegistros() {
        global $conexion;
        $selectSql = "SELECT * FROM comentarios";

        $resultados = [];
        $query = mysqli_query($conexion, $selectSql);
        if ($query) {
            while ($fila = mysqli_fetch_assoc($query)) {
                $resultados[] = $fila;
            }
        }
        return $resultados;
    }

    public static function añadirComentario(Comentario $comentario) {
        global $conexion;
        $insertSql = "INSERT INTO comentarios (id_publicacion, id_usuario, Contenido, FechaComentario) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $insertSql);
        if ($stmt) {
            $publicacionId = $comentario->getPublicacionId();
            $usuarioId = $comentario->getUsuarioId();
            $contenido = $comentario->getContenido();
            $fecha = $comentario->getFechaComentario()->format('Y-m-d H:i:s');

            mysqli_stmt_bind_param($stmt, "iiss", $publicacionId, $usuarioId, $contenido, $fecha);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    public static function eliminarComentario(int $comentarioId) {
        global $conexion;
        $deleteSql = "DELETE FROM comentarios WHERE id_comentario = ?";
        $stmt = mysqli_prepare($conexion, $deleteSql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $comentarioId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    public static function modificarComentario(Comentario $comentario, int $comentarioIdOriginal) {
        global $conexion;
        $updateSql = "UPDATE comentarios SET Contenido = ? WHERE id_comentario = ?";
        $stmt = mysqli_prepare($conexion, $updateSql);
        if ($stmt) {
            $contenido = $comentario->getContenido();
            mysqli_stmt_bind_param($stmt, "si", $contenido, $comentarioIdOriginal);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // ✅ Nuevo método: obtener comentarios por publicación
    public static function obtenerPorPublicacion(int $publicacionId) {
        global $conexion;
        $selectSql = "SELECT * FROM comentarios WHERE id_publicacion = ? ORDER BY FechaComentario ASC";
        $stmt = mysqli_prepare($conexion, $selectSql);
        $resultados = [];
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $publicacionId);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            while ($fila = mysqli_fetch_assoc($res)) {
                $resultados[] = $fila;
            }
            mysqli_stmt_close($stmt);
        }
        return $resultados;
    }
}
?>

<?php
require_once 'conexion.php';

class ComentariosCRUD {

    public static function recibirRegistros() {
        global $conexion;
        $selectSql = "SELECT * from comentarios";

        try {
            $query = mysqli_query($conexion, $selectSql);
            if (!$query) {
                throw new Exception("Error en la consulta: " . mysqli_error($conexion));
            }

            $resultados = [];
            while ($fila = mysqli_fetch_assoc($query)) {
                $resultados[] = $fila;
            }

            return $resultados;

        } catch (Exception $e) {
            echo "Error al obtener comentarios: " . $e->getMessage();
            return [];
        }
    }

    // Añadir comentario
    public static function añadirComentario(Comentario $comentario) {
        global $conexion;
        $insertSql = "INSERT into comentarios (id_publicacion, id_usuario, Contenido, FechaComentario) VALUES (?, ?, ?, ?)";

        try {
            $stmt = mysqli_prepare($conexion, $insertSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            $publicacionId = $comentario->getPublicacionId();
            $usuarioId = $comentario->getUsuarioId();
            $contenido = $comentario->getContenido();
            $fecha = $comentario->getFechaComentario()->format('Y-m-d H:i:s');

            mysqli_stmt_bind_param(
                $stmt,
                "iiss",
                $publicacionId,
                $usuarioId,
                $contenido,
                $fecha
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el INSERT: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al añadir comentario: " . $e->getMessage();
        }
    }

    public static function eliminarComentario(int $comentarioId) {
        global $conexion;
        $deleteSql = "DELETE from comentarios where id_comentario = ?";

        try {
            $stmt = mysqli_prepare($conexion, $deleteSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt, "i", $comentarioId);

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el DELETE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al eliminar comentario: " . $e->getMessage();
        }
    }

    public static function modificarComentario(Comentario $comentario, int $comentarioIdOriginal) {
        global $conexion;
        $updateSql = "UPDATE comentarios set Contenido = ? WHERE id_comentario = ?";

        try {
            $stmt = mysqli_prepare($conexion, $updateSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            $contenido = $comentario->getContenido();

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $contenido,
                $comentarioIdOriginal
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el UPDATE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al modificar comentario: " . $e->getMessage();
        }
    }

}

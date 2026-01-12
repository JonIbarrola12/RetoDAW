<?php
require_once 'conexion.php';

class LikesCRUD {

    public static function recibirRegistros() {
        global $conexion;
        $selectSql = "SELECT * from likes";

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
            echo "Error al obtener likes: " . $e->getMessage();
            return [];
        }
    }

    public static function añadirLike(Like $like) {
        global $conexion;
        $insertSql = "INSERT INTO likes (id_usuario, id_publicacion) VALUES (?, ?)";

        try {
            $stmt = mysqli_prepare($conexion, $insertSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            $usuarioId = $like->getUsuarioId();
            $publicacionId = $like->getPublicacionId();

            mysqli_stmt_bind_param(
                $stmt,
                "ii",
                $usuarioId,
                $publicacionId
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el insert: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al añadir like: " . $e->getMessage();
        }
    }

    public static function eliminarLike(int $likeId) {
        global $conexion;
        $deleteSql = "DELETE from likes where id_like = ?";

        try {
            $stmt = mysqli_prepare($conexion, $deleteSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt, "i", $likeId);

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el DELETE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al eliminar like: " . $e->getMessage();
        }
    }
}
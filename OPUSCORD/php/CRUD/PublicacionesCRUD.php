<?php
require_once '../conexion.php';

class PublicacionesCRUD {

    public static function recibirRegistros() {
        global $conexion;
        $selectSql = "SELECT * from publicaciones";

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
            echo "Error al obtener publicaciones: " . $e->getMessage();
            return [];
        }
    }

    public static function añadirPublicacion(Publicacion $publicacion) {
        global $conexion;
        $insertSql = "INSERT into publicaciones (id_usuario, Contenido, ImagenUrl, FechaPublicacion, Visibilidad) VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt = mysqli_prepare($conexion, $insertSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            $idUsuario = $publicacion->getUsuarioId();
            $contenido = $publicacion->getContenido();
            $imagenUrl = $publicacion->getImagenUrl();
            $fechaPublicacion = $publicacion->getFechaPublicacion()->format('Y-m-d H:i:s');
            $visibilidad = $publicacion->getVisibilidad();

            mysqli_stmt_bind_param(
                $stmt,
                "issss",
                $idUsuario,
                $contenido,
                $imagenUrl,
                $fechaPublicacion,
                $visibilidad
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el INSERT: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al añadir publicación: " . $e->getMessage();
        }
    }

    public static function eliminarPublicacion(int $publicacionId) {
        global $conexion;
        $deleteSql = "DELETE from publicaciones where id_publicacion = ?";

        try {
            $stmt = mysqli_prepare($conexion, $deleteSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt, "i", $publicacionId);

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el DELETE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al eliminar publicación: " . $e->getMessage();
        }
    }

    public static function modificarPublicacion(Publicacion $publicacion, int $publicacionIdOriginal) {
        global $conexion;
        $updateSql = "UPDATE publicaciones set Contenido = ?, ImagenUrl = ?, Visibilidad = ? WHERE id_publicacion = ?";

        try {
            $stmt = mysqli_prepare($conexion, $updateSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            $contenido = $publicacion->getContenido();
            $imagenUrl = $publicacion->getImagenUrl();
            $visibilidad = $publicacion->getVisibilidad();

            mysqli_stmt_bind_param(
                $stmt,
                "sssi",
                $contenido,
                $imagenUrl,
                $visibilidad,
                $publicacionIdOriginal
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el UPDATE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al modificar publicación: " . $e->getMessage();
        }
    }

    public static function cuantosPublicaciones() {
        $publicaciones = self::recibirRegistros();
        return count($publicaciones);
    }
}

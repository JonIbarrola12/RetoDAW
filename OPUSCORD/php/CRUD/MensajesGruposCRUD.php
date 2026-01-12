<?php
require_once 'conexion.php';

class MensajesGruposCRUD {

    public static function recibirRegistros() {
        global $conexion;
        $selectSql = "SELECT * from mensajesGrupos";

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
            echo "Error al obtener mensajes: " . $e->getMessage();
            return [];
        }
    }

    public static function obtenerMensajesPorGrupo(int $grupoId) {
        global $conexion;
        $sql = "SELECT * from mensajesGrupos where id_receptor = ? order by FechaEnvio ASC";

        try {
            $stmt = mysqli_prepare($conexion, $sql);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt, "i", $grupoId);
            mysqli_stmt_execute($stmt);

            $resultado = mysqli_stmt_get_result($stmt);
            $mensajes = [];

            while ($fila = mysqli_fetch_assoc($resultado)) {
                $mensajes[] = $fila;
            }

            mysqli_stmt_close($stmt);
            return $mensajes;

        } catch (Exception $e) {
            echo "Error al obtener mensajes: " . $e->getMessage();
            return [];
        }
    }

    public static function añadirMensaje(MensajesGrupos $mensaje) {
        global $conexion;
        $insertSql = "INSERT INTO mensajesGrupos (id_emisor, id_receptor, Contenido, FechaEnvio) VALUES (?, ?, ?, ?)";

        try {
            $stmt = mysqli_prepare($conexion, $insertSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            $emisorId = $mensaje->getEmisorId();
            $grupoId = $mensaje->getReceptorId();
            $contenido = $mensaje->getContenido();
            $fecha = $mensaje->getFechaEnvio()->format('Y-m-d H:i:s');

            mysqli_stmt_bind_param(
                $stmt,
                "iiss",
                $emisorId,
                $grupoId,
                $contenido,
                $fecha
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar INSERT: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al enviar mensaje: " . $e->getMessage();
        }
    }

    public static function eliminarMensaje(int $mensajeGrupoId) {
        global $conexion;
        $deleteSql = "delete from mensajesGrupos where id_mensajeGrupo = ?";

        try {
            $stmt = mysqli_prepare($conexion, $deleteSql);
            if (!$stmt) {
                throw new Exception("Error al preparar DELETE: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt, "i", $mensajeGrupoId);

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar DELETE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al eliminar mensaje: " . $e->getMessage();
        }
    }

    public static function modificarMensaje(MensajesGrupos $mensaje, int $mensajeGrupoIdOriginal) {
        global $conexion;
        $updateSql = "UPDATE mensajesGrupos SET Contenido = ? WHERE id_mensajeGrupo = ?";

        try {
            $stmt = mysqli_prepare($conexion, $updateSql);
            if (!$stmt) {
                throw new Exception("Error al preparar UPDATE: " . mysqli_error($conexion));
            }

            $contenido = $mensaje->getContenido();

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $contenido,
                $mensajeGrupoIdOriginal
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar UPDATE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al modificar mensaje: " . $e->getMessage();
        }
    }

}

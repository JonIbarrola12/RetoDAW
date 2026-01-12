<?php
require_once 'conexion.php';

class MensajesPrivadosCRUD {

    public static function recibirRegistros() {
        global $conexion;
        $sql = "SELECT * FROM mensajesPrivados";

        try {
            $query = mysqli_query($conexion, $sql);
            if (!$query) throw new Exception("Error en la consulta: " . mysqli_error($conexion));

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

    public static function obtenerMensajesRecibidos(int $usuarioId) {
        global $conexion;
        $sql = "SELECT * from mensajesPrivados WHERE id_receptor = ? ORDER BY FechaEnvio ASC";

        try {
            $stmt = mysqli_prepare($conexion, $sql);
            if (!$stmt) throw new Exception("Error al preparar consulta: " . mysqli_error($conexion));

            mysqli_stmt_bind_param($stmt, "i", $usuarioId);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            $mensajes = [];
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $mensajes[] = $fila;
            }

            mysqli_stmt_close($stmt);
            return $mensajes;

        } catch (Exception $e) {
            echo "Error al obtener mensajes recibidos: " . $e->getMessage();
            return [];
        }
    }

    public static function obtenerMensajesEnviados(int $usuarioId) {
        global $conexion;
        $sql = "SELECT * FROM mensajesPrivados WHERE id_emisor = ? ORDER BY FechaEnvio ASC";

        try {
            $stmt = mysqli_prepare($conexion, $sql);
            if (!$stmt) throw new Exception("Error al preparar consulta: " . mysqli_error($conexion));

            mysqli_stmt_bind_param($stmt, "i", $usuarioId);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            $mensajes = [];
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $mensajes[] = $fila;
            }

            mysqli_stmt_close($stmt);
            return $mensajes;

        } catch (Exception $e) {
            echo "Error al obtener mensajes enviados: " . $e->getMessage();
            return [];
        }
    }

    public static function añadirMensaje(MensajesPrivados $mensaje) {
        global $conexion;
        $sql = "INSERT INTO mensajesPrivados (id_emisor, id_receptor, Contenido, FechaEnvio, Leido) VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt = mysqli_prepare($conexion, $sql);
            if (!$stmt) throw new Exception("Error al preparar INSERT: " . mysqli_error($conexion));

            $emisor = $mensaje->getEmisorId();
            $receptor = $mensaje->getReceptorId();
            $contenido = $mensaje->getContenido();
            $fecha = $mensaje->getFechaEnvio()->format('Y-m-d H:i:s');
            $leido = $mensaje->getLeido() ? 1 : 0;

            mysqli_stmt_bind_param($stmt, "iissi", $emisor, $receptor, $contenido, $fecha, $leido);
            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al enviar mensaje: " . $e->getMessage();
        }
    }

    public static function marcarComoLeido(int $mensajeId) {
        global $conexion;
        $sql = "UPDATE mensajesPrivados set Leido = 1 where id_mensaje = ?";

        try {
            $stmt = mysqli_prepare($conexion, $sql);
            if (!$stmt) throw new Exception("Error al preparar UPDATE: " . mysqli_error($conexion));

            mysqli_stmt_bind_param($stmt, "i", $mensajeId);
            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al marcar mensaje como leído: " . $e->getMessage();
        }
    }

    public static function eliminarMensaje(int $mensajeId) {
        global $conexion;
        $sql = "DELETE FROM mensajesPrivados WHERE id_mensaje = ?";

        try {
            $stmt = mysqli_prepare($conexion, $sql);
            if (!$stmt) throw new Exception("Error al preparar DELETE: " . mysqli_error($conexion));

            mysqli_stmt_bind_param($stmt, "i", $mensajeId);
            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al eliminar mensaje: " . $e->getMessage();
        }
    }

}

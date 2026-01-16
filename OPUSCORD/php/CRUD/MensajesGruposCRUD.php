<?php
require_once '../conexion.php';
require_once '../Clases/MensajesGrupos.php';

class MensajesGruposCRUD {

    // insertar un mensaje en la base de datos
    public static function añadirMensaje(MensajesGrupos $mensaje) {
        global $conexion;

        $insertSql = "INSERT INTO mensajesgrupos (id_emisor, id_receptor, Contenido, FechaEnvio) VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conexion, $insertSql);
        if (!$stmt) {
            die("Error al preparar INSERT: " . mysqli_error($conexion));
        }

        $emisorId = $mensaje->getEmisorId();
        $receptorId = $mensaje->getReceptorId();
        $contenido = $mensaje->getContenido();
        $fecha = $mensaje->getFechaEnvio()->format('Y-m-d H:i:s');

        mysqli_stmt_bind_param($stmt, "iiss", $emisorId, $receptorId, $contenido, $fecha);

        if (!mysqli_stmt_execute($stmt)) {
            die("Error al ejecutar INSERT: " . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
    }

    // obtener todos los mensajes de un grupo
    public static function obtenerMensajesPorGrupo(int $grupoId): array {
        global $conexion;

        $sql = "SELECT * FROM mensajesgrupos WHERE id_receptor = ? ORDER BY FechaEnvio ASC";

        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            die("Error al preparar SELECT: " . mysqli_error($conexion));
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
    }

    // eliminar un mensaje por su id
    public static function eliminarMensaje(int $mensajeGrupoId) {
        global $conexion;

        $deleteSql = "DELETE FROM mensajesgrupos WHERE id_mensajeGrupo = ?";

        $stmt = mysqli_prepare($conexion, $deleteSql);
        if (!$stmt) {
            die("Error al preparar DELETE: " . mysqli_error($conexion));
        }

        mysqli_stmt_bind_param($stmt, "i", $mensajeGrupoId);
        if (!mysqli_stmt_execute($stmt)) {
            die("Error al ejecutar DELETE: " . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
    }

    // modificar contenido de un mensaje por su id
    public static function modificarMensaje(MensajesGrupos $mensaje, int $mensajeGrupoIdOriginal) {
        global $conexion;

        $updateSql = "UPDATE mensajesgrupos SET Contenido = ? WHERE id_mensajeGrupo = ?";

        $stmt = mysqli_prepare($conexion, $updateSql);
        if (!$stmt) {
            die("Error al preparar UPDATE: " . mysqli_error($conexion));
        }

        $contenido = $mensaje->getContenido();
        mysqli_stmt_bind_param($stmt, "si", $contenido, $mensajeGrupoIdOriginal);

        if (!mysqli_stmt_execute($stmt)) {
            die("Error al ejecutar UPDATE: " . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
    }

    // obtener solicitudes pendientes mensajes con contenido especial
    public static function obtenerSolicitudesPendientes(int $grupoId): array {
        global $conexion;

        $sql = "SELECT * FROM mensajesgrupos WHERE id_receptor = ? AND Contenido = 'SOLICITUD_UNIRSE' ORDER BY FechaEnvio ASC";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $grupoId);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        $pendientes = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $pendientes[] = $fila;
        }

        mysqli_stmt_close($stmt);
        return $pendientes;
    }

    // eliminar solicitud cuando se acepta o rechazar
    public static function eliminarSolicitud(int $mensajeId) {
        global $conexion;

        $sql = "DELETE FROM mensajesgrupos WHERE id_mensajeGrupo = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $mensajeId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // obtener todos los registros de mensajes 
    public static function recibirRegistros(): array {
        global $conexion;

        $selectSql = "SELECT * FROM mensajesgrupos";
        $query = mysqli_query($conexion, $selectSql);

        if (!$query) {
            die("Error en la consulta: " . mysqli_error($conexion));
        }

        $resultados = [];
        while ($fila = mysqli_fetch_assoc($query)) {
            $resultados[] = $fila;
        }

        return $resultados;
    }
}

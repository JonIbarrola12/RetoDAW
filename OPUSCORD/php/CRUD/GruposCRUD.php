<?php
require_once '../conexion.php';
require_once '../Clases/Grupo.php';

class GruposCRUD {

    // obtenemos todos los grupos
    public static function recibirRegistros() {
        global $conexion;

        $sql = "SELECT * FROM grupos";
        $query = mysqli_query($conexion, $sql);

        return mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    // crear grupo
    public static function añadirGrupo(Grupo $grupo): int {
        global $conexion;

        $sql = "INSERT INTO grupos (Nombre, Descripcion, id_creador, FechaCreacion, Pfp)
                VALUES (?, ?, ?, ?, ?)";


        $stmt = mysqli_prepare($conexion, $sql);
        $pfp = '../../Recursos/fotogrupo.png';
        $nombre = $grupo->getNombre();
        $descripcion = $grupo->getDescripcion();
        $creadorId = $grupo->getCreadorId();
        $fecha = $grupo->getFechaCreacion()->format('Y-m-d H:i:s');

        mysqli_stmt_bind_param(
            $stmt,
            "ssiss",
            $nombre,
            $descripcion,
            $creadorId,
            $fecha,
            $pfp
        );

        mysqli_stmt_execute($stmt);

        // id del grupo recien creado
        $idGrupoNuevo = mysqli_insert_id($conexion);

        mysqli_stmt_close($stmt);

        return $idGrupoNuevo;
    }

    //eliminar grupo
    public static function eliminarGrupo(int $grupoId) {
        global $conexion;

        try {
            // borrar mensajes asociados
            $sqlMensajes = "DELETE FROM mensajesgrupos WHERE id_receptor = ?";
            $stmtMensajes = mysqli_prepare($conexion, $sqlMensajes);
            mysqli_stmt_bind_param($stmtMensajes, "i", $grupoId);
            mysqli_stmt_execute($stmtMensajes);
            mysqli_stmt_close($stmtMensajes);

            // borrar miembros asociados
            $sqlMiembros = "DELETE FROM miembros WHERE GrupoId = ?";
            $stmtMiembros = mysqli_prepare($conexion, $sqlMiembros);
            mysqli_stmt_bind_param($stmtMiembros, "i", $grupoId);
            mysqli_stmt_execute($stmtMiembros);
            mysqli_stmt_close($stmtMiembros);

            // borrar el grupo
            $sqlGrupo = "DELETE FROM grupos WHERE id_grupo = ?";
            $stmtGrupo = mysqli_prepare($conexion, $sqlGrupo);
            mysqli_stmt_bind_param($stmtGrupo, "i", $grupoId);
            mysqli_stmt_execute($stmtGrupo);
            mysqli_stmt_close($stmtGrupo);

        } catch (Exception $e) {
            echo "Error al eliminar grupo: " . $e->getMessage();
        }
    }


    // modificar grupo
    public static function modificarGrupo(Grupo $grupo, int $grupoId) {
        global $conexion;

        $sql = "UPDATE grupos 
                SET Nombre = ?, Descripcion = ?
                WHERE id_grupo = ?";

        $stmt = mysqli_prepare($conexion, $sql);

        $nombre = $grupo->getNombre();
        $descripcion = $grupo->getDescripcion();

        mysqli_stmt_bind_param(
            $stmt,
            "ssi",
            $nombre,
            $descripcion,
            $grupoId
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    //obtener un grupo por ID
    public static function obtenerPorId(int $id): ?array {
        global $conexion;

        $sql = "SELECT * FROM grupos WHERE id_grupo = ?";
        $stmt = mysqli_prepare($conexion, $sql);

        if (!$stmt) return null;

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $res = mysqli_stmt_get_result($stmt);
        $grupo = mysqli_fetch_assoc($res);

        mysqli_stmt_close($stmt);

        return $grupo ?: null;
    }


}

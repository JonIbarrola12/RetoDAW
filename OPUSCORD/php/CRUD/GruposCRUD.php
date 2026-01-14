<?php
require_once '../conexion.php';

class GruposCRUD {

    // Obtener todos los grupos
    public static function recibirRegistros() {
        global $conexion;
        $selectSql = "SELECT * from grupos";

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
            echo "Error al obtener registros: " . $e->getMessage();
            return [];
        }
    }

    public static function añadirGrupo(Grupo $grupo) {
        global $conexion;
        $insertSql = "INSERT into grupos (Nombre, Descripcion, id_creador, Pfp, FechaCreacion) VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt = mysqli_prepare($conexion, $insertSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            $nombre = $grupo->getNombre();
            $descripcion = $grupo->getDescripcion();
            $idCreador = $grupo->getCreadorId();
            $pfp = $grupo->getPfp();
            $fechaCreacion = $grupo->getFechaCreacion()->format('Y-m-d H:i:s');

            mysqli_stmt_bind_param(
                $stmt,
                "ssiss",
                $nombre,
                $descripcion,
                $idCreador,
                $pfp,
                $fechaCreacion
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el INSERT: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al añadir grupo: " . $e->getMessage();
        }
    }

    public static function eliminarGrupo(int $grupoId) {
        global $conexion;
        $deleteSql = "DELETE from grupos where id_grupo = ?";

        try {
            $stmt = mysqli_prepare($conexion, $deleteSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt, "i", $grupoId);

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el DELETE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al eliminar grupo: " . $e->getMessage();
        }
    }

    // Modificar un grupo
    public static function modificarGrupo(Grupo $grupo, int $grupoIdOriginal) {
        global $conexion;
        $updateSql = "UPDATE grupos set Nombre = ?, Descripcion = ?, id_creador = ?, Pfp = ? where id_grupo = ?";

        try {
            $stmt = mysqli_prepare($conexion, $updateSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            $nombre = $grupo->getNombre();
            $descripcion = $grupo->getDescripcion();
            $idCreador = $grupo->getCreadorId();
            $pfp = $grupo->getPfp();

            mysqli_stmt_bind_param(
                $stmt,
                "ssisi",
                $nombre,
                $descripcion,
                $idCreador,
                $pfp,
                $grupoIdOriginal
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el update: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al modificar grupo: " . $e->getMessage();
        }
    }

    public static function cuantosGrupos() {
        $grupos = self::recibirRegistros();
        return count($grupos);
    }
}

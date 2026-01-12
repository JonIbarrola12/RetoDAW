<?php
require_once 'conexion.php';

class AmigosCRUD {

    public static function recibirRegistros() {
        global $conexion;
        $selectSql = "SELECT * from amigos";

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
            echo "Error al obtener amigos: " . $e->getMessage();
            return [];
        }
    }

    public static function añadirAmigo(Amigo $amigo) {
        global $conexion;
        $insertSql = "INSERT into amigos (id_usuario, id_amigo_usuario, Estado, FechaSolicitud, FechaAceptacion) VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt = mysqli_prepare($conexion, $insertSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            $usuarioId = $amigo->getUsuarioId();
            $amigoUsuarioId = $amigo->getAmigoUsuarioId();
            $estado = $amigo->getEstado();
            $fechaSolicitud = $amigo->getFechaSolicitud()->format('Y-m-d H:i:s');
            $fechaAceptacion = $amigo->getFechaAceptacion() ?
                $amigo->getFechaAceptacion()->format('Y-m-d H:i:s') : null;

            mysqli_stmt_bind_param(
                $stmt,
                "iisss",
                $usuarioId,
                $amigoUsuarioId,
                $estado,
                $fechaSolicitud,
                $fechaAceptacion
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el INSERT: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al añadir amigo: " . $e->getMessage();
        }
    }

    public static function eliminarAmigo(int $amigoId) {
        global $conexion;
        $deleteSql = "DELETE from amigos where id_amigo = ?";

        try {
            $stmt = mysqli_prepare($conexion, $deleteSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt, "i", $amigoId);

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el DELETE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al eliminar amigo: " . $e->getMessage();
        }
    }

    public static function modificarAmigo(Amigo $amigo, int $amigoIdOriginal) {
        global $conexion;
        $updateSql = "UPDATE amigos set Estado = ?, FechaAceptacion = ? where id_amigo = ?";

        try {
            $stmt = mysqli_prepare($conexion, $updateSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            $estado = $amigo->getEstado();
            $fechaAceptacion = $amigo->getFechaAceptacion() ?
                $amigo->getFechaAceptacion()->format('Y-m-d H:i:s') : null;

            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $estado,
                $fechaAceptacion,
                $amigoIdOriginal
            );

            $resultado = mysqli_stmt_execute($stmt);
            if (!$resultado) {
                throw new Exception("Error al ejecutar el UPDATE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al modificar amigo: " . $e->getMessage();
        }
    }

    public static function cuantosAmigos() {
        $amigos = self::recibirRegistros();
        return count($amigos);
    }
}

<?php
require_once 'conexion.php';
require_once '../Clases/Miembro.php';

class MiembrosCRUD {
    public static function recibirRegistros() {
        global $conexion;
        $sql = "SELECT * FROM miembros";

        try {
            $query = mysqli_query($conexion, $sql);
            if (!$query) {
                throw new Exception("Error en la consulta: " . mysqli_error($conexion));
            }

            $resultados = [];
            while ($fila = mysqli_fetch_assoc($query)) {
                $resultados[] = $fila;
            }

            return $resultados;

        } catch (Exception $e) {
            echo "Error al obtener miembros: " . $e->getMessage();
            return [];
        }
    }

    // Añadir un miembro a un grupo
    public static function añadirMiembro(Miembro $miembro) {
        global $conexion;

        $sql = "INSERT INTO miembros (UsuarioId, GrupoId, Rol, FechaIngreso) VALUES (?, ?, ?, ?)";

        try {
            $stmt = mysqli_prepare($conexion, $sql);
            if (!$stmt) {
                throw new Exception("Error al preparar INSERT: " . mysqli_error($conexion));
            }

            $usuarioId = $miembro->getUsuarioId();
            $grupoId = $miembro->getGrupoId();
            $rol = $miembro->getRol();
            $fecha = $miembro->getFechaIngreso()->format('Y-m-d H:i:s');

            mysqli_stmt_bind_param($stmt, "iiss", $usuarioId, $grupoId, $rol, $fecha);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al ejecutar INSERT: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al añadir miembro: " . $e->getMessage();
        }
    }

    // Eliminar un miembro
    public static function eliminarMiembro(int $miembroId) {
        global $conexion;
        $sql = "DELETE FROM miembros WHERE MiembroId = ?";

        try {
            $stmt = mysqli_prepare($conexion, $sql);
            if (!$stmt) throw new Exception("Error al preparar DELETE: " . mysqli_error($conexion));

            mysqli_stmt_bind_param($stmt, "i", $miembroId);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al ejecutar DELETE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al eliminar miembro: " . $e->getMessage();
        }
    }

    // Modificar rol de un miembro
    public static function modificarRol(Miembro $miembro, int $miembroId) {
        global $conexion;
        $sql = "UPDATE miembros SET Rol = ? WHERE MiembroId = ?";

        try {
            $stmt = mysqli_prepare($conexion, $sql);
            if (!$stmt) throw new Exception("Error al preparar UPDATE: " . mysqli_error($conexion));

            $rol = $miembro->getRol();
            mysqli_stmt_bind_param($stmt, "si", $rol, $miembroId);

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al ejecutar UPDATE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al modificar rol: " . $e->getMessage();
        }
    }

}

<?php
require_once '../conexion.php'; 

class AmigosCRUD {

    // funcion para obtener todos los registros de amigos
    public static function recibirRegistros() {
        global $conexion; 
        $selectSql = "SELECT * from amigos"; // consulta sql para obtener todos los amigos

        try {
            $query = mysqli_query($conexion, $selectSql); // ejecutamos la consulta
            if (!$query) {
                throw new Exception("Error en la consulta: " . mysqli_error($conexion)); 
            }

            $resultados = []; // array para almacenar los resultados
            while ($fila = mysqli_fetch_assoc($query)) { // recorremos todos los registros obtenidos
                $resultados[] = $fila; // añadimos cada fila al array
            }

            return $resultados; // devolvemos todos los amigos

        } catch (Exception $e) {
            echo "Error al obtener amigos: " . $e->getMessage();
            return []; //array vacio en caso de que falle
        }
    }

    // funcion para añadir un amigo nuevo
    public static function añadirAmigo(Amigo $amigo) {
        global $conexion;

        $sql = "INSERT INTO amigos 
                (id_usuario, id_amigo_usuario, Estado, FechaSolicitud, FechaAceptacion)
                VALUES (?, ?, ?, ?, NULL)";

        $stmt = mysqli_prepare($conexion, $sql); 
        if (!$stmt) {
            die("Error prepare: " . mysqli_error($conexion)); 
        }

        // obtenemos los datos del objeto amigo
        $usuarioId = $amigo->getUsuarioId();
        $amigoUsuarioId = $amigo->getAmigoUsuarioId();
        $estado = $amigo->getEstado();
        $fechaSolicitud = $amigo->getFechaSolicitud()->format('Y-m-d H:i:s');

        mysqli_stmt_bind_param(
            $stmt,
            "iiss", 
            $usuarioId,
            $amigoUsuarioId,
            $estado,
            $fechaSolicitud
        );

        // ejecutamos la consulta
        if (!mysqli_stmt_execute($stmt)) {
            die("Error execute: " . mysqli_stmt_error($stmt)); 
        }

        mysqli_stmt_close($stmt); // Cerrar statement
    }

    // funcion para eliminar un amigo por su id
    public static function eliminarAmigo(int $amigoId) {
        global $conexion;
        $deleteSql = "DELETE from amigos where id_amigo = ?"; // sql para eliminar amigo

        try {
            $stmt = mysqli_prepare($conexion, $deleteSql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
            }

            mysqli_stmt_bind_param($stmt, "i", $amigoId); 

            $resultado = mysqli_stmt_execute($stmt); // ejecutamos la delete
            if (!$resultado) {
                throw new Exception("Error al ejecutar el DELETE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al eliminar amigo: " . $e->getMessage();
        }
    }

    // funcion para modificar datos de un amigo
    public static function modificarAmigo(Amigo $amigo, int $amigoIdOriginal) {
        global $conexion;
        $updateSql = "UPDATE amigos set Estado = ?, FechaAceptacion = ? where id_amigo = ?"; // sql para actualizar

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

            $resultado = mysqli_stmt_execute($stmt); // Ejecutamos update
            if (!$resultado) {
                throw new Exception("Error al ejecutar el UPDATE: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);

        } catch (Exception $e) {
            echo "Error al modificar amigo: " . $e->getMessage(); 
        }
    }

    // funcion para contar la cantidad de amigos
    public static function cuantosAmigos() {
        $amigos = self::recibirRegistros(); // obtener todos los amigos
        return count($amigos); // devolver cantidad
    }

    // funcion para aceptar una solicitud de amistad
    public static function aceptarAmigo(int $idAmigoRegistro) {
        global $conexion;

        $updateSql = "UPDATE amigos SET Estado = ?, FechaAceptacion = ? WHERE id_amigo = ?";
        $stmt = mysqli_prepare($conexion, $updateSql); 
        $estado = Amigo::ESTADO_ACEPTADO; // estado aceptado
        $fechaAceptacion = (new DateTime())->format('Y-m-d H:i:s'); // fecha actual
        mysqli_stmt_bind_param($stmt, "ssi", $estado, $fechaAceptacion, $idAmigoRegistro); 
        mysqli_stmt_execute($stmt); // ejecutamos la update
        mysqli_stmt_close($stmt); 
    }

    // funcion para verificar si dos usuarios son amigos
    public static function sonAmigos(int $id1, int $id2): bool {
        global $conexion;

        $sql = "SELECT * FROM amigos 
                WHERE Estado = 'aceptado'
                AND (
                    (id_usuario = ? AND id_amigo_usuario = ?)
                    OR
                    (id_usuario = ? AND id_amigo_usuario = ?)
                )";

        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "iiii", $id1, $id2, $id2, $id1);
        mysqli_stmt_execute($stmt);

        $res = mysqli_stmt_get_result($stmt); // obtenemos los resultados
        $ok = mysqli_num_rows($res) > 0; //si hay registros son amigos

        mysqli_stmt_close($stmt); 
        return $ok; // devolvemos true o false
    }

}


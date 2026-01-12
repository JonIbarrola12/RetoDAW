<?php
    require_once 'conexion.php';
    class UsuariosCRUD{
        public static function recibirRegistros(){
            global $conexion;
            $selectSql = "SELECT * from usuario";
            try{
                $query = mysqli_query($conexion,$selectSql);
                if (!$query) {
                    throw new Exception("Error en la consulta: " . mysqli_error($conexion));
                }

                $resultados = [];
                while ($fila = mysqli_fetch_assoc($query)) {
                    $resultados[] = $fila;
                }

                return $resultados;
            }catch(Exception $e){
                echo "Error al obtener registros: " . $e->getMessage();
                return [];
            }
        }

        public static function añadirUsuario(Usuario $usuario){
            global $conexion;
            $insertSql = "INSERT into usuario (Nombre,Apellido,Username,Email,Password,Pfp,Bio,FechaRegistro) values (?,?,?,?,?,?,?,?)";
            try {
                $stmt = mysqli_prepare($conexion, $insertSql);
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
                }

                $nombre = $usuario->getNombre();
                $apellido = $usuario->getApellido();
                $Username = $usuario->getUsername();
                $Email = $usuario->getEmail();
                $Password = $usuario->getPassword();
                $Pfp = $usuario->getPfp();
                $Bio = $usuario->getBio();
                $FechaRegistro = $usuario->getFechaRegistro()->format('Y-m-d');

                $Password = password_hash($Password, PASSWORD_DEFAULT);

                mysqli_stmt_bind_param(
                    $stmt,
                    "ssssssss",
                    $nombre,
                    $apellido,
                    $Username,
                    $Email,
                    $Password,
                    $Pfp,
                    $Bio,
                    $FechaRegistro
                );

                $resultado = mysqli_stmt_execute($stmt);

                if (!$resultado) {
                    throw new Exception("Error al ejecutar el INSERT: " . mysqli_stmt_error($stmt));
                }

                mysqli_stmt_close($stmt);

            } catch (Exception $e) {
                echo "Error al añadir usuario: " . $e->getMessage();
            }
        }

        public static function eliminarUsuario(string $Username){
            global $conexion;
            $deleteSql = "DELETE from usuario where Username = ? ";

            try {
                $stmt = mysqli_prepare($conexion, $deleteSql);
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
                }

                mysqli_stmt_bind_param(
                    $stmt,
                    "s",
                    $Username
                );

                $resultado = mysqli_stmt_execute($stmt);

                if (!$resultado) {
                    throw new Exception("Error al ejecutar el DELETE: " . mysqli_stmt_error($stmt));
                }
                mysqli_stmt_close($stmt);

            } catch (Exception $e) {
                echo "Error al eliminar usuario: " . $e->getMessage();
            }
        }

        public static function modificarUsuario(Usuario $Usuario, string $UsernameOriginal){
            global $conexion;

            $modificarSql = "UPDATE usuario SET Nombre = ?, Apellido = ?, Username = ?, Email = ?, Pfp = ?, Bio = ? WHERE Username = ?";

            try {
                $stmt = mysqli_prepare($conexion, $modificarSql);
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
                }

                $nombre = $Usuario->getNombre();
                $apellido = $Usuario->getApellido();
                $nuevoUsername = $Usuario->getUsername();
                $Email = $Usuario->getEmail();
                $Pfp = $Usuario->getPfp();
                $Bio = $Usuario->getBio();

                mysqli_stmt_bind_param(
                    $stmt,
                    "sssssss",
                    $nombre,
                    $apellido,
                    $nuevoUsername,
                    $Email,
                    $Pfp,
                    $Bio,
                    $UsernameOriginal
                );

                $resultado = mysqli_stmt_execute($stmt);

                if (!$resultado) {
                    throw new Exception("Error al ejecutar el UPDATE: " . mysqli_stmt_error($stmt));
                }

                mysqli_stmt_close($stmt);

            } catch (Exception $e) {
                echo "Error al modificar usuario: " . $e->getMessage();
            }
        }
        public static function cuantosUsuarios(){
            $usuarios = self::recibirRegistros();
            return count($usuarios);
        }
        
    }
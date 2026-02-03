<?php
$hostname = "localhost";
$username = "opususer";
$password = "password_seguro";
$database = "opuscord";

$conexion = mysqli_connect($hostname, $username, $password, $database)
    or die("Error conexión BD");
?>


<?php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=opuscord;charset=utf8mb4",
        "opususer",
        "password_seguro",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    error_log($e->getMessage()); // NO mostrar en producción
    die("Error conexión BD");
}

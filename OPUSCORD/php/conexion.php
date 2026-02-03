<?php
    $hostname = "localhost";
    $username = "root";
    $password = "admin";
    $database = "opuscord";
    $conexion = mysqli_connect($hostname,$username,$password,$database)

    or die("Problemas al establecer conexion");
?>

<?php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=opuscord;charset=utf8",
        "root",
        "admin", // o ""
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Error conexión BD: ' . $e->getMessage()
    ]);
    exit;   
}

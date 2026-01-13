<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPUSCORD - Mensajes</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <?php require_once('../conexion.php');?>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>OPUSCORD</h2>
            <!-- Navegación arriba -->
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php"><button>Feed</button></a></li>
                    <li><a href="mensajes.php"><button>Mensajes</button></a></li>
                </ul>
            </nav>
            <!-- Botones de sesión abajo -->
             <?php
                    if (isset($_SESSION['Usuario'])) {
                        echo'
                        <p>Usuario' . htmlspecialchars($_SESSION['Usuario']) .'</p>
                        <a href="../Login/Logout.php"><button class="login-btn">Cerrar Sesión</button></a>
                        ';
                    }else{
                        echo'
                         <div class="auth-buttons">
                            <a href="../Login/Index.php"><button class="login-btn">Iniciar Sesión</button></a>
                            <a href="../Login/registrarse.php"><button class="register-btn">Registrarse</button></a>
                        </div>
                        ';
                    }
           ?>
        </aside>

        <main class="main-content">
            <aside class="conversations">
                <h3>Conversaciones</h3>
                <ul>
                    <!-- Cada conversación redirige a chat.php con query string -->
                    <li><a href="chat.php?user=usuario1">usuario1</a></li>
                    <li><a href="chat.php?user=usuario2">usuario2</a></li>
                    <li><a href="chat.php?user=usuario3">usuario3</a></li>
                </ul>
            </aside>
        </main>
    </div>
</body>
</html>
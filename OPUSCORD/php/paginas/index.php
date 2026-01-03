<!DOCTYPE html>
<?php session_start() ?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPUSCORD - Feed</title>
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
                    <li><a href="index.html"><button>Feed</button></a></li>
                    <li><a href="mensajes.html"><button>Mensajes</button></a></li>
                </ul>
            </nav>

            <!-- Botones de sesión abajo -->
             <?php
                    if (isset($_SESSION['Usuario'])) {
                        echo'
                        <p>Usuario: ' . htmlspecialchars($_SESSION['Usuario']) .'</p>
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
            <section class="feed">
                <div class="post">
                    <div class="post-header">
                        <span class="username">usuario1</span>
                    </div>
                    <img src="https://animalfactguide.com/wp-content/uploads/2025/03/panda-climbing.jpg" alt="Publicación">
                    <p class="caption">Mi primera publicación!</p>
                </div>

                <div class="post">
                    <div class="post-header">
                        <span class="username">usuario2</span>
                    </div>
                    <img src="https://animalfactguide.com/wp-content/uploads/2025/03/panda-climbing.jpg" alt="Publicación">
                    <p class="caption">Disfrutando el día 😎</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
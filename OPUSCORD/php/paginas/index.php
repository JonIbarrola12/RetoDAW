<!DOCTYPE html>
<?php session_start();?>

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
                    <li><a href="index.php"><button>Feed</button></a></li>
                    <li><a href="amigos.php"><button>Amigos</button></a></li>
                    <li><a href="chatprivado.php"><button>Chat</button></a></li>
                    <li><a href="grupos.php"><button>Grupos</button></a></li>

                </ul>
            </nav>
            
            <!-- Botones de sesión abajo -->
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
<?php
session_start();
require_once '../conexion.php';
require_once '../CRUD/AmigosCRUD.php';
require_once '../CRUD/UsuariosCRUD.php';
require_once '../Clases/Amigo.php';
require_once '../Clases/Usuario.php';

// si el usuario no esta logueado lo redirigimos al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Login/Index.php");
    exit;
}

// guardamos el id del usuario logueado
$idUsuario = $_SESSION['id_usuario'];

//obtenemos los datos
$amigos = AmigosCRUD::recibirRegistros();
$usuarios = UsuariosCRUD::recibirRegistros();

// creamos un array de usuarios indexado por ID
$usuariosPorId = [];
foreach ($usuarios as $u) {
    $usuariosPorId[$u['id_usuario']] = $u;
}

// enviar solicitud de amistad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_amigo'])) {
    $idAmigo = (int)$_POST['id_amigo'];

    if ($idAmigo != $idUsuario) {
        // evitar solicitudes duplicadas
        $yaExiste = false;
        $amigosExistentes = AmigosCRUD::recibirRegistros();
        foreach ($amigosExistentes as $a) {
            if (
                ($a['id_usuario'] == $idUsuario && $a['id_amigo_usuario'] == $idAmigo) ||
                ($a['id_usuario'] == $idAmigo && $a['id_amigo_usuario'] == $idUsuario)
            ) {
                $yaExiste = true;
                break;
            }
        }

        if (!$yaExiste) {
            $amigo = new Amigo($idUsuario, $idAmigo, Amigo::ESTADO_PENDIENTE);
            AmigosCRUD::añadirAmigo($amigo);

            $nombreAmigo = '';
            if (isset($usuariosPorId[$idAmigo])) {
                $nombreAmigo = !empty($usuariosPorId[$idAmigo]['Nombre']) 
                    ? $usuariosPorId[$idAmigo]['Nombre'] 
                    : $usuariosPorId[$idAmigo]['Username'];
            } else {
                $nombreAmigo = 'usuario desconocido'; //si no encuentra al usuario
            }
            
            // guardamos mensaje en la sesión
            $_SESSION['mensaje_solicitud'] = "Se ha enviado la solicitud a " . $nombreAmigo;
        }
    }

    header("Location: amigos.php");
    exit;
}

// aceptar solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aceptar_amigo'])) {
    $idAmigoRegistro = (int)$_POST['aceptar_amigo'];
    AmigosCRUD::aceptarAmigo($idAmigoRegistro);

    header("Location: amigos.php");
    exit;
}

// rechazar solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rechazar_amigo'])) {
    $idAmigoRegistro = (int)$_POST['rechazar_amigo'];
    AmigosCRUD::eliminarAmigo($idAmigoRegistro); // elimina la solicitud
    header("Location: amigos.php");
    exit;
}


// eliminar amigo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_amigo'])) {
    $idAmigoRegistro = (int)$_POST['eliminar_amigo'];
    AmigosCRUD::eliminarAmigo($idAmigoRegistro);

    header("Location: amigos.php");
    exit;
}

$busqueda = $_GET['buscar'] ?? '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>OPUSCORD - Amigos</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="../../js/Perfil.js"></script>
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
                    <li><a href="chatprivado.php"><button>Mensajes</button></a></li>
                    <li><a href="grupos.php"><button>Grupos</button></a></li>
                </ul>
            </nav>

            <!-- Botones de sesión abajo -->

            <div class="PerfilContenedor"onclick="abrirPerfil()" style="cursor:pointer;">
            <?php
            if (isset($_SESSION['Usuario'])) {

                $Foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/mamiy.png';


                echo '
                <div class="perfil-horiz">
                    <img src="' . htmlspecialchars($Foto) . '" class="profile-pic fotoPerfil">
                    <div class="perfil-info">
                        <p class="perfil-nombre">' . htmlspecialchars($_SESSION['Usuario']) . '</p>

                    </div>
                </div>
                ';
            } else {
                echo '
                <div class="auth-buttons">
                    <a href="../Login/Index.php"><button class="login-btn">Iniciar Sesión</button></a>
                    <a href="../Login/registrarse.php"><button class="register-btn">Registrarse</button></a>
                </div>
                ';
            }
            ?>
            </div>
    

        </aside>

    <main class="main-content">
        
        <!--mensaje de solicitud enviada -->
        <?php
            if (isset($_SESSION['mensaje_solicitud'])) {
                echo '<div class="mensaje-solicitud">' . htmlspecialchars($_SESSION['mensaje_solicitud']) . '</div>';
                unset($_SESSION['mensaje_solicitud']); // borramos para que no aparezca siempre
            }
        ?>

        <h2>👥 Amigos</h2>

        <!------------buscador-------------->
        <form method="GET" class="search-form">
            <div class="search-wrapper">
                <input type="text" name="buscar" placeholder="Buscar amigos..." value="<?= htmlspecialchars($busqueda) ?>">
            </div>
        </form>


        <?php if ($busqueda): ?>
            <h3>Resultados</h3>
            
            <?php foreach ($usuarios as $u): ?>
                <?php if (
                    stripos($u['Username'], $busqueda) !== false &&
                    $u['id_usuario'] != $idUsuario
                ): ?>
                    <div class="message">
                        <?= htmlspecialchars($u['Username']) ?>
                        <!-- Enviar solicitud -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_amigo" value="<?= $u['id_usuario'] ?>">
                            <button>➕ Añadir</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <hr>

        <br>
        <h3>Solicitudes pendientes</h3>
        <br>

        <?php
            $hayPendientes = false;
            foreach ($amigos as $a):
                if (
                    $a['Estado'] === Amigo::ESTADO_PENDIENTE &&
                    $a['id_amigo_usuario'] == $idUsuario
                ):
                    $hayPendientes = true;
                    $username = $usuariosPorId[$a['id_usuario']]['Username'] ?? 'Usuario desconocido';
            ?>
                <div class="message">
                    <?= htmlspecialchars($username) ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="aceptar_amigo" value="<?= $a['id_amigo'] ?>">
                        <button>✔ Aceptar</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="rechazar_amigo" value="<?= $a['id_amigo'] ?>">
                        <button>✖ Rechazar</button>
                    </form>

                </div>
            <?php
                endif;
            endforeach;

            if (!$hayPendientes) {
                echo "<p>No hay solicitudes.</p>";
            }
        ?>

        <br>
        <hr>
        <br>

        <!---------------lista de amigos------------------>
        <h3>🌟 Amistades</h3>
        <br>

        <?php
        $hayAmigos = false;
        foreach ($amigos as $a):
            if ($a['Estado'] === Amigo::ESTADO_ACEPTADO &&
                ($a['id_usuario'] == $idUsuario || $a['id_amigo_usuario'] == $idUsuario)):

                $hayAmigos = true;
                $idOtro = ($a['id_usuario'] == $idUsuario) ? $a['id_amigo_usuario'] : $a['id_usuario'];
        ?>
            <div class="message">
                <?= htmlspecialchars($usuariosPorId[$idOtro]['Nombre'] ?? $usuariosPorId[$idOtro]['Username'] ?? $idOtro) ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="eliminar_amigo" value="<?= $a['id_amigo'] ?>">
                    <button>❌ Eliminar</button>
                </form>
            </div>
        <?php
            endif;
        endforeach;
        if (!$hayAmigos) echo "<p>No tienes amigos todavía.</p>";
        ?>

    </main>
    <div id="perfilModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarPerfil()">&times;</span>
            <div id="perfilContenido"></div>
        </div>
    </div>


</div>
</div>

</body>
</html>
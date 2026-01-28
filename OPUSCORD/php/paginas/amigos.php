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

        $yaExiste = false;
        foreach ($amigos as $a) {
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

            $_SESSION['mensaje'] = "Solicitud enviada correctamente ";
        } else {
            $_SESSION['mensaje'] = "Ya existe una solicitud";
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
<body class="bodyAmigos">

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

                $Foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png';


                echo '
                <div class="perfil-horiz">
                    <img src="' . htmlspecialchars($Foto) . '" class="profile-pic fotoPerfil foto-mia" id="perfilImagen">
                    <div class="perfil-info">
                        <p class="perfil-nombre nombre-mio">' . htmlspecialchars($_SESSION['Usuario']) . '</p>

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
        
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="mensaje-solicitud" id="mensajeFlash">
                <?= htmlspecialchars($_SESSION['mensaje']) ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

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
                    <div class="message solicitud-item">

                        <div class="perfil-horiz">
                            <img
                                src="<?= htmlspecialchars($u['Pfp'] ?: '../../Recursos/fotousuario.png') ?>"
                                class="profile-pic"
                                alt="Foto de <?= htmlspecialchars($u['Username']) ?>"
                            >

                            <div class="perfil-info">
                                <p class="perfil-nombre">
                                    <?= htmlspecialchars($u['Username']) ?>
                                </p>
                            </div>
                        </div>

                        <div class="acciones-solicitud">
                            <form method="POST">
                                <input type="hidden" name="id_amigo" value="<?= $u['id_usuario'] ?>">
                                <button class="btn-aceptar">Enviar</button>
                            </form>
                        </div>

                    </div>

                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <hr>

        <br>
        <h3>Solicitudes pendientes</h3>
        <br>
        <div id="listaSolicitudes">
            <?php
            $hayPendientes = false;

            foreach ($amigos as $a):
                if (
                    $a['Estado'] === Amigo::ESTADO_PENDIENTE &&
                    $a['id_amigo_usuario'] == $idUsuario
                ):
                    $hayPendientes = true;

                    // Obtener datos del usuario que envió la solicitud
                    $stmt = $pdo->prepare("
                        SELECT id_usuario, Username, Pfp, estado
                        FROM usuarios
                        WHERE id_usuario = ?
                    ");
                    $stmt->execute([$a['id_usuario']]);
                    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$usuario) continue;
            ?>
            <div class="message solicitud">
                
                <div class="perfil-horiz" data-usuario-id="<?= $usuario['id_usuario'] ?>">
                    <img
                        src="<?= htmlspecialchars($usuario['Pfp'] ?: '../../Recursos/fotousuario.png') ?>"
                        class="profile-pic"
                    >

                    <div class="perfil-info">
                        <p class="perfil-nombre">
                            <?= htmlspecialchars($usuario['Username']) ?>
                        </p>

                        <div class="estado-usuario">
                            <span class="estado-dot <?= $usuario['estado'] === 'Online' ? 'online' : 'offline' ?>"></span>
                            <span class="estado-texto">
                                <?= $usuario['estado'] === 'Online' ? 'En línea' : 'Desconectado' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="acciones-solicitud">
                    <form method="POST">
                        <input type="hidden" name="aceptar_amigo" value="<?= $a['id_amigo'] ?>">
                        <button class="btn-aceptar">✔</button>
                    </form>

                    <form method="POST">
                        <input type="hidden" name="rechazar_amigo" value="<?= $a['id_amigo'] ?>">
                        <button class="btn-rechazar">✖</button>
                    </form>
                </div>

            </div>


            <?php
                endif;
            endforeach;

            if (!$hayPendientes) {
                echo "<p>No hay solicitudes.</p>";
            }
            ?>


      
        </div>
        <!---------------lista de amigos------------------>
    <section class="bloque-amigos">
            <h3>🌟 Amistades</h3>
            <br>
        <div class="lista-amigos">
            <?php
            $hayAmigos = false;

            foreach ($amigos as $a) {

                if (
                    $a['Estado'] === Amigo::ESTADO_ACEPTADO &&
                    ($a['id_usuario'] == $idUsuario || $a['id_amigo_usuario'] == $idUsuario)
                ) {

                    $hayAmigos = true;

                    // ID del amigo
                    $idOtro = ($a['id_usuario'] == $idUsuario)
                        ? $a['id_amigo_usuario']
                        : $a['id_usuario'];

                    // Datos del amigo
                    $stmt = $pdo->prepare("
                        SELECT id_usuario, Username, Pfp, estado 
                        FROM usuarios 
                        WHERE id_usuario = ?
                    ");
                    $stmt->execute([$idOtro]);
                    $amigo = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$amigo) {
                        continue;
                    }
                    ?>

                    <div class="message">
                        <div class="perfil-horiz" data-usuario-id="<?= $amigo['id_usuario'] ?>">
                            <img src="<?= htmlspecialchars($amigo['Pfp'] ?: '../../Recursos/fotousuario.png') ?>" class="profile-pic">
                            <div class="perfil-info">
                                <p class="perfil-nombre"><?= htmlspecialchars($amigo['Username']) ?></p>
                                <div class="estado-usuario">
                                    <span class="estado-dot <?= $amigo['estado'] === 'Online' ? 'online' : 'offline' ?>"></span>
                                    <span class="estado-texto"><?= $amigo['estado'] === 'Online' ? 'En línea' : 'Desconectado' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php
                }
            }

            ?>

        </div>
    </section>  
    </main>
    <div id="perfilModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarPerfil()">&times;</span>
            <div id="perfilContenido"></div>
        </div>
    </div>

    <!-- Modal para mostrar perfil de amigos -->
    <div id="perfilAmigoModal" class="perfil-modal" style="display:none;">
        <div id="perfilAmigoContenido" class="perfil-modal-content"></div>
        <span id="cerrarPerfilAmigoModal" class="cerrar-modal">&times;</span>
    </div>


</div>
</div>
<div id="confirmEliminarOverlay" class="confirm-overlay" style="display:none;">
    <div class="confirm-box">
        <p class="confirm-text">¿Deseas eliminar a <?= htmlspecialchars($amigo['Username']) ?>?</p>
        <br>
        <div class="confirm-actions">
            <button class="confirm-accept btn-aceptar" onclick="aceptarConfirmEliminar()">Eliminar</button>
            <button class="confirm-cancel btn-rechazar" onclick="cerrarConfirmEliminar()">Cancelar</button>
        </div>
    </div>
</div>

<script>
//mensaje que desaparece al de 3 segundos 
document.addEventListener("DOMContentLoaded", () => {
    const mensaje = document.getElementById("mensajeFlash");

    if (mensaje) {
        setTimeout(() => {
            mensaje.style.transition = "opacity 0.5s ease";
            mensaje.style.opacity = "0";

            setTimeout(() => mensaje.remove(), 300);
        }, 3000); // 5 segundos
    }
});
</script>

</body>
</html>
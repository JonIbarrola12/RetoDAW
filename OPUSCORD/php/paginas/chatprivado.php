<?php
//inicio sesion 
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Login/Index.php");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../CRUD/UsuariosCRUD.php';
require_once __DIR__ . '/../CRUD/MensajesPrivadosCRUD.php';
require_once __DIR__ . '/../Clases/MensajesPrivados.php';


//obtenemos los usuarios
$usuarios = UsuariosCRUD::recibirRegistros();
$usuariosPorId = [];

foreach ($usuarios as $u) {
    $usuariosPorId[$u['id_usuario']] = $u;
}

//usuarios con los que hablas
$receptorId = $_GET['usuario'] ?? null;

//ajax para cargar en tiempo real
if (isset($_GET['ajax']) && $_GET['ajax'] == 1 && $receptorId) {

    // obtener mensajes enviados y recibidos por el usuario actual
    $mensajes = array_merge(
        MensajesPrivadosCRUD::obtenerMensajesEnviados($idUsuario),
        MensajesPrivadosCRUD::obtenerMensajesRecibidos($idUsuario)
    );

    // ordenarlos por fecha
    usort($mensajes, function($a, $b) {
        return strtotime($a['FechaEnvio']) <=> strtotime($b['FechaEnvio']);
    });

    // mostrar solo los mensajes entre los dos usuarios
    foreach ($mensajes as $msg) {

        $esChat =
            ($msg['id_emisor'] == $idUsuario && $msg['id_receptor'] == $receptorId) ||
            ($msg['id_emisor'] == $receptorId && $msg['id_receptor'] == $idUsuario);

        if (!$esChat) continue;

        $clase = ($msg['id_emisor'] == $idUsuario) ? 'propio' : 'otro';

        // nombre del emisor
        $nombre = $usuariosPorId[$msg['id_emisor']]['Username'] ?? 'Usuario';

        echo "<div class='message $clase'>";
        echo "<strong>" . htmlspecialchars($nombre) . ":</strong> ";
        echo nl2br(htmlspecialchars($msg['Contenido']));
        echo "</div>";

        // marcar como leído si lo recibe el usuario actual
        if ($msg['id_receptor'] == $idUsuario && !$msg['Leido']) {
            MensajesPrivadosCRUD::marcarComoLeido($msg['id_mensaje']);
        }
    }
    exit;
}

//enviar mensaje mediante post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_mensaje']) && $receptorId) {

    $contenido = trim($_POST['mensaje'] ?? '');

    if ($contenido !== '') {
        try {
            $mensaje = new MensajesPrivados($idUsuario, $receptorId, $contenido);
            MensajesPrivadosCRUD::añadirMensaje($mensaje);
        } catch (Exception $e) {
            $_SESSION['mensaje'] = $e->getMessage();
        }
    }

    // Respuesta AJAX
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo "ok";
        exit;
    }

    header("Location: chatprivado.php?usuario=$receptorId");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>OPUSCORD - Chat Privado</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="../../js/Perfil.js"></script>

</head>
<body>

<div class="container">

    <!-- sidebar -->
        <aside class="sidebar">
            <h2>OPUSCORD</h2>
            <!-- Navegación arriba -->
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php"><button>Feed</button></a></li>
                    <li><a href="amigos.php"><button>Amigos</button></a></li>
                    <li><a href="chatprivado.php"><button>Mensajes</button></a></li>
                    <li><a href="grupos.php"><button>Grupos</button></a></li>
                    <li><a href="galerias.php?id=<?= $_SESSION['id_usuario'] ?>"><button>Galería</button></a></li>
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

        <?php if (!$receptorId): ?>
            <h3>Selecciona un usuario para chatear</h3>
        <?php else: ?>

            <h3>
                Chat con <?= htmlspecialchars($usuariosPorId[$receptorId]['Username'] ?? 'Usuario') ?>
            </h3>

            <div class="chat-messages"></div>

            <form id="chat-form" method="POST" class="chat-input">
                <input type="text" name="mensaje" id="mensaje-input"
                       placeholder="Escribe un mensaje..." autocomplete="off">
                <button name="enviar_mensaje">Enviar</button>
            </form>

            <script>
            const chatMessages = document.querySelector('.chat-messages');
            const chatForm = document.getElementById('chat-form');
            const input = document.getElementById('mensaje-input');

            // cargar mensajes por ajax
            function cargarMensajes() {
                fetch('chatprivado.php?usuario=<?= $receptorId ?>&ajax=1') // <-- CORREGIDO
                    .then(res => res.text())
                    .then(html => {
                        chatMessages.innerHTML = html;
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    });
            }

            // enviar mensaje por ajax
            chatForm.addEventListener('submit', e => {
                e.preventDefault();

                const data = new FormData(chatForm);
                data.append('enviar_mensaje', '1');

                fetch('chatprivado.php?usuario=<?= $receptorId ?>', { // <-- CORREGIDO
                    method: 'POST',
                    body: data,
                    headers: {'X-Requested-With':'XMLHttpRequest'}
                })
                .then(() => {
                    input.value = '';
                    cargarMensajes();
                });
            });

            // actualizar cada 3 segundos
            setInterval(cargarMensajes, 3000);
            cargarMensajes();
            </script>

        <?php endif; ?>

    </main>
<aside class="sidebar sidebar-left">

    <h3>Chats</h3>
    <?php
    $stmt = $pdo->prepare("
        SELECT u.id_usuario, u.Username, u.Pfp, u.estado
        FROM amigos a
        JOIN usuarios u 
            ON (u.id_usuario = a.id_usuario OR u.id_usuario = a.id_amigo_usuario)
        WHERE a.Estado = 'aceptado'
        AND u.id_usuario != ?
    ");
    $stmt->execute([$idUsuario]);
    $amigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="lista-chats">
        <?php if (empty($amigos)): ?>
            <p class="sin-amigos">No tienes Amigos</p>
        <?php else: ?>
            <?php foreach ($amigos as $amigo): ?>
                <a href="chatprivado.php?usuario=<?= $amigo['id_usuario'] ?>" class="chat-amigo">
                    
                    <div class="perfil-horiz" data-usuario-id="<?= $amigo['id_usuario'] ?>">
                        
                        <img 
                            src="<?= htmlspecialchars($amigo['Pfp'] ?: '../../Recursos/fotousuario.png') ?>"
                            class="profile-pic"
                            alt="Foto de <?= htmlspecialchars($amigo['Username']) ?>"
                        >

                        <div class="perfil-info">
                            <p class="perfil-nombre">
                                <?= htmlspecialchars($amigo['Username']) ?>
                            </p>

                            <div class="estado-usuario">
                                <span class="estado-dot <?= $amigo['estado'] === 'Online' ? 'online' : 'offline' ?>"></span>
                                <span class="estado-texto">
                                    <?= $amigo['estado'] === 'Online' ? 'En línea' : 'Desconectado' ?>
                                </span>
                            </div>
                        </div>

                    </div>

                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</aside>

</div>
    <div id="perfilModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarPerfil()">&times;</span>
            <div id="perfilContenido"></div>
        </div>
    </div>
    <div id="alertaCustom" class="alerta-custom"></div>

</body>
</html>

<?php
session_start();
require_once '../conexion.php';
require_once '../CRUD/GruposCRUD.php';
require_once '../CRUD/MiembrosCRUD.php';
require_once '../CRUD/MensajesGruposCRUD.php';
require_once '../CRUD/UsuariosCRUD.php';
require_once '../Clases/Grupo.php';
require_once '../Clases/Miembro.php';
require_once '../Clases/MensajesGrupos.php';

// seguridad sesion
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Login/Index.php");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];

// grupos del usuario
$miembros = MiembrosCRUD::obtenerGruposUsuario($idUsuario);

// usuarios para mostrar nombres en el chat
$usuarios = UsuariosCRUD::recibirRegistros();
$usuariosPorId = [];
foreach ($usuarios as $u) {
    $usuariosPorId[$u['id_usuario']] = $u;
}

// grupo activo
$grupoActivoId = $_GET['grupo'] ?? null;
$grupoActivo = null;
$mensajes = [];

if ($grupoActivoId) {
    $grupoActivo = GruposCRUD::obtenerPorId($grupoActivoId);
    $mensajes = MensajesGruposCRUD::obtenerMensajesPorGrupo($grupoActivoId);
}

// respuesta AJAX para actualizar mensajes
if(isset($_GET['ajax']) && $_GET['ajax'] == 1 && $grupoActivoId){
    foreach ($mensajes as $msg):

        $miembroEmisor = MiembrosCRUD::obtenerPorId($msg['id_emisor']);
        $emisorIdUsuario = $miembroEmisor['id_usuario'] ?? null;
        $emisor = $usuariosPorId[$emisorIdUsuario]['Username'] ?? 'Desconocido';
        $clase = ($emisorIdUsuario == $idUsuario) ? 'propio' : 'otro';

        // procesar enlaces de imagenes y PDFs
        $contenido = $msg['Contenido'];

        // Convertir SOLO enlaces antiguos a imágenes
        $contenido = preg_replace(
            '/<a[^>]+href=[\'"]([^\'"]+\.(?:jpg|jpeg|png|gif|webp))[\'"][^>]*>📎[^<]+<\/a>/i',
            '<img src="$1" style="max-width:200px;max-height:200px;border-radius:5px;margin:2px;cursor:pointer;" onclick="abrirImagen(\'$1\')">',
            $contenido
        );




        $foto = $usuariosPorId[$emisorIdUsuario]['Pfp']
    ?? '../../Recursos/fotousuario.png';

        echo "
        <div class='mensaje-discord $clase'>
            <img 
                src='".htmlspecialchars($foto)."'
                class='mensaje-avatar'
                data-usuario-id='{$emisorIdUsuario}'
            >

            <div class='mensaje-contenido'>
                <div class='mensaje-header'>
                    <span 
                        class='mensaje-nombre nombre-usuario-click'
                        data-usuario-id='{$emisorIdUsuario}'
                    >
                        ".htmlspecialchars($emisor)."
                    </span>

                    <span class='mensaje-hora'>
                        ".date('H:i', strtotime($msg['FechaEnvio']))."
                    </span>
                </div>

                <div class='mensaje-texto'>
                    $contenido
                </div>
            </div>
        </div>
        ";

    endforeach;
    exit;
}

// crear grupo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_grupo'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    if ($nombre !== '') {
        $grupo = new Grupo($nombre, $descripcion, $idUsuario);
        $idGrupoNuevo = GruposCRUD::añadirGrupo($grupo);

        $miembro = new Miembro($idUsuario, $idGrupoNuevo, 'admin');
        MiembrosCRUD::añadirMiembro($miembro);

        header("Location: grupos.php?grupo=$idGrupoNuevo");
        exit;
    }
}


// eliminar grupo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_grupo']) && $grupoActivoId) {
    if (MiembrosCRUD::esAdmin($idUsuario, $grupoActivoId)) {
        GruposCRUD::eliminarGrupo($grupoActivoId);
        $_SESSION['mensaje'] = "Grupo eliminado correctamente";
        header("Location: grupos.php");
        exit;
    }
}

// enviar mensajes o archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_mensaje']) && $grupoActivoId) {
    $contenido = trim($_POST['mensaje'] ?? '');
    $contenidoSeguro = htmlspecialchars($contenido, ENT_QUOTES, 'UTF-8');
    $archivoSubido = null;

    // manejar archivo
    if (!empty($_FILES['archivo']['name']) && $_FILES['archivo']['error'] === 0) {

        $tipoArchivo = mime_content_type($_FILES['archivo']['tmp_name']);
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];

        if (in_array($tipoArchivo, $tiposPermitidos)) {

            $uploadsDir = '../../uploads/';
            if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);

            $extension = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);

            // Crear nombre único y seguro
            $nombreArchivo = uniqid('file_', true) . '.' . strtolower($extension);

            $destino = $uploadsDir . $nombreArchivo;

            if (move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) {
                $archivoSubido = $nombreArchivo;
            }else {
                $_SESSION['mensaje'] = "Error al subir el archivo";
            }
        } else {
            $_SESSION['mensaje'] = "Tipo de archivo no permitido. Solo imágenes y PDFs.";
        }
    }

    if ($contenidoSeguro !== '' || $archivoSubido) {
        
        $miembro = MiembrosCRUD::obtenerPorUsuarioYGrupo($idUsuario, $grupoActivoId);
        if ($miembro) {
            $idMiembro = $miembro['id_miembro'];
            $textoFinal = $contenidoSeguro;
            if ($archivoSubido) {
                $ruta = "../../uploads/" . urlencode($archivoSubido);

                if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $archivoSubido)) {
                    // Si es imagen, guardarla directamente como <img>
                    $textoFinal .= "<br><img src='$ruta' class='imagen-chat' style='max-width:200px;max-height:200px;border-radius:5px;cursor:pointer;' onclick=\"abrirImagen('$ruta')\">";
                } else {
                    // Si es PDF u otro archivo
                    $textoFinal .= "<br><a class='archivo-adjunto' href='$ruta' target='_blank'>📎 $archivoSubido</a>";
                }
            }
            $mensaje = new MensajesGrupos($idMiembro, $grupoActivoId, $textoFinal);
            MensajesGruposCRUD::añadirMensaje($mensaje);
        } else {
            $_SESSION['mensaje'] = "No eres miembro de este grupo";
        }
    }

    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){
        echo "ok";
        exit;
    }

    header("Location: grupos.php?grupo=$grupoActivoId");
    exit;
}

// invitar usuarios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invitar']) && $grupoActivoId) {
    $username = trim($_POST['usuario_invitar']);
    $usuarioInvitado = UsuariosCRUD::obtenerPorUsername($username);

    if ($usuarioInvitado) {
        if (!MiembrosCRUD::existeMiembro($usuarioInvitado['id_usuario'], $grupoActivoId)) {

            $miembro = new Miembro($usuarioInvitado['id_usuario'], $grupoActivoId, 'miembro');
            MiembrosCRUD::añadirMiembro($miembro);
            $_SESSION['mensaje'] = "Usuario invitado correctamente";
        } else {
            $_SESSION['mensaje'] = "Ese usuario ya está en el grupo";
        }
    } else {
        $_SESSION['mensaje'] = "Usuario no encontrado";
    }

    header("Location: grupos.php?grupo=$grupoActivoId");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>OPUSCORD - Grupos</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="../../js/Perfil.js"></script>
    <style>
        .chat-messages { max-height: 500px; overflow-y: auto; }
        .message.prop { background-color: #dcf8c6; padding:5px; margin:5px 0; border-radius:5px; }
        .message.otro { background-color: #949494; padding:5px; margin:5px 0; border-radius:5px; }
        .chat-input { display:flex; gap:5px; margin-top:10px; }
    </style>
    <link rel="icon" href="../../Recursos/OpusLogo.png" type="image/png">

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
                    <li><a href="grupos.php" class="activo"><button>Grupos</button></a></li>
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
    <!-- chat -->
    <main class="main-content chatcontent">
        
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="mensaje-solicitud" id="mensajeFlash">
                <?= $_SESSION['mensaje'] ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <?php if ($grupoActivo): ?>
            <div 
                class="perfil-horiz grupo-header"
                data-grupo-id="<?= $grupoActivo['id_grupo'] ?>"
                style="cursor:pointer;"
            >

                <img
                    src="<?= htmlspecialchars($grupoActivo['Pfp'] ?: '/Recursos/fotogrupo.png') ?>"
                    class="profile-pic grupo-foto-header"
                    data-grupo-id="<?= $grupoActivo['id_grupo'] ?>"
                >
                <div class="perfil-info" data-grupo-id="<?= $idGrupo ?>">
                    <p
                        class="perfil-nombre grupo-nombre-dinamico"
                        id="perfilGrupoNombre"
                        data-grupo-id="<?= $grupoActivo['id_grupo'] ?>"
                    >
                        <?= htmlspecialchars($grupoActivo['Nombre']) ?>
                    </p>

                </div>

            </div>




            <div class="chat-messages">
                <?php foreach ($mensajes as $msg):
                    $miembroEmisor = MiembrosCRUD::obtenerPorId($msg['id_emisor']);
                    $emisorIdUsuario = $miembroEmisor['id_usuario'] ?? null;
                    $emisor = $usuariosPorId[$emisorIdUsuario]['Username'] ?? 'Desconocido';
                    $clase = ($emisorIdUsuario == $idUsuario) ? 'propio' : 'otro';

                    $contenido = $msg['Contenido'];
                    $foto = $usuariosPorId[$emisorIdUsuario]['Pfp']
    ?? '../../Recursos/fotousuario.png';

echo "
<div class='mensaje-discord $clase'>
    <img 
        src='".htmlspecialchars($foto)."'
        class='mensaje-avatar'
        data-usuario-id='{$emisorIdUsuario}'
    >

    <div class='mensaje-contenido'>
        <div class='mensaje-header'>
            <span 
                class='mensaje-nombre nombre-usuario-click'
                data-usuario-id='{$emisorIdUsuario}'
            >
                ".htmlspecialchars($emisor)."
            </span>

            <span class='mensaje-hora'>
                ".date('H:i', strtotime($msg['FechaEnvio']))."
            </span>
        </div>

        <div class='mensaje-texto'>
            $contenido
        </div>
    </div>
</div>
";

                endforeach; ?>
            </div>

            <form id="chat-form" method="POST" class="chat-input" enctype="multipart/form-data">
                <button type="button" id="file-upload-btn">+</button>
                <input type="file" name="archivo" id="file-upload" style="display:none;" accept="image/*,application/pdf">
                <input type="text" name="mensaje" id="mensaje-input" placeholder="Escribe tu mensaje..." autocomplete="off">
                <button type="submit" name="enviar_mensaje">Enviar</button>
            </form>

            <script>
            const fileInput = document.getElementById('file-upload');
            const chatForm = document.getElementById('chat-form');
            const chatMessages = document.querySelector('.chat-messages');

            // abrir selector de archivos
            document.getElementById('file-upload-btn').addEventListener('click', e => { e.preventDefault(); fileInput.click(); });

            // actualizar mensajes AJAX
            function actualizarMensajes(forzarScroll = false) {
                const estabaAbajo =
                    chatMessages.scrollTop + chatMessages.clientHeight >=
                    chatMessages.scrollHeight - 50;

                fetch('grupos.php?grupo=<?= $grupoActivoId ?>&ajax=1')
                    .then(res => res.text())
                    .then(data => {
                        chatMessages.innerHTML = data;

                        // Bajar si:
                        // - el usuario ya estaba abajo
                        // - o se forzó el scroll (al enviar mensaje)
                        if (estabaAbajo || forzarScroll) {
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    })
                    .catch(err => console.error(err));
            }



            // enviar archivo automáticamente
            fileInput.addEventListener('change', function() {
                if(this.files.length > 0){
                    const formData = new FormData(chatForm);
                    formData.append('enviar_mensaje','1');
                    fetch('grupos.php?grupo=<?= $grupoActivoId ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {'X-Requested-With':'XMLHttpRequest'}
                    }).then(res => res.text()).then(() => {
                        fileInput.value = '';
                        actualizarMensajes();
                    }).catch(err => console.error(err));
                }
            });

            // enviar mensaje de texto
            chatForm.addEventListener('submit', e => {
                e.preventDefault();

                const formData = new FormData(chatForm);
                formData.append('enviar_mensaje','1');

                fetch('grupos.php?grupo=<?= $grupoActivoId ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {'X-Requested-With':'XMLHttpRequest'}
                })
                .then(res => res.text())
                .then(() => {
                    document.getElementById('mensaje-input').value = '';

                    // Aquí FORZAMOS ir abajo porque el usuario envió mensaje
                    actualizarMensajes(true);
                })
                .catch(err => console.error(err));
            });


            // actualizar mensajes cada 5 segundos
            setInterval(actualizarMensajes, 5000);

            // al cargar la página, hacer scroll al final
            window.addEventListener('load', () => {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
            </script>

        <?php else: ?>
            <h3>Selecciona o crea un grupo</h3>
        <?php endif; ?>
    </main>
    <aside class="sidebar sidebar-left">

        <h3>Crear grupo</h3>
        <form method="POST" class="crear-grupo">
            <input type="text" name="nombre" placeholder="Nombre del grupo" required>
            <input type="text" name="descripcion" placeholder="Descripción">
            <button name="crear_grupo">Crear</button>
        </form>

        <hr>

        <h3>Mis Grupos</h3>

        <div class="grupos-scroll ">
            <ul>
                <?php foreach ($miembros as $m):
                    $g = GruposCRUD::obtenerPorId($m['GrupoId']);
                ?>
                <li class="grupo-item" data-grupo-id="<?= $g['id_grupo'] ?>">
                    <a href="grupos.php?grupo=<?= $g['id_grupo'] ?>" class="grupo-link">
                        <img data-grupo-id="<?= $g['id_grupo'] ?>"
                            src="<?= htmlspecialchars($g['Pfp'] ?: '/Recursos/fotogrupo.png') ?>" 
                            class="grupo-foto-sidebar" 
                        >
                        <span class="grupo-nombre">
                            <?= htmlspecialchars($g['Nombre']) ?>
                        </span>
                    </a>
                </li>

                <?php endforeach; ?>
            </ul>
        </div>

    </aside>

</div>
    <div id="perfilModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarPerfil()">&times;</span>
            <div id="perfilContenido"></div>
        </div>
    </div>
    
<div id="modalPerfilGrupo" class="modalgrupo hidden">
    <div class="modalgrupo-contenido">
        <button class="cerrar-modalgrupo">✖</button>
        <div id="modalGrupoContenido"></div>
    </div>
</div>
<div id="alertaCustom" class="alerta-custom"></div>

    <div id="perfilAmigoModal" class="perfil-modal" style="display:none;">
        <div id="perfilAmigoContenido" class="perfil-modal-content"></div>
        <span id="cerrarPerfilAmigoModal" class="cerrar-modal">&times;</span>
    </div>

<!-- Modal para ver imagen en chats -->
<div id="imagenModal" style="
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.8);
    justify-content:center;
    align-items:center;
    z-index:9999;
">
    <img id="imagenModalContenido" src="" style="
        max-width:90%;
        max-height:90%;
        border-radius:10px;
        cursor:pointer;
    ">
</div>

<script>
function abrirImagen(src) {
    const modal = document.getElementById('imagenModal');
    const img = document.getElementById('imagenModalContenido');
    img.src = src;
    modal.style.display = 'flex';
}

const modal = document.getElementById('imagenModal');

modal.onclick = function(e) {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
};

document.getElementById('imagenModalContenido').onclick = () => {
    modal.style.display = 'none';
};
</script>

</body>
</html>



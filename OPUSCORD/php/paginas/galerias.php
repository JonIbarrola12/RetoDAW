<?php
session_start();
require_once '../conexion.php';
require_once '../CRUD/PublicacionesCRUD.php';
require_once '../CRUD/UsuariosCRUD.php';
require_once '../clases/Publicacion.php';


// comprobar login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login/index.php");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];

// Traer todos los usuarios para el buscador
$usuarios = UsuariosCRUD::recibirRegistros();



// Manejar seguir/dejar de seguir vía AJAX
if (isset($_POST['ajax']) && $_POST['ajax'] === 'seguirUsuario') {
    $idSeguido = (int)($_POST['id_usuario'] ?? 0);
    $seguir = $_POST['seguir'] == 1; // 1 = seguir, 0 = dejar de seguir

    if ($idSeguido > 0 && $idSeguido !== $_SESSION['id_usuario']) {
        UsuariosCRUD::seguirUsuario($_SESSION['id_usuario'], $idSeguido, $seguir);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit; // importante para que no cargue el HTML normal
}

// Obtener seguidos vía AJAX
if(isset($_GET['ajax']) && $_GET['ajax'] === 'obtenerSeguidos'){
    header('Content-Type: application/json');
    $id = (int)($_GET['id_usuario'] ?? 0);
    if($id > 0){
        $seguidos = UsuariosCRUD::obtenerSeguidos($id); // Debes implementarlo en tu CRUD
        echo json_encode($seguidos);
    } else {
        echo json_encode([]);
    }
    exit;
}

// Buscador de usuarios vía AJAX
if (isset($_GET['ajax']) && $_GET['ajax'] === 'buscarUsuarios') {
    header('Content-Type: application/json');

    $q = trim($_GET['q'] ?? '');
    $resultados = [];

    if ($q !== '') {
        // Obtener todos los usuarios
        $usuarios = UsuariosCRUD::recibirRegistros();

        foreach ($usuarios as $u) {
            // Buscar en cualquier parte del username (case-insensitive)
            if (stripos($u['Username'], $q) !== false) {
                $resultados[] = [
                    'id_usuario' => $u['id_usuario'],
                    'Username' => $u['Username']
                ];
            }
        }
    }

    echo json_encode($resultados);
    exit; // importante para que no cargue el HTML normal
}



// Determinar qué galería ver
$idVer = isset($_GET['id']) ? (int)$_GET['id'] : $idUsuario;
$esPropio = $idVer === $idUsuario;

// obtener usuario que vemos
$usuario = UsuariosCRUD::obtenerPorId($idVer);
$fotoPerfil = $usuario['Pfp'] ?? '/Recursos/fotousuario.png';
$nombreUsuario = $usuario['Username'] ?? 'Usuario';
$numeroSeguidores = UsuariosCRUD::contarSeguidores($idVer);


// Determinar qué galería ver
$idVer = isset($_GET['id']) ? (int)$_GET['id'] : $idUsuario;
$esPropio = $idVer === $idUsuario;

// obtener usuario que vemos
$usuario = UsuariosCRUD::obtenerPorId($idVer);
$fotoPerfil = $usuario['Pfp'] ?? '/Recursos/fotousuario.png';
$nombreUsuario = $usuario['Username'] ?? 'Usuario';




// saber si ya seguimos al usuario (solo si no es propio)
$sigo = false;
if (!$esPropio) {
    $seguidos = UsuariosCRUD::obtenerSeguidos($idUsuario);
    $idsSeguidos = array_column($seguidos, 'id_usuario'); // extrae solo los ids
    $sigo = in_array($idVer, $idsSeguidos);
}



// Subir foto (solo propio)
if ($esPropio && isset($_POST['subir_foto'])) {
    $imagenUrl = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $rutaDestino = '../../uploads/' . basename($_FILES['imagen']['name']);
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $imagenUrl = 'uploads/' . basename($_FILES['imagen']['name']);
        }
    }

    if ($imagenUrl) {
        $publicacion = new Publicacion($idUsuario, '', $imagenUrl, null, 'publica');
        PublicacionesCRUD::añadirPublicacion($publicacion);
        header("Location: galerias.php?id=$idUsuario");
        exit;
    }
}

// obtener publicaciones de usuario visto
$publicaciones = array_filter(
    PublicacionesCRUD::recibirRegistros(),
    fn($p) => $p['id_usuario'] == $idVer
);

$numeroFotos = count($publicaciones);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Galería de <?= htmlspecialchars($nombreUsuario) ?></title>
<script src="../../js/Perfil.js"></script>
<link rel="stylesheet" href="../../css/estilos.css">

<link rel="stylesheet" href="../../css/galerias.css">

</head>
<body>

<div class="container">

    <!-- Sidebar -->
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
        

    <!-- Contenido principal -->
    <main class="main-content">

        <!-- Buscador -->
        <div class="buscador-galeria">
            <input type="text" id="buscador" placeholder="Buscar galería...">
            <div id="resultados-buscador"></div>
        </div>

        <!-- Header galería -->
        <div class="perfil-header">
            <img src="<?= htmlspecialchars($fotoPerfil) ?>" class="foto-perfil header-foto profile-pic fotoPerfil foto-mia">


            <div class="perfil-info">
                <div class="fila-superior">
                    <p class="nombre-usuario"><?= htmlspecialchars($nombreUsuario) ?></p>
                    <div class="botones-header">
                    <?php if (!$esPropio): ?>
                        <button id="btn-seguir" class="btn-seguir" data-sigo="<?= $sigo ? 1 : 0 ?>">
                            <?= $sigo ? 'Siguiendo' : 'Seguir' ?>
                        </button>
                    <?php endif; ?>

                </div>

                </div>

                <div class="contadores-perfil">
                    <span><?= $numeroSeguidores ?> Seguidores</span>
                    <span>·</span>
                    <span><?= $numeroFotos ?> Fotos</span>
                    <?php if ($esPropio): ?>
                        <button id="btn-seguidos" class="btn-seguidos">Seguidos</button>
                        <div id="lista-seguidos" class="lista-seguidos" style="display: none;"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>


        <?php if ($esPropio): ?>
        <div class="subir-foto-container">
            <form method="POST" enctype="multipart/form-data">
                        <div class="upload-imagen">
            <label for="imagen" class="btn-imagen">+</label>
            <span id="nombre-archivo">Ningún archivo</span>
                <button type="submit" name="subir_foto" class="boton-subir">Subir</button>


            <input type="file" id="imagen" name="imagen" accept="image/*" hidden>
        </div>
            <script>
            document.getElementById('imagen').addEventListener('change', function () {
                const nombre = this.files.length > 0
                    ? this.files[0].name
                    : 'Ningún archivo';

                document.getElementById('nombre-archivo').textContent = nombre;
            });
            </script>
        </form>
            
        </div>

        <?php endif; ?>


        


        <!-- Galería -->
        <div class="galeria-container">
            <?php if (count($publicaciones) > 0): ?>
            <div class="galeria-grid">
                <?php foreach ($publicaciones as $p): ?>
                    <div class="foto-item">
                        <img src="../../<?= htmlspecialchars($p['ImagenUrl']) ?>" onclick="abrirModal(this.src)">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p class="no-publicaciones">Aún no hay fotos.</p>
            <?php endif; ?>
        </div>

    </main>
</div>

<!-- Modal imagen -->
<div id="modalImagen" class="modal-imagen">
    <img id="modalImagenContenido" src="">
</div>

<script>
// Modal para ver imagen
function abrirModal(src){
    const modal = document.getElementById('modalImagen');
    const img = document.getElementById('modalImagenContenido');
    img.src = src;
    modal.style.display = 'flex';
}
document.getElementById('modalImagen').onclick = function(e){
    if(e.target === this){ this.style.display = 'none'; }
}

// Seguidores
const btnSeguir = document.getElementById('btn-seguir');
if(btnSeguir){
    btnSeguir.onclick = () => {
        const sigo = btnSeguir.dataset.sigo == 1;
        fetch('galerias.php', {
            method: 'POST',
            body: new URLSearchParams({ 
                ajax: 'seguirUsuario', 
                id_usuario: <?= $idVer ?>, 
                seguir: sigo ? 0 : 1 
            })
        })  .then(()=> {
            btnSeguir.textContent = sigo ? 'Seguir' : 'Siguiendo';
            btnSeguir.dataset.sigo = sigo ? 0 : 1;
        });
    };
}



const buscador = document.getElementById('buscador');
const resultadosDiv = document.getElementById('resultados-buscador');

buscador.addEventListener('input', () => {
    const q = buscador.value.trim();
    resultadosDiv.innerHTML = '';

    if (q === '') return;

    fetch(`galerias.php?ajax=buscarUsuarios&q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {

                return;
            }

            data.forEach(u => {
                const div = document.createElement('div');
                div.className = 'resultado-usuario';
                div.textContent = u.Username;
                div.onclick = () => {
                    buscador.value = u.Username;
                    resultadosDiv.innerHTML = '';
                    // Redirigir a la galería del usuario seleccionado
                    window.location.href = `galerias.php?id=${u.id_usuario}`;
                };
                resultadosDiv.appendChild(div);
            });
        })
        .catch(err => {
            console.error('Error buscando usuarios:', err);
        });
});

const btnSeguidos = document.getElementById('btn-seguidos');
const listaSeguidos = document.getElementById('lista-seguidos');

if(btnSeguidos){
    btnSeguidos.onclick = () => {
        if(listaSeguidos.style.display === 'none'){
            // Abrir la lista
            fetch(`galerias.php?ajax=obtenerSeguidos&id_usuario=<?= $idUsuario ?>`)
                .then(res => res.json())
                .then(data => {
                    listaSeguidos.innerHTML = '';
                    if(data.length === 0){
                        listaSeguidos.innerHTML = '<div>No sigue a nadie</div>';
                    } else {
                        data.forEach(u => {
                            const div = document.createElement('div');
                            div.textContent = u.Username;
                            div.onclick = () => {
                                window.location.href = `galerias.php?id=${u.id_usuario}`;
                            };
                            listaSeguidos.appendChild(div);
                        });
                    }
                    listaSeguidos.style.display = 'block';
                });
        } else {
            listaSeguidos.style.display = 'none';
        }
    };
}


</script>
<div id="alertaCustom" class="alerta-custom"></div>

</body>
</html>


    <div id="perfilModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarPerfil()">&times;</span>
            <div id="perfilContenido"></div>
        </div>
    </div>
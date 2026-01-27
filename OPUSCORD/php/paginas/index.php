<?php

// iniciar sesion
session_start();

// incluimos los archivos necesarios
require_once '../conexion.php';
require_once '../CRUD/PublicacionesCRUD.php';
require_once '../CRUD/UsuariosCRUD.php';
require_once '../CRUD/ComentariosCRUD.php';
require_once '../CRUD/LikeCRUD.php';
require_once '../CRUD/AmigosCRUD.php';
require_once '../clases/Comentario.php';
require_once '../clases/Like.php';
require_once '../clases/Publicacion.php';

// comprobamos si el usuario esta logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login/index.php");
    exit;
}

// obtenemos el id del usuario logueado
$idUsuario = $_SESSION['id_usuario'];

$orden = $_GET['orden'] ?? 'reciente';

/* usuarios */

// obtenemos todos los usuarios
$usuarios = UsuariosCRUD::recibirRegistros();

// creamos array del id_usuario con el username
$usuariosPorId = [];
foreach ($usuarios as $u) {
    $usuariosPorId[$u['id_usuario']] = $u['Username'];
}

/* crear publicacion */

// comprobamos si se envio el formulario
if (isset($_POST['crear_publicacion'])) {

    // obtenemos el contenido y limpiamos el html
    $contenido = htmlspecialchars($_POST['contenido']);

    // visibilidad de la publicacion por defecto en publica
    $visibilidad = $_POST['visibilidad'] ?? 'publica';

    // variable para la imagen
    $imagenUrl = null;

    // comprobamos si se subio una imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {

        // ruta destino de la imagen
        $rutaDestino = '../../uploads/' . basename($_FILES['imagen']['name']);

        // movemos la imagen al servidor
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $imagenUrl = 'uploads/' . basename($_FILES['imagen']['name']);
        }
    }

    // creamos objeto publicacion
    $nuevaPublicacion = new Publicacion($idUsuario, $contenido, $imagenUrl, null, $visibilidad);

    // guardamos la publicacion en la base de datos
    PublicacionesCRUD::añadirPublicacion($nuevaPublicacion);

    // recargar pagina
    header("Location: index.php");
    exit;
}

/* filtrar publicaciones  */

// obtener el texto del buscador
$busquedaUsuario = $_GET['buscar_usuario'] ?? '';

// array final de publicaciones visibles
$publicaciones = [];

// recorremos todas las publicaciones
foreach (PublicacionesCRUD::recibirRegistros() as $p) {

    // obtenemos el nombre del usuario de la publicacion
    $nombreUsuario = $usuariosPorId[$p['id_usuario']] ?? '';

    // comprobamos la visibilidad y filtro de busqueda
    if (
        (
            $p['Visibilidad'] === 'publica' ||
            $p['id_usuario'] == $idUsuario ||
            (
                $p['Visibilidad'] === 'privada' &&
                AmigosCRUD::sonAmigos($idUsuario, $p['id_usuario'])
            )
        )
        &&
        (
            !$busquedaUsuario ||
            stripos($nombreUsuario, $busquedaUsuario) !== false
        )
    ) {
        $publicaciones[] = $p;
    }
}

/* ordenar publicaciones */

if ($orden === 'likes') {
    usort($publicaciones, function ($a, $b) {
        $likesA = LikesCRUD::contarLikes($a['id_publicacion']);
        $likesB = LikesCRUD::contarLikes($b['id_publicacion']);
        return $likesB <=> $likesA;
    });
} else {
    // más recientes primero
    usort($publicaciones, function ($a, $b) {
        return $b['id_publicacion'] <=> $a['id_publicacion'];
    });
}


/* ajax */

// comprobamos si es una peticion ajax
if (isset($_GET['ajax'])) {

    // devolvemos la respuesta json
    header('Content-Type: application/json');

    switch ($_GET['ajax']) {

        /*  eliminar publicacion  */
        case 'eliminarPublicacion':

            $idPub = (int)($_POST['id_publicacion'] ?? 0);

            // comprobar que la publicacion sea del usuario
            foreach (PublicacionesCRUD::recibirRegistros() as $p) {
                if ($p['id_publicacion'] == $idPub && $p['id_usuario'] == $idUsuario) {
                    PublicacionesCRUD::eliminarPublicacion($idPub);
                    echo json_encode(['status' => 'ok']);
                    exit;
                }
            }

            echo json_encode(['status' => 'no']);
            exit;

        /*  like  */
        case 'like':

            $idPublicacion = (int)($_POST['id_publicacion'] ?? 0);

            // comprobar si ya existe like
            $like = LikesCRUD::obtenerLikeUsuario($idUsuario, $idPublicacion);

            if ($like) {
                // eliminar like
                LikesCRUD::eliminarLike($like['id_like']);
                $action = 'unlike';
            } else {
                // añadir like
                LikesCRUD::añadirLike(new Like($idUsuario, $idPublicacion));
                $action = 'like';
            }

            // devolvemos la accion y el contador
            echo json_encode([
                'action' => $action,
                'count'  => LikesCRUD::contarLikes($idPublicacion)
            ]);
            exit;

        /* comentario */
        case 'comentario':

            $idPub = (int)($_POST['id_publicacion'] ?? 0);
            $contenido = trim($_POST['contenido'] ?? '');

            if ($idPub > 0 && $contenido !== '') {

                // añadir comentario
                ComentariosCRUD::añadirComentario(
                    new Comentario($idPub, $idUsuario, $contenido)
                );

                // obtener ultimo comentario
                $comentarios = ComentariosCRUD::obtenerPorPublicacion($idPub);
                $ultimo = end($comentarios);

                // devolver los datos del comentario
                echo json_encode([
                    'id_comentario' => $ultimo['id_comentario'],
                    'usuario'       => $usuariosPorId[$idUsuario] ?? 'Usuario',
                    'contenido'     => $contenido,
                    'propio'        => true
                ]);
            } else {
                echo json_encode(['error' => 'datos invalidos']);
            }
            exit;

        /*  borrar comentario  */
        case 'borrarComentario':

            $idComentario = (int)($_POST['id_comentario'] ?? 0);

            if ($idComentario > 0) {
                ComentariosCRUD::eliminarComentario($idComentario);
                echo json_encode(['status' => 'ok']);
            } else {
                echo json_encode(['status' => 'error']);
            }
            exit;

        /*  buscador usuarios  */
        case 'buscarUsuarios':

            $query = trim($_GET['q'] ?? '');
            $resultados = [];

            if ($query) {
                foreach ($usuarios as $u) {
                    if (stripos($u['Username'], $query) === 0) {
                        $resultados[] = $u['Username'];
                    }
                }
            }

            echo json_encode($resultados);
            exit;

        /*  ver mas comentarios */
        case 'verMasComentarios':

            $idPub = (int)($_GET['id_publicacion'] ?? 0);
            $comentarios = ComentariosCRUD::obtenerPorPublicacion($idPub);
            $result = [];

            foreach ($comentarios as $c) {
                $result[] = [
                    'id_comentario' => $c['id_comentario'],
                    'usuario'       => $usuariosPorId[$c['id_usuario']] ?? 'Usuario',
                    'contenido'     => $c['Contenido'],
                    'propio'        => $c['id_usuario'] == $idUsuario
                ];
            }

            echo json_encode($result);
            exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>opuscord</title>
<link rel="stylesheet" href="../../css/estilos.css">
<link rel="stylesheet" href="../../css/publicaciones.css">
</head>
<body>
<div class="container">

<!-- menu -->
<aside class="sidebar">
    <h2>OPUSCORD</h2>

    <!-- navegación -->
    <nav class="main-nav">
        <ul>
            <li><a href="index.php"><button>Feed</button></a></li>
            <li><a href="amigos.php"><button>Amigos</button></a></li>
            <li><a href="chatprivado.php"><button>Mensajes</button></a></li>
            <li><a href="grupos.php"><button>Grupos</button></a></li>
        </ul>
    </nav>

    <!-- parte inferior -->
    <div class="user-info">
        <img src="<?= $_SESSION['Foto'] ?? '../../Recursos/mamiy.png' ?>" class="profile-pic">
        <p><?= htmlspecialchars($_SESSION['Usuario'] ?? 'Usuario') ?></p>
        <a href="../login/logout.php">
            <button class="login-btn">Cerrar sesión</button>
        </a>
    </div>
</aside>


<main class="main-content">

<!--  buscador  -->
<section class="search-form">
    <input type="text" id="buscador" placeholder="buscar usuario...">
    <div id="resultados-buscador" class="resultados-buscador"></div>
</section>

<section class="ordenar-publicaciones">
    <label for="orden">Ordenar por:</label>
    <select id="orden" onchange="cambiarOrden(this.value)">
        <option value="reciente" <?= $orden === 'reciente' ? 'selected' : '' ?>>
            Más recientes
        </option>
        <option value="likes" <?= $orden === 'likes' ? 'selected' : '' ?>>
            Más likes
        </option>
    </select>
</section>


<!-- crear publicacion desplegable -->
<section class="crear-publicacion">

    <button type="button" id="toggle-publicacion" class="toggle-publicacion">
        Crear Publicación
    </button>

    <div id="contenido-publicacion" class="contenido-publicacion">
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <textarea name="contenido" placeholder="que estas pensando?" required></textarea>

            <input type="file" name="imagen" accept="image/*">

            <div style="position: relative; display: inline-block;">
                <button type="button" class="btn-visibilidad">visibilidad: publica ▼</button>
                <div class="desplegable-visibilidad">
                    <button type="button" data-value="publica">publica</button>
                    <button type="button" data-value="privada">privada</button>
                </div>
            </div>

            <input type="hidden" name="visibilidad" value="publica">

            <button type="submit" name="crear_publicacion" class="publicar">
                publicar
            </button>
        </form>
    </div>

</section>


<!-- feed -->
<section class="feed">
<?php foreach ($publicaciones as $p): 
    // obtener comentarios de la publicacion
    $comentarios = ComentariosCRUD::obtenerPorPublicacion($p['id_publicacion']);
    // comprobar si el usuario le dio like
    $likeUsuario = LikesCRUD::obtenerLikeUsuario($idUsuario, $p['id_publicacion']);
?>
<div class="post" data-id="<?= $p['id_publicacion'] ?>">

    <!-- boton eliminar solo si es eres dueño -->
    <?php if ($p['id_usuario'] == $idUsuario): ?>
        <button class="eliminar-publicacion" data-id="<?= $p['id_publicacion'] ?>">✖</button>
    <?php endif; ?>

    <!-- nombre usuario -->
    <div class="post-header">
        <?= htmlspecialchars($usuariosPorId[$p['id_usuario']] ?? 'Usuario') ?>
    </div>

    <!-- contenido -->
    <p><?= htmlspecialchars($p['Contenido']) ?></p>

    <!-- imagen si existe -->
    <?php if ($p['ImagenUrl']): ?>
        <img src="../../<?= $p['ImagenUrl'] ?>">
    <?php endif; ?>

    <!-- boton like -->
    <button class="like-btn <?= $likeUsuario ? 'liked' : '' ?>" data-id="<?= $p['id_publicacion'] ?>">
        <span class="heart">❤️</span>
        <span class="like-count"><?= LikesCRUD::contarLikes($p['id_publicacion']) ?></span>
    </button>

    <!-- comentarios -->
    <div class="comentarios" data-id="<?= $p['id_publicacion'] ?>">
    <?php 
    $totalComentarios = count($comentarios);
    $comentariosMostrar = array_slice($comentarios, -3); // ultimos 3
    foreach ($comentariosMostrar as $c): ?>
        <div class="comentario" data-id="<?= $c['id_comentario'] ?>">
            <strong><?= htmlspecialchars($usuariosPorId[$c['id_usuario']] ?? 'Usuario') ?></strong>
            <span><?= htmlspecialchars($c['Contenido']) ?></span>
            <?php if ($c['id_usuario'] == $idUsuario): ?>
                <button class="comentario-borrar" data-id="<?= $c['id_comentario'] ?>">✖</button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <!-- ver mas comentarios si hay mas de 3 -->
    <?php if ($totalComentarios > 3): ?>
        <button class="ver-mas" data-id="<?= $p['id_publicacion'] ?>">ver mas comentarios</button>
    <?php endif; ?>
    </div>

    <!-- formulario agregar comentario -->
    <form class="comentario-form" data-id="<?= $p['id_publicacion'] ?>">
        <input type="text" placeholder="añade un comentario..." required>
        <button>publicar</button>
    </form>

</div>
<?php endforeach; ?>
</section>

</main>
</div>

<script>
// visibilidad
const btnVisibilidad = document.querySelector('.btn-visibilidad');
const desplegable = document.querySelector('.desplegable-visibilidad');
const inputVisibilidad = document.querySelector('input[name="visibilidad"]');

// mostrar u ocultar desplegable
btnVisibilidad.onclick = () => desplegable.style.display = desplegable.style.display === 'block' ? 'none' : 'block';

// seleccionar opcion de visibilidad
desplegable.querySelectorAll('button').forEach(b => {
    b.onclick = () => {
        inputVisibilidad.value = b.dataset.value;
        btnVisibilidad.textContent = 'visibilidad: ' + (b.dataset.value === 'publica' ? 'publica' : 'privada') + ' ▼';
        desplegable.style.display = 'none';
    };
});

// likes 
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.onclick = () => {
        fetch('index.php?ajax=like', {
            method: 'POST',
            body: new URLSearchParams({ id_publicacion: btn.dataset.id })
        })
        .then(res => res.json())
        .then(data => {
            btn.classList.toggle('liked', data.action==='like');
            btn.querySelector('.like-count').textContent = data.count;
        });
    };
});

//  comentarios ajax 
document.querySelectorAll('.comentario-form').forEach(form => {
    form.onsubmit = e => {
        e.preventDefault();
        const input = form.querySelector('input');
        const contenido = input.value.trim();
        if (!contenido) return;

        fetch('index.php?ajax=comentario', {
            method: 'POST',
            body: new URLSearchParams({ id_publicacion: form.dataset.id, contenido })
        })
        .then(res => res.json())
        .then(data => {
            if(data.error) { alert(data.error); return; }

            const comentariosDiv = form.closest('.post').querySelector('.comentarios');

            // Crear el div del comentario
            const div = document.createElement('div');
            div.className = 'comentario';
            div.dataset.id = data.id_comentario;
            div.innerHTML = `<strong>${data.usuario}</strong> <span>${data.contenido}</span>`;

            // Botón para borrar
            const borrarBtn = document.createElement('button');
            borrarBtn.textContent = '✖';
            borrarBtn.className = 'comentario-borrar';
            borrarBtn.dataset.id = data.id_comentario;
            borrarBtn.onclick = () => {
                if (!confirm('¿Seguro que quieres eliminar este comentario?')) return;

                fetch('index.php?ajax=borrarComentario', {
                    method: 'POST',
                    body: new URLSearchParams({ id_comentario: data.id_comentario })
                }).then(() => div.remove());
            };

            div.appendChild(borrarBtn);

            // Agregar comentario antes del botón "ver más" si existe
            const verMasBtn = comentariosDiv.querySelector('.ver-mas');
            if(verMasBtn) {
                comentariosDiv.insertBefore(div, verMasBtn);
            } else {
                comentariosDiv.appendChild(div);
            }

            input.value = '';
        })
        .catch(err => console.error(err));
    };
});


// borrar comentarios existentes 
document.querySelectorAll('.comentario-borrar').forEach(btn => {
    btn.onclick = () => {
        if (!confirm('¿Seguro que quieres eliminar este comentario?')) return;

        const div = btn.closest('.comentario');
        fetch('index.php?ajax=borrarComentario', {
            method: 'POST',
            body: new URLSearchParams({ id_comentario: btn.dataset.id })
        }).then(() => div.remove());
    };
});

//  eliminar publicacion si eres propietario
document.querySelectorAll('.eliminar-publicacion').forEach(btn => {
    btn.onclick = () => {
        if (!confirm('quieres eliminar esta publicacion?')) return;
        fetch('index.php?ajax=eliminarPublicacion', {
            method: 'POST',
            body: new URLSearchParams({ id_publicacion: btn.dataset.id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status==='ok') btn.closest('.post').remove();
            else alert('no puedes eliminar esta publicacion');
        });
    };
});

// ver mas o ver menos comentarios 
document.querySelectorAll('.ver-mas').forEach(btn => {
    btn.onclick = () => {
        const postId = btn.dataset.id;
        const comentariosDiv = btn.closest('.comentarios');

        // si ya esta en ver menos lo colapsamos los ultimos 3
        if(btn.dataset.estado === 'menos') {
            fetch(`index.php?ajax=verMasComentarios&id_publicacion=${postId}`)
                .then(res => res.json())
                .then(data => {
                    comentariosDiv.innerHTML = '';
                    const ultimos = data.slice(-3); // ultimos 3
                    ultimos.forEach(c => {
                        const div = document.createElement('div');
                        div.className = 'comentario';
                        div.dataset.id = c.id_comentario;
                        div.innerHTML = `<strong>${c.usuario}</strong> <span>${c.contenido}</span>`;
                        if(c.propio){
                            const borrarBtn = document.createElement('button');
                            borrarBtn.textContent = '✖';
                            borrarBtn.className = 'comentario-borrar';
                            borrarBtn.dataset.id = c.id_comentario;
                            borrarBtn.onclick = () => {
                                fetch('index.php?ajax=borrarComentario', {
                                    method:'POST',
                                    body: new URLSearchParams({id_comentario:c.id_comentario})
                                }).then(()=>div.remove());
                            };
                            div.appendChild(borrarBtn);
                        }
                        comentariosDiv.appendChild(div);
                    });
                    // volvemos a agregar el boton
                    comentariosDiv.appendChild(btn);
                    btn.textContent = 'ver más comentarios';
                    btn.dataset.estado = 'mas';
                });
            return;
        }

        // si esta en ver mas mostramos todos
        fetch(`index.php?ajax=verMasComentarios&id_publicacion=${postId}`)
            .then(res => res.json())
            .then(data => {
                comentariosDiv.innerHTML = '';
                data.forEach(c => {
                    const div = document.createElement('div');
                    div.className = 'comentario';
                    div.dataset.id = c.id_comentario;
                    div.innerHTML = `<strong>${c.usuario}</strong> <span>${c.contenido}</span>`;
                    if(c.propio){
                        const borrarBtn = document.createElement('button');
                        borrarBtn.textContent = '✖';
                        borrarBtn.className = 'comentario-borrar';
                        borrarBtn.dataset.id = c.id_comentario;
                        borrarBtn.onclick = () => {
                            fetch('index.php?ajax=borrarComentario', {
                                method:'POST',
                                body: new URLSearchParams({id_comentario:c.id_comentario})
                            }).then(()=>div.remove());
                        };
                        div.appendChild(borrarBtn);
                    }
                    comentariosDiv.appendChild(div);
                });
                // volvemos a agregar el boton
                comentariosDiv.appendChild(btn);
                btn.textContent = 'ver menos comentarios';
                btn.dataset.estado = 'menos';
            });
    };
});


// buscador en vivo 
const buscador = document.getElementById('buscador');
const resultadosDiv = document.getElementById('resultados-buscador');

buscador.addEventListener('input', () => {
    const q = buscador.value.trim();
    if(!q){ resultadosDiv.innerHTML=''; return; }

    fetch(`index.php?ajax=buscarUsuarios&q=${encodeURIComponent(q)}`)
        .then(res=>res.json())
        .then(data=>{
            resultadosDiv.innerHTML='';
            data.forEach(u=>{
                const div = document.createElement('div');
                div.className='resultado-usuario';
                div.textContent=u;
                div.onclick=()=>{
                    buscador.value=u;
                    resultadosDiv.innerHTML='';
                    window.location.href=`index.php?buscar_usuario=${encodeURIComponent(u)}`;
                };
                resultadosDiv.appendChild(div);
            });
        });
});

// desplegable crear publicacion
const toggleBtn = document.getElementById('toggle-publicacion');
const contenidoPub = document.getElementById('contenido-publicacion');

toggleBtn.onclick = () => {
    const abierto = contenidoPub.style.display === 'block';
    contenidoPub.style.display = abierto ? 'none' : 'block';
    toggleBtn.textContent = abierto
        ? ' Crear publicación'
        : ' Cerrar publicación';
};

//filtrado de post
function cambiarOrden(valor) {
    const url = new URL(window.location.href);
    url.searchParams.set('orden', valor);
    window.location.href = url.toString();
}

</script>
</body>
</html>

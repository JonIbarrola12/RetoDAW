<?php
session_start();
require_once '../conexion.php';
require_once '../CRUD/MiembrosCRUD.php';

if (!isset($_SESSION['id_usuario'])) {
    exit('No has iniciado sesión');
}

if (!isset($_GET['id'])) {
    exit('Grupo no especificado');
}

$idUsuario = $_SESSION['id_usuario'];
$idGrupo = (int) $_GET['id'];

/* obtener datos del grupo */
$stmt = $pdo->prepare("
    SELECT g.Nombre, g.Descripcion, g.Pfp, g.FechaCreacion, u.Username AS creador
    FROM grupos g
    JOIN usuarios u ON g.id_creador = u.id_usuario
    WHERE g.id_grupo = ?
");
$stmt->execute([$idGrupo]);
$grupo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$grupo) {
    exit('Grupo no encontrado');
}

/* comprobar rol del usuario */
$stmt = $pdo->prepare("
    SELECT Rol FROM miembros
    WHERE id_usuario = ? AND GrupoId = ? AND activo = 1
");
$stmt->execute([$idUsuario, $idGrupo]);
$miembro = $stmt->fetch(PDO::FETCH_ASSOC);

$esAdmin = $miembro && $miembro['Rol'] === 'admin';

$foto = !empty($grupo['Pfp'])
    ? $grupo['Pfp']
    : '../../Recursos/fotogrupo.png';
?>
<?php 
$stmt = $pdo->prepare("
    SELECT u.id_usuario, u.Username, u.Pfp, m.Rol
    FROM miembros m
    JOIN usuarios u ON m.id_usuario = u.id_usuario
    WHERE m.GrupoId = ?
    ORDER BY 
        FIELD(m.Rol, 'admin','moderador','miembro'),
        u.Username ASC
");


$stmt->execute([$idGrupo]);
$miembros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="perfilModalContent" data-grupo-id="<?= $idGrupo ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <div class="perfil-header">

        <div class="perfil-foto-container">
            <img 
                src="<?= htmlspecialchars($foto) ?>" 
                class="perfil-foto <?= $esAdmin ? 'editable' : '' ?>" 
                id="fotoGrupo"
                data-grupo-id="<?= $idGrupo ?>"
            >
            <div class="overlay">
                <img src="../../Recursos/camara.png" alt="Cambiar foto" class="camara-icon">
            </div>
            <?php if ($esAdmin): ?>
                <input 
                    type="file" 
                    id="inputFotoGrupo" 
                    accept="image/*" 
                    hidden
                >
            <?php endif; ?>
        </div>


        <div class="perfil-info-texto">
            <div class="grupo-header-info">
                <div class="grupo-nombre-editable">
                <h2 id="grupoNombreTexto"><?= htmlspecialchars($grupo['Nombre']) ?></h2>

            </div>

            <?php if ($esAdmin): ?>
                <input 
                    type="text" 
                    id="inputGrupoNombre"
                    class="input-editar hidden"
                    placeholder="Nombre"
                    maxlength="12"
                    value="<?= htmlspecialchars($grupo['Nombre']) ?>"
                >

            <?php endif; ?>

                <?php if ($esAdmin): ?>
                    <button id="btnEditarNombre" class="btn-editar botonlapiz">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                <?php endif; ?>
                <?php if ($esAdmin): ?>
                    <button id="btnMostrarInvitar" class="btn-añadir-miembro">
                        Añadir
                    </button>
                    
                <?php endif; ?>
            </div>
            <br>
            <p class="perfil-creador">
                Creado por <?= htmlspecialchars($grupo['creador']) ?>
            </p>
        </div>


    </div>
    
    <?php if ($esAdmin): ?>
    <div id="invitarBox" class="invitar-box hidden">
        <script src="../../js/Perfil.js"></script>

        <form method="POST" action="../Funcionalidades/anadir_miembro.php">
            <input type="hidden" name="id_grupo" value="<?= $idGrupo ?>">

            <input 
                type="text" class="input-añadir"
                name="username" 
                placeholder="Usuario"
                required
            >

            <button class="agregar-miembro-btn">Agregar</button>
        </form>
    </div>
    <?php endif; ?>
    <hr>

    <div class="perfil-bio">
        <div class="bio-header">
            <h3>Descripción</h3>

            <?php if ($esAdmin): ?>
                <button id="editarDescripcionGrupoBtn" class="perfil-btn botonlapiz">
                    <i class="fa-solid fa-pen"></i>
                </button>
            <?php endif; ?>
        </div>

        <p id="grupoDescripcionTexto" class="bio">
            <?= $grupo['Descripcion'] ?: 'Sin descripción' ?>
        </p>

        <?php if ($esAdmin): ?>
            <!-- textarea oculto -->
            <textarea
                id="grupoDescripcionInput"
                class="bio-edit"
                maxlength="200"
                style="display:none;"
            ><?= htmlspecialchars($grupo['Descripcion']) ?></textarea>

            <div class="bio-footer" id="grupoBioFooter" style="display:none;">
                <span id="grupoBioContador">0</span>/200
            </div>

            <div class="bio-actions" id="grupoBioActions" style="display:none;">
                <button id="guardarDescripcionGrupoBtn" class="bio-btn">Guardar</button>
                <button id="cancelarDescripcionGrupoBtn" class="bio-btn">Cancelar</button>
            </div>
        <?php endif; ?>

        <br>
        <h4>Creado el</h4>
        <p><?= date('d/m/Y', strtotime($grupo['FechaCreacion'])) ?></p>
    </div>

    <hr>
    <h3>Miembros del grupo <span class="contador-miembros">[ <?= count($miembros) ?> ]</span></h3>


    <div class="lista-miembros">
        <?php foreach ($miembros as $m): ?>
            <div 
                class="perfil-horiz miembro-item post-user"
                data-usuario-id="<?= $m['id_usuario'] ?>"
            >
                <img
                    src="<?= htmlspecialchars($m['Pfp'] ?: '/Recursos/fotogrupo.png') ?>"
                    class="profile-pic"
                >

                <div class="perfil-info">
                    <p class="perfil-nombre">
                        <?= htmlspecialchars($m['Username']) ?>
                <?php if ($esAdmin && $m['id_usuario'] != $idUsuario): ?>
                    <form method="POST" action="../Funcionalidades/expulsar_miembro.php"
                        class="form-expulsar"
                        onsubmit="return confirm('¿Expulsar a este usuario del grupo?');">
                        <input type="hidden" name="id_grupo" value="<?= $idGrupo ?>">
                        <input type="hidden" name="id_usuario" value="<?= $m['id_usuario'] ?>">
                        <button class="btn-expulsar">Expulsar</button>
                    </form>
                <?php endif; ?>
                        <?php if ($m['Rol'] === 'admin'): ?>
                            <span class="rol admin">👑 Admin</span>
                        <?php elseif ($m['Rol'] === 'moderador'): ?>
                            <span class="rol mod">🛡️ Mod</span>
                        <?php endif; ?>
                    </p>
                </div>

            </div> 
        <?php endforeach; ?>
    </div>
    <div class="grupo-acciones-final">

    <?php if (!$esAdmin): ?>
    <!-- BOTÓN ABANDONAR -->
        <form 
            method="POST" 
            action="../Funcionalidades/abandonar_grupo.php"
            onsubmit="return confirm('¿Seguro que quieres abandonar el grupo?');"
        >
            <input type="hidden" name="id_grupo" value="<?= $idGrupo ?>">
            <button class="btn-abandonar">
                Abandonar Grupo
            </button>
        </form>
    <?php endif; ?>
    <?php if ($esAdmin): ?>
    <form 
        method="POST" 
        action="../Funcionalidades/eliminar_grupo.php"
        class="form-eliminar-grupo"
    >
        <input type="hidden" name="id_grupo" value="<?= $idGrupo ?>">
        <button type="submit" class="btn-eliminar">
            Eliminar Grupo
        </button>
    </form>
    <?php endif; ?>

</div>

</div>
<div id="alertaCustom" class="alerta-custom"></div>

<div id="perfilAmigoModal" class="perfil-modalgru" style="display:none;">
    <div id="perfilAmigoContenido" class="perfil-modal-contentgru">
        <span id="cerrarPerfilAmigoModal" class="cerrar-modalgru">&times;</span>
    </div>
</div>

<div id="confirmModalgru" class="confirmgru-modal hidden">
    <div class="confirmgru-box">
        <p id="confirmMensajegru"></p>
        <div class="confirmgru-actions">
            <button id="confirmSigru" class="btn-confirmargru">Eliminar</button>
            <button id="confirmNogru" class="btn-cancelar">Cancelar</button>
        </div>
    </div>
</div>
        <script src="../../js/Perfil.js"></script>

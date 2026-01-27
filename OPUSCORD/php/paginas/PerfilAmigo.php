<?php
session_start();
require_once '../conexion.php';

// Revisar que el usuario que ve el perfil está logueado
if (!isset($_SESSION['Usuario'])) {
    echo '<p>No has iniciado sesión</p>';
    exit;
}

// ID del amigo que queremos mostrar
if (!isset($_GET['id'])) {
    echo '<p>ID de usuario no especificado</p>';
    exit;
}

$idAmigo = intval($_GET['id']);

// Traer datos del amigo
$stmt = $pdo->prepare("SELECT Username, Bio, Pfp, FechaRegistro, estado FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$idAmigo]);
$amigo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$amigo) {
    echo '<p>Usuario no encontrado</p>';
    exit;
}

$foto = !empty($amigo['Pfp']) ? $amigo['Pfp'] : '/Recursos/fotousuario.png';
?>

<div id="perfilModalContent">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <div class="perfil-header">

        <div class="perfil-foto-container">
            <img src="<?= htmlspecialchars($foto) ?>" class="perfil-foto foto-amigo">
        </div>

        <!-- TEXTO PERFIL -->
        <div class="perfil-info-texto">

            <div class="bio-header">
                <h2><?= htmlspecialchars($amigo['Username']) ?></h2>
            </div>

            <div class="estado-usuario">
                <span class="estado-dot <?= $amigo['estado'] === 'Online' ? 'online' : 'offline' ?>"></span>
                <span class="estado-texto"><?= $amigo['estado'] === 'Online' ? 'En línea' : 'Desconectado' ?></span>
            </div>

        </div>

    </div>

    <br>
    <hr>

    <div class="perfil-bio">
        <div class="bio-header">
            <h3>Sobre mí</h3>
        </div>
        <p class="bio">
            <?= !empty($amigo['Bio']) ? htmlspecialchars($amigo['Bio']) : "Sin biografía"; ?>
        </p>

        <br>
        <h4>Miembro desde</h4>
        <p>
            <?php
            if(!empty($amigo['FechaRegistro'])) {
                $fecha = date_create($amigo['FechaRegistro']);
                $meses = [
                    1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',
                    7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'
                ];
                $dia = date_format($fecha, 'd');
                $mes = $meses[(int)date_format($fecha, 'm')];
                $anio = date_format($fecha, 'Y');
                echo "$dia de $mes de $anio";
            } else {
                echo "Fecha no disponible";
            }
            ?>
        </p>
    </div>
    <br>
    <?php if ($idAmigo !== $_SESSION['id_usuario']): ?>
    <form id="formEliminarAmigo" method="POST" action="../Funcionalidades/eliminar_amigo.php">
        <input type="hidden" name="eliminar_amigo" value="<?= $idAmigo ?>">
        <button
            type="button"
            class="eliminar-amigo-btn"
            onclick="event.stopPropagation(); abrirConfirmEliminar();"
        >
            Eliminar amigo
        </button>
    </form>
    <?php endif; ?>
</div>

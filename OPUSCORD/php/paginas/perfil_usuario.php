<?php
session_start();
require_once '../conexion.php';



if (!isset($_SESSION['id_usuario'])) exit;
if (!isset($_GET['id'])) exit;

$idUsuario = $_SESSION['id_usuario'];
$idPerfil = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT Username, Bio, Pfp, FechaRegistro, estado
    FROM usuarios
    WHERE id_usuario = ?
");
$stmt->execute([$idPerfil]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) exit;

// relación
$stmt = $pdo->prepare("
    SELECT Estado FROM amigos
    WHERE (id_usuario = ? AND id_amigo_usuario = ?)
       OR (id_usuario = ? AND id_amigo_usuario = ?)
");
$stmt->execute([$idUsuario, $idPerfil, $idPerfil, $idUsuario]);
$relacion = $stmt->fetch(PDO::FETCH_ASSOC);

$foto = $usuario['Pfp'] ?: '/Recursos/fotousuario.png';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<div id="perfilModalContent">

    <div class="perfil-header">
        <div class="perfil-foto-container">
            <img src="<?= htmlspecialchars($foto) ?>" class="perfil-foto foto-amigo">
        </div>

        <div class="perfil-info-texto">
            <div class="bio-header">
                <h2 class="compreTexto"><?= htmlspecialchars($usuario['Username']) ?></h2>
            </div>

            <div class="estado-usuario">
                <span class="estado-dot <?= $usuario['estado'] === 'Online' ? 'online' : 'offline' ?>"></span>
                <span class="estado-texto">
                    <?= $usuario['estado'] === 'Online' ? 'En línea' : 'Desconectado' ?>
                </span>
            </div>
        </div>
    </div>

    <br><hr>

    <div class="perfil-bio">
        <h3>Sobre mí</h3>
        <p class="bio">
            <?= !empty($usuario['Bio']) ? htmlspecialchars($usuario['Bio']) : 'Sin biografía'; ?>
        </p>

        <br>
        <h4>Miembro desde</h4>
        <p>
            <?php
            $fecha = date_create($usuario['FechaRegistro']);
            $meses = [
                1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',
                7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'
            ];
            echo date_format($fecha, 'd') . ' de ' .
                 $meses[(int)date_format($fecha, 'm')] . ' de ' .
                 date_format($fecha, 'Y');
            ?>
        </p>
    </div>

    <br>
<?php if (!$relacion && $idPerfil !== $_SESSION['id_usuario']): ?>
    <form method="POST" action="../paginas/amigos.php">
        <input type="hidden" name="id_amigo" value="<?= $idPerfil ?>">
        <button class="btn-aceptar">Enviar solicitud</button>
    </form>

<?php elseif ($relacion && $relacion['Estado'] === 'pendiente'): ?>
    <p class="estado-pendiente">Solicitud pendiente</p>
<?php endif; ?>

</div>
<div id="alertaCustom" class="alerta-custom"></div>

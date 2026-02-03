<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    echo '<p>No has iniciado sesión</p>';
    exit;
}

$foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png';
?>

<div id="perfilModalContent">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="../../js/Perfil.js"></script>
    <div class="perfil-header">

        <div class="perfil-foto-container">
            <img id="perfilImagen" src="<?php echo htmlspecialchars($foto); ?>" class="perfil-foto foto-mia">
            <div class="overlay">
                <img src="../../Recursos/camara.png" alt="Cambiar foto" class="camara-icon">
            </div>
        </div>
        

        <!-- TEXTO PERFIL -->
        <div class="perfil-info-texto">

            <div class="bio-header">
                <h2 id="usernameTexto" class="compreTexto">
                    <?php echo htmlspecialchars($_SESSION['Usuario']); ?>
                </h2>
                <button id="editarUsernameBtn" class="perfil-btn botonlapiz" title="Editar Username">
                    <i class="fa-solid fa-pen"></i>
                </button>
                <br>

            </div>

            <div class="estado-usuario">
                <span class="estado-dot online"></span>
                <span class="estado-texto">En línea</span>
            </div>

        </div>

    </div>
    

    <!-- MODAL USERNAME -->
    <div id="usernameModal" class="username-modal">
        
        
        <div class="username-modal-content">
            <span id="cerrarUsernameModal">&times;</span>
            <h3>Cambiar nombre de usuario</h3>
            <input type="text" id="usernameInput" maxlength="12"  placeholder="Nuevo nombre de usuario">
            <span id="error-username" class="textoerror"></span>

            <br>
            <button id="guardarUsernameBtn" class="editusu-btn">Guardar</button>
        </div>
    </div>


        <a href="../paginas/galerias.php?id=<?= $_SESSION['id_usuario'] ?>" class="btn-galeria">
            <i class="fa-solid fa-images"></i> Galeria
        </a>
    <br>
    <hr>
    <div class="perfil-bio">
        <div class="bio-header">
            <h3>Sobre mí</h3>
            <button id="editarPerfilBtn" class="perfil-btn botonlapiz" title="Editar biografia">
                <i class="fa-solid fa-pen"></i>
            </button>
        </div>
        <p id="bioTexto" class="bio">
            <?php 
                echo isset($_SESSION['Biografia']) && !empty($_SESSION['Biografia']) 
                    ? htmlspecialchars($_SESSION['Biografia']) 
                    : "Sin biografía"; 
            ?>
        </p>

        <!-- textarea oculto -->
        <textarea id="bioInput" class="bio-edit" maxlength="200" style="display:none;"></textarea>

        <div class="bio-footer" style="display:none;">
            <span id="bioContador">0</span>
        </div>

        <div class="bio-actions" style="display:none;">
            <button id="guardarBioBtn" class="bio-btn">Guardar</button>
            <button id="cancelarBioBtn" class="bio-btn">Cancelar</button>
        </div>
        <br>
        <H4>Miembro desde</H4>
        <p>
            <?php
            if(isset($_SESSION['FechaReg']) && !empty($_SESSION['FechaReg'])) {
                $fecha = date_create($_SESSION['FechaReg']);
                $meses = [
                    1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',
                    7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'
                ];
                $dia = date_format($fecha, 'd');
                $mes = $meses[(int)date_format($fecha, 'm')];
                $anio = date_format($fecha, 'Y');
                echo "$dia de $mes de $anio"; // ejemplo: 14 de enero de 2026
            } else {
                echo "Fecha no disponible";
            }
            ?>

        </p>
    </div>

    <form id="formFoto" method="post" enctype="multipart/form-data" action="subir_foto.php" style="display:none;">
        <input type="file" name="nuevaFoto" id="inputFoto" accept="image/*">
    </form>
    <br>
    <a href="../Login/Logout.php">
        <button class="logout-btn">Cerrar Sesión</button>
    </a>


</div>


<div id="alertaCustom" class="alerta-custom"></div>

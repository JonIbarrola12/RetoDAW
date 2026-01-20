// --- Abrir y cerrar modal de perfil ---
function abrirPerfil() {
    fetch('../paginas/Perfil.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById('perfilContenido').innerHTML = html;
            document.getElementById('perfilModal').style.display = 'block';
        });
}

function cerrarPerfil() {
    document.getElementById('perfilModal').style.display = 'none';
    document.getElementById('perfilContenido').innerHTML = '';
}

// Cerrar modal al hacer click fuera
window.onclick = function(e) {
    const modal = document.getElementById('perfilModal');
    if (e.target === modal) cerrarPerfil();
}

// --- BroadcastChannel para actualizar foto en otras pestañas ---
const canalFoto = new BroadcastChannel('fotoPerfil');

canalFoto.onmessage = (event) => {
    if(event.data.nuevaFoto){
        const imgs = document.querySelectorAll('.fotoPerfil');
        imgs.forEach(img => {
            img.src = event.data.nuevaFoto + '?t=' + new Date().getTime();
        });
    }
};

// --- Cambiar foto de perfil al clickar en la imagen ---
document.addEventListener('click', function(e){
    if(e.target && e.target.id === 'perfilImagen'){
        const inputFoto = document.getElementById('inputFoto');
        const perfilImagen = document.getElementById('perfilImagen');

        inputFoto.click(); // abrir explorador

        inputFoto.onchange = () => {
            if(inputFoto.files && inputFoto.files[0]){
                // Vista previa inmediata en el modal
                const reader = new FileReader();
                reader.onload = function(ev){
                    perfilImagen.src = ev.target.result;
                }
                reader.readAsDataURL(inputFoto.files[0]);

                // Subir al servidor con AJAX
                const formData = new FormData();
                formData.append('nuevaFoto', inputFoto.files[0]);

                fetch('../Funcionalidades/subir_foto.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'ok'){
                        console.log('Foto actualizada en la BD');

                        // Actualizar todas las fotos de perfil en la página actual
                        const imgs = document.querySelectorAll('.fotoPerfil');
                        imgs.forEach(img => {
                            img.src = data.nuevaFoto + '?t=' + new Date().getTime();
                        });

                        // Enviar mensaje a otras pestañas para actualizar allí también
                        canalFoto.postMessage({ nuevaFoto: data.nuevaFoto });

                    } else {
                        alert('Error al subir la foto: ' + data.msg);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error de conexión');
                });
            }
        }
    }
});

document.addEventListener('click', function(e){

    // Entrar en modo edición
    if(e.target && e.target.id === 'editarPerfilBtn'){
        const bioTexto = document.getElementById('bioTexto');
        const bioInput = document.getElementById('bioInput');
        const acciones = document.querySelector('.bio-actions');
        const footer = document.querySelector('.bio-footer');
        const contador = document.getElementById('bioContador');

        bioInput.value = bioTexto.innerText.trim();
        bioTexto.style.display = 'none';
        bioInput.style.display = 'block';
        acciones.style.display = 'block';
        footer.style.display = 'flex';

        contador.innerText = `${bioInput.value.length} / 200`;
        bioInput.focus();
    }

    // Cancelar
    if(e.target && e.target.id === 'cancelarBioBtn'){
        document.getElementById('bioTexto').style.display = 'block';
        document.getElementById('bioInput').style.display = 'none';
        document.querySelector('.bio-actions').style.display = 'none';
        document.querySelector('.bio-footer').style.display = 'none';
    }

    // Guardar
    if(e.target && e.target.id === 'guardarBioBtn'){
        const bioInput = document.getElementById('bioInput');
        const nuevaBio = bioInput.value.trim();

        fetch('../Funcionalidades/guardar_bio.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ bio: nuevaBio })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'ok'){
                const bioTexto = document.getElementById('bioTexto');
                bioTexto.innerText = nuevaBio || 'Sin biografía';

                bioTexto.style.display = 'block';
                bioInput.style.display = 'none';
                document.querySelector('.bio-actions').style.display = 'none';
                document.querySelector('.bio-footer').style.display = 'none';
            } else {
                alert(data.msg);
            }
        });
    }
});

// CONTADOR EN TIEMPO REAL
document.addEventListener('input', function(e){
    if(e.target && e.target.id === 'bioInput'){
        const contador = document.getElementById('bioContador');
        const len = e.target.value.length;

        contador.innerText = `${len} / 200`;

        contador.classList.remove('contador-normal','contador-warning','contador-rojo');

        if(len >= 200){
            contador.classList.add('contador-rojo');
        } else if(len >= 180){
            contador.classList.add('contador-warning');
        } else {
            contador.classList.add('contador-normal');
        }
    }
});

// CAMBIAR USERNAME

document.addEventListener('click', function (e) {

    // ABRIR MODAL
    if (e.target.closest('#editarUsernameBtn')) {
        e.stopPropagation();

        const modal = document.getElementById('usernameModal');
        const input = document.getElementById('usernameInput');

        modal.style.display = 'flex';
        input.focus();
    }

    // CERRAR CON X
    if (e.target.id === 'cerrarUsernameModal') {
        document.getElementById('usernameModal').style.display = 'none';
    }

    // CERRAR CLICK FUERA
    if (e.target.id === 'usernameModal') {
        document.getElementById('usernameModal').style.display = 'none';
    }

});

document.addEventListener('click', function (e) {

    if (e.target.id === 'guardarUsernameBtn') {

        const input = document.getElementById('usernameInput');
        const nuevoUsername = input.value.trim();

        if (nuevoUsername.length < 3) {
            alert('El nombre debe tener al menos 3 caracteres');
            return;
        }

        fetch('../Funcionalidades/actualizar_username.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: nuevoUsername })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'ok') {

                // Actualizar texto en pantalla
                document.getElementById('usernameTexto').textContent = nuevoUsername;
                document.querySelectorAll('.perfil-nombre').forEach(el => {
                    el.textContent = nuevoUsername;
                });

                // Cerrar modal
                document.getElementById('usernameModal').style.display = 'none';
            } else {
                alert(data.msg);
            }
        })
        .catch(() => alert('Error de conexión'));
    }

});

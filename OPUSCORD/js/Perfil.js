// --- Abrir y cerrar modal de perfil ---
let grupoIdActivo = null;
document.addEventListener("DOMContentLoaded", () => {
    const mensaje = document.getElementById("mensajeFlash");

    if (mensaje) {
        setTimeout(() => {
            mensaje.style.transition = "opacity 0.5s ease";
            mensaje.style.opacity = "0";

            setTimeout(() => mensaje.remove(), 500);
        }, 3000);
    }
});


function confirmCustom(mensaje) {
    return new Promise(resolve => {
        const modal = document.getElementById('confirmModal');
        document.getElementById('confirmMessage').textContent = mensaje;

        modal.style.display = 'flex';

        const ok = document.getElementById('confirmOk');
        const cancel = document.getElementById('confirmCancel');

        ok.onclick = () => {
            modal.style.display = 'none';
            resolve(true);
        };

        cancel.onclick = () => {
            modal.style.display = 'none';
            resolve(false);
        };
    });
}
    function confirmCustomGru(mensaje) {
    return new Promise(resolve => {

        const modal = document.getElementById('confirmModalgru');
        const texto = document.getElementById('confirmMensajegru');
        const btnSi = document.getElementById('confirmSigru');
        const btnNo = document.getElementById('confirmNogru');

        texto.textContent = mensaje;
        modal.classList.remove('hidden');

        const limpiar = () => {
            modal.classList.add('hidden');
            btnSi.onclick = null;
            btnNo.onclick = null;
        };

        btnSi.onclick = () => {
            limpiar();
            resolve(true);   // ✅ MUY IMPORTANTE
        };

        btnNo.onclick = () => {
            limpiar();
            resolve(false);  // ✅ MUY IMPORTANTE
        };
    });
}
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

document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('perfilAmigoModal');
    const contenido = document.getElementById('perfilAmigoContenido');
    const cerrar = document.getElementById('cerrarPerfilAmigoModal');

    // Función para abrir modal
    function abrirPerfilAmigo(idAmigo) {
        fetch(`PerfilAmigo.php?id=${idAmigo}`)
            .then(res => res.text())
            .then(html => {
                contenido.innerHTML = html;
                modal.style.display = 'flex';
            })
            .catch(err => console.error('Error cargando perfil:', err));
    }

    // Cerrar modal al hacer click en la X
    cerrar.addEventListener('click', () => {
        modal.style.display = 'none';
        contenido.innerHTML = '';
    });

    // Cerrar modal al hacer click fuera del contenido
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            contenido.innerHTML = '';
        }
    });

    // Detectar click en cualquier amigo
    document.querySelectorAll('.perfil-horiz').forEach(div => {
        div.addEventListener('click', () => {
            const idAmigo = div.dataset.usuarioId; // asegúrate que cada .perfil-horiz tiene data-usuario-id
            if (idAmigo) {
                abrirPerfilAmigo(idAmigo);
            }
        });
    });

});

document.addEventListener('submit', async function (e) {

    const form = e.target;

    if (!form.classList.contains('form-eliminar-grupo')) return;

    e.preventDefault();

    const confirmado = await confirmCustomGru(
        'Esto eliminará el grupo para todos. ¿Continuar?'
    );

    if (confirmado) {
        form.submit();
    }
});



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
                        const imgs = document.querySelectorAll('.foto-mia');
                        imgs.forEach(img => {
                            img.src = data.nuevaFoto + '?t=' + Date.now();
                        });

                        // Enviar mensaje a otras pestañas para actualizar allí también
                        canalFoto.postMessage({
                            tipo: 'miFoto',
                            nuevaFoto: data.nuevaFoto
                         });
                        canalFoto.onmessage = (e) => {
                            if (e.data.tipo === 'miFoto') {
                                document.querySelectorAll('.foto-mia').forEach(img => {
                                    img.src = e.data.nuevaFoto + '?t=' + Date.now();
                                });
                            }
                        };

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

    setInterval(() => {
        fetch('../Funcionalidades/actualizar_estado.php');
    }, 10000); // cada 10 segundos
    setInterval(() => {
        fetch('../Funcionalidades/obtener_estados_amigos.php')
            .then(res => res.json())
            .then(amigos => {
                amigos.forEach(amigo => {
                    const contenedor = document.querySelector(
                        `[data-usuario-id="${amigo.id_usuario}"]`
                    );

                    if (!contenedor) return;

                    const dot = contenedor.querySelector('.estado-dot');
                    const texto = contenedor.querySelector('.estado-texto');

                    dot.classList.toggle('online', amigo.estado === 'Online');
                    dot.classList.toggle('offline', amigo.estado === 'Offline');

                    texto.textContent =
                        amigo.estado === 'Online' ? 'En línea' : 'Desconectado';
                });
            });
    }, 5000); // cada 5 segundos

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
        const errorSpan = document.getElementById('error-username');

        // limpiar error
        errorSpan.textContent = '';

        // validaciones front
        if (nuevoUsername.length < 3) {
            errorSpan.textContent = 'El nombre debe tener al menos 3 caracteres';
            return;
        }

        if (nuevoUsername.length > 12) {
            errorSpan.textContent = 'Máximo 12 caracteres';
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

                // actualizar texto en pantalla
                document.getElementById('usernameTexto').textContent = nuevoUsername;
                document.querySelectorAll('.nombre-mio').forEach(el => {
                    el.textContent = nuevoUsername;
                });

                // cerrar modal
                document.getElementById('usernameModal').style.display = 'none';

                // limpiar input y error
                input.value = '';
                errorSpan.textContent = '';

            } else {
                // ERROR desde backend (usuario en uso, etc)
                errorSpan.textContent = data.msg;
            }
        })
        .catch(() => {
            errorSpan.textContent = 'Error de conexión';
        });
    }

});

function mostrarAlerta(msg, tipo = 'error') {
    const alerta = document.getElementById('alertaCustom');
    alerta.textContent = msg;
    alerta.className = `alerta-custom ${tipo}`;
    alerta.style.display = 'block';

    setTimeout(() => {
        alerta.style.display = 'none';
    }, 3000);
}

// Cada 5 segundos revisa si hay cambios en la lista de amigos
setInterval(() => {
    fetch('../Funcionalidades/obtener_amigos.php')
        .then(res => res.json())
        .then(amigos => {
            const contenedor = document.querySelector('.lista-amigos');
            if (!contenedor) return;

            // Crear un set de IDs actuales
            const idsActuales = new Set();
            amigos.forEach(a => idsActuales.add(a.id_usuario));

            // Eliminar amigos que ya no están
            contenedor.querySelectorAll('.perfil-horiz').forEach(div => {
                const id = parseInt(div.dataset.usuarioId);
                if (!idsActuales.has(id)) {
                    div.closest('.message').remove();
                }
            });

            // Actualizar o añadir amigos
            amigos.forEach(amigo => {
                let div = contenedor.querySelector(`[data-usuario-id="${amigo.id_usuario}"]`);
                if (div) {
                    // 🔹 Actualizar nombre
                    const nombre = div.querySelector('.perfil-nombre');
                    if (nombre.textContent !== amigo.Username) {
                        nombre.textContent = amigo.Username;
                    }

                    // 🔹 Actualizar foto
                    const img = div.querySelector('img.profile-pic');
                    const nuevaFoto = amigo.Pfp || '../../Recursos/mamiy.png';
                    if (img.src !== nuevaFoto && !img.src.endsWith(nuevaFoto)) {
                        img.src = nuevaFoto;
                    }

                    // 🔹 Actualizar estado
                    const dot = div.querySelector('.estado-dot');
                    const texto = div.querySelector('.estado-texto');

                    dot.classList.toggle('online', amigo.estado === 'Online');
                    dot.classList.toggle('offline', amigo.estado !== 'Online');

                    texto.textContent =
                        amigo.estado === 'Online' ? 'En línea' : 'Desconectado';
                }

                 else {
                    // Crear nuevo amigo
                    const div = document.createElement('div');
                    div.classList.add('message');
                    div.innerHTML = `
                        <div class="perfil-horiz" data-usuario-id="${amigo.id_usuario}">
                            <img src="${amigo.Pfp || '../../Recursos/mamiy.png'}" class="profile-pic fotoPerfil" alt="Foto de ${amigo.Username}">
                            <div class="perfil-info">
                                <p class="perfil-nombre">${amigo.Username}</p>
                                <div class="estado-usuario">
                                    <span class="estado-dot ${amigo.estado === 'Online' ? 'online' : 'offline'}"></span>
                                    <span class="estado-texto">${amigo.estado === 'Online' ? 'En línea' : 'Desconectado'}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    contenedor.appendChild(div);

                    // Aquí puedes añadir el listener para abrir modal del amigo
                    div.querySelector('.perfil-horiz').addEventListener('click', () => abrirPerfilAmigo(amigo.id_usuario));
                }
            });
        })
        .catch(err => console.error(err));
}, 5000);

setInterval(() => {
    fetch('../Funcionalidades/obtener_chats.php')
        .then(res => res.json())
        .then(chats => {
            const contenedor = document.querySelector('.lista-chats');
            if (!contenedor) return;

            contenedor.innerHTML = '';

            if (chats.length === 0) {
                contenedor.innerHTML = '<p class="sin-amigos">No tienes chats</p>';
                return;
            }

            chats.forEach(amigo => {
                const a = document.createElement('a');
                a.href = `chatprivado.php?usuario=${amigo.id_usuario}`;
                a.className = 'chat-amigo';

                a.innerHTML = `
                    <div class="perfil-horiz" data-usuario-id="${amigo.id_usuario}">
                        <img 
                            src="${amigo.Pfp || '../../Recursos/mamiy.png'}"
                            class="profile-pic"
                        >
                        <div class="perfil-info">
                            <p class="perfil-nombre">${amigo.Username}</p>
                            <div class="estado-usuario">
                                <span class="estado-dot ${amigo.estado === 'Online' ? 'online' : 'offline'}"></span>
                                <span class="estado-texto">
                                    ${amigo.estado === 'Online' ? 'En línea' : 'Desconectado'}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
                contenedor.appendChild(a);
            });
        });
}, 5000);

function abrirConfirmEliminar() {
    document.getElementById('confirmEliminarOverlay').style.display = 'flex';
}

function cerrarConfirmEliminar() {
    document.getElementById('confirmEliminarOverlay').style.display = 'none';
}

function aceptarConfirmEliminar() {
    document.getElementById('formEliminarAmigo').submit();
}

setInterval(() => {
    fetch('../Funcionalidades/obtener_solicitudes.php')
        .then(res => res.json())
        .then(solicitudes => {
            const contenedor = document.getElementById('listaSolicitudes');
            if (!contenedor) return;

            contenedor.innerHTML = '';

            if (solicitudes.length === 0) {
                contenedor.innerHTML = '<p>No hay solicitudes.</p>';
                return;
            }

            solicitudes.forEach(usuario => {
                const div = document.createElement('div');
                div.classList.add('message', 'solicitud');

                div.innerHTML = `
                    <div class="perfil-horiz">
                        <img src="${usuario.Pfp || '../../Recursos/mamiy.png'}" class="profile-pic">
                        <div class="perfil-info">
                            <p class="perfil-nombre">${usuario.Username}</p>
                            <div class="estado-usuario">
                                <span class="estado-dot ${usuario.estado === 'Online' ? 'online' : 'offline'}"></span>
                                <span class="estado-texto">
                                    ${usuario.estado === 'Online' ? 'En línea' : 'Desconectado'}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="acciones-solicitud">
                        <form method="POST">
                            <input type="hidden" name="aceptar_amigo" value="${usuario.id_amigo}">
                            <button class="btn-aceptar">✔</button>
                        </form>

                        <form method="POST">
                            <input type="hidden" name="rechazar_amigo" value="${usuario.id_amigo}">
                            <button class="btn-rechazar">✖</button>
                        </form>
                    </div>
                `;

                contenedor.appendChild(div);
            });
        })
        .catch(err => console.error(err));
}, 5000); // cada 5 segundos

function abrirPerfilUsuario(idUsuario, esAmigo) {
    const url = esAmigo
        ? `../paginas/perfilAmigo.php?id=${idUsuario}`
        : `../paginas/perfil_usuario.php?id=${idUsuario}`;

    fetch(url)
        .then(res => res.text())
        .then(html => {
            document.getElementById('perfilAmigoContenido').innerHTML = html;
            document.getElementById('perfilAmigoModal').style.display = 'flex';
        });
}


document.addEventListener('click', function (e) {
    const perfil = e.target.closest('.post-user');
    if (!perfil) return;

    const idUsuario = perfil.dataset.usuarioId;

    fetch(`../paginas/perfil_usuario.php?id=${idUsuario}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('perfilAmigoContenido').innerHTML = html;
            document.getElementById('perfilAmigoModal').style.display = 'flex';
        });
});

document.addEventListener('click', e => {
    const grupo = e.target.closest('.grupo-header');
    if (!grupo) return;

    const idGrupo = grupo.dataset.grupoId;

    fetch(`../paginas/perfil_grupo.php?id=${idGrupo}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('modalGrupoContenido').innerHTML = html;
            document.getElementById('modalPerfilGrupo').classList.remove('hidden');
        });
});

document.addEventListener('click', e => {
    const fotoGrupo = e.target.closest('#fotoGrupo');
    if (!fotoGrupo) return;

    grupoIdActivo = fotoGrupo.dataset.grupoId; // 🔥 GUARDAMOS ID
    document.getElementById('inputFotoGrupo').click();
});

document.addEventListener('change', e => {
    if (e.target.id !== 'inputFotoGrupo') return;

    const file = e.target.files[0];
    if (!file) return;

    const fotoGrupo = document.getElementById('fotoGrupo');
    const idGrupo = fotoGrupo.dataset.grupoId;

    const formData = new FormData();
    formData.append('foto', file);
    formData.append('id_grupo', idGrupo);

    fetch('../Funcionalidades/subir_foto_grupo.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {

            const nuevaFoto = data.nuevaFoto + '?t=' + Date.now();

            // 🔹 MODAL DEL GRUPO
            fotoGrupo.src = nuevaFoto;

            // 🔹 HEADER DEL CHAT
            document.querySelectorAll(
                `.grupo-foto-header[data-grupo-id="${idGrupo}"]`
            ).forEach(img => img.src = nuevaFoto);

            // 🔥 SIDEBAR
            document.querySelectorAll(
                `.grupo-foto-sidebar[data-grupo-id="${idGrupo}"]`
            ).forEach(img => img.src = nuevaFoto);
        }
        else {
            alert(data.msg);
        }
    });
});

function obtenerGrupoIdActivo() {
    return document
        .getElementById('perfilModalContent')
        ?.dataset.grupoId;
}


document.addEventListener('click', e => {

    /* EDITAR NOMBRE */
    if (e.target.closest('#btnEditarNombre')) {
        document.getElementById('grupoNombreTexto').classList.add('hidden');
        document.getElementById('btnEditarNombre').classList.add('hidden');

        const input = document.getElementById('inputGrupoNombre');
        input.classList.remove('hidden');
        input.focus();
    }

    /* EDITAR DESCRIPCIÓN */
    if (e.target.closest('#btnEditarDescripcion')) {
        document.getElementById('grupoDescripcionTexto').classList.add('hidden');
        document.getElementById('btnEditarDescripcion').classList.add('hidden');

        const textarea = document.getElementById('inputGrupoDescripcion');
        textarea.classList.remove('hidden');
        textarea.focus();
    }
});
document.addEventListener('blur', e => {
    if (e.target.id === 'inputGrupoNombre') {
        guardarNombreGrupo();
    }
}, true);

document.addEventListener('keydown', e => {
    if (e.target.id === 'inputGrupoNombre' && e.key === 'Enter') {
        e.preventDefault();
        guardarNombreGrupo();
    }
});


function guardarNombreGrupo() {
    const input = document.getElementById('inputGrupoNombre');
    const nuevoNombre = input.value.trim();
    if (!nuevoNombre) return;

    fetch('../Funcionalidades/editar_grupo_ajax.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({
            campo: 'Nombre',
            valor: nuevoNombre,
            id_grupo: obtenerGrupoIdActivo()
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {
            document.getElementById('grupoNombreTexto').textContent = nuevoNombre;
        }
        input.classList.add('hidden');
        document.getElementById('grupoNombreTexto').classList.remove('hidden');
        document.getElementById('btnEditarNombre').classList.remove('hidden');
    });
}
document.addEventListener('click', function (e) {
    const modal = document.getElementById('perfilAmigoModal');
    const contenido = document.getElementById('perfilAmigoContenido');

    if (!modal || modal.style.display !== 'flex') return;

    // click fuera del contenido
    if (!contenido.contains(e.target)) {
        modal.style.display = 'none';
        contenido.innerHTML = '';
    }
});

document.addEventListener('blur', e => {
    if (e.target.id === 'inputGrupoDescripcion') {
        guardarDescripcionGrupo();
    }
}, true);

document.addEventListener('keydown', e => {
    if (e.target.id === 'inputGrupoNombre' && e.key === 'Enter') {
        e.preventDefault();
        guardarNombreGrupo();
    }

    if (e.target.id === 'inputGrupoNombre' && e.key === 'Escape') {
        cancelarEdicionNombreGrupo();
    }
});

/* Abrir edición */
document.addEventListener('click', e => {
    if (e.target.closest('#btnEditarNombre')) {
        const texto = document.getElementById('grupoNombreTexto');
        const input = document.getElementById('inputGrupoNombre');
        const btn = document.getElementById('btnEditarNombre');

        input.value = texto.textContent.trim();

        texto.classList.add('hidden');
        btn.classList.add('hidden');
        input.classList.remove('hidden');

        input.focus();
        input.select();
    }
});

/* Guardar con ENTER / Cancelar con ESC */
document.addEventListener('keydown', e => {
    if (e.target.id !== 'inputGrupoNombre') return;

    if (e.key === 'Enter') {
        e.preventDefault();
        guardarNombreGrupo();
    }

    if (e.key === 'Escape') {
        cancelarEdicionNombreGrupo();
    }
});

/* Guardar AJAX */
function guardarNombreGrupo() {
    const input = document.getElementById('inputGrupoNombre');
    const nuevoNombre = input.value.trim();

    if (!nuevoNombre) {
        cancelarEdicionNombreGrupo();
        return;
    }

    const idGrupo = document
        .querySelector('.perfilModalContent')
        .dataset.grupoId;

    fetch('../Funcionalidades/editar_grupo_ajax.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({
            campo: 'Nombre',
            valor: nuevoNombre,
            id_grupo: idGrupo
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {
            document.getElementById('grupoNombreTexto').textContent = nuevoNombre;
        }
        cerrarEdicionNombreGrupo();
    });
}
function cancelarEdicionNombreGrupo() {
    const input = document.getElementById('inputGrupoNombre');
    const texto = document.getElementById('grupoNombreTexto');
    const btn = document.getElementById('btnEditarNombre');

    input.classList.add('hidden');
    texto.classList.remove('hidden');
    btn.classList.remove('hidden');
}

/* Cancelar */
function cancelarEdicionNombreGrupo() {
    cerrarEdicionNombreGrupo();
}

/* Cerrar edición (una sola función 💅) */
function cerrarEdicionNombreGrupo() {
    const input = document.getElementById('inputGrupoNombre');
    const texto = document.getElementById('grupoNombreTexto');
    const btn = document.getElementById('btnEditarNombre');

    input.classList.add('hidden');
    texto.classList.remove('hidden');
    btn.classList.remove('hidden');
}


document.addEventListener('click', e => {

    /* Abrir edición */
    if (e.target.closest('#editarDescripcionGrupoBtn')) {
        const texto = document.getElementById('grupoDescripcionTexto');
        const textarea = document.getElementById('grupoDescripcionInput');
        const footer = document.getElementById('grupoBioFooter');
        const actions = document.getElementById('grupoBioActions');

        textarea.value = texto.textContent.trim() === 'Sin descripción'
            ? ''
            : texto.textContent.trim();

        texto.style.display = 'none';
        textarea.style.display = 'block';
        footer.style.display = 'block';
        actions.style.display = 'flex';

        actualizarContadorGrupo();
        textarea.focus();
    }

    /* Guardar */
    if (e.target.closest('#guardarDescripcionGrupoBtn')) {
        guardarDescripcionGrupo();
    }

    /* Cancelar */
    if (e.target.closest('#cancelarDescripcionGrupoBtn')) {
        cancelarEdicionDescripcionGrupo();
    }
});

/* contador */
document.addEventListener('input', e => {
    if (e.target.id === 'grupoDescripcionInput') {
        actualizarContadorGrupo();
    }
});

function actualizarContadorGrupo() {
    const textarea = document.getElementById('grupoDescripcionInput');
    document.getElementById('grupoBioContador').textContent = textarea.value.length;
}

/* guardar AJAX */
function guardarDescripcionGrupo() {
    const textarea = document.getElementById('grupoDescripcionInput');
    const nuevaDescripcion = textarea.value.trim();

    const contenedor = document.querySelector('.perfilModalContent');
    const idGrupo = contenedor.dataset.grupoId;

    fetch('../Funcionalidades/editar_grupo_ajax.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({
            campo: 'Descripcion',
            valor: nuevaDescripcion,
            id_grupo: idGrupo
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {
            document.getElementById('grupoDescripcionTexto').textContent =
                nuevaDescripcion || 'Sin descripción';
        } else {
            console.error(data.msg);
        }
        cerrarEdicionDescripcionGrupo();
    })
    .catch(err => console.error(err));
}

/* cancelar */
function cancelarEdicionDescripcionGrupo() {
    cerrarEdicionDescripcionGrupo();
}

function cerrarEdicionDescripcionGrupo() {
    document.getElementById('grupoDescripcionTexto').style.display = 'block';
    document.getElementById('grupoDescripcionInput').style.display = 'none';
    document.getElementById('grupoBioFooter').style.display = 'none';
    document.getElementById('grupoBioActions').style.display = 'none';
}

// cerrar modal
document.addEventListener('click', e => {
    if (
        e.target.classList.contains('cerrar-modalgrupo') ||
        e.target.id === 'modalPerfilGrupo'
    ) {
        document.getElementById('modalPerfilGrupo').classList.add('hidden');
    }
});
document.addEventListener('click', e => {
    if (e.target.id === 'btnMostrarInvitar') {
        document.getElementById('invitarBox').classList.toggle('hidden');
    }
});

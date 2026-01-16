<script>
/* obtenemos las referencias a elementos del DOM */
const fileInput = document.getElementById('file-upload'); // input para subir archivos
const chatForm = document.getElementById('chat-form'); // formulario del chat
const chatMessages = document.querySelector('.chat-messages'); // contenedor donde salen los mensajes

/* seleccion de archivos al hacer clic en un boton */
document.getElementById('file-upload-btn').addEventListener('click', e => {
    e.preventDefault(); // evitamos el comportamiento por defecto del boton enviar
    fileInput.click(); // simulamos un clic en el input de archivos
});

/* funcion para actualizar los mensajes con AJAX */
function actualizarMensajes() {

    fetch('grupos.php?grupo=<?= $grupoActivoId ?>&ajax=1') // llamada get a php para obtener mensajes
        .then(res => res.text()) // convertir la respuesta en texto
        .then(data => {
            chatMessages.innerHTML = data; // actualizar el contenedor con los nuevos mensajes
            // scroll al ultimo mensaje con pequeño delay
            setTimeout(() => {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }, 50);
        })
        .catch(err => console.error('Error al actualizar mensajes:', err)); 
    
/* hacer scroll al ultimo mensaje al cargar la pagina */
window.addEventListener('load', () => {
    setTimeout(() => {
        chatMessages.scrollTop = chatMessages.scrollHeight; // llevar el scroll al final
    }, 50);
});

/* Enviar archivo automáticamente al seleccionarlo */
fileInput.addEventListener('change', function() {
    if(this.files.length > 0){ // si hay archivos seleccionados
        const formData = new FormData(chatForm); // creamos FormData con los datos del formulario
        formData.append('enviar_mensaje','1'); // añadir indicador de envio
        fetch('grupos.php?grupo=<?= $grupoActivoId ?>', {
            method: 'POST',
            body: formData, // enviar archivo y demas datos
            headers: {'X-Requested-With':'XMLHttpRequest'} // indicamos que es una peticion AJAX
        })
        .then(res => res.text())
        .then(() => {
            fileInput.value = ''; // limpiar input de archivos
            actualizarMensajes(); // actualizamos mensajes para mostrar el nuevo archivo
        })
        .catch(err => console.error('Error al subir archivo:', err)); 
});

/* enviar mensaje de texto desde el formulario */
chatForm.addEventListener('submit', e => {
    e.preventDefault(); // evitar que el formulario se recargue
    const formData = new FormData(chatForm); // crear FormData con los datos del formulario
    formData.append('enviar_mensaje','1'); // añadimos el indicador de envío
    fetch('grupos.php?grupo=<?= $grupoActivoId ?>', {
        method: 'POST',
        body: formData, // enviar datos del mensaje
        headers: {'X-Requested-With':'XMLHttpRequest'} // indicamos que es peticion AJAX
    })
    .then(res => res.text())
    .then(() => {
        document.getElementById('mensaje-input').value = ''; // limpiamos el input de texto
        actualizarMensajes(); // actualizamos mensajes para mostrar el nuevo mensaje
    })
    .catch(err => console.error('Error al enviar mensaje:', err));
});

/* actualizar automaticamente los mensajes cada 5 segundos */
setInterval(actualizarMensajes, 5000);

</script>

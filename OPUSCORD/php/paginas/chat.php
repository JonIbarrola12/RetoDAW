<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPUSCORD - Chat</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <?php require_once('../conexion.php');?>
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <h2>OPUSCORD</h2>
        <!-- Navegación arriba -->
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php"><button>Feed</button></a></li>
                    <li><a href="mensajes.php"><button>Mensajes</button></a></li>
                </ul>
            </nav>
            <!-- Botones de sesión abajo -->
             <?php
                    if (isset($_SESSION['Usuario'])) {
                        echo'
                        <p>Usuario' . htmlspecialchars($_SESSION['Usuario']) .'</p>
                        <a href="../Login/Logout.php"><button class="login-btn">Cerrar Sesión</button></a>
                        ';
                    }else{
                        echo'
                         <div class="auth-buttons">
                            <a href="../Login/Index.php"><button class="login-btn">Iniciar Sesión</button></a>
                            <a href="../Login/registrarse.php"><button class="register-btn">Registrarse</button></a>
                        </div>
                        ';
                    }
           ?>
    </aside>

    <main class="main-content">
        <section class="chat">
            <h3 id="chat-title">Chat</h3>
            <div class="chat-messages" id="chat-messages"></div>
            <div class="chat-input">
                <input type="text" id="message-input" placeholder="Escribe un mensaje...">
                <button id="send-btn">Enviar</button>
            </div>
        </section>
    </main>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const usuarioActual = urlParams.get('user') || 'Desconocido';
    const chatTitle = document.getElementById('chat-title');
    const chatMessages = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');

    chatTitle.textContent = usuarioActual;

    // Mensajes simulados del otro usuario
    const mensajesPorUsuario = {
        'usuario1': ['Hola, ¿cómo estás?', 'Todo bien, ¿y tú?'],
        'usuario2': ['Hey, ¿listo para el proyecto?', 'Sí, empecemos ahora.'],
        'usuario3': ['Buenas tardes!', 'Hola, ¿qué tal?']
    };

    // Función para agregar mensajes al chat
    const agregarMensaje = (texto, tipo) => {
        const div = document.createElement('div');
        div.className = `message ${tipo}`; // tipo: 'otro' o 'propio'
        div.textContent = texto;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };

    // Cargar mensajes iniciales (del otro usuario)
    (mensajesPorUsuario[usuarioActual] || []).forEach(msg => agregarMensaje(msg, 'otro'));

    // Enviar mensaje
    sendBtn.addEventListener('click', () => {
        const texto = messageInput.value.trim();
        if (!texto) return;
        agregarMensaje(texto, 'propio');

        // Guardar en objeto simulado
        if (!mensajesPorUsuario[usuarioActual]) mensajesPorUsuario[usuarioActual] = [];
        mensajesPorUsuario[usuarioActual].push(texto);

        messageInput.value = '';
    });
</script>
</body>
</html>
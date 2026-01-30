<!doctype html>
<html lang="en">
    <head>
        <title>Title</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
        <link href="../../css/estilos.css" rel="stylesheet"/>
    </head>
<body class="formregistro">
    <br> <br> <br>
    <div class="container d-flex justify-content-center align-items-center " >
        <div class="card shadow p-4 formuregistro" style="width: 22rem;">
            <h3 class="text-center mb-4">Iniciar sesión</h3>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Ingresa tu usuario" required>
                </div>

                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="Ingresa tu contraseña" required>
                    <?php
                    session_start();
                    if (isset($_SESSION['error_login'])) {
                        echo '<div class="text-danger mt-2">' . $_SESSION['error_login'] . '</div>';
                        unset($_SESSION['error_login']);
                    }
                    ?>
                    <a href="registrarse.php" id="registroTexto"><p id="registroTexto">Registrarse</p></a>
                </div>
                

                <button type="submit" class="btn btn-purple w-100 mb-2 mt-3 boton-sesion">Ingresar</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


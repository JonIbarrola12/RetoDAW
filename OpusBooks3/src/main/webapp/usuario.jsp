<%@ page language="java" contentType="text/html; charset=UTF-8"
    pageEncoding="UTF-8"%>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>OpusBooks - Registro de Usuario</title>
</head>
<body>

<h1 style="text-align:center;">OpusBooks - Registro de Usuario</h1>

<%
String error = (String) request.getAttribute("error");
if (error != null) {
%>
    <p style="color:red; text-align:center;"><%= error %></p>
<%
}
%>

<form method="post" action="SrvAltaUsuario"
      style="width:450px;margin:0 auto;"
      onsubmit="return validarFormulario();">

    <div style="margin-bottom:10px;">
        <label>Nombre:</label>
        <input type="text" id="nombre" name="nombre" style="float:right;width:220px;">
    </div>

    <div style="margin-bottom:10px;">
        <label>Primer apellido:</label>
        <input type="text" id="apellido1" name="apellido1" style="float:right;width:220px;">
    </div>

    <div style="margin-bottom:10px;">
        <label>Segundo apellido:</label>
        <input type="text" id="apellido2" name="apellido2" style="float:right;width:220px;">
    </div>

    <div style="margin-bottom:10px;">
        <label>DNI:</label>
        <input type="text" id="dni" name="dni" placeholder="12345678A"
               style="float:right;width:220px;">
    </div>

    <div style="margin-bottom:10px;">
        <label>Dirección:</label>
        <input type="text" id="direccion" name="direccion"
               style="float:right;width:220px;">
    </div>

    <div style="margin-bottom:10px;">
        <label>Fecha nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
               style="float:right;width:220px;">
    </div>

    <div style="margin-bottom:10px;">
        <label>Email:</label>
        <input type="email" id="email" name="email"
               style="float:right;width:220px;">
    </div>

    <div style="margin-bottom:10px;">
        <label>Usuario:</label>
        <input type="text" id="usuario" name="usuario"
               style="float:right;width:220px;">
    </div>

    <div style="margin-bottom:10px;">
        <label>Clave:</label>
        <input type="password" id="contrasena" name="contrasena"
               style="float:right;width:220px;">
    </div>

    <div style="margin-bottom:20px;">
        <label>Confirmar clave:</label>
        <input type="password" id="rep_contrasena" name="rep_contrasena"
               style="float:right;width:220px;">
    </div>

    <div style="text-align:center;clear:both;">
        <button type="submit">Alta Usuario</button>
    </div>

</form>

<script>
function validarFormulario() {

    let correcto = true;
    document.querySelectorAll(".error").forEach(e => e.remove());

    function error(input, mensaje) {
        const span = document.createElement("span");
        span.className = "error";
        span.style.color = "red";
        span.style.fontSize = "12px";
        span.innerText = mensaje;
        input.parentNode.appendChild(span);
        correcto = false;
    }

    const nombre = document.getElementById("nombre");
    const apellido1 = document.getElementById("apellido1");
    const dni = document.getElementById("dni");
    const direccion = document.getElementById("direccion");
    const fecha = document.getElementById("fecha_nacimiento");
    const email = document.getElementById("email");
    const usuario = document.getElementById("usuario");
    const pass = document.getElementById("contrasena");
    const repPass = document.getElementById("rep_contrasena");

    if (nombre.value.trim() === "")
        error(nombre, "Campo obligatorio");

    if (apellido1.value.trim() === "")
        error(apellido1, "Campo obligatorio");

    if (direccion.value.trim() === "")
        error(direccion, "Campo obligatorio");

    // Fecha obligatoria
    if (fecha.value === "")
        error(fecha, "Debe introducir la fecha de nacimiento");

    // DNI
    const dniRegex = /^[0-9]{8}[A-Za-z]$/;
    if (!dniRegex.test(dni.value))
        error(dni, "Formato DNI incorrecto");

    // Email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value))
        error(email, "Email incorrecto");

    // Usuario
    if (usuario.value.length < 4)
        error(usuario, "Mínimo 4 caracteres");

    // Contraseña
    if (pass.value.length < 6)
        error(pass, "Mínimo 6 caracteres");

    // Confirmación
    if (pass.value !== repPass.value)
        error(repPass, "Las contraseñas no coinciden");

    return correcto;
}
</script>

</body>
</html>

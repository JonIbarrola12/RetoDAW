<%@page contentType="text/html;charset=UTF-8" language="java" %>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>OpusBooks - Acceso</title>
    <style>
        .error { color: red; font-size: 0.9em; margin-top: 2px; }
    </style>
</head>
<body>

<h1 style="text-align: center; font-weight: bold;">
    OpusBooks - Acceso
</h1>

<form method="post" action="SrvLogin" style="text-align: center; margin-top: 40px;">

    <div style="margin-bottom: 12px;">
        <input 
            type="text" 
            name="usuario"
            placeholder="Christian..." 
            value="<%= request.getParameter("usuario") != null ? request.getParameter("usuario") : "" %>"
            style="width:220px; padding:5px;">
        <br>
        <span class="error"><%= request.getAttribute("errorUsuario") != null ? request.getAttribute("errorUsuario") : "" %></span>
    </div>

    <div style="margin-bottom: 12px;">
        <input 
            type="password" 
            name="contrasena"
            placeholder="contraseña123..." 
            style="width:220px; padding:5px;">
        <br>
        <span class="error"><%= request.getAttribute("errorContrasena") != null ? request.getAttribute("errorContrasena") : "" %></span>
    </div>

    <div style="margin-bottom: 12px;">
        <span class="error"><%= request.getAttribute("errorLogin") != null ? request.getAttribute("errorLogin") : "" %></span>
    </div>

    <div style="margin-bottom: 20px;">
        <button type="submit" style="padding:5px 20px;">Entrar</button>
    </div>

    <div>
        Si no estás registrado, 
        <a href="usuario.jsp">regístrate aquí</a>
    </div>

</form>

</body>
</html>

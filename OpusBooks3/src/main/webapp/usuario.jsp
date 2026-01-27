<%@page import="opusbooks.beans.Usuario"%>
<%
	String user = (String)session.getAttribute("user");	
	Usuario usuario = null;
%>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Librería Virtual - Registro</title>
</head>
<body>
    <h1 style="text-align: center; font-weight: bold;">
        Libreria Virtual: Registro de usuario
    </h1>

    <form style="width: 450px; margin: 0 auto;" method="post" action="SrvAltaUsuario">

        <div style="margin-bottom: 12px; clear: both;">
            <label>Nombre:</label>
            <input type="text" name="nombre" placeholder="Nombre" style="float:right; width:220px;">
        </div>

        <div style="margin-bottom: 12px; clear: both;">
            <label>Primer apellido:</label>
            <input type="text" name="apellido1" placeholder="Primer apellido" style="float:right; width:220px;">
        </div>

        <div style="margin-bottom: 12px; clear: both;">
            <label>Segundo apellido:</label>
            <input type="text" name="apellido2" placeholder="Segundo apellido" style="float:right; width:220px;">
        </div>

        <div style="margin-bottom: 12px; clear: both;">
            <label>DNI:</label>
            <input type="text" name="dni" placeholder="12345678A" style="float:right; width:220px;">
        </div>

        <div style="margin-bottom: 12px; clear: both;">
            <label>Dirección:</label>
            <input type="text" name="direccion" placeholder="Dirección" style="float:right; width:220px;">
        </div>

        <div style="margin-bottom: 12px; clear: both;">
		    <label>Fecha de nacimiento:</label>
		    <input 
		        type="date" 
		        name="fecha_nacimiento"
		        style="float:right; width:220px;">
		</div>

        <div style="margin-bottom: 12px; clear: both;">
            <label>Email:</label>
            <input type="email" name="email" placeholder="ejemplo@email.com" style="float:right; width:220px;">
        </div>

        <div style="margin-bottom: 12px; clear: both;">
            <label>Usuario:</label>
            <input type="text" name="usuario" placeholder="Usuario" style="float:right; width:220px;">
        </div>

        <div style="margin-bottom: 12px; clear: both;">
            <label>Clave:</label>
            <input type="password" name="contrasena" placeholder="Contraseña" style="float:right; width:220px;">
        </div>

        <div style="margin-bottom: 20px; clear: both;">
            <label>Confirmar clave:</label>
            <input type="password" name="rep_contrasena" placeholder="Repetir contraseña" style="float:right; width:220px;">
        </div>

        <div style="text-align:center; clear:both;">
    <button type="submit">Alta Usuario</button>
</div>

    </form>

</body>
</html>
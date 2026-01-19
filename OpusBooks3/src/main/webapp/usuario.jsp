<%@page import="opusbooks.beans.Usuario"%>
<%
	String user = (String)session.getAttribute("user");	
	Usuario usuario = null;
%>
<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Registrarse</title>
</head>
<body>
	<form name=form1 method=post>
		DNI: <input type=text name=dni>
		<br>
		Nombre: <input type=text name=nombre size=30>
		<br>
		Primer Apellido: <input type=text name=apellido1 size=30>
		<br>
		Segundo Apellido: <input type=text name=apellido2 size=30>
		<br>
		Fecha de Nacimiento: <input type=date name=edad>
		<br>
		Dirección: <input type=text name=direccion size=30>
		<br>
		Email: <input type=email name=direccion size=30>
		<br>
		Nombre de Usuario: <input type=text name=usuario size=30>
		<br>
		Contraseña: <input type=text name=contrasena size=30>
		<br>
		
		<input type=button value=Grabar onclick="">
	</form>
</body>
</html>
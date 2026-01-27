<%@page import="opusbooks.beans.Libro"%>
<%@page import="java.util.List"%>
<%
    String user = (String) session.getAttribute("user");
    if (user == null) {
        response.sendRedirect("login.jsp");
        return;
    }

    List<Libro> libros = (List<Libro>) request.getAttribute("libros");
%>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>OpusBooks - Menú</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f9f9f9;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #333;
            color: #fff;
            padding: 15px 30px;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        .usuario {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .boton-cerrar {
            padding: 5px 15px;
            cursor: pointer;
            background: #e74c3c;
            border: none;
            color: #fff;
            border-radius: 4px;
        }

        main {
            padding: 20px 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        thead {
            background: #333;
            color: #fff;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        tbody tr:hover {
            background: #f2f2f2;
        }

        .comprar-btn {
            margin-top: 20px;
            text-align: center;
        }

        .comprar-btn button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background: #27ae60;
            border: none;
            color: #fff;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<header>
    <h1>OpusBooks</h1>
    <div class="usuario">
        <span>Usuario: <b><%= user %></b></span>
        <form method="post" action="SrvLogout" style="display:inline;">
            <button type="submit" class="boton-cerrar">Cerrar sesión</button>
        </form>
    </div>
</header>

<main>

<table>
    <thead>
        <tr>
            <th>ISBN</th>
            <th>Título</th>
            <th>Autor</th>
            <th>Editorial</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
    <%
        if (libros != null && !libros.isEmpty()) {
            for (Libro libro : libros) {
    %>
        <tr>
            <td><%= libro.getIsbn() %></td>
            <td><%= libro.getTitulo() %></td>
            <td><%= libro.getNombreAutor() %></td>
            <td><%= libro.getNombreEditorial() %></td>
            <td><%= libro.getNombreCategoria() %></td>
            <td>$<%= libro.getPrecio() %></td>
            <td><%= libro.getStock() %></td>
        </tr>
    <%
            }
        } else {
    %>
        <tr>
            <td colspan="7">No hay libros disponibles</td>
        </tr>
    <%
        }
    %>
    </tbody>
</table>

<div class="comprar-btn">
    <form method="get" action="SrvCompra">
        <button type="submit">Comprar libros</button>
    </form>
</div>

</main>

</body>
</html>

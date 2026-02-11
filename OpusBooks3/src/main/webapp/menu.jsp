<%@page import="opusbooks.beans.Libro"%>
<%@page import="java.util.List"%>
<%@page import="opusbooks.beans.Autor"%>
<%@page import="opusbooks.beans.Editorial"%>
<%@page import="opusbooks.beans.Categoria"%>
<%@page import="opusbooks.beans.Poblacion"%>
<%
    String user = (String) session.getAttribute("user");
    if (user == null) {
        response.sendRedirect("login.jsp");
        return;
    }

    List<Libro> libros = (List<Libro>) request.getAttribute("libros");

    String autorSeleccionado = (String) request.getAttribute("autorSeleccionado");
    String editorialSeleccionada = (String) request.getAttribute("editorialSeleccionada");
    String categoriaSeleccionada = (String) request.getAttribute("categoriaSeleccionada");
    String poblacionSeleccionada = (String) request.getAttribute("poblacionSeleccionada");
    String fechaInicioSeleccionada = (String) request.getAttribute("fechaInicioSeleccionada");
    String fechaFinSeleccionada = (String) request.getAttribute("fechaFinSeleccionada");
%>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>OpusBooks - Menú</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; background:#f9f9f9; }
        header { display:flex; justify-content:space-between; align-items:center; background:#333; color:#fff; padding:15px 30px; }
        header h1 { margin:0; font-size:24px; }
        .usuario { display:flex; align-items:center; gap:15px; }
        .boton-cerrar { padding:6px 16px; cursor:pointer; background:#e74c3c; border:none; color:#fff; border-radius:4px; }
        main { padding:20px 30px; }
        .filtro-form { background:#fff; padding:15px 20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); margin-bottom:25px; display:flex; gap:15px; align-items:center; flex-wrap:wrap; }
        .filtro-form label { font-weight:bold; font-size:14px; }
        .filtro-form select, .filtro-form input { padding:6px 10px; border-radius:4px; border:1px solid #ccc; min-width:150px; }
        .filtro-form button { padding:8px 18px; background:#2980b9; border:none; color:#fff; border-radius:4px; cursor:pointer; font-size:14px; }
        .filtro-form button:hover { background:#1f6391; }
        table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        thead { background:#333; color:#fff; }
        th, td { padding:10px; text-align:center; border-bottom:1px solid #ddd; }
        tbody tr:hover { background:#f2f2f2; }
        .comprar-btn { margin-top:20px; text-align:center; }
        .comprar-btn button { padding:10px 20px; font-size:16px; cursor:pointer; background:#27ae60; border:none; color:#fff; border-radius:4px; }
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

    <!-- Filtro -->
    <form method="get" action="SrvMenu" class="filtro-form">
        <label>Autor:</label>
        <select name="autor">
            <option value="">Todos</option>
            <%
                List<Autor> autores = (List<Autor>) request.getAttribute("autores");
                if (autores != null) {
                    for (Autor a : autores) {
            %>
                <option value="<%= a.getId_autor() %>" <%= (autorSeleccionado != null && autorSeleccionado.equals(String.valueOf(a.getId_autor()))) ? "selected" : "" %>>
                    <%= a.getNombre() %>
                </option>
            <%
                    }
                }
            %>
        </select>

        <label>Editorial:</label>
        <select name="editorial">
            <option value="">Todas</option>
            <%
                List<Editorial> editoriales = (List<Editorial>) request.getAttribute("editoriales");
                if (editoriales != null) {
                    for (Editorial e : editoriales) {
            %>
                <option value="<%= e.getId_editorial() %>" <%= (editorialSeleccionada != null && editorialSeleccionada.equals(String.valueOf(e.getId_editorial()))) ? "selected" : "" %>>
                    <%= e.getNombre() %>
                </option>
            <%
                    }
                }
            %>
        </select>

        <label>Categoría:</label>
        <select name="categoria">
            <option value="">Todas</option>
            <%
                List<Categoria> categorias = (List<Categoria>) request.getAttribute("categorias");
                if (categorias != null) {
                    for (Categoria c : categorias) {
            %>
                <option value="<%= c.getId_categoria() %>" <%= (categoriaSeleccionada != null && categoriaSeleccionada.equals(String.valueOf(c.getId_categoria()))) ? "selected" : "" %>>
                    <%= c.getNombre() %>
                </option>
            <%
                    }
                }
            %>
        </select>

        <label>Población:</label>
        <select name="poblacion">
            <option value="">Todas</option>
            <%
                List<Poblacion> poblaciones = (List<Poblacion>) request.getAttribute("poblaciones");
                if (poblaciones != null) {
                    for (Poblacion p : poblaciones) {
            %>
                <option value="<%= p.getId_poblacion() %>" <%= (poblacionSeleccionada != null && poblacionSeleccionada.equals(String.valueOf(p.getId_poblacion()))) ? "selected" : "" %>>
                    <%= p.getNom_poblacion() %>
                </option>
            <%
                    }
                }
            %>
        </select>

        <label>Fecha Edición Desde:</label>
        <input type="date" name="fechaInicio" value="<%= fechaInicioSeleccionada != null ? fechaInicioSeleccionada : "" %>">

        <label>Hasta:</label>
        <input type="date" name="fechaFin" value="<%= fechaFinSeleccionada != null ? fechaFinSeleccionada : "" %>">

        <button type="submit">Filtrar</button>
    </form>

    <!-- Tabla -->
    <table>
        <thead>
            <tr>
                <th>ISBN</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Editorial</th>
                <th>Categoría</th>
                <th>Población</th>
                <th>Fecha Edición</th>
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
                <td><%= libro.getNombrePoblacion() != null ? libro.getNombrePoblacion() : "-" %></td>
                <td><%= libro.getFechaEdicion() %></td>
                <td><%= libro.getPrecio() %>&euro;</td>
                <td><%= libro.getStock() %></td>
            </tr>
            <%
                    }
                } else {
            %>
            <tr>
                <td colspan="9">No hay libros disponibles</td>
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

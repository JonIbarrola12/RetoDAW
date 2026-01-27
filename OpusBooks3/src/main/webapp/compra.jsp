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
    <title>OpusBooks - Comprar libros</title>

    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f9f9f9; }
        header { display: flex; justify-content: space-between; align-items: center; background: #333; color: #fff; padding: 15px 30px; }
        header h1 { margin: 0; font-size: 24px; }
        main { padding: 20px 30px; }

        .buscador {
            width: 300px;
            padding: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .libros-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .libro-card {
            background: #fff;
            padding: 15px;
            width: 230px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .libro-card h3 { margin: 0 0 10px 0; font-size: 18px; }
        .libro-card p { margin: 4px 0; font-size: 14px; }

        .acciones {
            margin-top: 10px;
        }

        .acciones input[type="number"] {
            width: 60px;
        }

        .finalizar {
            text-align: center;
            margin-top: 30px;
        }

        .finalizar button {
            padding: 12px 25px;
            font-size: 16px;
            background: #27ae60;
            border: none;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>

    <script>
        function filtrarLibros() {
            let filtro = document.getElementById("buscador").value.toLowerCase();
            let libros = document.getElementsByClassName("libro-card");

            for (let libro of libros) {
                let titulo = libro.getAttribute("data-titulo");
                libro.style.display = titulo.includes(filtro) ? "block" : "none";
            }
        }
    </script>
</head>
<body>

<header>
    <h1>OpusBooks - Comprar</h1>
    <span>Usuario: <b><%= user %></b></span>
</header>

<main>

    <!-- Buscador -->
    <input type="text" id="buscador" class="buscador"
           placeholder="Buscar libro..." onkeyup="filtrarLibros()">

    <form method="post" action="FinalizarCompra">

        <div class="libros-container">
            <%
                if (libros != null && !libros.isEmpty()) {
                    for (Libro libro : libros) {
            %>
            <div class="libro-card" data-titulo="<%= libro.getTitulo().toLowerCase() %>">
                <h3><%= libro.getTitulo() %></h3>
                <p><b>ISBN:</b> <%= libro.getIsbn() %></p>
                <p><b>Precio:</b> $<%= libro.getPrecio() %></p>
                <p><b>Stock:</b> <%= libro.getStock() %></p>

                <div class="acciones">
                    <label>
                        <input type="checkbox" name="librosSeleccionados"
                               value="<%= libro.getIsbn() %>">
                        Seleccionar
                    </label>
                    <br><br>
                    Cantidad:
                    <input type="number"
                           name="cantidad_<%= libro.getIsbn() %>"
                           min="1"
                           max="<%= libro.getStock() %>"
                           value="1">
                </div>
            </div>
            <%
                    }
                } else {
            %>
            <p>No hay libros disponibles.</p>
            <%
                }
            %>
        </div>

        <div class="finalizar">
            <button type="submit">Finalizar compra</button>
        </div>

    </form>

</main>

</body>
</html>

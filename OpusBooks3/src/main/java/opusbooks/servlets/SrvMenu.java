package opusbooks.servlets;

import java.io.IOException;
import java.util.List;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import opusbooks.bd.BdOperaciones;
import opusbooks.beans.Libro;
import opusbooks.beans.Autor;
import opusbooks.beans.Editorial;
import opusbooks.beans.Categoria;
import java.sql.Date;
import opusbooks.beans.Poblacion;


public class SrvMenu extends HttpServlet {
    private static final long serialVersionUID = 1L;

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        // 1️⃣ Comprobar sesión
        String user = (String) request.getSession().getAttribute("user");
        if (user == null) {
            response.sendRedirect("login.jsp");
            return;
        }
        request.setAttribute("user", user);

        // 2️⃣ Leer parámetros de filtro
        String autorId = request.getParameter("autor");
        String editorialId = request.getParameter("editorial");
        String categoriaId = request.getParameter("categoria");
        String poblacionId = request.getParameter("poblacion");
        String fechaInicioStr = request.getParameter("fechaInicio");
        String fechaFinStr = request.getParameter("fechaFin");

        Date fechaInicio = null;
        Date fechaFin = null;

        try {
            if (fechaInicioStr != null && !fechaInicioStr.isEmpty()) {
                fechaInicio = Date.valueOf(fechaInicioStr);
            }
            if (fechaFinStr != null && !fechaFinStr.isEmpty()) {
                fechaFin = Date.valueOf(fechaFinStr);
            }
        } catch (IllegalArgumentException e) {
            e.printStackTrace();
        }


        BdOperaciones bd = new BdOperaciones(getServletContext());

        if (!bd.abrirConexion()) {
            response.getWriter().println("Error al conectar con la BD");
            return;
        }

        // 3️⃣ Cargar libros filtrados
        List<Libro> libros = bd.getLibrosFiltrados(autorId, editorialId, categoriaId, poblacionId, fechaInicio, fechaFin);
        request.setAttribute("libros", libros);

        // 4️⃣ Cargar listas para los select
        List<Autor> autores = bd.getAutores();
        List<Editorial> editoriales = bd.getEditoriales();
        List<Categoria> categorias = bd.getCategorias();

        request.setAttribute("autores", autores);
        request.setAttribute("editoriales", editoriales);
        request.setAttribute("categorias", categorias);
        
        List<Poblacion> poblaciones = bd.getPoblaciones();
        request.setAttribute("poblaciones", poblaciones);

        bd.cerrarConexion();

        // 5️⃣ Mantener seleccionadas las opciones
        request.setAttribute("autorSeleccionado", autorId);
        request.setAttribute("editorialSeleccionada", editorialId);
        request.setAttribute("categoriaSeleccionada", categoriaId);
        request.setAttribute("poblacionSeleccionada", poblacionId);
        request.setAttribute("fechaInicioSeleccionada", fechaInicioStr);
        request.setAttribute("fechaFinSeleccionada", fechaFinStr);


        // 6️⃣ Enviar al JSP
        request.getRequestDispatcher("/menu.jsp").forward(request, response);
    }
}

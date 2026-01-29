package opusbooks.servlets;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import javax.servlet.RequestDispatcher;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

import opusbooks.bd.BdOperaciones;
import opusbooks.beans.DatosCompra;
import opusbooks.beans.ItemCompra;
import opusbooks.beans.Libro;

/**
 * Servlet implementation class SrvCompra
 */
//@WebServlet("/SrvCompra")
public class SrvCompra extends HttpServlet {
	private static final long serialVersionUID = 1L;
       
	// GET: cargar libros y mostrar compra.jsp
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("user") == null) {
            response.sendRedirect("login.jsp");
            return;
        }

        BdOperaciones bd = new BdOperaciones(getServletContext());
        if (!bd.abrirConexion()) {
            response.getWriter().println("Error al conectar con la base de datos.");
            return;
        }

        // Obtener todos los libros
        List<Libro> libros = bd.getLibros();
        bd.cerrarConexion();

        // Pasar la lista al JSP
        request.setAttribute("libros", libros);

        RequestDispatcher rd = request.getRequestDispatcher("compra.jsp");
        rd.forward(request, response);
    }

    // POST: procesar libros seleccionados y enviar a resumenCompra.jsp
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("user") == null) {
            response.sendRedirect("login.jsp");
            return;
        }

        String[] librosSeleccionados = request.getParameterValues("librosSeleccionados");

        if (librosSeleccionados == null || librosSeleccionados.length == 0) {
            request.setAttribute("errorCompra", "No seleccionaste ningún libro.");
            
            // Volvemos a cargar los libros
            BdOperaciones bd = new BdOperaciones(getServletContext());
            if (!bd.abrirConexion()) {
                response.getWriter().println("Error al conectar con la base de datos.");
                return;
            }
            List<Libro> libros = bd.getLibros();
            bd.cerrarConexion();
            
            request.setAttribute("libros", libros); // Muy importante
            request.getRequestDispatcher("compra.jsp").forward(request, response);
            return;
        }

        BdOperaciones bd = new BdOperaciones(getServletContext());
        if (!bd.abrirConexion()) {
            response.getWriter().println("Error al conectar con la base de datos.");
            return;
        }

        List<ItemCompra> itemsCompra = new ArrayList<>();

        for (String isbn : librosSeleccionados) {

        	System.out.println("Procesando ISBN: " + isbn);
            Libro libro = bd.getLibroPorIsbn(isbn);
            if (libro == null) {
                System.out.println("No se encontró el libro con ISBN: " + isbn);
                continue;
            }
            System.out.println("Libro encontrado: " + libro.getTitulo());

            String cantidadStr = request.getParameter("cantidad_" + isbn);
            int cantidad = 1;

            if (cantidadStr != null) {
                try {
                    cantidad = Integer.parseInt(cantidadStr);
                } catch (NumberFormatException e) {
                    cantidad = 1;
                }
            }

            if (cantidad < 1) cantidad = 1;
            if (cantidad > libro.getStock()) cantidad = libro.getStock();

            ItemCompra item = new ItemCompra(libro, cantidad);
            itemsCompra.add(item);
        }

        // Guardar en sesión
        request.getSession().setAttribute("itemsCompra", itemsCompra);

        // Enviar al resumen
        request.setAttribute("itemsCompra", itemsCompra);
        request.getRequestDispatcher("resumenCompra.jsp").forward(request, response);
    }

}

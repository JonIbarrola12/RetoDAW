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
            RequestDispatcher rd = request.getRequestDispatcher("compra.jsp");
            rd.forward(request, response);
            return;
        }

        BdOperaciones bd = new BdOperaciones(getServletContext());
        if (!bd.abrirConexion()) {
            response.getWriter().println("Error al conectar con la base de datos.");
            return;
        }

        List<Libro> librosCompra = new ArrayList<>();

        for (String isbn : librosSeleccionados) {
            Libro libro = bd.getLibroPorIsbn(isbn);
            if (libro != null) {
                // obtener la cantidad ingresada en el formulario
                String cantidadStr = request.getParameter("cantidad_" + isbn);
                int cantidad = 1;
                try {
                    cantidad = Integer.parseInt(cantidadStr);
                    if (cantidad > libro.getStock()) cantidad = libro.getStock();
                    if (cantidad < 1) cantidad = 1;
                } catch (NumberFormatException e) {
                    cantidad = 1;
                }

                // Guardamos la cantidad seleccionada en el libro
                //libro.setCantidadSeleccionada(cantidad);

                librosCompra.add(libro);
            }
        }

        bd.cerrarConexion();

        // Guardamos la lista de libros seleccionados en el request para mostrar en resumenCompra.jsp
        request.setAttribute("librosCompra", librosCompra);

        RequestDispatcher rd = request.getRequestDispatcher("resumenCompra.jsp");
        rd.forward(request, response);
    }

}

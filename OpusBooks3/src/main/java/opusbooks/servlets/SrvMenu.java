package opusbooks.servlets;

import java.io.IOException;
import java.util.List;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import opusbooks.bd.BdOperaciones;
import opusbooks.beans.Libro;

/**
 * Servlet implementation class SrvMenu
 */
//@WebServlet("/SrvMenu")
public class SrvMenu extends HttpServlet {
	private static final long serialVersionUID = 1L;
       
	 @Override
	    protected void doGet(HttpServletRequest request, HttpServletResponse response)
	            throws ServletException, IOException {

	        BdOperaciones bd = new BdOperaciones(getServletContext());

	        if (!bd.abrirConexion()) {
	            response.getWriter().println("Error al conectar con la BD");
	            return;
	        }

	        List<Libro> libros = bd.getLibros();
	        bd.cerrarConexion();

	        System.out.println("Libros cargados: " + libros.size());

	        request.setAttribute("libros", libros);
	        request.getRequestDispatcher("/menu.jsp")
	               .forward(request, response);
	    }

}

package opusbooks.servlets;

import java.io.IOException;
import java.util.List;

import javax.servlet.RequestDispatcher;
import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

import opusbooks.bd.BdOperaciones;
import opusbooks.beans.Usuario;

/**
 * Servlet implementation class SrvUsuarios
 */
//@WebServlet("/SrvUsuarios")
public class SrvUsuarios extends HttpServlet {
	private static final long serialVersionUID = 1L;
       
	public void service (HttpServletRequest request,HttpServletResponse response)
			throws IOException,ServletException
			{				
				HttpSession sesion = request.getSession(false);	
				if (sesion!=null)
				{			
					BdOperaciones bdOperaciones = new BdOperaciones(getServletContext());
					bdOperaciones.abrirConexion();
					List<Usuario> usuarios = bdOperaciones.getUsuarios();
					bdOperaciones.cerrarConexion();
					request.setAttribute("usuarios",usuarios);
					ServletContext ct = getServletContext();
					RequestDispatcher rd = ct.getRequestDispatcher("/usuario.jsp");
					rd.forward(request,response);
				}
				else
				{
					response.sendRedirect("login.jsp");
				}
			}

}

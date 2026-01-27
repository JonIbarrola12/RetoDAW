package opusbooks.servlets;

import java.io.IOException;

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
 * Servlet implementation class SrvProcesarUsuario
 */
//@WebServlet("/SrvProcesarUsuario")
public class SrvProcesarUsuario extends HttpServlet {
	private static final long serialVersionUID = 1L;
       
	public void service (HttpServletRequest request,HttpServletResponse response)
			throws IOException,ServletException
			{				
				HttpSession sesion = request.getSession(false);	
				if (sesion!=null)
				{			
					String dni = request.getParameter("dni");
					BdOperaciones bdOperaciones = new BdOperaciones(getServletContext());
					bdOperaciones.abrirConexion();
					Usuario usuario = bdOperaciones.getUsuario(dni);
					bdOperaciones.cerrarConexion();
					request.setAttribute("usuario",usuario);
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

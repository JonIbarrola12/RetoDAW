package opusbooks.servlets;

import java.io.IOException;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import opusbooks.bd.BdOperaciones;
import opusbooks.beans.Usuario;

public class SrvAltaUsuario extends HttpServlet {
    private static final long serialVersionUID = 1L;

    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws IOException, ServletException {

        Usuario usuario = new Usuario();
        usuario.setNombre(request.getParameter("nombre"));
        usuario.setApellido1(request.getParameter("apellido1"));
        usuario.setApellido2(request.getParameter("apellido2"));
        usuario.setDni(request.getParameter("dni"));
        usuario.setDireccion(request.getParameter("direccion"));
        usuario.setEmail(request.getParameter("email"));
        usuario.setUsuario(request.getParameter("usuario"));
        usuario.setContrasena(request.getParameter("contrasena"));

        String fechaStr = request.getParameter("fecha_nacimiento");
        if (fechaStr != null && !fechaStr.isEmpty()) {
            usuario.setFecha_nacimiento(java.sql.Date.valueOf(fechaStr));
        }

        // BdOperaciones inicializa properties automáticamente
        BdOperaciones bd = new BdOperaciones(getServletContext());
        if (!bd.abrirConexion()) {
            response.getWriter().println("Error al conectar con la base de datos.");
            return;
        }

        boolean ok = bd.insertarUsuario(usuario);
        bd.cerrarConexion();

        if (ok) {
            response.sendRedirect("login.jsp");
        } else {
            
        }
    }
}

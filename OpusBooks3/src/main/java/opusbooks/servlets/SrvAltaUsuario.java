package opusbooks.servlets;

import java.io.IOException;
import java.sql.SQLIntegrityConstraintViolationException;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import opusbooks.bd.BdOperaciones;
import opusbooks.beans.Usuario;

public class SrvAltaUsuario extends HttpServlet {
    private static final long serialVersionUID = 1L;

    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        // Validación servidor: fecha obligatoria
        String fecha = request.getParameter("fecha_nacimiento");
        if (fecha == null || fecha.isEmpty()) {
            request.setAttribute("error", "Debe introducir la fecha de nacimiento");
            request.getRequestDispatcher("usuario.jsp").forward(request, response);
            return;
        }

        Usuario usuario = new Usuario();
        usuario.setNombre(request.getParameter("nombre"));
        usuario.setApellido1(request.getParameter("apellido1"));
        usuario.setApellido2(request.getParameter("apellido2"));
        usuario.setDni(request.getParameter("dni"));
        usuario.setDireccion(request.getParameter("direccion"));
        usuario.setEmail(request.getParameter("email"));
        usuario.setUsuario(request.getParameter("usuario"));
        usuario.setContrasena(request.getParameter("contrasena"));
        usuario.setFecha_nacimiento(java.sql.Date.valueOf(fecha));

        BdOperaciones bd = new BdOperaciones(getServletContext());

        if (!bd.abrirConexion()) {
            request.setAttribute("error", "Error al conectar con la base de datos");
            request.getRequestDispatcher("usuario.jsp").forward(request, response);
            return;
        }

        boolean ok;
        try {
            ok = bd.insertarUsuario(usuario);
        } catch (Exception e) {

            if (e.getCause() instanceof SQLIntegrityConstraintViolationException) {
                request.setAttribute("error",
                        "El DNI o el usuario ya existen en el sistema");
            } else {
                request.setAttribute("error",
                        "Error al registrar el usuario");
            }

            bd.cerrarConexion();
            request.getRequestDispatcher("usuario.jsp").forward(request, response);
            return;
        }

        bd.cerrarConexion();

        if (ok) {
            response.sendRedirect("login.jsp");
        } else {
            request.setAttribute("error", "No se pudo registrar el usuario");
            request.getRequestDispatcher("usuario.jsp").forward(request, response);
        }
    }
}

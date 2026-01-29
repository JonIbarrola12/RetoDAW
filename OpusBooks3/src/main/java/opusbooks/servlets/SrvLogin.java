package opusbooks.servlets;

import java.io.IOException;
import javax.servlet.*;
import javax.servlet.http.*;
import opusbooks.bd.BdBase;
import opusbooks.bd.BdOperaciones;
import opusbooks.config.Configuracion;
import opusbooks.config.GestorConfiguracion;

public class SrvLogin extends HttpServlet {
    private static final long serialVersionUID = 1L;

    public void init(ServletConfig config) throws ServletException {
        super.init(config);
        String fichero = config.getInitParameter("fichero_propiedades");
        String rutaReal = getServletContext().getRealPath("/WEB-INF/" + fichero);
        boolean cargaCorrecta = GestorConfiguracion.cargarConfiguracion(rutaReal);
        if (!cargaCorrecta) {
            System.out.println("Fichero de configuración NO cargado correctamente");
        } else {
            BdBase.inicializarParametrosConexion(Configuracion.getInstancia());
            System.out.println("Configuración cargada correctamente: " + Configuracion.getInstancia().getUrl());
        }
    }

    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws IOException, ServletException {

        String user = request.getParameter("usuario");
        String password = request.getParameter("contrasena");

        // Validaciones básicas antes de consultar DB
        boolean hayError = false;
        if (user == null || user.isEmpty()) {
            request.setAttribute("errorUsuario", "Debe ingresar un usuario");
            hayError = true;
        }
        if (password == null || password.isEmpty()) {
            request.setAttribute("errorContrasena", "Debe ingresar la contraseña");
            hayError = true;
        }

        if (hayError) {
            RequestDispatcher rd = request.getRequestDispatcher("/login.jsp");
            rd.forward(request, response);
            return;
        }

        BdOperaciones bd = new BdOperaciones(getServletContext());
        if (!bd.abrirConexion()) {
            response.getWriter().println("Error al conectar con la base de datos.");
            return;
        }

        String dni = bd.obtenerDniUsuario(user, password); // <-- aquí llamas a la función
        bd.cerrarConexion();

        if (dni != null) {
            HttpSession sesion = request.getSession(true);
            sesion.setAttribute("user", user);  // nombre de usuario
            sesion.setAttribute("dni", dni);    // DNI real para las compras
            response.sendRedirect("SrvMenu");
        } else {
            request.setAttribute("errorLogin", "Usuario o contraseña incorrectos");
            RequestDispatcher rd = request.getRequestDispatcher("/login.jsp");
            rd.forward(request, response);
        }
    }
}

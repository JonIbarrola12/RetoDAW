package opusbooks.servlets;

import java.io.IOException;
import java.sql.Date;
import java.time.LocalDate;
import java.util.List;
import javax.servlet.ServletException;
import javax.servlet.http.*;

import opusbooks.bd.BdOperaciones;
import opusbooks.beans.*;

//@WebServlet("/SrvConfirmarCompra")
public class SrvConfirmarCompra extends HttpServlet {
    private static final long serialVersionUID = 1L;

    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("user") == null) {
            response.sendRedirect("login.jsp");
            return;
        }

        // Recoger items de compra de la sesión
        List<ItemCompra> items = (List<ItemCompra>) session.getAttribute("itemsCompra");
        if (items == null || items.isEmpty()) {
            response.sendRedirect("SrvCompra");
            return;
        }

        // Recoger datos de tarjeta (solo para validación)
        String numero = request.getParameter("numero");
        String nombre = request.getParameter("nombre");
        String vencimiento = request.getParameter("vencimiento");
        String cvv = request.getParameter("cvv");

        if (numero == null || nombre == null || vencimiento == null || cvv == null) {
            response.sendRedirect("resumenCompra.jsp");
            return;
        }

        BdOperaciones bd = new BdOperaciones(getServletContext());
        if (!bd.abrirConexion()) {
            response.getWriter().println("Error al conectar con la base de datos.");
            return;
        }

        try {
        	bd.abrirTransaccion();
            // 1️⃣ Insertar Compra
            Compra compra = new Compra();
            compra.setFecha_compra(Date.valueOf(LocalDate.now()));
            compra.setDni((String) session.getAttribute("dni")); 
            int idCompra = bd.insertarCompra(compra); // bd debe devolver id auto_incremen
            compra.setId_compra(idCompra);

            // 2️⃣ Insertar datos de cada libro
            for (ItemCompra item : items) {
                bd.insertarDatosCompra(new DatosCompra(compra.getId_compra(),
                        item.getLibro().getIsbn(), item.getCantidad()));

                // 3️⃣ Actualizar stock
                int nuevoStock = item.getLibro().getStock() - item.getCantidad();
                if (nuevoStock < 0) nuevoStock = 0;
                bd.actualizarStock(item.getLibro().getIsbn(), nuevoStock);
            }
            bd.hacerCommit();
            // Limpiar carrito
            session.removeAttribute("itemsCompra");

        } catch (Exception e) {
        	bd.hacerRollback();
            e.printStackTrace();
            response.getWriter().println("Error procesando la compra.");
            return;
        } finally {
            bd.cerrarConexion();
        }

        // Redirigir a página de éxito
        response.sendRedirect("compraExitosa.jsp");
    }
}

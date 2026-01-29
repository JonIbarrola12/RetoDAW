<%@ page contentType="text/html; charset=UTF-8" %>
<%@ page import="java.util.List" %>
<%@ page import="opusbooks.beans.ItemCompra" %>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>OpusBooks - Resumen de compra</title>
<style>
    body { font-family: Arial, sans-serif; margin: 0; background: #f9f9f9; }
    header { display: flex; justify-content: space-between; align-items: center; background: #333; color: #fff; padding: 15px 30px; }
    header h1 { margin: 0; font-size: 24px; }

    main { padding: 20px 30px; max-width: 800px; margin: auto; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table th, table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    table th { background: #eee; }

    h3 { margin-top: 20px; }

    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
    .error { color: red; font-size: 13px; display: none; margin-top: 3px; }

    .btn {
        display: inline-block;
        padding: 8px 16px;
        font-size: 14px;
        border: none;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }
    .btn-confirmar { background: #27ae60; }
    .btn-volver { background: #c0392b; }

    .btn-container {
        text-align: center;
        margin-top: 15px;
    }
</style>

<script>
function validarTarjeta(event) {
    let correcto = true;

    const numero = document.getElementById("numero");
    const nombre = document.getElementById("nombre");
    const vencimiento = document.getElementById("vencimiento");
    const cvv = document.getElementById("cvv");

    document.querySelectorAll(".error").forEach(e => e.style.display = "none");

    if (!/^\d{16}$/.test(numero.value)) {
        document.getElementById("error-numero").style.display = "block";
        correcto = false;
    }

    if (nombre.value.trim() === "") {
        document.getElementById("error-nombre").style.display = "block";
        correcto = false;
    }

    if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(vencimiento.value)) {
        document.getElementById("error-vencimiento").style.display = "block";
        correcto = false;
    }

    if (!/^\d{3}$/.test(cvv.value)) {
        document.getElementById("error-cvv").style.display = "block";
        correcto = false;
    }

    if (!correcto) {
        event.preventDefault(); // evita el envío si hay errores
    }
}
</script>
</head>
<body>
<header>
    <h1>OpusBooks - Resumen de compra</h1>
    <span>Usuario: <b><%= session.getAttribute("user") %></b></span>
</header>

<main>
<%
    List<ItemCompra> items = (List<ItemCompra>) request.getAttribute("itemsCompra");
    double total = 0;
%>

<h2>Resumen de la compra</h2>
<table>
    <tr>
        <th>Libro</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
    </tr>
<%
    if (items != null) {
        for (ItemCompra item : items) {
            double subtotal = item.getSubtotal();
            total += subtotal;
%>
    <tr>
        <td><%= item.getLibro().getTitulo() %></td>
        <td><%= item.getLibro().getPrecio() %> €</td>
        <td><%= item.getCantidad() %></td>
        <td><%= subtotal %> €</td>
    </tr>
<%
        }
    }
%>
</table>
<h3>Total: <%= total %> €</h3>

<h2>Datos de la tarjeta</h2>
<form method="post" action="SrvConfirmarCompra" onsubmit="validarTarjeta(event)">
    <div class="form-group">
        <label for="numero">Número de tarjeta</label>
        <input type="text" id="numero" name="numero" maxlength="16" placeholder="1234123412341234">
        <div class="error" id="error-numero">Número de tarjeta inválido</div>
    </div>

    <div class="form-group">
        <label for="nombre">Nombre en la tarjeta</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre completo">
        <div class="error" id="error-nombre">Nombre requerido</div>
    </div>

    <div class="form-group">
        <label for="vencimiento">Fecha de vencimiento (MM/YY)</label>
        <input type="text" id="vencimiento" name="vencimiento" placeholder="MM/YY">
        <div class="error" id="error-vencimiento">Formato inválido</div>
    </div>

    <div class="form-group">
        <label for="cvv">CVV</label>
        <input type="text" id="cvv" name="cvv" maxlength="3" placeholder="123">
        <div class="error" id="error-cvv">CVV inválido</div>
    </div>

    <div class="btn-container">
        <button type="submit" class="btn btn-confirmar">Confirmar compra</button>
        <button type="button" class="btn btn-volver" onclick="window.location.href='SrvCompra'">Volver</button>
    </div>
</form>
</main>
</body>
</html>

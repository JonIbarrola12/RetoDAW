package opusbooks.beans;

import java.io.Serializable;

public class DatosCompra implements Serializable{
	
	private int id_compra;
	private String isbn;
	private int cantidad;
	
	public DatosCompra () {}

	public DatosCompra(int id_compra, String isbn, int cantidad) {
		super();
		this.id_compra = id_compra;
		this.isbn = isbn;
		this.cantidad = cantidad;
	}

	public int getId_compra() {
		return id_compra;
	}

	public void setId_compra(int id_compra) {
		this.id_compra = id_compra;
	}

	public String getIsbn() {
		return isbn;
	}

	public void setIsbn(String isbn) {
		this.isbn = isbn;
	}

	public int getCantidad() {
		return cantidad;
	}

	public void setCantidad(int cantidad) {
		this.cantidad = cantidad;
	}
	
}

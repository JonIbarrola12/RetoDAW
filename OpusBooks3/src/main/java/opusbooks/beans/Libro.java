package opusbooks.beans;

import java.io.Serializable;

public class Libro implements Serializable{
	
	private String isbn;
	private double precio;
	private int stock;
	private String titulo;
	private int id_autor;
	private int id_editorial;
	private int id_categoria;
	private String nombreAutor;
	private String nombreEditorial;
	private String nombreCategoria;
	
	public Libro() {}

	public Libro(String isbn, double precio, int stock, String titulo, int id_autor, int id_editorial,
			int id_categoria) {
		super();
		this.isbn = isbn;
		this.precio = precio;
		this.stock = stock;
		this.titulo = titulo;
		this.id_autor = id_autor;
		this.id_editorial = id_editorial;
		this.id_categoria = id_categoria;
	}

	public String getIsbn() {
		return isbn;
	}

	public void setIsbn(String isbn) {
		this.isbn = isbn;
	}

	public double getPrecio() {
		return precio;
	}

	public void setPrecio(double precio) {
		this.precio = precio;
	}

	public int getStock() {
		return stock;
	}

	public void setStock(int stock) {
		this.stock = stock;
	}

	public String getTitulo() {
		return titulo;
	}

	public void setTitulo(String titulo) {
		this.titulo = titulo;
	}

	public int getId_autor() {
		return id_autor;
	}

	public void setId_autor(int id_autor) {
		this.id_autor = id_autor;
	}

	public int getId_editorial() {
		return id_editorial;
	}

	public void setId_editorial(int id_editorial) {
		this.id_editorial = id_editorial;
	}

	public int getId_categoria() {
		return id_categoria;
	}

	public void setId_categoria(int id_categoria) {
		this.id_categoria = id_categoria;
	}
	public String getNombreAutor() {
	    return nombreAutor;
	}
	public void setNombreAutor(String nombreAutor) {
	    this.nombreAutor = nombreAutor;
	}

	public String getNombreEditorial() { return nombreEditorial; }
	public void setNombreEditorial(String nombreEditorial) { this.nombreEditorial = nombreEditorial; }

	public String getNombreCategoria() { return nombreCategoria; }
	public void setNombreCategoria(String nombreCategoria) { this.nombreCategoria = nombreCategoria; }
	
	
}

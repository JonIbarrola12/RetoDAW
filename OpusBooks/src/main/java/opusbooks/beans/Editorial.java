package opusbooks.beans;

import java.io.Serializable;

public class Editorial implements Serializable {
	
	private int id_editorial;
	private String nombre;
	
	public Editorial() {}

	public Editorial(int id_editorial, String nombre) {
		super();
		this.id_editorial = id_editorial;
		this.nombre = nombre;
	}

	public int getId_editorial() {
		return id_editorial;
	}

	public void setId_editorial(int id_editorial) {
		this.id_editorial = id_editorial;
	}

	public String getNombre() {
		return nombre;
	}

	public void setNombre(String nombre) {
		this.nombre = nombre;
	}
	
}

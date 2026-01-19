package opusbooks.beans;

import java.io.Serializable;
import java.sql.Date;

public class Compra implements Serializable {
	
	private int id_compra;
	private Date fecha_compra;
	private String dni;
	
	public Compra () {}

	public Compra(int id_compra, Date fecha_compra, String dni) {
		this.id_compra = id_compra;
		this.fecha_compra = fecha_compra;
		this.dni = dni;
	}

	public int getId_compra() {
		return id_compra;
	}

	public void setId_compra(int id_compra) {
		this.id_compra = id_compra;
	}

	public Date getFecha_compra() {
		return fecha_compra;
	}

	public void setFecha_compra(Date fecha_compra) {
		this.fecha_compra = fecha_compra;
	}

	public String getDni() {
		return dni;
	}

	public void setDni(String dni) {
		this.dni = dni;
	}
	
	
}

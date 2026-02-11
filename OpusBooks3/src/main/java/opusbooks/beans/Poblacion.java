package opusbooks.beans;

import java.io.Serializable;

public class Poblacion implements Serializable {

    private int id_poblacion;
    private String nom_poblacion;
    private int id_pais;

    public Poblacion() {}

    public Poblacion(int id_poblacion, String nom_poblacion, int id_pais) {
        this.id_poblacion = id_poblacion;
        this.nom_poblacion = nom_poblacion;
        this.id_pais = id_pais;
    }

    public int getId_poblacion() { return id_poblacion; }
    public void setId_poblacion(int id_poblacion) { this.id_poblacion = id_poblacion; }

    public String getNom_poblacion() { return nom_poblacion; }
    public void setNom_poblacion(String nom_poblacion) { this.nom_poblacion = nom_poblacion; }

    public int getId_pais() { return id_pais; }
    public void setId_pais(int id_pais) { this.id_pais = id_pais; }
}

package opusbooks.beans;

import java.io.Serializable;

public class Pais implements Serializable {

    private int id_pais;
    private String nom_pais;

    public Pais() {}

    public Pais(int id_pais, String nom_pais) {
        this.id_pais = id_pais;
        this.nom_pais = nom_pais;
    }

    public int getId_pais() { return id_pais; }
    public void setId_pais(int id_pais) { this.id_pais = id_pais; }

    public String getNom_pais() { return nom_pais; }
    public void setNom_pais(String nom_pais) { this.nom_pais = nom_pais; }
}

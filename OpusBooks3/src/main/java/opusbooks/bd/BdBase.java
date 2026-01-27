package opusbooks.bd;

import java.sql.Connection;
import java.sql.DriverManager;
import opusbooks.config.Configuracion;

public class BdBase {
    private static String DRIVER;
    private static String URL;
    private static String USER;
    private static String PASSWORD;

    protected Connection conexion;

    public static void inicializarParametrosConexion(Configuracion configuracion) {
        DRIVER = configuracion.getDriver();
        URL = configuracion.getUrl();
        USER = configuracion.getUser();
        PASSWORD = configuracion.getPassword();
    }

    protected BdBase() { }

    public boolean abrirConexion() {
        System.out.println("DRIVER = " + DRIVER);
        System.out.println("URL    = " + URL);
        try {
            Class.forName(DRIVER);
            conexion = DriverManager.getConnection(URL, USER, PASSWORD);
            return true;
        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
    }

    public boolean cerrarConexion() {
        try {
            if (conexion != null) conexion.close();
            return true;
        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
    }

    public boolean abrirTransaccion() {
        try {
            conexion.setAutoCommit(false);
            return true;
        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
    }

    public boolean hacerCommit() {
        try {
            conexion.commit();
            return true;
        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
    }

    public boolean hacerRollback() {
        try {
            conexion.rollback();
            return true;
        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
    }
}

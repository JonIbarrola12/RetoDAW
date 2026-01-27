package opusbooks.config;

public class Configuracion {
    private static Configuracion configuracion;

    private String driver;
    private String url;
    private String user;
    private String password;

    public static synchronized Configuracion getInstancia() {
        if (configuracion == null) {
            configuracion = new Configuracion();
        }
        return configuracion;
    }

    private Configuracion() { }

    public String getDriver() { return driver; }
    public void setDriver(String driver) { this.driver = driver; }

    public String getUrl() { return url; }
    public void setUrl(String url) { this.url = url; }

    public String getUser() { return user; }
    public void setUser(String user) { this.user = user; }

    public String getPassword() { return password; }
    public void setPassword(String password) { this.password = password; }
}

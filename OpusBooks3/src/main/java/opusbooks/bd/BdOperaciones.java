package opusbooks.bd;

import java.io.InputStream;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Properties;

import javax.servlet.ServletContext;

import opusbooks.beans.Libro;
import opusbooks.beans.Usuario;
import opusbooks.config.Configuracion;

public class BdOperaciones extends BdBase {

    // Constructor que carga el properties automáticamente
    public BdOperaciones(ServletContext context) {
        super();
        try {
            String ruta = context.getInitParameter("fichero_propiedades");
            InputStream is = context.getResourceAsStream(ruta);
            if (is == null) throw new RuntimeException("No se encontró el fichero: " + ruta);

            Properties prop = new Properties();
            prop.load(is);

            Configuracion config = Configuracion.getInstancia();
            config.setDriver(prop.getProperty("driver"));
            config.setUrl(prop.getProperty("url"));
            config.setUser(prop.getProperty("user"));
            config.setPassword(prop.getProperty("password"));

            BdBase.inicializarParametrosConexion(config);
            System.out.println("Propiedades de BD cargadas correctamente");
        } catch (Exception e) {
            e.printStackTrace();
            throw new RuntimeException("Error al inicializar propiedades de BD", e);
        }
    }

    public boolean validarUsuario(String user, String password) {
        boolean correcto = false;
        String sql = "SELECT 1 FROM usuarios WHERE usuario = ? AND contrasena = ?";
        try (PreparedStatement ps = conexion.prepareStatement(sql)) {
            ps.setString(1, user);
            ps.setString(2, password);
            try (ResultSet rs = ps.executeQuery()) {
                correcto = rs.next();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return correcto;
    }

    public boolean insertarUsuario(Usuario usuario) {
        String sql = "INSERT INTO usuarios (dni,nombre,apellido1,apellido2,direccion,fecha_nacimiento,email,usuario,contrasena) " +
                     "VALUES (?,?,?,?,?,?,?,?,?)";
        try (PreparedStatement ps = conexion.prepareStatement(sql)) {
            ps.setString(1, usuario.getDni());
            ps.setString(2, usuario.getNombre());
            ps.setString(3, usuario.getApellido1());
            ps.setString(4, usuario.getApellido2());
            ps.setString(5, usuario.getDireccion());
            ps.setDate(6, usuario.getFecha_nacimiento());
            ps.setString(7, usuario.getEmail());
            ps.setString(8, usuario.getUsuario());
            ps.setString(9, usuario.getContrasena());
            ps.executeUpdate();
            return true;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }

    public List<Usuario> getUsuarios() {
        List<Usuario> usuarios = new ArrayList<>();
        String sql = "SELECT dni,nombre,apellido1,apellido2,direccion,fecha_nacimiento,email,usuario FROM usuarios";
        try (Statement stmt = conexion.createStatement(); ResultSet rs = stmt.executeQuery(sql)) {
            while (rs.next()) {
                Usuario u = new Usuario();
                u.setDni(rs.getString("dni"));
                u.setNombre(rs.getString("nombre"));
                u.setApellido1(rs.getString("apellido1"));
                u.setApellido2(rs.getString("apellido2"));
                u.setDireccion(rs.getString("direccion"));
                u.setFecha_nacimiento(rs.getDate("fecha_nacimiento"));
                u.setEmail(rs.getString("email"));
                u.setUsuario(rs.getString("usuario"));
                usuarios.add(u);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return usuarios;
    }

    public Usuario getUsuario(String dni) {
        Usuario usuario = null;
        String sql = "SELECT dni,nombre,apellido1,apellido2,direccion,fecha_nacimiento,email,usuario FROM usuarios WHERE dni=?";
        try (PreparedStatement ps = conexion.prepareStatement(sql)) {
            ps.setString(1, dni);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    usuario = new Usuario();
                    usuario.setDni(rs.getString("dni"));
                    usuario.setNombre(rs.getString("nombre"));
                    usuario.setApellido1(rs.getString("apellido1"));
                    usuario.setApellido2(rs.getString("apellido2"));
                    usuario.setDireccion(rs.getString("direccion"));
                    usuario.setFecha_nacimiento(rs.getDate("fecha_nacimiento"));
                    usuario.setEmail(rs.getString("email"));
                    usuario.setUsuario(rs.getString("usuario"));
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return usuario;
    }
    
    public List<Libro> getLibros() {
        List<Libro> libros = new ArrayList<>();
        try {
        	String sql =
        		    "SELECT l.isbn, l.titulo, l.precio, l.stock, " +
        		    "CONCAT(a.nombre, ' ', a.apellidos) AS autor, " +
        		    "e.nombre AS editorial, " +
        		    "c.nombre AS categoria " +
        		    "FROM libros l " +
        		    "JOIN autores a ON l.id_autor = a.id_autor " +
        		    "JOIN editoriales e ON l.id_editorial = e.id_editorial " +
        		    "JOIN categorias c ON l.id_categoria = c.id_categoria";
            Statement stmt = conexion.createStatement();
            ResultSet rs = stmt.executeQuery(sql);
            while (rs.next()) {
            	Libro libro = new Libro();
            	libro.setIsbn(rs.getString("isbn"));
            	libro.setTitulo(rs.getString("titulo"));
            	libro.setPrecio(rs.getDouble("precio"));
            	libro.setStock(rs.getInt("stock"));

            	libro.setNombreAutor(rs.getString("autor"));
            	libro.setNombreEditorial(rs.getString("editorial"));
            	libro.setNombreCategoria(rs.getString("categoria"));

            	libros.add(libro);
            }
            rs.close();
            stmt.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return libros;
    }
    public Libro getLibroPorIsbn(String isbn) {
        Libro libro = null;

        String sql = "SELECT l.isbn, l.titulo, l.precio, l.stock, " +
                     "CONCAT(a.nombre, ' ', a.apellido1) AS autor, " +
                     "e.nombre AS editorial, " +
                     "c.nombre AS categoria " +
                     "FROM libros l " +
                     "JOIN autores a ON l.id_autor = a.id_autor " +
                     "JOIN editoriales e ON l.id_editorial = e.id_editorial " +
                     "JOIN categorias c ON l.id_categoria = c.id_categoria " +
                     "WHERE l.isbn = ?";

        try (PreparedStatement ps = conexion.prepareStatement(sql)) {
            ps.setString(1, isbn);
            ResultSet rs = ps.executeQuery();

            if (rs.next()) {
                libro = new Libro();
                libro.setIsbn(rs.getString("isbn"));
                libro.setTitulo(rs.getString("titulo"));
                libro.setPrecio(rs.getDouble("precio"));
                libro.setStock(rs.getInt("stock"));
                libro.setNombreAutor(rs.getString("autor"));       // necesitarías setter en Libro
                libro.setNombreEditorial(rs.getString("editorial")); 
                libro.setNombreCategoria(rs.getString("categoria"));
            }

            rs.close();
        } catch (Exception e) {
            e.printStackTrace();
        }

        return libro;
    }


}

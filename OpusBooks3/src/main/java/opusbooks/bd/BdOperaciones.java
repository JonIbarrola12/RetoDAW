package opusbooks.bd;

import java.io.InputStream;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Properties;

import javax.servlet.ServletContext;

import opusbooks.beans.Autor;
import opusbooks.beans.Categoria;
import opusbooks.beans.Editorial;
import opusbooks.beans.Libro;
import opusbooks.beans.Pais;
import opusbooks.beans.Poblacion;
import opusbooks.beans.Usuario;
import opusbooks.config.Configuracion;

public class BdOperaciones extends BdBase {

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

    public String obtenerDniUsuario(String usuario, String contrasena) {
        String dni = null;
        String sql = "SELECT dni FROM usuarios WHERE usuario=? AND contrasena=?";
        try (PreparedStatement ps = conexion.prepareStatement(sql)) {
            ps.setString(1, usuario);
            ps.setString(2, contrasena);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    dni = rs.getString("dni");
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return dni;
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
            String sql = "SELECT l.isbn, l.titulo, l.precio, l.stock, l.fecha_edicion, " +
                         "CONCAT(a.nombre, ' ', a.apellidos) AS autor, " +
                         "e.nombre AS editorial, " +
                         "c.nombre AS categoria, " +
                         "p.nom_poblacion AS poblacion " +
                         "FROM libros l " +
                         "JOIN autores a ON l.id_autor = a.id_autor " +
                         "JOIN editoriales e ON l.id_editorial = e.id_editorial " +
                         "JOIN categorias c ON l.id_categoria = c.id_categoria " +
                         "LEFT JOIN poblacion p ON l.id_poblacion = p.id_poblacion";
            Statement stmt = conexion.createStatement();
            ResultSet rs = stmt.executeQuery(sql);
            while (rs.next()) {
                Libro libro = new Libro();
                libro.setIsbn(rs.getString("isbn"));
                libro.setTitulo(rs.getString("titulo"));
                libro.setPrecio(rs.getDouble("precio"));
                libro.setStock(rs.getInt("stock"));
                libro.setFechaEdicion(rs.getDate("fecha_edicion"));
                libro.setNombreAutor(rs.getString("autor"));
                libro.setNombreEditorial(rs.getString("editorial"));
                libro.setNombreCategoria(rs.getString("categoria"));
                libro.setNombrePoblacion(rs.getString("poblacion"));
                libros.add(libro);
            }
            rs.close();
            stmt.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return libros;
    }

    public List<Libro> getLibrosFiltrados(String autorId, String editorialId, String categoriaId, String poblacionId, Date fechaInicio, Date fechaFin) {
        List<Libro> lista = new ArrayList<>();
        try {
            String sql = "SELECT l.*, a.nombre AS nombreAutor, e.nombre AS nombreEditorial, c.nombre AS nombreCategoria, p.nom_poblacion " +
                         "FROM libros l " +
                         "JOIN autores a ON l.id_autor = a.id_autor " +
                         "JOIN editoriales e ON l.id_editorial = e.id_editorial " +
                         "JOIN categorias c ON l.id_categoria = c.id_categoria " +
                         "LEFT JOIN poblacion p ON l.id_poblacion = p.id_poblacion " +
                         "WHERE 1=1 ";

            if (autorId != null && !autorId.isEmpty()) sql += " AND l.id_autor = " + autorId;
            if (editorialId != null && !editorialId.isEmpty()) sql += " AND l.id_editorial = " + editorialId;
            if (categoriaId != null && !categoriaId.isEmpty()) sql += " AND l.id_categoria = " + categoriaId;
            if (poblacionId != null && !poblacionId.isEmpty()) sql += " AND l.id_poblacion = " + poblacionId;
            if (fechaInicio != null) sql += " AND l.fecha_edicion >= ?";
            if (fechaFin != null) sql += " AND l.fecha_edicion <= ?";

            PreparedStatement pst = conexion.prepareStatement(sql);

            int index = 1;
            if (fechaInicio != null) pst.setDate(index++, fechaInicio);
            if (fechaFin != null) pst.setDate(index++, fechaFin);

            ResultSet rs = pst.executeQuery();
            while (rs.next()) {
                Libro l = new Libro();
                l.setIsbn(rs.getString("isbn"));
                l.setTitulo(rs.getString("titulo"));
                l.setPrecio(rs.getDouble("precio"));
                l.setStock(rs.getInt("stock"));
                l.setId_autor(rs.getInt("id_autor"));
                l.setId_editorial(rs.getInt("id_editorial"));
                l.setId_categoria(rs.getInt("id_categoria"));
                l.setIdPoblacion(rs.getInt("id_poblacion"));
                l.setFechaEdicion(rs.getDate("fecha_edicion"));
                l.setNombreAutor(rs.getString("nombreAutor"));
                l.setNombreEditorial(rs.getString("nombreEditorial"));
                l.setNombreCategoria(rs.getString("nombreCategoria"));
                l.setNombrePoblacion(rs.getString("nom_poblacion"));
                lista.add(l);
            }
            rs.close();
            pst.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return lista;
    }

    public List<Autor> getAutores() {
        List<Autor> lista = new ArrayList<>();
        try {
            String sql = "SELECT id_autor, nombre, apellidos FROM autores";
            Statement stmt = conexion.createStatement();
            ResultSet rs = stmt.executeQuery(sql);
            while (rs.next()) {
                Autor a = new Autor();
                a.setId_autor(rs.getInt("id_autor"));
                a.setNombre(rs.getString("nombre"));
                a.setApellidos(rs.getString("apellidos"));
                lista.add(a);
            }
            rs.close();
            stmt.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return lista;
    }

    public List<Editorial> getEditoriales() {
        List<Editorial> lista = new ArrayList<>();
        try {
            String sql = "SELECT id_editorial, nombre FROM editoriales";
            Statement stmt = conexion.createStatement();
            ResultSet rs = stmt.executeQuery(sql);
            while (rs.next()) {
                Editorial e = new Editorial();
                e.setId_editorial(rs.getInt("id_editorial"));
                e.setNombre(rs.getString("nombre"));
                lista.add(e);
            }
            rs.close();
            stmt.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return lista;
    }

    public List<Categoria> getCategorias() {
        List<Categoria> lista = new ArrayList<>();
        try {
            String sql = "SELECT id_categoria, nombre FROM categorias";
            Statement stmt = conexion.createStatement();
            ResultSet rs = stmt.executeQuery(sql);
            while (rs.next()) {
                Categoria c = new Categoria();
                c.setId_categoria(rs.getInt("id_categoria"));
                c.setNombre(rs.getString("nombre"));
                lista.add(c);
            }
            rs.close();
            stmt.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return lista;
    }

    public List<Poblacion> getPoblaciones() {
        List<Poblacion> lista = new ArrayList<>();
        try {
            String sql = "SELECT id_poblacion, nom_poblacion, id_pais FROM poblacion";
            Statement stmt = conexion.createStatement();
            ResultSet rs = stmt.executeQuery(sql);
            while (rs.next()) {
                Poblacion p = new Poblacion();
                p.setId_poblacion(rs.getInt("id_poblacion"));
                p.setNom_poblacion(rs.getString("nom_poblacion"));
                p.setId_pais(rs.getInt("id_pais"));
                lista.add(p);
            }
            rs.close();
            stmt.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return lista;
    }

    public List<Pais> getPaises() {
        List<Pais> lista = new ArrayList<>();
        try {
            String sql = "SELECT id_pais, nom_pais FROM pais";
            Statement stmt = conexion.createStatement();
            ResultSet rs = stmt.executeQuery(sql);
            while (rs.next()) {
                Pais p = new Pais();
                p.setId_pais(rs.getInt("id_pais"));
                p.setNom_pais(rs.getString("nom_pais"));
                lista.add(p);
            }
            rs.close();
            stmt.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return lista;
    }

}

package opusbooks.bd;

import java.io.InputStream;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Properties;

import javax.servlet.ServletContext;

import opusbooks.beans.Autor;
import opusbooks.beans.Categoria;
import opusbooks.beans.Compra;
import opusbooks.beans.DatosCompra;
import opusbooks.beans.Editorial;
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
                     "CONCAT(a.nombre, ' ', a.apellidos) AS autor, " +
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
    
    public int insertarCompra(Compra compra) {
        String sql = "INSERT INTO compras (fecha_compra, dni) VALUES (?, ?)";

        try (PreparedStatement ps = conexion.prepareStatement(
                sql, PreparedStatement.RETURN_GENERATED_KEYS)) {

            ps.setDate(1, compra.getFecha_compra());
            ps.setString(2, compra.getDni());
            ps.executeUpdate();

            ResultSet rs = ps.getGeneratedKeys();
            if (rs.next()) {
                return rs.getInt(1); // id_compra generado
            }

        } catch (SQLException e) {
            e.printStackTrace();
        }
        return -1; // error
    }
    public boolean insertarDatosCompra(DatosCompra datosCompra) {
        String sql = "INSERT INTO datoscompras (id_compra, isbn, cantidad) VALUES (?, ?, ?)";

        try (PreparedStatement ps = conexion.prepareStatement(sql)) {
            ps.setInt(1, datosCompra.getId_compra());
            ps.setString(2, datosCompra.getIsbn());
            ps.setInt(3, datosCompra.getCantidad());
            ps.executeUpdate();
            return true;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }
    
    public boolean actualizarStock(String isbn, int nuevoStock) {
        String sql = "UPDATE libros SET stock = ? WHERE isbn = ?";
        try (PreparedStatement ps = conexion.prepareStatement(sql)) {
            ps.setInt(1, nuevoStock);
            ps.setString(2, isbn);
            ps.executeUpdate();
            return true;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }
    
    public String obtenerDniUsuario(String usuario, String contrasena) {
        String dni = null;
        String sql = "SELECT dni FROM usuarios WHERE usuario = ? AND contrasena = ?";
        try (PreparedStatement ps = conexion.prepareStatement(sql)) {
            ps.setString(1, usuario);
            ps.setString(2, contrasena);
            ResultSet rs = ps.executeQuery();
            if (rs.next()) {
                dni = rs.getString("dni");
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return dni;
    }
    
    public List<Autor> getAutores() {
        List<Autor> lista = new ArrayList<>();
        try {
            String sql = "SELECT id_autor, nombre, apellidos FROM autores";
            PreparedStatement ps = conexion.prepareStatement(sql);
            ResultSet rs = ps.executeQuery();

            while (rs.next()) {
                Autor a = new Autor();
                a.setId_autor(rs.getInt("id_autor"));
                a.setNombre(rs.getString("nombre"));
                a.setApellidos(rs.getString("apellidos"));
                lista.add(a);
            }
            rs.close();
            ps.close();
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return lista;
    }

    public List<Editorial> getEditoriales() {
        List<Editorial> lista = new ArrayList<>();
        try {
            String sql = "SELECT id_editorial, nombre FROM editoriales";
            PreparedStatement ps = conexion.prepareStatement(sql);
            ResultSet rs = ps.executeQuery();

            while (rs.next()) {
                Editorial e = new Editorial();
                e.setId_editorial(rs.getInt("id_editorial"));
                e.setNombre(rs.getString("nombre"));
                lista.add(e);
            }
            rs.close();
            ps.close();
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return lista;
    }

    public List<Categoria> getCategorias() {
        List<Categoria> lista = new ArrayList<>();
        try {
            String sql = "SELECT id_categoria, nombre FROM categorias";
            PreparedStatement ps = conexion.prepareStatement(sql);
            ResultSet rs = ps.executeQuery();

            while (rs.next()) {
                Categoria c = new Categoria();
                c.setId_categoria(rs.getInt("id_categoria"));
                c.setNombre(rs.getString("nombre"));
                lista.add(c);
            }
            rs.close();
            ps.close();
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return lista;
    }
    
    public List<Libro> getLibrosFiltrados(String autorId, String editorialId, String categoriaId) {
        List<Libro> lista = new ArrayList<>();
        try {
            String sql = "SELECT l.isbn, l.titulo, l.precio, l.stock, " +
                         "a.id_autor, a.nombre AS nombreAutor, " +
                         "e.id_editorial, e.nombre AS nombreEditorial, " +
                         "c.id_categoria, c.nombre AS nombreCategoria " +
                         "FROM libros l " +
                         "JOIN autores a ON l.id_autor = a.id_autor " +
                         "JOIN editoriales e ON l.id_editorial = e.id_editorial " +
                         "JOIN categorias c ON l.id_categoria = c.id_categoria " +
                         "WHERE 1=1 ";

            // Filtrar por autor
            if (autorId != null && !autorId.isEmpty()) {
                sql += " AND a.id_autor = ? ";
            }
            // Filtrar por editorial
            if (editorialId != null && !editorialId.isEmpty()) {
                sql += " AND e.id_editorial = ? ";
            }
            // Filtrar por categoría
            if (categoriaId != null && !categoriaId.isEmpty()) {
                sql += " AND c.id_categoria = ? ";
            }

            PreparedStatement ps = conexion.prepareStatement(sql);

            int index = 1;
            if (autorId != null && !autorId.isEmpty()) ps.setInt(index++, Integer.parseInt(autorId));
            if (editorialId != null && !editorialId.isEmpty()) ps.setInt(index++, Integer.parseInt(editorialId));
            if (categoriaId != null && !categoriaId.isEmpty()) ps.setInt(index++, Integer.parseInt(categoriaId));

            ResultSet rs = ps.executeQuery();

            while (rs.next()) {
                Libro libro = new Libro();
                libro.setIsbn(rs.getString("isbn"));
                libro.setTitulo(rs.getString("titulo"));
                libro.setPrecio(rs.getDouble("precio"));
                libro.setStock(rs.getInt("stock"));
                libro.setId_autor(rs.getInt("id_autor"));
                libro.setNombreAutor(rs.getString("nombreAutor"));
                libro.setId_editorial(rs.getInt("id_editorial"));
                libro.setNombreEditorial(rs.getString("nombreEditorial"));
                libro.setId_categoria(rs.getInt("id_categoria"));
                libro.setNombreCategoria(rs.getString("nombreCategoria"));
                lista.add(libro);
            }
            rs.close();
            ps.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return lista;
    }
}





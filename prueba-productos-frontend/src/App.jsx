import { useState, useEffect } from 'react';
import api from './api';
import ProductoCard from './components/ProductoCard';
import './App.css';

function App() {
  const [productos, setProductos] = useState([]);
  const [cargando, setCargando] = useState(true);
  const [subiendo, setSubiendo] = useState(false);
  const [errores, setErrores] = useState([]);
  const [mensaje, setMensaje] = useState('');

  // Cargar productos al abrir la página
  useEffect(() => {
    cargarProductos();
  }, []);

  const cargarProductos = async () => {
    setCargando(true);
    try {
      const respuesta = await api.get('/productos');
      setProductos(respuesta.data);
    } catch (err) {
      setMensaje('No se pudo conectar con el servidor.');
    } finally {
      setCargando(false);
    }
  };

  const handleArchivoSeleccionado = async (e) => {
    const archivo = e.target.files[0];
    if (!archivo) return;

    setSubiendo(true);
    setErrores([]);
    setMensaje('');

    const formData = new FormData();
    formData.append('archivo', archivo);

    try {
      const respuesta = await api.post('/productos/importar', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      setMensaje(`Se procesó el archivo. Productos guardados hasta ahora: ${respuesta.data.productos_guardados}`);
      setErrores(respuesta.data.errores || []);

      // Recargar la lista para ver los nuevos productos
      await cargarProductos();
    } catch (err) {
      if (err.response?.data?.message) {
        setMensaje(err.response.data.message);
      } else {
        setMensaje('Ocurrió un error al subir el archivo.');
      }
    } finally {
      setSubiendo(false);
      e.target.value = ''; // permite volver a subir el mismo archivo si se corrige
    }
  };

  // Cuando una card se edita con éxito, actualizamos solo ese producto en la lista
  const handleProductoActualizado = (productoActualizado) => {
    setProductos((prev) =>
      prev.map((p) => (p.id === productoActualizado.id ? productoActualizado : p))
    );
  };

  return (
    <div className="contenedor">
      <h1>Gestión de Productos</h1>

      <div className="zona-carga">
        <label className="boton-subir">
          {subiendo ? 'Subiendo...' : 'Subir archivo Excel'}
          <input
            type="file"
            accept=".xlsx,.xls,.csv"
            onChange={handleArchivoSeleccionado}
            disabled={subiendo}
            hidden
          />
        </label>

        {mensaje && <p className="mensaje">{mensaje}</p>}

        {errores.length > 0 && (
          <div className="lista-errores">
            <p><strong>Se encontraron {errores.length} fila(s) con errores:</strong></p>
            <ul>
              {errores.map((err, i) => (
                <li key={i}>
                  Fila {err.fila} — columna "{err.columna}": {err.errores.join(' ')}
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>

      {cargando ? (
        <p>Cargando productos...</p>
      ) : productos.length === 0 ? (
        <p>No hay productos cargados todavía. Sube un archivo Excel para empezar.</p>
      ) : (
        <div className="grid-productos">
          {productos.map((producto) => (
            <ProductoCard
              key={producto.id}
              producto={producto}
              onActualizado={handleProductoActualizado}
            />
          ))}
        </div>
      )}
    </div>
  );
}

export default App;
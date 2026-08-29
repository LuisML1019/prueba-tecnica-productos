import { useState } from 'react';
import api from '../api';

function ProductoCard({ producto, onActualizado }) {
  const [editando, setEditando] = useState(false);
  const [form, setForm] = useState({
    nombre: producto.nombre,
    precio: producto.precio,
    descripcion: producto.descripcion,
    cantidad: producto.cantidad,
    imagen: producto.imagen,
  });
  const [guardando, setGuardando] = useState(false);
  const [error, setError] = useState('');
  const [imagenRota, setImagenRota] = useState(false);

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleGuardar = async () => {
    setGuardando(true);
    setError('');
    try {
      const respuesta = await api.put(`/productos/${producto.id}`, form);
      onActualizado(respuesta.data); // avisa al padre que este producto cambió
      setEditando(false);
    } catch (err) {
      if (err.response?.data?.errors) {
        const mensajes = Object.values(err.response.data.errors).flat();
        setError(mensajes.join(' '));
      } else {
        setError('Ocurrió un error al guardar los cambios.');
      }
    } finally {
      setGuardando(false);
    }
  };

  const handleCancelar = () => {
    setForm({
      nombre: producto.nombre,
      precio: producto.precio,
      descripcion: producto.descripcion,
      cantidad: producto.cantidad,
      imagen: producto.imagen,
    });
    setError('');
    setEditando(false);
  };

  if (editando) {
    return (
      <div className="card card-editando">
        <input name="nombre" value={form.nombre} onChange={handleChange} placeholder="Nombre" />
        <input name="precio" type="number" step="0.01" value={form.precio} onChange={handleChange} placeholder="Precio" />
        <textarea name="descripcion" value={form.descripcion} onChange={handleChange} placeholder="Descripción" />
        <input name="cantidad" type="number" value={form.cantidad} onChange={handleChange} placeholder="Cantidad" />
        <input name="imagen" value={form.imagen} onChange={handleChange} placeholder="URL de imagen" />

        {error && <p className="error-texto">{error}</p>}

        <div className="card-botones">
          <button onClick={handleGuardar} disabled={guardando}>
            {guardando ? 'Guardando...' : 'Guardar'}
          </button>
          <button onClick={handleCancelar} disabled={guardando}>Cancelar</button>
        </div>
      </div>
    );
  }

  return (
    <div className="card">
      {imagenRota ? (
        <div className="imagen-rota">Imagen no disponible</div>
      ) : (
        <img
          src={producto.imagen}
          alt={producto.nombre}
          onError={() => setImagenRota(true)}
        />
      )}
      <h3>{producto.nombre}</h3>
      <p className="precio">${Number(producto.precio).toFixed(2)}</p>
      <p className="descripcion">{producto.descripcion}</p>
      <p className="cantidad">Cantidad: {producto.cantidad}</p>
      <button onClick={() => setEditando(true)}>Editar</button>
    </div>
  );
}

export default ProductoCard;
function VistaPreviaExcel({ datos, onConfirmar, onCancelar, confirmando }) {
  return (
    <div className="preview-container">
      <p className="preview-resumen">
        Se encontraron <strong>{datos.total_filas}</strong> fila(s) en el archivo:{' '}
        <strong className="texto-verde">{datos.total_validas} válida(s)</strong>
        {datos.total_filas - datos.total_validas > 0 && (
          <> — <strong className="texto-rojo">{datos.total_filas - datos.total_validas} con error(es)</strong></>
        )}
      </p>

      <div className="preview-tabla-scroll">
        <table className="preview-tabla">
          <thead>
            <tr>
              <th>Fila</th>
              <th>Estado</th>
              <th>Nombre</th>
              <th>Precio</th>
              <th>Descripción</th>
              <th>Cantidad</th>
              <th>Imagen</th>
              <th>Errores</th>
            </tr>
          </thead>
          <tbody>
            {datos.filas.map((f) => (
              <tr key={f.fila} className={f.valido ? 'fila-valida' : 'fila-invalida'}>
                <td>{f.fila}</td>
                <td>{f.valido ? '✅ Válido' : '❌ Error'}</td>
                <td>{f.datos.nombre}</td>
                <td>{f.datos.precio}</td>
                <td>{f.datos.descripcion}</td>
                <td>{f.datos.cantidad}</td>
                <td className="celda-imagen">{f.datos.link_de_imagen_del_producto}</td>
                <td className="celda-errores">{f.errores.join(' ')}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="preview-botones">
        <button
          className="boton-confirmar"
          onClick={onConfirmar}
          disabled={confirmando || datos.total_validas === 0}
        >
          {confirmando ? 'Guardando...' : `Confirmar y guardar ${datos.total_validas} producto(s)`}
        </button>
        <button className="boton-cancelar" onClick={onCancelar} disabled={confirmando}>
          Cancelar
        </button>
      </div>
    </div>
  );
}

export default VistaPreviaExcel;
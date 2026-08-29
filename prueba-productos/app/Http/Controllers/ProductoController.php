<?php

namespace App\Http\Controllers;

use App\Imports\ProductosImport;
use App\Models\Producto;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductoController extends Controller
{
    /**
     * Devuelve todos los productos guardados (para mostrar las cards).
     */
    public function index()
    {
        $productos = Producto::orderBy('created_at', 'desc')->get();
        return response()->json($productos);
    }

    /**
     * Recibe el archivo Excel, lo valida y guarda los productos válidos.
     */
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new ProductosImport();
        Excel::import($import, $request->file('archivo'));

        $errores = [];
        foreach ($import->failures() as $failure) {
            $errores[] = [
                'fila'      => $failure->row(),       // número de fila del Excel
                'columna'   => $failure->attribute(),  // columna con el error
                'errores'   => $failure->errors(),     // mensaje(s) de error
                'valores'   => $failure->values(),     // los datos que traía esa fila
            ];
        }

        return response()->json([
            'mensaje'          => 'Archivo procesado.',
            'productos_guardados' => Producto::count(),
            'errores'          => $errores,
        ]);
    }

    /**
     * Actualiza un producto existente (edición en tiempo real).
     */
    public function update(Request $request, Producto $producto)
    {
        $datos = $request->validate([
            'nombre'      => ['required', 'string', 'max:255', 'regex:/^[^\'"´`¨]+$/u'],
            'precio'      => ['required', 'numeric', 'min:0'],
            'descripcion' => ['required', 'string', 'regex:/^[^\'"´`¨]+$/u'],
            'cantidad'    => ['required', 'integer', 'min:0'],
            'imagen'      => ['required', 'string', 'url', 'regex:/^[^\'"´`¨]+$/u'],
        ], [
            'nombre.regex'      => 'El nombre contiene caracteres no permitidos.',
            'descripcion.regex' => 'La descripción contiene caracteres no permitidos.',
            'imagen.regex'      => 'El link de imagen contiene caracteres no permitidos.',
            'imagen.url'        => 'El link de imagen debe ser una URL válida.',
        ]);

        $producto->update($datos);

        return response()->json($producto);
    }
}
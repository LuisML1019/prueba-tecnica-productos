<?php

namespace App\Http\Controllers;

use App\Imports\ProductosImport;
use App\Imports\ProductosPreviewImport;
use App\Models\Producto;
use App\Services\ProductoValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('created_at', 'desc')->get();
        return response()->json($productos);
    }

    /**
     * Paso 1: lee el Excel y devuelve una vista previa SIN guardar nada aun.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        // Guardamos el archivo temporalmente para poder usarlo si el usuario confirma
        $rutaTemporal = $request->file('archivo')->store('excel-temporales');

        $lector = new ProductosPreviewImport();
        Excel::import($lector, $request->file('archivo'));

        $filasPreview = [];
        $totalValidas = 0;

        foreach ($lector->filas as $indice => $fila) {
            $datos = $fila->toArray();

            $validador = Validator::make($datos, ProductoValidator::reglas(), ProductoValidator::mensajes());
            $esValida = $validador->passes();

            if ($esValida) {
                $totalValidas++;
            }

            $filasPreview[] = [
                'fila'    => $indice + 2, // fila 1 es el encabezado
                'valido'  => $esValida,
                'datos'   => $datos,
                'errores' => $esValida ? [] : $validador->errors()->all(),
            ];
        }

        return response()->json([
            'archivo_temporal' => $rutaTemporal,
            'filas'            => $filasPreview,
            'total_filas'      => count($filasPreview),
            'total_validas'    => $totalValidas,
        ]);
    }

    /**
     * Paso 2: el usuario confirmo, ahora si guardamos los productos validos.
     */
    public function confirmar(Request $request)
    {
        $request->validate([
            'archivo_temporal' => ['required', 'string'],
        ]);

        $ruta = $request->input('archivo_temporal');

        // Seguridad: solo procesamos archivos dentro de nuestra carpeta temporal
        if (!str_starts_with($ruta, 'excel-temporales/') || !Storage::exists($ruta)) {
            return response()->json(['mensaje' => 'El archivo temporal no existe o ya expiro.'], 404);
        }

        $import = new ProductosImport();
        Excel::import($import, Storage::path($ruta));

        $errores = [];
        foreach ($import->failures() as $failure) {
            $errores[] = [
                'fila'    => $failure->row(),
                'columna' => $failure->attribute(),
                'errores' => $failure->errors(),
                'valores' => $failure->values(),
            ];
        }

        Storage::delete($ruta); // ya no necesitamos el archivo temporal

        return response()->json([
            'mensaje'             => 'Productos guardados correctamente.',
            'productos_guardados' => Producto::count(),
            'errores'             => $errores,
        ]);
    }

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
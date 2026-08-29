<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class ProductosImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    /**
     * Regla que detecta comillas, tildes sueltas y acentos no permitidos.
     */
    private function sinCaracteresProhibidos()
    {
        return function ($attribute, $value, $fail) {
            if (preg_match('/[\'"´`¨]/u', $value)) {
                $fail("El campo {$attribute} contiene caracteres no permitidos (comillas o acentos sueltos).");
            }
        };
    }

    /**
     * Se ejecuta por cada fila válida del Excel y crea el producto.
     */
    public function model(array $row)
    {
        return new Producto([
            'nombre'      => $row['nombre'],
            'precio'      => $row['precio'],
            'descripcion' => $row['descripcion'],
            'cantidad'    => $row['cantidad'],
            'imagen'      => $row['link_de_imagen_del_producto'],
        ]);
    }

    /**
     * Reglas de validación para cada fila del Excel.
     */
    public function rules(): array
    {
        return [
            'nombre'                      => ['required', 'string', 'max:255', $this->sinCaracteresProhibidos()],
            'precio'                      => ['required', 'numeric', 'min:0'],
            'descripcion'                 => ['required', 'string', $this->sinCaracteresProhibidos()],
            'cantidad'                    => ['required', 'integer', 'min:0'],
            'link_de_imagen_del_producto' => ['required', 'string', 'url', $this->sinCaracteresProhibidos()],
        ];
    }

    /**
     * Mensajes de error personalizados y claros.
     */
    public function customValidationMessages()
    {
        return [
            'nombre.required'   => 'La columna "nombre" es obligatoria.',
            'nombre.string'     => 'La columna "nombre" debe ser texto.',
            'precio.required'   => 'La columna "precio" es obligatoria.',
            'precio.numeric'    => 'La columna "precio" debe ser un número.',
            'descripcion.required' => 'La columna "descripción" es obligatoria.',
            'cantidad.required' => 'La columna "cantidad" es obligatoria.',
            'cantidad.integer'  => 'La columna "cantidad" debe ser un número entero.',
            'link_de_imagen_del_producto.required' => 'La columna "link de imagen" es obligatoria.',
            'link_de_imagen_del_producto.url'      => 'La columna "link de imagen" debe ser una URL válida.',
        ];
    }
}
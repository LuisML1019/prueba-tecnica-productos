<?php

namespace App\Services;

class ProductoValidator
{
    public static function reglas(): array
    {
        return [
            'nombre'                      => ['required', 'string', 'max:255', self::sinCaracteresProhibidos()],
            'precio'                      => ['required', 'numeric', 'min:0'],
            'descripcion'                 => ['required', 'string', self::sinCaracteresProhibidos()],
            'cantidad'                    => ['required', 'integer', 'min:0'],
            'link_de_imagen_del_producto' => ['required', 'string', 'url', self::sinCaracteresProhibidos()],
        ];
    }

    public static function mensajes(): array
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

    public static function sinCaracteresProhibidos()
    {
        return function ($attribute, $value, $fail) {
            if (preg_match('/[\'"´`¨]/u', $value)) {
                $fail("El campo {$attribute} contiene caracteres no permitidos (comillas o acentos sueltos).");
            }
        };
    }
}
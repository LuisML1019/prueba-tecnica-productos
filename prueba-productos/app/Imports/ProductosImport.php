<?php

namespace App\Imports;

use App\Models\Producto;
use App\Services\ProductoValidator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class ProductosImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        Producto::updateOrCreate(
            ['nombre' => $row['nombre']], // condicion para buscar si ya existe
            [
                'precio'      => $row['precio'],
                'descripcion' => $row['descripcion'],
                'cantidad'    => $row['cantidad'],
                'imagen'      => $row['link_de_imagen_del_producto'],
            ]
        );

        return null; // ya guardamos manualmente arriba, no dejamos que el import lo haga de nuevo
    }

    public function rules(): array
    {
        return ProductoValidator::reglas();
    }

    public function customValidationMessages()
    {
        return ProductoValidator::mensajes();
    }
}
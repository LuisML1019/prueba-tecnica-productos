<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductosPreviewImport implements ToCollection, WithHeadingRow
{
    public $filas;

    public function collection($rows)
    {
        $this->filas = $rows;
    }
}
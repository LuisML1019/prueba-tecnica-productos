<?php

namespace Tests\Feature;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un producto con datos válidos SÍ debe poder actualizarse.
     */
    public function test_puede_actualizar_un_producto_con_datos_validos(): void
    {
        $producto = Producto::create([
            'nombre'      => 'Mouse original',
            'precio'      => 10.00,
            'descripcion' => 'Descripcion original',
            'cantidad'    => 5,
            'imagen'      => 'https://picsum.photos/200',
        ]);

        $respuesta = $this->putJson("/api/productos/{$producto->id}", [
            'nombre'      => 'Mouse actualizado',
            'precio'      => 19.99,
            'descripcion' => 'Nueva descripcion',
            'cantidad'    => 8,
            'imagen'      => 'https://picsum.photos/201',
        ]);

        $respuesta->assertStatus(200);
        $this->assertDatabaseHas('productos', [
            'id'     => $producto->id,
            'nombre' => 'Mouse actualizado',
        ]);
    }

    /**
     * Un precio no numérico debe ser rechazado.
     */
    public function test_rechaza_precio_no_numerico(): void
    {
        $producto = Producto::create([
            'nombre' => 'Producto', 'precio' => 10, 'descripcion' => 'Desc',
            'cantidad' => 5, 'imagen' => 'https://picsum.photos/200',
        ]);

        $respuesta = $this->putJson("/api/productos/{$producto->id}", [
            'nombre'      => 'Producto',
            'precio'      => 'no-es-un-numero',
            'descripcion' => 'Desc',
            'cantidad'    => 5,
            'imagen'      => 'https://picsum.photos/200',
        ]);

        $respuesta->assertStatus(422); // 422 = error de validación
        $respuesta->assertJsonValidationErrors('precio');
    }

    /**
     * Una cantidad no entera debe ser rechazada.
     */
    public function test_rechaza_cantidad_no_entera(): void
    {
        $producto = Producto::create([
            'nombre' => 'Producto', 'precio' => 10, 'descripcion' => 'Desc',
            'cantidad' => 5, 'imagen' => 'https://picsum.photos/200',
        ]);

        $respuesta = $this->putJson("/api/productos/{$producto->id}", [
            'nombre'      => 'Producto',
            'precio'      => 10,
            'descripcion' => 'Desc',
            'cantidad'    => 'quince',
            'imagen'      => 'https://picsum.photos/200',
        ]);

        $respuesta->assertStatus(422);
        $respuesta->assertJsonValidationErrors('cantidad');
    }

    /**
     * Un nombre con comillas dobles debe ser rechazado.
     */
    public function test_rechaza_caracteres_no_permitidos_en_nombre(): void
    {
        $producto = Producto::create([
            'nombre' => 'Producto', 'precio' => 10, 'descripcion' => 'Desc',
            'cantidad' => 5, 'imagen' => 'https://picsum.photos/200',
        ]);

        $respuesta = $this->putJson("/api/productos/{$producto->id}", [
            'nombre'      => 'Producto "raro"',
            'precio'      => 10,
            'descripcion' => 'Desc',
            'cantidad'    => 5,
            'imagen'      => 'https://picsum.photos/200',
        ]);

        $respuesta->assertStatus(422);
        $respuesta->assertJsonValidationErrors('nombre');
    }

    /**
     * Una URL de imagen inválida debe ser rechazada.
     */
    public function test_rechaza_url_de_imagen_invalida(): void
    {
        $producto = Producto::create([
            'nombre' => 'Producto', 'precio' => 10, 'descripcion' => 'Desc',
            'cantidad' => 5, 'imagen' => 'https://picsum.photos/200',
        ]);

        $respuesta = $this->putJson("/api/productos/{$producto->id}", [
            'nombre'      => 'Producto',
            'precio'      => 10,
            'descripcion' => 'Desc',
            'cantidad'    => 5,
            'imagen'      => 'esto-no-es-una-url',
        ]);

        $respuesta->assertStatus(422);
        $respuesta->assertJsonValidationErrors('imagen');
    }
}
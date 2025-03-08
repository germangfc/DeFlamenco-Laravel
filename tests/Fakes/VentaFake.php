<?php
namespace Tests\Fakes;

class VentaFake
{
    public $id;
    public $created_at;
    public $lineasVenta;
    public $guid;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function find($id)
    {
        // Simulamos una búsqueda por ID
        if ($id === 1) {
            return new self([
                'id'          => 1,
                'created_at'  => now(),
                'lineasVenta' => [array_values(['1', '100', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid'])],
                //'lineasVenta' => [['1', '100', 'Evento 1', '01-10-2025', '18:00:00', 'Madrid']],
                'guid'        => 'guid-123',
            ]);
        }

        return null; // Si no se encuentra, devuelve null
    }

    public static function orderBy($column, $direction = 'asc')
    {
        return new class {
            public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
            {
                // Devuelve la instancia del paginador fake registrada en el contenedor
                return app('fake.paginatedVentas');
            }
        };
    }
}

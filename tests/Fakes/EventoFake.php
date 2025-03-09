<?php
namespace Tests\Fakes;

class EventoFake
{
    public $id;
    public $nombre;
    public $fecha;
    public $hora;
    public $ciudad;
    public $precio;
    public $stock;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    // Simula la búsqueda de un evento por id.
    public static function find($id)
    {
        if ($id === 1) {
            return new self([
                'id'      => 1,
                'nombre'  => 'Evento 1',
                'fecha'   => '01-10-2025',
                'hora'    => '18:00:00',
                'ciudad'  => 'Madrid',
                'precio'  => 100,
                'stock'   => 10,
            ]);
        }
        return null;
    }
}

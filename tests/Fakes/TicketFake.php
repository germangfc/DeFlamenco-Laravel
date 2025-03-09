<?php
namespace Tests\Fakes;

class TicketFake
{
    public $_id;
    public $idEvent;
    public $idClient;
    public $price;
    public $isReturned;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    // En este fake simulamos el guardado asignando un _id.
    public function save()
    {
        $this->_id = 'ticket_' . rand(1000, 9999);
    }
}

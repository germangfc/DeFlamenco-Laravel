<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;


class PagoConfirmado extends Mailable
{
    public $venta;

    public function __construct($venta)
    {
        $this->venta = $venta;
    }

    public function build()
    {
        $pdf = $this->generarPDF($this->venta);

        return $this->subject('Pago confirmado')
            ->view('emails.pago', ['venta' => $this->venta])
            ->attachData($pdf->output(), 'tickets.pdf'); // Adjuntamos el PDF
    }

    private function generarPDF($venta)
    {
        $lineasVenta = $venta->lineasVenta; // Obtenemos las líneas de venta

        // Creamos el PDF con los tickets
        $pdf = Pdf::loadView('pdf.tickets', ['lineasVenta' => $lineasVenta]);

        return $pdf;
    }
}



<?php

namespace App\Http\Controllers\Api;

use App\Models\Ticket;
use MongoDB\BSON\ObjectId;
use App\Http\Controllers\Controller;
use App\Models\Venta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

class VentasApiController extends Controller
{
    public function index()
    {
        return response()->json(Venta::all(), 200);
    }

    public function show($id){

        Log::info('Showing Venta');
        $cacheKey = "venta_{$id}";

        $venta = Cache::get($cacheKey);

        if (!$venta) {

            $venta = Venta::find($id);

            if (!$venta) {
                Log::info('Venta no encontrada');
                return response()->json(['message' => 'Venta not Found'], 404);
            }
            Log::info('Venta no en caché => Recuperada de BD');

            Cache::put($cacheKey, $venta, 60);
            Log::info('Guardada venta en caché');
        }
        else {
            Log::info('Venta recuperada de caché');
        }

        return response()->json($venta, 200);

    }
    public function store(Request $request){
        Log::info("Storing Venta. Validating...");
        try {
            $lineasVenta = array_map(function ($linea) {
                if (isset($linea[0]) && is_string($linea[0])) {
                    try {
                        $idTicket = new ObjectId($linea[0]);
                        $linea[0] = (string) $idTicket;
                    } catch (Exception $e) {
                        throw ValidationException::withMessages([
                            'lineasVenta.*.idTicket' => ['Formato de idTicket inválido.']
                        ]);
                    }
                }
                return $linea;
            }, $request->lineasVenta);

            $request->merge(['lineasVenta' => $lineasVenta]);
            Log::info("Lineas venta Merged");

            // Validación estándar
            $request->validate([
                'guid' => 'required|unique:mongodb.ventas',
                'lineasVenta' => 'required|array|min:1',
                'lineasVenta.*' => 'array|size:6', // Cada línea de venta debe tener exactamente 6 elementos
                'lineasVenta.*.0' => 'required', // idTicket
                'lineasVenta.*.1' => 'required|numeric|min:0.01', // precioUnitario
                'lineasVenta.*.2' => 'required|string', // descripción
                'lineasVenta.*.3' => 'required|date_format:Y-m-d', // fecha
                'lineasVenta.*.4' => 'required|date_format:H:i:s', // hora
                'lineasVenta.*.5' => 'required|string' // ubicación
            ],[
                    'guid.required'            => 'El campo GUID es obligatorio.',
                    'guid.unique'              => 'El GUID ya existe en la base de datos.',
                    'lineasVenta.required'     => 'Debes proporcionar al menos una línea de venta.',
                    'lineasVenta.array'        => 'El formato de las líneas de venta debe ser un array.',
                    'lineasVenta.min'          => 'Debes proporcionar al menos :min línea de venta.',
                    'lineasVenta.*.0.required' => 'El idTicket es obligatorio en cada línea de venta.',
                    'lineasVenta.*.1.required' => 'El precio unitario es obligatorio en cada línea de venta.',
                    'lineasVenta.*.1.numeric'  => 'El precio unitario debe ser un valor numérico.',
                    'lineasVenta.*.1.min'      => 'El precio unitario debe ser al menos :min.',
                    'lineasVenta.*.2.required' => 'La descripción es obligatoria en cada línea de venta.',
                    'lineasVenta.*.3.required' => 'La fecha es obligatoria en cada línea de venta.',
                    'lineasVenta.*.3.date_format' => 'La fecha debe tener el formato Y-m-d.',
                    'lineasVenta.*.4.required' => 'La hora es obligatoria en cada línea de venta.',
                    'lineasVenta.*.4.date_format' => 'La hora debe tener el formato H:i:s.',
                    'lineasVenta.*.5.required' => 'La ubicación es obligatoria en cada línea de venta.'
                ]
            );

            // Validar que idTicket exista y sea único en ventas
            foreach ($lineasVenta as $linea) {
                $idTicket = $linea[0];
                Log::info("Validando línea con idticket: " . $idTicket);

                if (!Ticket::where('_id', $idTicket)->exists()) {
                    Log::info("El idTicket no existe en la colección tickets");
                    throw ValidationException::withMessages([
                        'lineasVenta.*.idTicket' => ['El idTicket no existe en la colección tickets.']
                    ]);
                }
                // Usamos elemMatch para buscar en cualquier sub-array de lineasVenta
                $existingVenta = Venta::where('lineasVenta', 'elemMatch', (object)[ '0' => (string)$idTicket ])->first();

                if ($existingVenta) {
                    Log::info("El idTicket ya ha sido registrado en otra venta");
                    throw ValidationException::withMessages([
                        'lineasVenta.*.idTicket' => ['El idTicket ya ha sido registrado en otra venta.']
                    ]);
                }

            }

            Log::info("Venta Validated");

            // Crear y guardar la venta
            $venta = new Venta;
            $venta->guid = $request->guid;
            $venta->lineasVenta = $lineasVenta;
            $venta->save();

            Log::info("Venta Stored");
            return response()->json($venta, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al guardar la venta: ' . $e->getMessage()], 400);
        }
    }

    public function destroy($id){

        Log::info('Deleting Venta');

        $cacheKey = "venta_{$id}";
        $venta = Cache::get($cacheKey);

        if(!$venta) {

            $venta = Venta::find($id);

            if (!$venta) {
                Log::info('Venta no encontrada');
                return response()->json(['message' => 'Venta not Found'], 404);
            }
            Log::info('Venta no en caché => Recuperada de BD');

        } else {
            Log::info('Venta recuperada de caché');
        }

        $venta->delete();
        Log::info('Venta eliminada');

        Cache::forget($cacheKey);
        Log::info('Venta eliminada de caché');

        return response()->json(['message' => 'Venta eliminada'], 204);
    }
}

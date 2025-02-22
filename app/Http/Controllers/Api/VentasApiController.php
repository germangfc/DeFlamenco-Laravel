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
        //Log::info($request->all());
        try {

            // Convertir idTicket a ObjectId para que coincida con MongoDB
            $lineasVenta = array_map(function ($linea) {
                if (isset($linea['idTicket']) && is_string($linea['idTicket'])) {
                    try {
                        $linea['idTicket'] = new ObjectId($linea['idTicket']);
                    } catch (Exception $e) {
                        throw ValidationException::withMessages([
                            'lineasVenta.*.idTicket' => ['Formato de idTicket inválido.']
                        ]);
                    }
                }
                return $linea;
            }, $request->lineasVenta);

            $request->merge(['lineasVenta' => $lineasVenta]);

            // Validación estándar
            $request->validate([
                'guid' => 'required|unique:mongodb.ventas',
                'lineasVenta' => 'required|array|min:1',
                'lineasVenta.*.idTicket' => 'required',
                'lineasVenta.*.precioUnitario' => 'required|numeric|min:0.01'
            ]);


            // Validar manualmente que idTicket sea único en la colección tickets
            foreach ($lineasVenta as $linea) {
                // Verificar si el idTicket ya existe en la colección tickets
                $ticketExiste = Ticket::where('_id', $linea['idTicket'])->exists();
                if (!$ticketExiste) {
                    throw ValidationException::withMessages([
                        'lineasVenta.*.idTicket' => ['El idTicket no existe en la colección tickets.']
                    ]);
                }

                // Verificar si el idTicket ya está en otra venta
                $ticketDuplicado = Venta::where('lineasVenta.idTicket', $linea['idTicket'])->exists();
                if ($ticketDuplicado) {
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
            return response()->json(['error' => 'Error al guardar la venta: ' . $e], 400);
        }

    }

    // Solo se modificarían las líneas de venta,
    // debemos validar las líneas de venta que vienen
    // y que el guid sea el mismo, no se debe modificar
    public function update(Request $request, $id){
        Log::info("Updating Venta with ID: {$id}");
        try {
            $lineasVenta = array_map(function ($linea) {
                if (isset($linea['idTicket']) && is_string($linea['idTicket'])) {
                    try {
                        $linea['idTicket'] = new ObjectId($linea['idTicket']);
                    } catch (Exception $e) {
                        throw ValidationException::withMessages([
                            'lineasVenta.*.idTicket' => ['Formato de idTicket inválido.']
                        ]);
                    }
                }
                return $linea;
            }, $request->lineasVenta);

            $request->merge(['lineasVenta' => $lineasVenta]);

            // Validación estándar
            $request->validate([
                //'guid' => 'required|unique:mongodb.ventas', // el guid no se modifica por lo que no nos lo deben pasar
                'lineasVenta' => 'required|array|min:1',
                'lineasVenta.*.idTicket' => 'required',
                'lineasVenta.*.precioUnitario' => 'required|numeric|min:0.01'
            ]);

            // Validar manualmente que idTicket sea único en la colección tickets
            foreach ($lineasVenta as $linea) {
                // Verificar si el idTicket ya existe en la colección tickets
                $ticketExiste = Ticket::where('_id', $linea['idTicket'])->exists();
                if (!$ticketExiste) {
                    throw ValidationException::withMessages([
                        'lineasVenta.*.idTicket' => ['El idTicket no existe en la colección tickets.']
                    ]);
                }

                // Verificar si el idTicket ya está en otra venta que no sea la que vamos a modificar
                $ticketDuplicado = Venta::where('lineasVenta.idTicket', $linea['idTicket'])
                    ->where('_id', '!=', new ObjectId($id)) // Excluir la venta actual
                    ->exists();
                if ($ticketDuplicado) {
                    throw ValidationException::withMessages([
                        'lineasVenta.*.idTicket' => ['El idTicket ya ha sido registrado en otra venta.']
                    ]);
                }
            }

            Log::info("Venta Validated");

            $venta = Venta::find($id);
            if (!$venta) {
                return response()->json(['message' => 'Venta not Found'], 404);
            }
            $venta->update($request->all());

            // Eliminar las lineas de venta que sobren (pueden pasarme una venta con menos líneas, esas entradas se liberarían)

            return response()->json($venta, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);

        } catch (Exception $e) {
            return response()->json(['error' => 'Error al guardar la venta'], 400);
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

        //Cache::put($cacheKey, $venta, 20);
        Cache::forget($cacheKey);
        Log::info('Venta eliminada de caché');

        return response()->json(['message' => 'Venta eliminada'], 204);
    }
}

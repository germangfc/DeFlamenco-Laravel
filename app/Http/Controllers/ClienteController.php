<?php

namespace App\Http\Controllers;

use App\Mail\ClienteBienvenido;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{

    public function index(Request $request)
    {
        $clientes = Cliente::paginate(5);

        return view('clientes.index', compact('clientes'));
    }

    public function show($id)
    {
        $cacheKey = "cliente_{$id}";

        $cliente = Cache::get($cacheKey);

        if (!$cliente) {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return redirect()->route('clientes.index')->with('error', 'Cliente no encontrado');
            }

            Cache::put($cacheKey, $cliente, 60);
        }

        return view('clientes.show', compact('cliente'));
    }


    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedUserData = $request->validate([
                'name' => 'required|string|max:255|min:3',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required'
            ]);


            $validatedClientData = $request->validate([
                'dni' => 'required|string|unique:clientes,dni',
                'foto_dni' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $user = User::create([
                'name' => $validatedUserData['name'],
                'email' => $validatedUserData['email'],
                'password' => Hash::make($validatedUserData['password']),
            ]);

            $fotoDniPath = "";
            if ($request->hasFile('foto_dni')) {
                $image = $request->file('foto_dni');

                $customName = 'dni_' . $validatedClientData['dni'] . '.' . $image->getClientOriginalExtension();

                $image->storeAs('images', $customName, 'public');
                $fotoDniPath=$customName;
            }

            $cliente=Cliente::create([
                'user_id' => $user->id,
                'dni' => $validatedClientData['dni'],
                'foto_dni' => $fotoDniPath
            ]);

            Mail::to($user->email)->send(new ClienteBienvenido($cliente, $user));

            $user->assignRole('cliente');
            Auth::login($user);
            return redirect()->route('clientes.index')->with('success', 'Cliente creado con éxito');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit($id)
    {
        $cacheKey = "cliente_{$id}";

        $cliente = Cache::get($cacheKey);

        if (!$cliente) {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return redirect()->route('clientes.index')->with('error', 'Cliente no encontrado');
            }

            Cache::put($cacheKey, $cliente, 60);
        }

        return view('clientes.edit', compact('cliente'));
    }


    public function update(Request $request, $id)
    {
        $clienteCacheKey = "cliente_{$id}";

        $cliente = Cache::get($clienteCacheKey);

        if (!$cliente) {
            $cliente = Cliente::find($id);
            if (!$cliente) {
                return redirect()->route('clientes.index')->with('error', 'Cliente no encontrado');
            }

            Cache::put($clienteCacheKey, $cliente, 20);
        }

        $validatedData = $request->validate([
            'dni' => 'nullable|string|regex:/^[0-9]{8}[A-Z]$/|unique:clientes,dni,' . $cliente->id,
            'foto_dni' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:users,email,' . $cliente->user_id,
            'password' => 'nullable|string|min:8',
        ]);

        $cliente->update([
            'dni' => $validatedData['dni'] ?? $cliente->dni,
            'foto_dni' => $validatedData['foto_dni'] ?? $cliente->foto_dni,
        ]);

        $user = User::find($cliente->user_id);
        if ($user) {
            $user->update([
                'name' => $validatedData['name'] ?? $user->name,
                'email' => $validatedData['email'] ?? $user->email,
                'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password,
            ]);
        }

        Cache::forget($clienteCacheKey);

        Cache::put($clienteCacheKey, $cliente, 20);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado con éxito');
    }


    public function destroy($id)
    {
        $clienteCacheKey = "cliente_{$id}";
        $cliente = Cache::get($clienteCacheKey);

        if (!$cliente) {
            $cliente = Cliente::find($id);
            if (!$cliente) {
                return redirect()->route('clientes.index')->with('error', 'Cliente no encontrado');
            }
            Cache::put($clienteCacheKey, $cliente, 20);
        }

        $userCacheKey = "user_{$cliente->user_id}";
        $user = Cache::get($userCacheKey);

        if (!$user) {
            $user = User::find($cliente->user_id);
            if ($user) {
                Cache::put($userCacheKey, $user, 20);
            }
        }

        $cliente->delete();

        if ($user) {
            $user->delete();
        }

        Cache::forget($clienteCacheKey);
        Cache::forget($userCacheKey);

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente');
    }
}

<?php

namespace App\Http\Controllers;

use App\Mail\ClienteBienvenido;
use App\Mail\EliminacionCuenta;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{

    public function index(Request $request)
    {
        $searchTerm = $request->input('query');

        $clientes = Cliente::search($searchTerm)
            ->orderBy('id', 'ASC')
            ->paginate(5);

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

            $validatedData = $request->validate([
                'name' => 'required|string|max:255|min:3',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required',
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);


            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);


            $fotoAvatarPath = "";
            $timestamp = (int) (microtime(true) * 1000);
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $customName = 'perfil_' . $timestamp . '.' . $image->getClientOriginalExtension();
                $image->storeAs('images', $customName, 'public');
                $fotoAvatarPath = $customName;
            }


            $cliente = Cliente::create([
                'user_id' => $user->id,
                'avatar' => $fotoAvatarPath
            ]);


            Mail::to($user->email)->send(new ClienteBienvenido($cliente, $user));


            $user->assignRole('cliente');
            Auth::login($user);

            return redirect()->route('eventos')->with('success', 'Cliente creado con éxito');
        } catch (ValidationException $e) {
            return redirect()->route('register')->withErrors($e->errors())->withInput();
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:users,email,' . $cliente->user_id,
            'password' => 'nullable|string|min:8',
        ]);

        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $customName = 'perfil_' . ($validatedData['name'] ?? $cliente->name) . '.' . $image->getClientOriginalExtension();

            if ($cliente->avatar && $cliente->avatar !== $customName) {
                Storage::disk('public')->delete('images/' . $cliente->avatar);
            }

            $image->storeAs('images', $customName, 'public');

            $cliente->avatar = $customName;
        }

        $user = User::find($cliente->user_id);
        if ($user) {
            $user->update([
                'name' => $validatedData['name'] ?? $user->name,
                'email' => $validatedData['email'] ?? $user->email,
                'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password,
            ]);
        }

        $cliente->save();

        Cache::forget($clienteCacheKey);
        Cache::put($clienteCacheKey, $cliente->fresh(), 20);

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
        }

        $cliente->delete();

        if ($user) {
            $user->delete();
        }

        Cache::forget($clienteCacheKey);
        Cache::forget($userCacheKey);

        return redirect()->route('clientes.index')->with('success', 'Cliente y usuario eliminados correctamente');
    }


}

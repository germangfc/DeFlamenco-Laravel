<?php

namespace App\Http\Controllers;

use App\Mail\EmpresaBienvenida;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        // Capturamos el valor del input "query"
        $searchTerm = $request->input('query');

        // Aplicamos el scope 'search' y paginamos
        $empresas = Empresa::search($searchTerm)
            ->orderBy('name', 'ASC')
            ->paginate(8);

        // Retornamos la vista con la lista de empresas
        return view('empresas.admin', compact('empresas'));
    }



    public function show($id)
    {
        $cacheKey = "empresa_{$id}";

        if (Cache::has($cacheKey)) {
            return view('empresas.show', ['empresa' => Empresa::find($id)]);
        }

        $empresa = Empresa::find($id);

        if (!$empresa) {
            return redirect()->route('empresas.index')->with('error', 'Empresa no encontrada');
        }

        $view = view('empresas.show', compact('empresa'));

        Cache::put($cacheKey, $view->render(), 60);

        return $view; // Devuelve la vista real, no solo el HTML
    }

    public function showByNombre($nombre)
    {

        $nombre = trim($nombre); // Elimina espacios al inicio y final del nombre
        $empresa = Empresa::whereRaw('LOWER(name) = ?', [strtolower($nombre)])->first();

        if (!$empresa) {
            return redirect()->route('empresas.index')->with('error', 'Empresa no encontrada');
        }

        return redirect()->route('empresas.show', ['id' => $empresa->id]);
    }

    public function showByCif($cif)
    {

        $empresa = Empresa::where('cif', $cif)->first();

        if (!$empresa) {
            return redirect()->route('empresas.index')->with('error', 'Empresa no encontrada');
        }

        return redirect()->route('empresas.show',['id' => $empresa->id]);
    }

    public function create()
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return view('empresas.create-admin');
        }

        return view('empresas.create');
    }


    public function store(Request $request)
    {
        try {
            // 🔹 Validar los datos del usuario
            $validatedUserData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8',
            ]);

            // 🔹 Validar los datos de la empresa
            $validatedEmpresaData = $request->validate([
                'name' => 'required|string|max:255',
                'cif' => ['required', 'regex:/^[A-HJNP-SUVW][0-9]{7}[0-9]$/'],
                'direccion' => 'required|max:255',
                'email' => 'required|string|email',
                'cuentaBancaria' => ['required', 'regex:/^ES\d{2}\d{20}$/'],
                'telefono' => ['required', 'regex:/^(\+34|0034)?[679]\d{8}$/'],
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            // 🔹 Crear el usuario
            $user = User::create([
                'name' => $validatedUserData['name'],
                'email' => $validatedUserData['email'],
                'password' => Hash::make($validatedUserData['password']),
            ]);

            // 🔹 Asignar rol al usuario
            $user->assignRole('empresa');

            // 🔹 Procesar imagen de empresa
            $imagenPath = null;
            if ($request->hasFile('imagen')) {
                $image = $request->file('imagen');
                $customName = 'empresa_' . str_replace(' ', '_', strtolower($validatedEmpresaData['name'])) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('empresas', $customName, 'public');
                $imagenPath = $customName;
            }

            // 🔹 Crear la empresa
            $empresa = Empresa::create([
                'usuario_id' => $user->id,
                'name' => $validatedEmpresaData['name'],
                'cif' => $validatedEmpresaData['cif'],
                'direccion' => $validatedEmpresaData['direccion'],
                'email' => $validatedEmpresaData['email'],
                'cuentaBancaria' => $validatedEmpresaData['cuentaBancaria'],
                'telefono' => $validatedEmpresaData['telefono'],
                'imagen' => $imagenPath,
                'isDeleted' => false
            ]);

            // 🔹 Enviar correo de bienvenida
            Mail::to($user->email)->send(new EmpresaBienvenida($empresa, $user));

            // 🔹 Redirección según el rol
            if (Auth::check() && Auth::user()->hasRole('admin')) {
                return redirect()->route('empresas.index')->with('success', 'Empresa creada con éxito por el administrador');
            }

            Auth::login($user);
            return redirect()->route('empresas.index')->with('success', 'Empresa creada con éxito');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }


    public function edit($id)
    {
        $cacheKey = "empresa_{$id}_edit";

        Cache::forget($cacheKey);

        $empresa = Empresa::find($id);

        if (!$empresa) {
            return redirect()->route('empresas.index')->with('error', 'Empresa no encontrada');
        }

        Cache::put($cacheKey, $empresa, 20);

        return view('empresas.edit')->with('empresa', $empresa);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cif' => ['required', 'regex:/^[A-HJNP-SUVW][0-9]{7}[0-9A-J]$/'],
            'name' => 'required|max:255',
            'direccion' => 'required|max:255',
            'cuentaBancaria' => ['required', 'regex:/^ES\d{2}\d{20}$/'],
            'telefono' => ['required', 'regex:/^(\+34|0034)?[679]\d{8}$/'],
            'email' => 'required|email|max:255'
        ]);

        try {
            $empresa = Empresa::find($id);

            if (!$empresa) {
                return redirect()->route('empresas.index')->with('error', 'Empresa no encontrada');
            }

            $empresa->fill($request->all());

            if ($request->hasFile('imagen')) {
                if (Storage::exists($empresa->imagen)) {
                    Storage::delete($empresa->imagen);
                }
                $empresa->imagen = $request->file('imagen')->store('storage');
            }

            $empresa->save();

            Cache::forget("empresa_{$id}");
            Cache::forget("empresa_{$id}_edit");

            return redirect()->route('empresas.index')->with('success', 'Empresa actualizada correctamente');
        } catch (ValidationException $e) {

            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
    public function destroy($id)
    {
        $cacheKey = "empresa_{$id}";
        $empresa = Cache::get($cacheKey);

        if (!$empresa) {
            $empresa = Empresa::find($id);
        }

        if ($empresa) {
            Cache::forget($cacheKey);

            // 🔹 Verificar si la imagen existe y eliminarla
            if ($empresa->imagen && Storage::disk('public')->exists($empresa->imagen)) {
                Storage::disk('public')->delete($empresa->imagen);
            }

            $empresa->delete();

            return redirect()->route('empresas.index')->with('success', 'Empresa eliminada correctamente');
        }

        return redirect()->route('empresas.index')->with('error', 'No se ha encontrado la empresa');
    }

}

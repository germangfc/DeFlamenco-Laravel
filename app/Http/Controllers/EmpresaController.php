<?php

namespace App\Http\Controllers;

use App\Mail\EmpresaBienvenida;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EmpresaController extends Controller
{

    /**
     * Muestra el listado de empresas.
     *
     * @param Request $request para la peticion de busqueda.
     *
     * @return View con el listado de empresas.
     */
    public function index(Request $request)
    {
        // Capturamos el valor del input "query"
        $searchTerm = $request->input('query');

        // Aplicamos el scope 'search' y paginamos
        $empresas = Empresa::search($searchTerm)
            ->orderBy('name', 'ASC')
            ->paginate(9);

        if (auth()->check() && auth()->user()->getRoleNames()->contains('admin')) {
            return view('empresas.admin', compact('empresas'));
        }

        return view('empresas.user', compact('empresas'));
    }

    /**
     * Muestra el detalle de una empresa.
     *
     * @param int $id
     *
     * @return Factory|Application|object|View a la vista de detalle de empresa.
     */


    public function show($id)
    {
        try {
            $empresa = Empresa::with('eventos')->findOrFail($id);
            $eventos = $empresa->eventos()->paginate(10);

            return view('empresas.show', [
                'empresa' => $empresa,
                'eventos' => $eventos
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('empresas.index')->with('error', 'Empresa no encontrada');
        }
    }


    /**
     * Muestra el detalle de una empresa por su nombre.
     *
     * @param string $nombre
     *
     * @return Factory|Application|object|View a la vista de detalle de empresa.
     */
    public function showByNombre($nombre)
    {

        $nombre = trim($nombre); // Elimina espacios al inicio y final del nombre
        $empresa = Empresa::whereRaw('LOWER(name) = ?', [strtolower($nombre)])->first();

        if (!$empresa) {
            return redirect()->route('empresas.index')->with('error', 'Empresa no encontrada');
        }

        return redirect()->route('empresas.show', ['id' => $empresa->id]);
    }

    /**
     * Muestra el detalle de una empresa por su cif.
     *
     * @param string $cif
     *
     * @return Factory|Application|object|View a la vista de detalle de empresa.
     */
    public function showByCif($cif)
    {

        $empresa = Empresa::where('cif', $cif)->first();

        if (!$empresa) {
            return redirect()->route('empresas.index')->with('error', 'Empresa no encontrada');
        }

        return redirect()->route('empresas.show',['id' => $empresa->id]);
    }

    /**
     * Muestra el formulario para crear una nueva empresa.
     *
     * @return View con el formulario de creación de empresa.
     */
    public function create()
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return view('empresas.create-admin');
        }

        return view('empresas.create');
    }


    /**
     * Almacena una nueva empresa en la base de datos.
     *
     * @param Request $request
     *
     * @return RedirectResponse a la vista de listado de empresas.
     */
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


    /**
     * Muestra el formulario para editar una empresa.
     *
     * @param int $id ID de la empresa a editar.
     *
     * @return Application|Factory|object|View con el formulario de edición de empresa.
     */
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

    /**
     * Actualiza una empresa en la base de datos.
     *
     * @param Request $request
     * @param int $id ID de la empresa a actualizar.
     *
     * @return RedirectResponse a la vista de listado de empresas.
     */
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

    /**
     * Elimina una empresa de la base de datos.
     *
     * @param int $id ID de la empresa a eliminar.
     *
     * @return RedirectResponse a la vista de listado de empresas.
     */
    public function destroy($id)
    {
        $cacheKey = "empresa_{$id}";
        $empresa = Cache::get($cacheKey);

        if (!$empresa) {
            $empresa = Empresa::find($id);
        }

        if ($empresa) {
            Cache::forget($cacheKey);

            $empresa->eventos()->delete();

            $empresa->delete();

            return redirect()->route('empresas.index')->with('success', 'Empresa eliminada correctamente');
        }

        return redirect()->route('empresas.index')->with('error', 'No se ha encontrado la empresa');
    }

}

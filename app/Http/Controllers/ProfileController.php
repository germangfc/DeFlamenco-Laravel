<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Mail\EliminacionCuenta;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * @param Request $request
     *
     * @return View Vista para editar el perfil del usuario.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * @param ProfileUpdateRequest $request
     *
     * @return RedirectResponse Redirecciona a la vista de edición del perfil.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (Auth::user()->hasRole('cliente')){
            $cliente = $user->cliente;
            $user->fill($request->validated());
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $cliente->save();
            if ($request->hasFile('profile_photo')) {
                $image = $request->file('profile_photo');
                $customName = 'perfil_' . $cliente->avatar . '.' . $image->getClientOriginalExtension();
                if ($cliente->avatar) {
                    Storage::disk('public')->delete('images/' .$cliente->avatar);
                }
                $image->storeAs('images', $customName, 'public');

                $cliente->avatar = $customName;
            }
            $user->save();
            $cliente->save();
        }

        if (Auth::user()->hasRole('empresa')){
            $empresa = Empresa::where('usuario_id', auth()->id())->first();
            $user->fill($request->validated());
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->save();
            if ($request->hasFile('profile_photo')) {
                $image = $request->file('profile_photo');
                $customName = 'empresas_' . $user->name . '.' . $image->getClientOriginalExtension();
                if ($empresa->imagen) {
                    Storage::disk('public')->delete('empresas/' . $empresa->imagen);
                }
                $image->storeAs('empresas', $customName, 'public');
                $empresa->imagen = $customName;
            }
            $user->save();
            $empresa->save();
        }


        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }


        /**
        * @param Request $request
        *
        * @return RedirectResponse Redirecciona a la página de inicio.
        */
    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (Auth::user()->hasRole('cliente')){
            $idclient= $user->cliente()->first()->id;
            $cliente = Cliente::find($idclient);
            if (!$cliente) {
                return redirect()->route('profile.edit')->with('error', 'Cliente no encontrado');
            }
            $cliente->delete();
        }

        if (Auth::user()->hasRole('empresa')){
            $idempresa= Empresa::where('usuario_id', auth()->id())->first()->id;
            $empresa = Empresa::find($idempresa);
            if (!$empresa) {
                return redirect()->route('profile.edit')->with('error', 'Empresa no encontrada');
            }
            $empresa->delete();

        }


        Mail::to($user->email)->send(new EliminacionCuenta($user));
        $user->delete();
        auth()->logout();
        session()->flush();


        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

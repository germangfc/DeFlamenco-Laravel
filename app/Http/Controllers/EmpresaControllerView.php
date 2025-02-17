<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmpresaControllerView extends Controller
{
   public function index(Request $request)
   {
       $empresas = Empresa::search($request->name)->orderBy('id', 'ASC')->paginate(5);

       return view('empresas.index')->with('empresas', $empresas);
   }

   public function create()
   {
       return view ('empresas.create');
   }

   public function store(Request $request)
   {
       $request->validate([
           'cif'=> 'required|regex:/^[A-HJNP-SUVW][0-9]{7}[0-9A-J]$/',
           'nombre'=> 'required|max:255',
           'direccion'=> 'required|max:255',
           'cuentaBancaria'=>'required|regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/',
           'telefono'=> 'required|regex:/^(\+34|0034)?[679]\d{8}$/',
           'email'=> 'required|email|max:255'
       ]);

       try {
           $empresa = new Empresa($request->all());

           $empresa->imagen = $request->file('imagen')->store('storage');

           $empresa->save();

           return redirect()->route('empresas.index')->with('status', 'Empresa creada correctamente');
       }catch(\Exception $e){
           return redirect()->route('empresas.create')->with('error', 'Error al crear la empresa: '.$e->getMessage());
       }
   }

   public function show($id)
   {
       $empresa = Empresa::find($id);

       return view('empresas.show')->with('empresa', $empresa);
   }


   public function edit($id)
       {
           $empresa = Empresa::find($id);

           return view('empresas.edit')->with('empresa', $empresa);
       }

   public function update(Request $request, $id)
   {
       $request->validate([
           'cif'=> 'required|regex:/^[A-HJNP-SUVW][0-9]{7}[0-9A-J]$/',
           'nombre'=> 'required|max:255',
           'direccion'=> 'required|max:255',
           'cuentaBancaria'=>'required|regex:/^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}\s?\d{10}$/',
           'telefono'=> 'required|regex:/^(\+34|0034)?[679]\d{8}$/',
           'correo'=> 'required|email|max:255'
           ]);

       try{
           $empresa = Empresa::find($id);

           $empresa->fill($request->all());

           if($request->hasFile('imagen')){
               if(Storage::exists($empresa->imagen)){
                   Storage::delete($empresa->imagen);
               }
               $empresa->imagen = $request->file('imagen')->store('storage');
           }
           $empresa->save();

           return redirect()->route('empresas.index')->with('status', 'Empresa actualizada correctamente');
       }catch (\Exception $e) {
    return redirect()->route('empresas.edit', $id)->with('error', 'Error al actualizar la empresa: '.$e->getMessage());
       }
   }

   public function destroy($id)
   {
       $empresa = Empresa::find($id);

       if($empresa){
           if(Storage::exists($empresa->imagen)){
               Storage::delete($empresa->imagen);
           }
           $empresa->delete();

           return redirect()->route('empresas.index')->with('status', 'Empresa eliminada correctamente');
       }

       return redirect()->route('empresas.index')->with('error', 'No se ha encontrado la empresa');
   }
}

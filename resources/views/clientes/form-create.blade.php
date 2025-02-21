<form action="{{ route('clientes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group mb-3">
        <x-input-label for="name" :value="__('Nombre')" />
        <x-text-input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div class="form-group mb-3">
        <x-input-label for="email">Email</x-input-label>
        <x-text-input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required/>
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>
    <div class="form-group mb-3">
        <x-input-label for="password">Contraseña</x-input-label>
        <x-text-input type="password" name="password" id="password" class="form-control" required/>
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>
    <div class="form-group mb-3">
        <x-input-label for="dni">DNI</x-input-label>
        <x-text-input type="text" name="dni" id="dni" class="form-control" value="{{ old('dni') }}" required/>
        <x-input-error class="mt-2" :messages="$errors->get('dni')" />
    </div>
    <div class="form-group mb-3">
        <x-input-label for="foto_dni">Foto DNI</x-input-label>
        <x-text-input type="file" name="foto_dni" id="foto_dni" class="form-control"/>
        <x-input-error class="mt-2" :messages="$errors->get('foto_dni')" />
    </div>
    <button type="submit" id="submitCliente" class="btn btn-primary w-100">Crear Cliente</button>
</form>

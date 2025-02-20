<form action="{{ route('clientes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group mb-3">
        <label for="name">Nombre</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        @error('name')
        <div class="alert alert-danger mt-2">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
        @error('email')
        <div class="alert alert-danger mt-2">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group mb-3">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" class="form-control" required>
        @error('password')
        <div class="alert alert-danger mt-2">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group mb-3">
        <label for="dni">DNI</label>
        <input type="text" name="dni" id="dni" class="form-control" value="{{ old('dni') }}" required>
        @error('dni')
        <div class="alert alert-danger mt-2">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group mb-3">
        <label for="foto_dni">Foto DNI</label>
        <input type="file" name="foto_dni" id="foto_dni" class="form-control">
        @error('foto_dni')
        <div class="alert alert-danger mt-2">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" id="submitCliente" class="btn btn-primary w-100">Crear Cliente</button>
</form>

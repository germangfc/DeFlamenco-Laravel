<form action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
    @csrf
    <div class="container">
        <div class="row">
            <!-- Columna Izquierda -->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="text-center">
                    <h6>Sube una foto de perfil</h6>
                    <div class="form-group">
                        <input type="file" name="imagen" class="form-control text-center center-block well well-sm" accept="image/jpeg">
                    </div>
                </div>
            </div>
            <!-- Columna Derecha -->
            <div class="col-md-8 col-sm-6 col-xs-12 personal-info">
                <!-- Nombre -->
                <div class="form-group">
                    <label for="nombre" class="col-lg-3 control-label">Nombre:</label>
                    <div class="col-lg-6">
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control" required placeholder="Nombre de la Empresa">
                    </div>
                </div>
                <!-- CIF -->
                <div class="form-group">
                    <label for="cif" class="col-lg-3 control-label">CIF:</label>
                    <div class="col-lg-6">
                        <input type="text" name="cif" id="cif" value="{{ old('cif') }}" class="form-control" required placeholder="CIF de la Empresa">
                    </div>
                </div>
                <!-- Dirección -->
                <div class="form-group">
                    <label for="direccion" class="col-lg-3 control-label">Dirección:</label>
                    <div class="col-lg-6">
                        <input type="text" name="direccion" id="direccion" value="{{ old('direccion') }}" class="form-control" required placeholder="Dirección de la Empresa">
                    </div>
                </div>
                <!-- Teléfono -->
                <div class="form-group">
                    <label for="telefono" class="col-lg-3 control-label">Teléfono:</label>
                    <div class="col-lg-6">
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" class="form-control" required placeholder="Número de Teléfono">
                    </div>
                </div>
                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="col-lg-3 control-label">Email:</label>
                    <div class="col-lg-6">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required placeholder="Correo Electrónico">
                    </div>
                </div>
                <!-- Cuenta Bancaria -->
                <div class="form-group">
                    <label for="cuentaBancaria" class="col-lg-3 control-label">Cuenta Bancaria:</label>
                    <div class="col-lg-6">
                        <input type="text" name="cuentaBancaria" id="cuentaBancaria" value="{{ old('cuentaBancaria') }}" class="form-control" required placeholder="Cuenta Bancaria">
                    </div>
                </div>

                <!-- Botones -->
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-success">Registrar</button>
                        <a href="{{ route('empresas.index') }}" class="btn btn-default">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

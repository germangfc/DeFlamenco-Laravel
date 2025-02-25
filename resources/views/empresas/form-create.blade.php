<form id="form" action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
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
                    <label for="name" class="col-lg-3 control-label">Nombre:</label>
                    <div class="col-lg-6">
                        <input type="text" name="name" id="name" value="{{ old('nombre') }}" class="form-control field-validate" required placeholder="Nombre de la Empresa">
                        <span class="text-danger error-name"></span>
                        <!--<x-input-error class="mt-2" :messages="$errors->get('name')" />-->
                    </div>
                </div>
                <!-- CIF -->
                <div class="form-group">
                    <label for="cif" class="col-lg-3 control-label">CIF:</label>
                    <div class="col-lg-6">
                        <input type="text" name="cif" id="cif" value="{{ old('cif') }}" class="form-control field-validate" required placeholder="CIF de la Empresa">
                        <span class="text-danger error-cif"></span>
                    </div>
                </div>
                <!-- Dirección -->
                <div class="form-group">
                    <label for="direccion" class="col-lg-3 control-label">Dirección:</label>
                    <div class="col-lg-6">
                        <input type="text" name="direccion" id="direccion" value="{{ old('direccion') }}" class="form-control field-validate" required placeholder="Dirección de la Empresa">
                        <span class="text-danger error-direccion"></span>
                    </div>
                </div>
                <!-- Teléfono -->
                <div class="form-group">
                    <label for="telefono" class="col-lg-3 control-label">Teléfono:</label>
                    <div class="col-lg-6">
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" class="form-control field-validate" required placeholder="Número de Teléfono">
                        <span class="text-danger error-telefono"></span>
                    </div>
                </div>
                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="col-lg-3 control-label">Email:</label>
                    <div class="col-lg-6">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control field-validate" required placeholder="Correo Electrónico">
                        <span class="text-danger error-email"></span>
                    </div>
                </div>
                <!-- Cuenta Bancaria -->
                <div class="form-group">
                    <label for="cuentaBancaria" class="col-lg-3 control-label">Cuenta Bancaria:</label>
                    <div class="col-lg-6">
                        <input type="text" name="cuentaBancaria" id="cuentaBancaria" value="{{ old('cuentaBancaria') }}" class="form-control field-validate" required placeholder="Cuenta Bancaria">
                        <span class="text-danger error-cuentaBancaria"></span>
                    </div>
                </div>
                <!-- Contraseña -->
                <div class="form-group">
                    <label for="password" class="col-lg-3 control-label">Contraseña:</label>
                    <div class="col-lg-6">
                        <input type="password" name="password" id="password" class="form-control field-validate" required placeholder="Contraseña">
                        <span class="text-danger error-password"></span>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("form");

        // Reglas de validación
        const rules = {
            name: { regex: /^.{1,255}$/, message: "El nombre es obligatorio y no debe superar 255 caracteres." },
            cif: { regex: /^[A-HJNP-SUVW][0-9]{7}[0-9]$/, message: "El CIF debe tener el formato correcto." },
            direccion: { regex: /^.{1,255}$/, message: "La dirección es obligatoria y no debe superar 255 caracteres." },
            telefono: { regex: /^[679]\d{8}$/, message: "El número de teléfono debe comenzar con 6, 7 o 9 y tener 9 dígitos." },
            email: { regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, message: "Debe ser un email válido." },
            cuentaBancaria: { regex: /^ES\d{2}\s?\d{4}\s?\d{4}\s?\d{2}/, message: "Debe ser un IBAN español válido." },
            password: { regex: /^.{8,}$/, message: "La contraseña debe tener al menos 8 caracteres." }
        };

        // Función para validar un campo
        function validateField(field) {
            let value = field.value.trim();
            let fieldName = field.id;
            let errorSpan = document.querySelector(".error-" + fieldName);
            let rule = rules[fieldName];

            if (rule && !value.match(rule.regex)) {
                errorSpan.textContent = rule.message;
                return false;
            } else {
                errorSpan.textContent = "";
                return true;
            }
        }

        // Validación al salir del campo
        document.querySelectorAll(".field-validate").forEach(field => {
            field.addEventListener("blur", function () {
                validateField(field);
            });
        });

        // Validación al enviar el formulario
        form.addEventListener("submit", function (event) {
            let valid = true;
            document.querySelectorAll(".field-validate").forEach(field => {
                if (!validateField(field)) {
                    valid = false;
                }
            });

            if (!valid) {
                event.preventDefault(); // Evita que el formulario se envíe si hay errores
            }
        });
    });
</script>

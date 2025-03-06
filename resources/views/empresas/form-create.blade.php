<form id="form" action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
    @csrf
    <div class="container">
        <div class="row">
            <!-- Columna Izquierda -->
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="text-center">
                    <h6>Sube una foto de perfil</h6>
                    <div class="form-group">
                        <x-file-input-basico name="imagen" id="imagen" class="form-control text-center center-block well well-sm" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml"/>
                        <x-input-error class="mt-2" :messages="$errors->get('imagen')" />
                    </div>
                </div>
            </div>

            <!-- Columna Derecha -->
            <div class="col-md-8 col-sm-6 col-xs-12 personal-info">
                <!-- Nombre -->
                <div class="form-group mb-3">
                    <x-input-label for="name" :value="__('Nombre')" />
                    <x-text-input type="text" name="name" id="name" class="form-control field-validate" value="{{ old('name') }}" required placeholder="Nombre de la Empresa" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <!-- CIF -->
                <div class="form-group mb-3">
                    <x-input-label for="cif" :value="__('CIF')" />
                    <x-text-input type="text" name="cif" id="cif" class="form-control field-validate" value="{{ old('cif') }}" required placeholder="CIF de la Empresa" />
                    <x-input-error class="mt-2" :messages="$errors->get('cif')" />
                </div>

                <!-- Dirección -->
                <div class="form-group mb-3">
                    <x-input-label for="direccion" :value="__('Dirección')" />
                    <x-text-input type="text" name="direccion" id="direccion" class="form-control field-validate" value="{{ old('direccion') }}" required placeholder="Dirección de la Empresa" />
                    <x-input-error class="mt-2" :messages="$errors->get('direccion')" />
                </div>

                <!-- Teléfono -->
                <div class="form-group mb-3">
                    <x-input-label for="telefono" :value="__('Teléfono')" />
                    <x-text-input type="text" name="telefono" id="telefono" class="form-control field-validate" value="{{ old('telefono') }}" required placeholder="Número de Teléfono" />
                    <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                </div>

                <!-- Email -->
                <div class="form-group mb-3">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input type="email" name="email" id="email" class="form-control field-validate" value="{{ old('email') }}" required placeholder="Correo Electrónico" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <!-- Cuenta Bancaria -->
                <div class="form-group mb-3">
                    <x-input-label for="cuentaBancaria" :value="__('Cuenta Bancaria')" />
                    <x-text-input type="text" name="cuentaBancaria" id="cuentaBancaria" class="form-control field-validate" value="{{ old('cuentaBancaria') }}" required placeholder="Cuenta Bancaria" />
                    <x-input-error class="mt-2" :messages="$errors->get('cuentaBancaria')" />
                </div>

                <!-- Contraseña -->
                <div class="form-group mb-3">
                    <x-input-label for="password" :value="__('Contraseña')" />
                    <x-text-input type="password" name="password" id="password" class="form-control field-validate" required placeholder="Contraseña" />
                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>

                <!-- Botones -->
                <div class="form-group mb-3">
                    <a href="{{ route('empresas.index') }}" class="btn btn-secondary">Volver</a>
                    <x-primary-button class="ml-4" id="registerEmpresa">
                        {{ __('Registrar Empresa') }}
                    </x-primary-button>
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

<x-guest-layout>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded shadow-sm " name="remember">
                <span class="ms-2 text-sm ">Recordar Sesion</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4 gap-4">
            <a class="underline text-sm hover:text-gray-900 dark:hover:text-gray-100 rounded-md " href="{{ route('register') }}">
                Registrate
            </a>
            @if (Route::has('password.request'))
                <a class="underline text-sm hover:text-gray-900 dark:hover:text-gray-100 rounded-md " href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

            <x-primary-button id="submit" class="">
               Iniciar Sesión
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

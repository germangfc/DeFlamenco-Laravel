<div class="flex justify-end w-full">
    <form method="GET" action="{{ route('clientes.index') }}" class="w-full">
        <div class="flex gap-2">
            <input
                type="text"
                name="query"
                id="buscadorClientes"
                value="{{ request('query') }}"
                placeholder="Buscar cliente por nombre, DNI o email."
                class="input input-bordered w-full" />
            <button type="submit" id="buscadorBotonClientes" class="btn btn-primary">Buscar</button>
        </div>
    </form>
</div>

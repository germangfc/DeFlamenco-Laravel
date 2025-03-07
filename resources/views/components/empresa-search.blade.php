<div class="flex justify-end w-full">
    <form method="GET" action="{{ route('empresas.admin') }}" class="w-full">
        <div class="flex gap-2">
            <input
                type="text"
                name="query"
                id="buscadorEmpresas"
                value="{{ request('query') }}"
                placeholder="Buscar empresa por nombre, CIF o email."
                class="input input-bordered w-full" />
            <button type="submit" id="buscarBotonEmpresa" class="btn btn-primary">Buscar</button>
        </div>
    </form>
</div>

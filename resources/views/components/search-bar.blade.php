<div class="drawer drawer-end relative z-50 ">
    <input id="drawer-toggle" type="checkbox" class="drawer-toggle" />
    {{--Buscador--}}
    <div class="drawer-content">
        <div class="flex items-center justify-between mb-4 w-full">
            <label for="drawer-toggle" class="btn btn-secondary drawer-button">Filtrar</label>
            <form method="GET" action="{{ route('eventos') }}" class="w-1/2">
                <div class="flex gap-2">
                    <input
                        type="text"
                        name="query"
                        value="{{ request('query') }}"
                        placeholder="Nombre, Ciudad o Direccion"
                        class="input input-bordered w-full" />
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
    </div>
    {{--Filtros--}}
    <div class="drawer-side">
        <label for="drawer-toggle" class="drawer-overlay"></label>
        <div class="menu p-4 w-80 bg-base-200 text-base-content">
            <form method="GET" action="{{ route('eventos') }}">
                <!-- Conserva el término de búsqueda principal -->
                <input type="hidden" name="query" value="{{ request('query') }}">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Fecha</span>
                    </label>
                    <input
                        type="date"
                        name="fecha"
                        value="{{ request('fecha') }}"
                        class="input input-bordered" />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Precio Mínimo</span>
                    </label>
                    <input
                        type="number" step="0.01"
                        name="precio_min"
                        value="{{ request('precio_min') }}"
                        placeholder="Min"
                        class="input input-bordered" />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Precio Máximo</span>
                    </label>
                    <input
                        type="number" step="0.01"
                        name="precio_max"
                        value="{{ request('precio_max') }}"
                        placeholder="Max"
                        class="input input-bordered" />
                </div>
                <div class="form-control mt-4">
                    <button type="submit" class="btn btn-primary w-full">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>

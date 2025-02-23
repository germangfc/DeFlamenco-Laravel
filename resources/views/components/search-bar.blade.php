<div class="drawer drawer-end">
    <!-- Input toggle para abrir/cerrar el drawer -->
    <input id="drawer-toggle" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
        <!-- Contenido principal: barra de búsqueda visible -->
        <div class="flex flex-wrap items-center gap-4">
            <!-- Botón para abrir el drawer de filtros -->
            <label for="drawer-toggle" class="btn btn-secondary drawer-button">Filtros</label>
            <form method="GET" action="{{ route('eventos') }}" class="">
                <div class="flex gap-2">
                    <input
                        type="text"
                        name="query"
                        value="{{ request('query') }}"
                        placeholder="Buscar eventos..."
                        class="input input-bordered w-full" />
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
    </div>
    <div class="drawer-side">
        <!-- Overlay para cerrar el drawer al hacer clic -->
        <label for="drawer-toggle" class="drawer-overlay"></label>
        <div class="menu p-4 w-80 bg-base-200 text-base-content">
            <!-- Filtros adicionales -->
            <form method="GET" action="{{ route('eventos') }}">
                {{-- Mantener los valores actuales en la URL --}}
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
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>

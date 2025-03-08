@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section("content")
    <div class="p-8">
        <h1 class="text-center text-2xl font-bold">Empresas Registradas</h1>
        <x-empresa-search></x-empresa-search>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($empresas as $empresa)
                <div class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 bg-base-100 h-[400px]">
                    <a href="{{ route('empresas.show', $empresa->id) }}" class="block h-full">
                        <div class="h-60 overflow-hidden relative">
                            <img
                                src="{{ Str::startsWith($empresa->imagen, 'http') ? $empresa->imagen : asset('storage/empresas/' . $empresa->imagen) }}"
                                alt="{{ $empresa->name }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            >
                            <div class="absolute top-4 right-4 badge badge-lg bg-primary glass shadow-lg">
                                <i class="fas fa-phone mr-1"></i> {{ $empresa->telefono }}
                            </div>
                        </div>
                        <div class="p-6 transition-transform duration-500 group-hover:-translate-y-4">
                            <h3 class="text-xl font-bold text-base-content mb-3 relative inline-block">
                                {{ $empresa->name }}
                                <span class="absolute bottom-0 left-0 w-0 h-1 bg-primary transition-all duration-300 group-hover:w-full"></span>
                            </h3>
                        </div>

                        <div class="absolute inset-0 bg-gradient-to-t from-base-100 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-6 flex flex-col justify-end">
                            <p class="text-sm text-base-content line-clamp-3 mb-4">
                                {{ $empresa->direccion ?? '' }}
                            </p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        @if($empresas->count() > 0)
            <div class="flex justify-center mt-8 space-x-2">
                @if (!$empresas->onFirstPage())
                    <a href="{{ $empresas->previousPageUrl() }}" class="btn btn-square">«</a>
                @endif

                @for ($i = max(1, $empresas->currentPage() - 1); $i <= min($empresas->lastPage(), $empresas->currentPage() + 1); $i++)
                    <a href="{{ $empresas->url($i) }}" class="btn btn-square {{ $i == $empresas->currentPage() ? 'btn-active' : '' }}">
                        {{ $i }}
                    </a>
                @endfor

                @if ($empresas->hasMorePages())
                    <a href="{{ $empresas->nextPageUrl() }}" class="btn btn-square">»</a>
                @endif
            </div>
        @else
            <p class="text-center mt-8">No se han encontrado empresas.</p>
        @endif
    </div>
@endsection

@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section("content")
    <div class="p-8">
        <h1 class="text-center text-2xl font-bold">Empresas Registradas</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($empresas as $empresa)
                <div class="rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <a href="{{ route('empresas.show', $empresa->id) }}">
                        <figure class="relative overflow-hidden rounded-lg">
                            <img class="object-cover h-64 w-full transition-transform duration-300 hover:scale-110 rounded-lg"
                                 src='{{ Str::startsWith($empresa->imagen, 'http') ? $empresa->imagen : asset("storage/empresas/" . $empresa->imagen) }}'
                                 alt="Empresa {{ $empresa->id }}" />
                        </figure>
                        <div class="p-4 text-center font-serif">
                            <h3 class="text-lg font-semibold">{{ $empresa->name }}</h3>
                            <p class="text-sm flex items-center justify-center mt-1">
                                <i class="fas fa-phone mr-1"></i> {{ $empresa->telefono }}
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

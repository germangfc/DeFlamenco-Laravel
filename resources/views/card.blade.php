@extends('main')

@section("content")
    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($eventos as $evento)
                <div>
                    <a href="{{ route('eventos.show', $evento->id) }}" class="block w-full">
                        <figure class="relative overflow-hidden">
                            <img class="object-cover h-64 w-full transition-transform duration-300 hover:scale-110" src='https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp' alt="Evento {{ $evento->id }}" />
                        </figure>
                        <div class="p-4 bg-gray-900">
                            <h3 class="text-lg font-semibold text-white">{{ $evento->nombre }}</h3>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="flex justify-center mt-8 space-x-2">
            @if ($eventos->onFirstPage())
                <span class="btn btn-disabled">«</span>
            @else
                <a href="{{ $eventos->previousPageUrl() }}" class="btn btn-square">«</a>
            @endif

            @foreach ($eventos->getUrlRange(1, $eventos->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="btn btn-square {{ $page == $eventos->currentPage() ? 'btn-active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if ($eventos->hasMorePages())
                <a href="{{ $eventos->nextPageUrl() }}" class="btn btn-square">»</a>
            @else
                <span class="btn btn-disabled">»</span>
            @endif
        </div>
    </div>
@endsection

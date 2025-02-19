@extends('main')

@section("content")
    <div class="grid grid-cols-2 gap-x-12 gap-y-24 p-8">
        @foreach($eventos as $evento)
            <div class="flex justify-center p-4 my-6 mb-8">
                <div class="card bg-gray-800 shadow-lg rounded-2xl overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
                    <figure>
                        <img class="object-cover h-70 w-full" src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp" alt="Evento" />
                    </figure>
                    <div class="card-body p-6">
                        <h2 class="card-title text-xl font-semibold text-white-200">{{$evento->nombre}}</h2>
                        <div class="card-actions justify-end mt-4">
                            <button onclick="window.location.href='{{ route('eventos.show', $evento->id) }}'"
                                    class="btn bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition shadow-md">
                                Información
                            </button>
                        </div>
                    </div>
                </div>
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
@endsection

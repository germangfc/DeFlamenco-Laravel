@php
    use Illuminate\Support\Str;
@endphp

@extends('main')

@section("content")
    <div class="p-8">
        @include('components.slider')

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($eventos as $evento)
                <div class="rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <a href="{{ route('eventos.show', $evento->id) }}">
                        <figure class="relative overflow-hidden rounded-lg">
                            <img class="object-cover h-64 w-full transition-transform duration-300 hover:scale-110 rounded-lg"
                                 src='{{ Str::startsWith($evento->foto, 'http') ? $evento->foto : asset("storage/images/" . $evento->foto) }}'
                                 alt="Evento {{ $evento->id }}" />
                        </figure>
                    </a>
                    <div class="p-4  text-center font-serif">
                        <h3 class="text-lg font-semibold">{{ $evento->nombre }}</h3>
                        <p class="text-sm flex items-center justify-center mt-1">
                            <i class="fas fa-calendar-alt mr-1"></i> {{ $evento->fecha }}
                        </p>
                        <p class="text-sm flex items-center justify-center mt-1">
                            <i class="fas fa-tag mr-1"></i> {{ $evento->precio }}€
                        </p>
                    </div>
                </div>
            @endforeach
        </div>


        <div class="flex justify-center mt-8 space-x-2">
            @if (!$eventos->onFirstPage())
                <a href="{{ $eventos->previousPageUrl() }}" class="btn btn-square">«</a>
            @endif

            @for ($i = max(1, $eventos->currentPage() - 1); $i <= min($eventos->lastPage(), $eventos->currentPage() + 1); $i++)
                <a href="{{ $eventos->url($i) }}" class="btn btn-square {{ $i == $eventos->currentPage() ? 'btn-active' : '' }}">
                    {{ $i }}
                </a>
            @endfor

            @if ($eventos->hasMorePages())
                <a href="{{ $eventos->nextPageUrl() }}" class="btn btn-square">»</a>
            @endif
        </div>
    </div>

    <script>
        let index = 0;
        const slides = document.querySelectorAll('#slider .carousel-inner img');
        const totalSlides = slides.length;
        function changeSlide() {
            index = (index + 1) % totalSlides;
            document.querySelector('.carousel-inner').style.transform = `translateX(-${index * 100}%)`;
        }
        setInterval(changeSlide, 10000);
    </script>
@endsection

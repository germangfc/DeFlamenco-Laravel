@extends('main')

@section("content")
    <div class="p-8">
        <div class="carousel w-full mb-8 relative overflow-hidden" id="slider">
            <div class="carousel-inner flex transition-transform duration-1000 ease-in-out">
                <img src="https://img.daisyui.com/images/stock/photo-1625726411847-8cbb60cc71e6.webp" class="w-full flex-shrink-0" />
                <img src="https://img.daisyui.com/images/stock/photo-1609621838510-5ad474b7d25d.webp" class="w-full flex-shrink-0" />
                <img src="https://img.daisyui.com/images/stock/photo-1414694762283-acccc27bca85.webp" class="w-full flex-shrink-0" />
                <img src="https://img.daisyui.com/images/stock/photo-1665553365602-b2fb8e5d1707.webp" class="w-full flex-shrink-0" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($eventos as $evento)
                <div class="rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <a href="{{ route('eventos.show', $evento->id) }}">
                        <figure class="relative overflow-hidden rounded-lg">
                            <img class="object-cover h-64 w-full transition-transform duration-300 hover:scale-110 rounded-lg"
                                 src='{{ asset("storage/images/" . $evento->foto) }}'
                                 alt="Evento {{ $evento->id }}" />
                        </figure>
                    </a>
                    <div class="p-4 text-white text-center font-serif">
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

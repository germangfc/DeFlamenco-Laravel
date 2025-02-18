@extends('main')

@section("content")
    <div class="grid grid-cols-2 gap-6">
        @foreach($eventos as $evento)
            <div class="flex justify-center">
                <div class="card bg-base-100 image-full w-80 shadow-xl">
                    <figure>
                        <img class="object-cover h-40 w-full" src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp" alt="Evento" />
                    </figure>
                    <div class="card-body">
                        <h2 class="card-title">{{$evento->nombre}}</h2>
                        <div class="card-actions justify-end">
                            <button onclick="window.location.href='{{ route('eventos.show', $evento->id) }}'" class="btn btn-primary">
                                Información
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
            <br>
            <br>
    </div>
@endsection

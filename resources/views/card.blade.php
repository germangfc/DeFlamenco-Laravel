@extends('main')

@section("content")
    <div class="flex flex-wrap justify-center gap-4">
        @foreach($eventos as $index => $evento)
            <div class="w-1/4 p-2">
                <div class="card bg-base-100 image-full w-full shadow-xl">
                    <figure>
                        <img class="object-cover h-40 w-full" src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp" alt="Evento" />
                    </figure>
                    <div class="card-body">
                        <h2 class="card-title">{{$evento->nombre}}</h2>
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary">Información</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

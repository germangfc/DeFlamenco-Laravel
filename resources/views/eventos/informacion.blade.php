@extends('main')

@section("content")
    <div class="container mx-auto mt-10">
        <div class="card bg-base-100 shadow-xl p-6">
            <figure>
                <img class="object-cover h-60 w-full" src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp" alt="Evento" />
            </figure>
            <div class="card-body">
                <h2 class="card-title">{{ $evento->nombre }}</h2>
                <p>{{ $evento->fecha }}</p>
                <p>{{ $evento->hora }}</p>
                <p>{{ $evento->direccion }}</p>
                <p>{{ $evento->ciudad }}</p>
                <p>{{ $evento->precio }}</p>
                <div class="card-actions justify-end">
                    <a href="{{ route('eventos') }}" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </div>
    </div>
@endsection

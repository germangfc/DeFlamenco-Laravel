@extends('main')

@section("content")
    @foreach($clientes as $cliente)
    <div class="container mx-auto p-6 flex justify-center">
        <div class="flex justify-center gap-16 mt-10">
            <div class="card bg-base-100 image-full w-96 shadow-xl">
                <figure>
                    <img src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp" alt="Shoes" />
                </figure>
                <div class="card-body">
                    <h2 class="card-title">Shoes!</h2>
                    <p>{{$cliente->dni}}</p>
                    <div class="card-actions justify-end">
                        <button class="btn btn-primary">Buy Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endsection

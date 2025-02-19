@extends('main')

@section("content")
    <div class="grid grid-cols-2 gap-x-12 gap-y-24 p-8">
        @foreach($eventos as $evento)
            <div class="flex justify-center p-4 my-6 mb-8">
                <div class="card bg-white shadow-lg rounded-2xl overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
                    <figure>
                        <img class="object-cover h-52 w-full" src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp" alt="Evento" />
                    </figure>
                    <div class="card-body p-6">
                        <h2 class="card-title text-xl font-semibold text-gray-800">{{$evento->nombre}}</h2>
                        <div class="card-actions justify-end mt-4">
                            <button onclick="window.location.href='{{ route('eventos.show', $evento->id) }}'"
                                    class="btn bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-md">
                                Información
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@extends('main')

@section("content")
    <div class="container mt-4">
        <h1>Carrito</h1>


        @if(count($cart) > 0)
            <table class="w-full border border-gray-300 shadow-lg rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                <tr class="text-left">
                    <th class="py-3 px-4">Evento</th>
                    <th class="py-3 px-4">Precio</th>
                    <th class="py-3 px-4 text-center">Cantidad</th>
                    <th class="py-3 px-4">Subtotal</th>
                    <th class="py-3 px-4 text-center">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @php $total = 0; @endphp
                @foreach($cart as $item)
                    @php $subtotal = $item['price'] * $item['quantity']; @endphp
                    <tr class="border-b">
                        <!-- Nombre del evento -->
                        <td class="py-3 px-4">{{ $item['name'] }}</td>

                        <!-- Precio unitario -->
                        <td class="py-3 px-4 font-semibold">{{ number_format($item['price'], 2) }} €</td>

                        <!-- Selector de cantidad con botones + y - -->
                        <td class="py-3 px-4 text-center">
                            <form action="{{ route('cart.update', $item['idEvent']) }}" method="POST" class="flex items-center justify-center gap-2">
                                @csrf
                                <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}"
                                        {{ $item['quantity'] <= 1 ? 'disabled' : '' }}
                                        class="px-3 py-1 bg-gray-300 rounded-full hover:bg-gray-400 transition">
                                    -
                                </button>
                                <span class="text-lg font-semibold">{{ $item['quantity'] }}</span>
                                <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}"
                                        {{ $item['quantity'] >= 5 ? 'disabled' : '' }}
                                        class="px-3 py-1 bg-gray-300 rounded-full hover:bg-gray-400 transition">
                                    +
                                </button>
                            </form>
                        </td>

                        <!-- Subtotal -->
                        <td class="py-3 px-4 font-semibold">{{ number_format($subtotal, 2) }} €</td>

                        <!-- Botón de eliminar -->
                        <td class="py-3 px-4 text-center">
                            <form action="{{ route('cart.remove', $item['idEvent']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition text-xl">
                                    🗑
                                </button>
                            </form>
                        </td>
                    </tr>
                    @php $total += $subtotal; @endphp
                @endforeach
                </tbody>
            </table>


            <div class="flex items-center justify-between mb-4 mt-4 w-2/4 mx-auto">
                <h3>Total: {{ number_format($total, 2) }} €</h3>
                <form action="{{ route('stripe.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" id="pagar" class="btn btn-primary">Proceder al Pago con Stripe</button>
                </form>
            </div>
        @else
            <p>No tienes items en el carrito.</p>
        @endif
    </div>
@endsection


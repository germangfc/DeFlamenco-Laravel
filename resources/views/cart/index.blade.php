@extends('main')

@section("content")
    <div class="container">
        <h1>Carrito de Compras</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(count($cart) > 0)
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID del Evento</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @php $total = 0; @endphp
                @foreach($cart as $item)
                    @php $subtotal = $item['price'] * $item['quantity']; @endphp
                    <tr>
                        <td>{{ $item['idEvent'] }}</td>
                        <td>${{ number_format($item['price'], 2) }}</td>
                        <td>
                            <form action="{{ route('cart.update', $item['idEvent']) }}" method="POST" class="form-inline">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control" style="width: 70px;">
                                <button type="submit" class="btn btn-primary btn-sm ml-2">Actualizar</button>
                            </form>
                        </td>
                        <td>${{ number_format($subtotal, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $item['idEvent']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @php $total += $subtotal; @endphp
                @endforeach
                </tbody>
            </table>
            <h3>Total: ${{ number_format($total, 2) }}</h3>
            <form action="{{ route('stripe.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Proceder al Pago con Stripe</button>
            </form>
        @else
            <p>No tienes items en el carrito.</p>
        @endif
    </div>
@endsection


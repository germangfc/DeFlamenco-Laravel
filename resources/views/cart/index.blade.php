@extends('main')

@section("content")
    <div class="details-container relative z-10 p-4 md:p-8 max-w-6xl mx-auto">
        <div class="glass-card bg-white/5 backdrop-blur-xl rounded-2xl p-8 border flamenco-light:bg-base-200 border-white/20 shadow-2xl">
            <h1 class="event-title text-3xl md:text-4xl font-bold mb-8 border-l-4 border-primary pl-4">
                Tu Carrito
            </h1>

            @if(count($cart) > 0)
                <div class="overflow-x-auto rounded-lg border border-white/20">
                    <table class="w-full">
                        <thead class="bg-white/10">
                        <tr class="text-left text-base-content">
                            <th class="py-4 px-6 font-semibold">Evento</th>
                            <th class="py-4 px-6 font-semibold">Precio</th>
                            <th class="py-4 px-6 font-semibold text-center">Cantidad</th>
                            <th class="py-4 px-6 font-semibold">Subtotal</th>
                            <th class="py-4 px-6 font-semibold text-center">Eliminar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $total = 0; @endphp
                        @foreach($cart as $item)
                            @php $subtotal = $item['price'] * $item['quantity']; @endphp
                            <tr class="border-b border-white/10 hover:bg-white/5 transition">
                                <!-- Nombre del evento -->
                                <td class="py-4 px-6">{{ $item['name'] }}</td>

                                <!-- Precio unitario -->
                                <td class="py-4 px-6 font-medium">{{ number_format($item['price'], 2) }} €</td>

                                <!-- Selector de cantidad -->
                                <td class="py-4 px-6 text-center">
                                    <form action="{{ route('cart.update', $item['idEvent']) }}" method="POST"
                                          class="flex items-center justify-center gap-2">
                                        @csrf
                                        <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}"
                                                {{ $item['quantity'] <= 1 ? 'disabled' : '' }}
                                                class="btn btn-circle btn-xs bg-primary/20 hover:bg-primary/30 border-0 text-primary">
                                            -
                                        </button>
                                        <span class="text-lg font-medium w-8">{{ $item['quantity'] }}</span>
                                        <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}"
                                                {{ $item['quantity'] >= 5 ? 'disabled' : '' }}
                                                class="btn btn-circle btn-xs bg-primary/20 hover:bg-primary/30 border-0 text-primary">
                                            +
                                        </button>
                                    </form>
                                </td>

                                <!-- Subtotal -->
                                <td class="py-4 px-6 font-medium">{{ number_format($subtotal, 2) }} €</td>

                                <!-- Botón de eliminar -->
                                <td class="py-4 px-6 text-center">
                                    <form action="{{ route('cart.remove', $item['idEvent']) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-error hover:text-error/70 transition text-xl">
                                            🗑
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @php $total += $subtotal; @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between mt-8 p-6 bg-white/10 rounded-xl">
                    <div class="text-xl font-bold mb-4 md:mb-0">
                        Total: <span class="text-primary">{{ number_format($total, 2) }} €</span>
                    </div>
                    <form action="{{ route('stripe.checkout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                id="pagar"
                                class="btn btn-primary hover:btn-accent transform hover:scale-105 transition-all shadow-lg hover:shadow-primary/20">
                            Proceder al Pago
                        </button>
                    </form>
                </div>
            @else
                <div class="text-center p-8 bg-white/10 rounded-xl">
                    <p class="text-xl text-base-content/60">Tu carrito está vacío</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        .glass-card {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .event-title {
            font-family: 'Oswald', sans-serif;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 1rem;
            text-align: left;
        }

        th {
            background-color: rgba(255, 255, 255, 0.05);
        }

        tr:not(:last-child) {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Ajustes para light theme */
        .flamenco-light {
            th {
                background-color: rgba(0, 0, 0, 0.05);
            }

            tr:not(:last-child) {
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            }
        }
    </style>
@endsection

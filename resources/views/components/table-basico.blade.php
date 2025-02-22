@props([
    'headers' => []
])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'table table-zebra w-full']) }}>
        @if(count($headers))
            <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
            </thead>
        @endif
        <tbody>
        {{ $slot }}
        </tbody>
        @if(isset($footer))
            <tfoot>
            {{ $footer }}
            </tfoot>
        @endif
    </table>
</div>

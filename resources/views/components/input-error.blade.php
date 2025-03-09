@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'alert alert-error bg-error/10 text-error border border-error/20 px-4 py-3 rounded-lg']) }}>
        @foreach ((array) $messages as $message)
            <p class="text-sm">{{ $message }}</p>
        @endforeach
    </div>
@endif

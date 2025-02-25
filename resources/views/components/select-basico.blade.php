@props(['disabled' => false, 'options' => []])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'select border-base-100 focus:border-base-100 focus:ring-0 rounded-md shadow-sm']) }}>
    @if(count($options))
        @foreach($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    @else
        {{ $slot }}
    @endif
</select>

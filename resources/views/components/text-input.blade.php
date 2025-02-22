@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-base-100 focus:border-base-100 focus:ring-0 rounded-md shadow-sm']) }}>

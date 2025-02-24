@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input input-bordered border-base-100 focus:border-base-100 focus:ring-0 rounded-md shadow-sm']) }}>

@props(['disabled' => false])

<input type="file" @disabled($disabled) {{ $attributes->merge([
    'class' => 'file-input file-input-bordered border-base-100 focus:border-base-100 focus:ring-0 rounded-md shadow-sm'
]) }}>

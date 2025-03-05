@props(['disabled' => false])
<textarea @disabled($disabled) {{ $attributes->merge(['class' => 'textarea textarea-bordered border-base-100 focus:border-base-100 focus:ring-0 rounded-md shadow-sm']) }}></textarea>

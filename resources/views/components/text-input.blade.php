@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-base-600 flamenco-light:bg-base-200 flamenco-dark:bg-base-800 focus:border-accent focus:ring-accent rounded-md shadow-sm']) }}>

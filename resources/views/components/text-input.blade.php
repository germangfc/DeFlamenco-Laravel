@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-base-600 flamenco-light:bg-base-200 flamenco-dark:bg-base-800 transition-all duration-300
    hover:scale-[1.02] focus:scale-[1.02] focus:ring-2 focus:ring-accent rounded-md shadow-sm']) }}>

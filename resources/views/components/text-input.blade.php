@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'glass-input bg-base-100/20 border border-base-content/20 text-base-content placeholder-base-content/60 rounded-lg shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/30 transition duration-200 w-full p-3']) }}>

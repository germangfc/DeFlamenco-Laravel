/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('daisyui'),
        require('@tailwindcss/forms'),
    ],
    daisyui: {
        themes: [
            {
                flamencoLight: {
                    "primary": "#9b1c1c",      // Rojo sangre más intenso
                    "secondary": "#b58900",     // Oro viejo (como guitarra española)
                    "accent": "#6b8e23",        // Verde olivo (para contraste natural)
                    "neutral": "#0a0a0a",       // Negro azabache (trajes flamencos)
                    "base-100": "#f3e9d2",      // Crema cálido (fondo papel antiguo)
                    "info": "#1e40af",          // Azul profundo (para elementos frescos)
                    "success": "#166534",       // Verde oscuro (olivos)
                    "warning": "#c2410c",      // Naranja oxidado (tierra andaluza)
                    "error": "#991b1b",         // Rojo vino (sangre de toro)
                },
            },
            {
                flamencoDark: {
                    "primary": "#8b0000",       // Rojo carmesí intenso
                    "secondary": "#d4a017",     // Oro bruñido (destellos guitarra)
                    "accent": "#a3573a",        // Terracota (tonos tierra)
                    "neutral": "#1a1a1a",       // Negro terciopelo (noche flamenca)
                    "base-100": "#2a2626",      // Gris cálido oscuro (tablao)
                    "info": "#2563eb",          // Azul eléctrico suave
                    "success": "#15803d",       // Verde esmeralda
                    "warning": "#ea580c",      // Naranja fuego
                    "error": "#dc2626",         // Rojo pasión
                },
            },
        ],
    }
}

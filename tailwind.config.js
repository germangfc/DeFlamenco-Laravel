import plugin from 'tailwindcss/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        fontFamily: {
            cormorant: ['Cormorant', 'serif'],
            cinzel: ['Cinzel', 'serif'],
        },
        extend: {
            colors: {
                base: {
                    200: 'var(--color-base-200)',
                    300: 'var(--color-base-300)',
                    400: 'var(--color-base-400)',
                    500: 'var(--color-base-500)',
                    600: 'var(--color-base-600)',
                    700: 'var(--color-base-700)',
                    800: 'var(--color-base-800)',
                },
            }
        },
    },
    plugins: [
        require('daisyui'),
        require('@tailwindcss/forms'),
        plugin(function({ addVariant }) {
            addVariant('flamenco-light', '[data-theme="flamencoLight"] &');
            addVariant('flamenco-dark', '[data-theme="flamencoDark"] &');
        }),
    ],
    daisyui: {
        themes: [
            {
                flamencoLight: {
                    "primary": "#B80C3A",   // Rojo principal
                    "secondary": "#B80C3A",  // Mismo rojo con opacidad
                    "accent": "#FF3D6E",     // Variación más clara
                    "neutral": "#2D2D2D",    // Gris para texto
                    "base-100": "rgba(245,237,230,0.55)",   // Beige claro
                    "base-200": "#E8D9CD",
                    "base-300": "#DBC6B4",
                    "base-400": "#CEB39B",
                    "base-500": "#C1A082",
                    "base-600": "#A9876B",
                    "base-700": "#8F6E56",
                    "base-800": "#755541",
                    "info": "#007BFF",
                    "success": "#28a745",
                    "warning": "#ffc107",
                    "error": "#dc3545"
                },
            },
            {
                flamencoDark: {
                    "primary": "#B80C3A",    // Mismo rojo principal
                    "secondary": "#B80C3A",  // Mantenemos consistencia
                    "accent": "#FF3D6E",     // Mismo acento que light
                    "neutral": "#E0E0E0",    // Texto claro
                    "base-100": "#1A1A1A",   // Fondo oscuro
                    "base-200": "#262626",
                    "base-300": "#333333",
                    "base-400": "#404040",
                    "base-500": "#4D4D4D",
                    "base-600": "#666666",
                    "base-700": "#808080",
                    "base-800": "#999999",
                    "info": "#007BFF",
                    "success": "#28a745",
                    "warning": "#ffc107",
                    "error": "#dc3545"
                },
            },
        ],
    },
    safelist: [
        { pattern: /bg-base-(100|200|300|400|500|600|700|800)/ },
        { pattern: /text-base-(100|200|300|400|500|600|700|800)/ }
    ]
}

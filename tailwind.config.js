import plugin from 'tailwindcss/plugin';
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
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
                    "primary": "#9b1c1c",
                    "secondary": "#b58900",
                    "accent": "#6b8e23",
                    "neutral": "#0a0a0a",
                    "base-100": "#f3e9d2",
                    "base-200": "#e6d5b8",
                    "base-300": "#d9c19e",
                    "base-400": "#ccad84",
                    "base-500": "#bf996a",
                    "base-600": "#9b7d55",
                    "base-700": "#776140",
                    "base-800": "#53452b",
                    "info": "#1e40af",
                    "success": "#166534",
                    "warning": "#c2410c",
                    "error": "#991b1b",
                },
            },
            {
                flamencoDark: {
                    "primary": "#8b0000",
                    "secondary": "#d4a017",
                    "accent": "#a3573a",
                    "neutral": "#1a1a1a",
                    "base-100": "#2a2626",
                    "base-200": "#3a3633",
                    "base-300": "#4a4640",
                    "base-400": "#5a554d",
                    "base-500": "#6a645a",
                    "base-600": "#7a7367",
                    "base-700": "#8a8274",
                    "base-800": "#9a9181",
                    "info": "#2563eb",
                    "success": "#15803d",
                    "warning": "#ea580c",
                    "error": "#dc2626",
                },
            },
        ],
    },
    // 👇 El safelist debe estar en el nivel raíz (no dentro de daisyui)
    safelist: [
        { pattern: /bg-base-(100|200|300|400|500|600|700|800)/ },
        { pattern: /text-base-(100|200|300|400|500|600|700|800)/ }
    ]
}

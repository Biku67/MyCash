import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Work Sans', 'Manrope', ...defaultTheme.fontFamily.sans],
                heading: ['Manrope', 'sans-serif'],
                body: ['Work Sans', 'sans-serif'],
                mono: ['JetBrains Mono', 'monospace'],
            },
            colors: {
                // Navy palette (Primary dark blue)
                'navy': {
                    DEFAULT: '#1B4F72',
                    'light': '#2471A3',
                    'dark': '#154360',
                    'subtle': 'rgba(27, 79, 114, 0.08)',
                },
                // Sky accent palette
                'sky-accent': {
                    DEFAULT: '#38BDF8',
                    'light': '#7DD3FC',
                    'dark': '#0284C7',
                },
                // Teal accent palette
                'teal-accent': {
                    DEFAULT: '#5DCAA5',
                    'dark': '#48B08E',
                    'dim': 'rgba(93, 202, 165, 0.1)',
                },
                // Surface text colors
                'on-surface': {
                    'secondary': '#64748B',
                    'tertiary': '#94A3B8',
                },
            },
            boxShadow: {
                'nav': '0 2px 16px rgba(0, 0, 0, 0.06)',
                'card-hover': '0 8px 30px rgba(0, 0, 0, 0.08)',
            },
        },
    },

    plugins: [forms],
};

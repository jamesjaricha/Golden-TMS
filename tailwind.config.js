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
                sans: ['-apple-system', 'BlinkMacSystemFont', 'Inter', 'SF Pro Display', 'SF Pro Text', 'Segoe UI', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                apple: {
                    blue: '#007AFF',
                    gray: {
                        50: '#F5F5F7',
                        100: '#E8E8ED',
                        200: '#D2D2D7',
                        300: '#B0B0B8',
                        400: '#86868B',
                        500: '#6E6E73',
                        600: '#515154',
                        700: '#3A3A3C',
                        800: '#2C2C2E',
                        900: '#1C1C1E',
                    },
                },
            },
            boxShadow: {
                'apple-sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                'apple': '0 2px 8px 0 rgba(0, 0, 0, 0.08)',
                'apple-md': '0 4px 16px 0 rgba(0, 0, 0, 0.1)',
                'apple-lg': '0 8px 24px 0 rgba(0, 0, 0, 0.12)',
                'apple-xl': '0 12px 32px 0 rgba(0, 0, 0, 0.15)',
            },
            borderRadius: {
                'apple': '12px',
                'apple-lg': '16px',
                'apple-xl': '20px',
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
        },
    },

    plugins: [forms],
};

import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/**
 * Yuppy design tokens.
 * Palette: charcoal + black for structure, warm ivory for surfaces,
 * champagne gold as an ACCENT ONLY (focus, active state, small highlights),
 * warm neutral greys for text hierarchy and hairlines.
 *
 * @type {import('tailwindcss').Config}
 */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Support/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                ink: '#0E0D0C',
                charcoal: {
                    DEFAULT: '#22211F',
                    soft: '#2E2D2A',
                    muted: '#3A3834',
                },
                ivory: {
                    DEFAULT: '#FAF6EE',
                    50: '#FDFBF7',
                    100: '#FAF6EE',
                    200: '#F2EBDD',
                    300: '#E6DCC8',
                },
                champagne: {
                    DEFAULT: '#C2A267',
                    50: '#FBF7EE',
                    100: '#F5ECDA',
                    200: '#EADBB9',
                    300: '#DCC491',
                    400: '#CDB078',
                    500: '#C2A267',
                    600: '#A5844B',
                    700: '#7F653A',
                },
                grey: {
                    50: '#F7F6F4',
                    100: '#EEECE8',
                    200: '#DEDAD3',
                    300: '#C7C2B9',
                    400: '#A39E95',
                    500: '#7F7A72',
                    600: '#625E57',
                    700: '#4A4742',
                    800: '#33312E',
                    900: '#22211F',
                },
                success: { DEFAULT: '#3E6B4F', soft: '#E7EFE9' },
                danger: { DEFAULT: '#9F3A2F', soft: '#F6E6E3' },
            },
            fontFamily: {
                serif: ['"Cormorant Garamond"', 'Georgia', ...defaultTheme.fontFamily.serif],
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            letterSpacing: {
                eyebrow: '0.18em',
            },
            boxShadow: {
                soft: '0 1px 2px rgba(14, 13, 12, 0.04), 0 12px 32px -16px rgba(14, 13, 12, 0.14)',
                lift: '0 24px 64px -24px rgba(14, 13, 12, 0.35)',
            },
            maxWidth: {
                shell: '72rem',
            },
        },
    },

    plugins: [forms],
};

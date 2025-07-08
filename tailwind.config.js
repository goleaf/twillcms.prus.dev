import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import aspectRatio from '@tailwindcss/aspect-ratio'
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './storage/framework/views/*.php',
        './app/View/Components/**/*.php',
        './app/Http/Controllers/**/*.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },
                secondary: {
                    50: '#fdf2f8',
                    100: '#fce7f3',
                    200: '#fbcfe8',
                    300: '#f9a8d4',
                    400: '#f472b6',
                    500: '#ec4899',
                    600: '#db2777',
                    700: '#be185d',
                    800: '#9d174d',
                    900: '#831843',
                    950: '#500724',
                },
                neutral: {
                    50: '#fafafa',
                    100: '#f5f5f5',
                    200: '#e5e5e5',
                    300: '#d4d4d4',
                    400: '#a3a3a3',
                    500: '#737373',
                    600: '#525252',
                    700: '#404040',
                    800: '#262626',
                    900: '#171717',
                    950: '#0a0a0a',
                },
                success: {
                    50: '#f0fdf4',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                },
                warning: {
                    50: '#fffbeb',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                },
                danger: {
                    50: '#fef2f2',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '112': '28rem',
                '128': '32rem',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
                'spin-slow': 'spin 3s linear infinite',
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
                slideDown: {
                    '0%': { transform: 'translateY(-10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
            typography: {
                DEFAULT: {
                    css: {
                        maxWidth: 'none',
                        color: 'inherit',
                        a: {
                            color: 'inherit',
                            textDecoration: 'none',
                            fontWeight: '500',
                            '&:hover': {
                                color: '#3b82f6',
                            },
                        },
                        '[class~="lead"]': {
                            color: 'inherit',
                        },
                        strong: {
                            color: 'inherit',
                        },
                        'ol > li::before': {
                            color: 'inherit',
                        },
                        'ul > li::before': {
                            backgroundColor: 'currentColor',
                        },
                        hr: {
                            borderColor: 'currentColor',
                            opacity: '0.1',
                        },
                        blockquote: {
                            color: 'inherit',
                            borderLeftColor: 'currentColor',
                            opacity: '0.8',
                        },
                        h1: {
                            color: 'inherit',
                        },
                        h2: {
                            color: 'inherit',
                        },
                        h3: {
                            color: 'inherit',
                        },
                        h4: {
                            color: 'inherit',
                        },
                        'figure figcaption': {
                            color: 'inherit',
                        },
                        code: {
                            color: 'inherit',
                        },
                        'a code': {
                            color: 'inherit',
                        },
                        pre: {
                            color: 'inherit',
                            backgroundColor: 'transparent',
                        },
                        thead: {
                            color: 'inherit',
                            borderBottomColor: 'currentColor',
                        },
                        'tbody tr': {
                            borderBottomColor: 'currentColor',
                            opacity: '0.1',
                        },
                    },
                },
            },
            boxShadow: {
                'soft': '0 2px 15px 0 rgba(0, 0, 0, 0.1)',
                'strong': '0 10px 40px 0 rgba(0, 0, 0, 0.15)',
                'inner-soft': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.05)',
            },
        },
    },

    plugins: [
        forms,
        typography,
        aspectRatio,
        // Custom utilities and components
        function({ addUtilities, addComponents, theme }) {
            // Custom scrollbar styles
            addUtilities({
                '.scrollbar-thin': {
                    'scrollbar-width': 'thin',
                    'scrollbar-color': `${theme('colors.gray.400')} transparent`,
                },
                '.scrollbar-webkit': {
                    '&::-webkit-scrollbar': {
                        width: '8px',
                    },
                    '&::-webkit-scrollbar-track': {
                        background: 'transparent',
                    },
                    '&::-webkit-scrollbar-thumb': {
                        'background-color': theme('colors.gray.400'),
                        'border-radius': '20px',
                        border: '2px solid transparent',
                    },
                },
            });

            // Custom components
            addComponents({
                '.btn': {
                    'padding': `${theme('spacing.2')} ${theme('spacing.4')}`,
                    'border-radius': theme('borderRadius.md'),
                    'font-weight': theme('fontWeight.medium'),
                    'transition': 'all 0.2s ease-in-out',
                    'cursor': 'pointer',
                    'display': 'inline-flex',
                    'align-items': 'center',
                    'justify-content': 'center',
                    '&:focus': {
                        'outline': `2px solid ${theme('colors.primary.500')}`,
                        'outline-offset': '2px',
                    },
                },
                '.btn-primary': {
                    'background-color': theme('colors.primary.600'),
                    'color': theme('colors.white'),
                    '&:hover': {
                        'background-color': theme('colors.primary.700'),
                    },
                },
                '.btn-secondary': {
                    'background-color': theme('colors.neutral.200'),
                    'color': theme('colors.neutral.900'),
                    '&:hover': {
                        'background-color': theme('colors.neutral.300'),
                    },
                },
                '.card': {
                    'background-color': theme('colors.white'),
                    'border-radius': theme('borderRadius.lg'),
                    'box-shadow': theme('boxShadow.soft'),
                    'padding': theme('spacing.6'),
                    '&:hover': {
                        'box-shadow': theme('boxShadow.strong'),
                    },
                },
                '.section-padding': {
                    'padding-top': theme('spacing.16'),
                    'padding-bottom': theme('spacing.16'),
                    'border-radius': theme('borderRadius.xl'),
                },
            });
        },
    ],
}; 
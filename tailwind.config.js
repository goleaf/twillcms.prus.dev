import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import aspectRatio from '@tailwindcss/aspect-ratio'
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import aspectRatio from '@tailwindcss/aspect-ratio';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.ts',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
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
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
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
            },
        },
    },

    plugins: [
        forms,
        typography,
        aspectRatio,
    ],
};
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './resources/**/*.ts',
    './storage/framework/views/*.php',
    './app/View/Components/**/*.php',
    './app/Twill/Capsules/**/*.php',
    './app/Twill/Capsules/**/*.blade.php',
    './app/Twill/Capsules/**/*.vue',
    './app/Twill/Capsules/**/*.js',
    './app/Twill/Capsules/**/*.ts',
  ],
  
  // Dark mode strategy
  darkMode: 'class',
  
  theme: {
    extend: {
      // Optimized color palette
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
      
      // Optimized typography
      fontFamily: {
        sans: [
          'Inter var',
          'Inter',
          'ui-sans-serif',
          'system-ui',
          '-apple-system',
          'BlinkMacSystemFont',
          '"Segoe UI"',
          'Roboto',
          '"Helvetica Neue"',
          'Arial',
          '"Noto Sans"',
          'sans-serif',
          '"Apple Color Emoji"',
          '"Segoe UI Emoji"',
          '"Segoe UI Symbol"',
          '"Noto Color Emoji"',
        ],
        serif: [
          'Charter',
          'ui-serif',
          'Georgia',
          'Cambria',
          '"Times New Roman"',
          'Times',
          'serif',
        ],
        mono: [
          'ui-monospace',
          'SFMono-Regular',
          '"SF Mono"',
          'Menlo',
          'Monaco',
          'Consolas',
          '"Liberation Mono"',
          '"Courier New"',
          'monospace',
        ],
      },
      
      // Custom spacing for better rhythm
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '128': '32rem',
        '144': '36rem',
      },
      
      // Enhanced shadows
      boxShadow: {
        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
        'strong': '0 10px 40px -10px rgba(0, 0, 0, 0.1), 0 2px 10px -5px rgba(0, 0, 0, 0.04)',
        'glow': '0 0 20px rgba(59, 130, 246, 0.5)',
        'glow-lg': '0 0 40px rgba(59, 130, 246, 0.3)',
      },
      
      // Enhanced border radius
      borderRadius: {
        'xl': '1rem',
        '2xl': '1.5rem',
        '3xl': '2rem',
      },
      
      // Animation and transitions
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'fade-out': 'fadeOut 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'slide-down': 'slideDown 0.3s ease-out',
        'scale-in': 'scaleIn 0.2s ease-out',
        'bounce-gentle': 'bounceGentle 2s infinite',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'shimmer': 'shimmer 2s linear infinite',
      },
      
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        fadeOut: {
          '0%': { opacity: '1' },
          '100%': { opacity: '0' },
        },
        slideUp: {
          '0%': { transform: 'translateY(100%)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideDown: {
          '0%': { transform: 'translateY(-100%)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        scaleIn: {
          '0%': { transform: 'scale(0.9)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
        bounceGentle: {
          '0%, 100%': {
            transform: 'translateY(-5%)',
            animationTimingFunction: 'cubic-bezier(0.8, 0, 1, 1)',
          },
          '50%': {
            transform: 'translateY(0)',
            animationTimingFunction: 'cubic-bezier(0, 0, 0.2, 1)',
          },
        },
        shimmer: {
          '0%': { transform: 'translateX(-100%)' },
          '100%': { transform: 'translateX(100%)' },
        },
      },
      
      // Custom screens for better responsiveness
      screens: {
        'xs': '475px',
        '3xl': '1920px',
        '4xl': '2560px',
      },
      
      // Enhanced backdrop blur
      backdropBlur: {
        'xs': '2px',
        '3xl': '64px',
      },
      
      // Custom z-index scale
      zIndex: {
        '60': '60',
        '70': '70',
        '80': '80',
        '90': '90',
        '100': '100',
      },
      
      // Enhanced grid templates
      gridTemplateColumns: {
        '13': 'repeat(13, minmax(0, 1fr))',
        '14': 'repeat(14, minmax(0, 1fr))',
        '15': 'repeat(15, minmax(0, 1fr))',
        '16': 'repeat(16, minmax(0, 1fr))',
      },
      
      // Custom aspect ratios
      aspectRatio: {
        '4/3': '4 / 3',
        '3/2': '3 / 2',
        '2/3': '2 / 3',
        '9/16': '9 / 16',
      },
      
      // Enhanced line height
      lineHeight: {
        '12': '3rem',
        '14': '3.5rem',
        '16': '4rem',
      },
      
      // Custom max widths
      maxWidth: {
        '8xl': '88rem',
        '9xl': '96rem',
        '10xl': '104rem',
      },
      
      // Performance-optimized transitions
      transitionProperty: {
        'height': 'height',
        'spacing': 'margin, padding',
        'colors': 'color, background-color, border-color, text-decoration-color, fill, stroke',
      },
      
      // Custom transforms
      scale: {
        '102': '1.02',
        '103': '1.03',
        '175': '1.75',
      },
      
      // Enhanced backdrop brightness
      backdropBrightness: {
        '25': '.25',
        '175': '1.75',
      },
    },
  },
  
  plugins: [
    forms({
      strategy: 'class',
    }),
    typography({
      modifiers: [],
    }),
    aspectRatio,
    
    // Custom utility plugins for performance
    function({ addUtilities, addComponents, theme }) {
      // High-performance utilities
      const newUtilities = {
        '.gpu-accelerated': {
          'will-change': 'transform',
          'transform': 'translateZ(0)',
          'backface-visibility': 'hidden',
          'perspective': '1000px',
        },
        '.smooth-scroll': {
          'scroll-behavior': 'smooth',
        },
        '.scroll-touch': {
          '-webkit-overflow-scrolling': 'touch',
        },
        '.text-rendering-optimized': {
          'text-rendering': 'optimizeLegibility',
          'font-feature-settings': '"kern" 1',
        },
        '.font-smoothing': {
          '-webkit-font-smoothing': 'antialiased',
          '-moz-osx-font-smoothing': 'grayscale',
        },
        '.contain-layout': {
          'contain': 'layout',
        },
        '.contain-style': {
          'contain': 'style',
        },
        '.contain-paint': {
          'contain': 'paint',
        },
        '.contain-strict': {
          'contain': 'strict',
        },
      }
      
      addUtilities(newUtilities)
      
      // Enhanced component utilities
      const newComponents = {
        '.btn': {
          'padding': `${theme('spacing.2')} ${theme('spacing.4')}`,
          'border-radius': theme('borderRadius.md'),
          'font-weight': theme('fontWeight.medium'),
          'transition': 'all 0.15s ease-in-out',
          'display': 'inline-flex',
          'align-items': 'center',
          'justify-content': 'center',
          'text-decoration': 'none',
          'cursor': 'pointer',
          'border': 'none',
          'outline': 'none',
          'will-change': 'transform',
          '&:focus': {
            'outline': `2px solid ${theme('colors.primary.500')}`,
            'outline-offset': '2px',
          },
          '&:disabled': {
            'opacity': '0.5',
            'cursor': 'not-allowed',
          },
        },
        '.btn-primary': {
          'background-color': theme('colors.primary.600'),
          'color': theme('colors.white'),
          '&:hover': {
            'background-color': theme('colors.primary.700'),
            'transform': 'translateY(-1px)',
          },
        },
        '.btn-secondary': {
          'background-color': theme('colors.neutral.200'),
          'color': theme('colors.neutral.900'),
          '&:hover': {
            'background-color': theme('colors.neutral.300'),
            'transform': 'translateY(-1px)',
          },
        },
        '.card': {
          'background-color': theme('colors.white'),
          'border-radius': theme('borderRadius.lg'),
          'box-shadow': theme('boxShadow.soft'),
          'padding': theme('spacing.6'),
          'transition': 'all 0.2s ease-in-out',
          '&:hover': {
            'box-shadow': theme('boxShadow.strong'),
            'transform': 'translateY(-2px)',
          },
        },
        '.glass-morphism': {
          'background': 'rgba(255, 255, 255, 0.25)',
          'backdrop-filter': 'blur(10px)',
          'border': '1px solid rgba(255, 255, 255, 0.18)',
          'border-radius': theme('borderRadius.xl'),
        },
        '.loading-skeleton': {
          'background': 'linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%)',
          'background-size': '200% 100%',
          'animation': 'shimmer 2s infinite',
        },
      }
      
      addComponents(newComponents)
    },
  ],
  
  // Optimize for production
  corePlugins: {
    // Disable unused core plugins for smaller bundle size
    container: false,
    float: false,
    clear: false,
    skew: false,
    caretColor: false,
    sepia: false,
    rotate: false,
  },
  
  // Performance optimizations
  future: {
    hoverOnlyWhenSupported: true,
  },
  
  // Experimental features
  experimental: {
    optimizeUniversalDefaults: true,
  },
} 
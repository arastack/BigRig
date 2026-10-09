import plugin from "tailwindcss/plugin";
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // BigRig brand: cadmium yellow from the logo, near-black "ink", white
        brand: {
          50: '#FFFDE5',
          100: '#FFF9BF',
          200: '#FFF380',
          300: '#FFEC40',
          400: '#FFE500',
          500: '#F5D400',
          600: '#CCAE00',
          700: '#8F7A00',
          800: '#5C4F00',
          900: '#332C00',
        },
        ink: {
          50: '#F6F6F5',
          100: '#E9E9E7',
          200: '#D3D3CF',
          300: '#A9A9A3',
          400: '#7A7A73',
          500: '#57574F',
          600: '#3D3D37',
          700: '#2A2A26',
          800: '#1A1A17',
          900: '#111110',
          950: '#0A0A09',
        },
        // Default Tailwind accents used by older portal screens, folded into the brand
        blue: {
          50: '#FFFDE5', 100: '#FFF9BF', 200: '#FFF380', 300: '#FFEC40', 400: '#E6CE00',
          500: '#2A2A26', 600: '#111110', 700: '#0A0A09', 800: '#0A0A09', 900: '#0A0A09', 950: '#0A0A09',
        },
        indigo: {
          50: '#F6F6F5', 100: '#E9E9E7', 200: '#D3D3CF', 300: '#A9A9A3', 400: '#7A7A73',
          500: '#3D3D37', 600: '#2A2A26', 700: '#1A1A17', 800: '#111110', 900: '#0A0A09', 950: '#0A0A09',
        },
        purple: {
          50: '#F6F6F5', 100: '#E9E9E7', 200: '#D3D3CF', 300: '#A9A9A3', 400: '#7A7A73',
          500: '#3D3D37', 600: '#2A2A26', 700: '#1A1A17', 800: '#111110', 900: '#0A0A09', 950: '#0A0A09',
        },
        orange: {
          50: '#FFFDE5', 100: '#FFF9BF', 200: '#FFF380', 300: '#FFEC40', 400: '#FFE500',
          500: '#CCAE00', 600: '#8F7A00', 700: '#5C4F00', 800: '#332C00', 900: '#332C00', 950: '#332C00',
        },
        gray: {
          50: '#F9FAFB',
          100: '#F3F4F6',
          200: '#E5E7EB',
          300: '#BFC4CD',
          400: '#9CA3AF',
          500: '#6B7280',
          600: '#4B5563',
          700: '#374151',
          800: '#1F2937',
          900: '#111827',
          950: '#030712',
        },
        // The admin template's violet accent, remapped to the brand: light tints are
        // yellow, solid shades are near-black, so older screens match the new look.
        violet: {
          50: '#FFFDE5',
          100: '#FFF9BF',
          200: '#FFF380',
          300: '#FFEC40',
          400: '#E6CE00',
          500: '#111110',
          600: '#2A2A26',
          700: '#0A0A09',
          800: '#0A0A09',
          900: '#0A0A09',
          950: '#0A0A09',
        },
        sky: {
          50: '#E3F3FF',
          100: '#D1ECFF',
          200: '#B6E1FF',
          300: '#A0D7FF',
          400: '#7BC8FF',
          500: '#67BFFF',
          600: '#56B1F3',
          700: '#3193DA',
          800: '#1C71AE',
          900: '#124D79',
          950: '#0B324F',
        },
        green: {
          50: '#D2FFE2',
          100: '#B1FDCD',
          200: '#8BF0B0',
          300: '#67E294',
          400: '#4BD37D',
          500: '#3EC972',
          600: '#34BD68',
          700: '#239F52',
          800: '#15773A',
          900: '#0F5429',
          950: '#0A3F1E',
        },
        red: {
          50: '#FFE8E8',
          100: '#FFD1D1',
          200: '#FFB2B2',
          300: '#FF9494',
          400: '#FF7474',
          500: '#FF5656',
          600: '#FA4949',
          700: '#E63939',
          800: '#C52727',
          900: '#941818',
          950: '#600F0F',
        },
        yellow: {
          50: '#FFF2C9',
          100: '#FFE7A0',
          200: '#FFE081',
          300: '#FFD968',
          400: '#F7CD4C',
          500: '#F0BB33',
          600: '#DFAD2B',
          700: '#BC9021',
          800: '#816316',
          900: '#4F3D0E',
          950: '#342809',
        },
      },
      fontFamily: {
        inter: ['Inter', 'sans-serif'],
        display: ['"Barlow Condensed"', 'Inter', 'sans-serif'],
      },
      fontSize: {
        xs: ['0.75rem', { lineHeight: '1.5' }],
        sm: ['0.875rem', { lineHeight: '1.5715' }],
        base: ['1rem', { lineHeight: '1.5', letterSpacing: '-0.01em' }],
        lg: ['1.125rem', { lineHeight: '1.5', letterSpacing: '-0.01em' }],
        xl: ['1.25rem', { lineHeight: '1.5', letterSpacing: '-0.01em' }],
        '2xl': ['1.5rem', { lineHeight: '1.33', letterSpacing: '-0.01em' }],
        '3xl': ['1.88rem', { lineHeight: '1.33', letterSpacing: '-0.01em' }],
        '4xl': ['2.25rem', { lineHeight: '1.25', letterSpacing: '-0.02em' }],
        '5xl': ['3rem', { lineHeight: '1.25', letterSpacing: '-0.02em' }],
        '6xl': ['3.75rem', { lineHeight: '1.2', letterSpacing: '-0.02em' }],
      },
      screens: {
        xs: '480px',
      },
      borderWidth: {
        3: '3px',
      },
      minWidth: {
        36: '9rem',
        44: '11rem',
        56: '14rem',
        60: '15rem',
        72: '18rem',
        80: '20rem',
      },
      maxWidth: {
        '8xl': '88rem',
        '9xl': '96rem',
      },
      zIndex: {
        60: '60',
      },
    },
  },

  plugins: [
    forms,
    typography,
    // add custom variant for expanding sidebar
    plugin(({ addVariant, e }) => {
      addVariant('sidebar-expanded', ({ modifySelectors, separator }) => {
        modifySelectors(({ className }) => `.sidebar-expanded .${e(`sidebar-expanded${separator}${className}`)}`);
      });
    }),
  ],
};

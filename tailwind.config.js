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
        // Custom colors sesuai desain Anda
        'wms-red': {
          950: '#5C0A13',
          900: '#7A0D18',
          800: '#96101F',
          700: '#AF1424',
          600: '#C81E2C',
          550: '#D62E3C',
          500: '#E14B54',
          300: '#EE9299',
          200: '#F5BEC2',
          100: '#FBDEE0',
          50: '#FDF1F2',
        },
        'wms-ink': {
          950: '#0E0D12',
          900: '#17151C',
          800: '#26232C',
          700: '#3D3A44',
          600: '#57545F',
          500: '#75727C',
          400: '#948F99',
          300: '#B7B3BB',
          200: '#DCDADF',
        },
        'wms-paper': '#F7F6F5',
        'wms-paper-dim': '#F1F0EE',
        'wms-line': '#E7E5E3',
        'wms-line2': '#EFEDEB',
        'wms-green': '#1B7A46',
        'wms-green-bg': '#E4F3EA',
        'wms-amber': '#9A6206',
        'wms-amber-bg': '#FAF0D9',
        'wms-blue': '#25538C',
        'wms-blue-bg': '#E8F0F9',
        'wms-gray': '#75727C',
        'wms-gray-bg': '#EFEDEC',
      },
      fontFamily: {
        display: ['"Space Grotesk"', 'sans-serif'],
        body: ['"Inter"', 'sans-serif'],
        mono: ['"IBM Plex Mono"', 'monospace'],
      },
      boxShadow: {
        'wms-sm': '0 1px 2px rgba(14,13,18,0.05)',
        'wms-md': '0 4px 16px rgba(14,13,18,0.06)',
        'wms-lg': '0 16px 40px rgba(14,13,18,0.12)',
        'wms-red': '0 8px 20px rgba(200,30,44,0.24)',
      },
      animation: {
        'fade-in': 'fadeIn 0.2s ease',
        'fade-in-up': 'fadeInUp 0.18s ease',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: 0, transform: 'translateY(4px)' },
          '100%': { opacity: 1, transform: 'translateY(0)' },
        },
        fadeInUp: {
          '0%': { opacity: 0, transform: 'translateY(10px)' },
          '100%': { opacity: 1, transform: 'translateY(0)' },
        },
      },
    },
  },
  plugins: [],
}
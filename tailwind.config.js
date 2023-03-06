const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
  content: [
    "./index.php",
    "./pages/**/*.{php,js}",
    "./system/parts/**/*.{php,js}",
  ],
  theme: {
    extend: {
      backgroundOpacity: ["active"],
      fontFamily: {
        sans: ["Epilogue", ...defaultTheme.fontFamily.sans],
        kitab: ["Kitab"],
      },
      colors: {
        'green-1': '#5E878A',
        'green-2': '#007380',
        'green-3': '#17484D',

        'brown-1': '#8A7448',
        'brown-2': '#665233',
        'brown-3': '#474235',

        'light-1': '#FAFAFA',
        'light-2': '#F0E6DD',
        'light-3': '#BCC5D1',
        'light-4': '#3DCCCC',

        'stroke-1': '#E5E5E5',
        'stroke-2': '#CCCCCC'
      },
      screens: {
        xs: '420px' 
      }
    },
  },
  plugins: [],
};
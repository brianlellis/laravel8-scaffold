module.exports = {
  mode: 'jit',
  purge: [
    './storage/framework/views/*.php',
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './app/Modules/**/Views/**/*.blade.php',
    './app/Modules/**/Views/**/*.js',
    './app/Modules/**/Views/**/*.vue',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      height: {
        "screen-5":  "5vh",
        "screen-10": "10vh",
        "screen-15": "15vh",
        "screen-20": "20vh",
        "screen-25": "25vh",
        "screen-30": "30vh",
        "screen-35": "35vh",
        "screen-40": "40vh",
        "screen-45": "45vh",
        "screen-50": "50vh",
        "screen-55": "55vh",
        "screen-60": "60vh",
        "screen-65": "65vh",
        "screen-70": "70vh",
        "screen-75": "75vh",
        "screen-80": "80vh",
        "screen-85": "85vh",
        "screen-90": "90vh",
        "screen-90": "90vh"
      },
      lineHeight: {
        "vh-5":  "5vh",
        "vh-10": "10vh",
        "vh-15": "15vh",
        "vh-20": "20vh",
        "vh-25": "25vh",
        "vh-30": "30vh",
        "vh-35": "35vh",
        "vh-40": "40vh",
        "vh-45": "45vh",
        "vh-50": "50vh",
        "vh-55": "55vh",
        "vh-60": "60vh",
        "vh-65": "65vh",
        "vh-70": "70vh",
        "vh-75": "75vh",
        "vh-80": "80vh",
        "vh-85": "85vh",
        "vh-90": "90vh",
        "vh-90": "90vh"
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
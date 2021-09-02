module.exports = {
  plugins: [
    require("tailwindcss"),
    require('postcss-import'),
    require('postcss-nesting'),
    require('postcss-custom-selectors'),
    require('postcss-custom-media'),
    require('postcss-media-minmax')
  ]
}
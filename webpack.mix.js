const mix         = require('laravel-mix');
const tailwindcss = require('tailwindcss');
const fs          = require('fs');
const homedir     = require('os').homedir();
const valet_cert  = 'Certificates/laravel8.test';

if ( fs.existsSync( homedir + '/.valet/' + valet_cert + '.key' ) ) {
  var ssl_cert_path = homedir + '/.valet/' + valet_cert; 
} else if ( fs.existsSync( homedir + '/.config/valet/' + valet_cert + '.key' ) ) {
  var ssl_cert_path = homedir + '/.config/valet/' + valet_cert; 
}

mix.js('resources/js/app.js', 'public/js')
  .postCss('resources/css/app.css', 'public/css')  
  .browserSync({
    proxy: 'https://laravel8.test',
    host: 'laravel8.test',
    open: 'external',
    https: {
      key:  ssl_cert_path + '.key',
      cert: ssl_cert_path + '.crt',
    }
  });
const mix = require('laravel-mix');

mix.options({
  clearConsole: false,
  cssNano: {
    minifyFontValues: false,
  },
  processCssUrls: false,
});

mix.js('wp-content/themes/acesdelta/assets/js/main.js', 'wp-content/themes/acesdelta/build')
  .sass('wp-content/themes/acesdelta/assets/sass/style.scss', 'wp-content/themes/acesdelta/build');

const mix = require('laravel-mix');

mix.js('assets/js/app.js', 'build')
	.sass('assets/scss/app.scss', 'build')
	.setPublicPath('build');

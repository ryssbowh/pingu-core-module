const mix = require('laravel-mix');
const path = require('path');
const webpack = require('webpack');

var dir = __dirname;
var name = dir.split(path.sep).pop();

var assetPath = __dirname + '/Resources/assets';
var publicPath = path.resolve(__dirname,'../../public/modules/'+name);

mix.webpackConfig({
  resolve: {
    alias: {
      'pingu-helpers': path.resolve(assetPath + '/js/components', './helpers'),
      'pingu-core': path.resolve(assetPath + '/js/components', './core')
    }
  }
});

//Javascript
mix.js(assetPath + '/js/app.js', publicPath + '/js/'+name+'.js').sourceMaps();
mix.autoload({
	'jquery': ['$', 'jQuery'],
	'moment' : ['moment'],
	'bootstrap': ['bootstrap'],
	'tempusdominus-bootstrap-4': ['datetimepicker']
});

//Css
mix.sass(assetPath + '/sass/app.scss', publicPath + '/css/'+name+'.css');
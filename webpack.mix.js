const mix = require('laravel-mix');
const path = require('path');
const webpack = require('webpack');

var dir = __dirname;
var name = dir.split(path.sep).pop();

var assetPath = __dirname + '/Resources/assets';
var publicPath = 'module-assets/';

//Javascript
mix.js(assetPath + '/js/app.js', publicPath + name+'.js').sourceMaps();

//Css
mix.sass(assetPath + '/sass/app.scss', publicPath + name+'.css');
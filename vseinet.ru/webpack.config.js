var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('web/bundles/')
    .setPublicPath('/bundles')
    .cleanupOutputBeforeBuild()
    .addEntry('libs', './assets/libs.js')
    .addEntry('scripts', './assets/app.js')
    .addStyleEntry('styles', './assets/app.css')
    .enableVersioning()
    .enableSourceMaps(!Encore.isProduction());

module.exports = Encore.getWebpackConfig();
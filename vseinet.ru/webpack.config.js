var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('web/bundles/')
    .setPublicPath('/bundles')
    .cleanupOutputBeforeBuild()
    .configureBabel(function(babelConfig) {
        babelConfig.presets = [['env', {
            modules: false,
            useBuiltIns: true,
            targets: {
                browsers: [
                    '> 1%',
                    'last 2 versions',
                    'Firefox ESR',
                ],
            }
        }]];
    })
    .addEntry('libs', './assets/libs.js')
    .addEntry('scripts', './assets/app.js')
    .addStyleEntry('styles', './assets/app.css')
    .enableVersioning(Encore.isProduction())
    .enableSourceMaps(!Encore.isProduction());

const es5Config = Encore.getWebpackConfig();

Encore.reset();

Encore
    .setOutputPath('web/bundles/')
    .setPublicPath('/bundles')
    .cleanupOutputBeforeBuild()
    .configureBabel(function(babelConfig) {
        babelConfig.presets = [['env', {
            modules: false,
            useBuiltIns: true,
            targets: {
            browsers: [
                'Chrome >= 60',
                'Safari >= 10.1',
                'iOS >= 10.3',
                'Firefox >= 54',
                'Edge >= 15',
            ],
            },
        }]];
    })
    .addEntry('es6_libs', './assets/libs.js')
    .addEntry('es6_scripts', './assets/app.js')
    .enableVersioning(Encore.isProduction())
    .enableSourceMaps(!Encore.isProduction());

const es6Config = Encore.getWebpackConfig();

module.exports = [es5Config, es6Config];

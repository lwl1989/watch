let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
// mix.webpackConfig({
//     module: {
//         rules: [
//             {
//                 test: /\.js$/,
//                 exclude: /node_modules/,
//                 loader: 'babel-loader',
//                 options: {
//                     "plugins": [["component", [
//                         {
//                             "libraryName": "element-ui",
//                             "styleLibraryName": "theme-chalk"
//                         }
//                     ]]]
//                 }
//             },
//             // {
//             //     test: /\.css$/,
//             //     use: 'css-loader'
//             // },
//             // {
//             //     test: /\.css$/,
//             //     loader: "style-loader!css-loader"
//             // },
//         ]
//     },
//     // plugins:[
//     //     new ExtractTextPlugin('styles.css')
//     // ]
// });

mix
    .js('resources/assets/js/admin.js', 'public/js')
    .js('resources/assets/js/auth.js', 'public/js')
    .js('resources/assets/js/tools/jquery-1.11.0.min.js', 'public/js')
    .extract(['element-ui'],'public/js/element-ui.js')
    .extract(['vue','vue-router','vue2-google-maps'],'public/js/vue.js')
    .autoload({
        vue: ['Vue', 'window.Vue']
    })
    .copy('resources/assets/css/login.css', 'public/css')
    .copy('resources/assets/css/',          'public/css')
    .copy('resources/assets/images/',       'public/images')
    .copy('resources/assets/html/',         'public/html')
    .copy('resources/assets/sights/',       'public/sights')
    .copy('resources/assets/js/tools/speech.js',            'public/js')
    .copy('resources/assets/js/tools/zepto.min.js',         'public/js')
    .copy('resources/assets/js/tools/swiper-4.5.3.min.js',  'public/js')
    .sass('resources/assets/sass/admin.scss',           'public/css')
;
mix.version();
//if (mix.config.inProduction) {

//}
//
// console.log(mix.config);
// return;
const del = require('del');
const fs = require('fs-extra');
const mix = require('laravel-mix');
const convertToFileHash = require('laravel-mix-make-file-hash');

// Delete asset directories.
del.sync([
    'public/css',
    'public/js',
    'public/lib',
    'public/webfonts',
]);

// Compile styles.
mix.sass('resources/sass/app.scss', 'public/css');

// Compress JavaScript files.
if (mix.inProduction()) {
    fs.readdirSync('./resources/js', {withFileTypes: true})
        .filter(item => !item.isDirectory())
        .map(item => item.name)
        .forEach(file => mix.js('resources/js/' + file, 'public/js/' + file));
} else {
    mix.copy('resources/js/*.*', 'public/js');
}

// Copy required libraries from node_modules.
let filesFromLibraries = [
    'node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid*.*',
    'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
];
mix.copy(filesFromLibraries, 'public/lib');

if (mix.inProduction()) {
    // Settings only for production environment.
    mix.disableNotifications();

    // Setup hash-based file names for the production environment.
    mix.version();
    mix.then(() => {
        convertToFileHash({
            publicPath: 'public',
            manifestFilePath: 'public/mix-manifest.json',
            blacklist: ['lib/fa-**'] // Exclude webfont files due to fixed path in Font Awesome SCSS.
        });
    });
} else {
    // Settings only for development environment.
    mix.webpackConfig({devtool: 'source-map'})
        .sourceMaps();
}

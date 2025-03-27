const fs = require('fs-extra');
const mix = require('laravel-mix');
const convertToFileHash = require('laravel-mix-make-file-hash');

// Delete asset directories.
[
    'public/css',
    'public/js',
    'public/lib',
].forEach(outputPath => fs.removeSync(outputPath));

// Compile styles.
mix.sass('resources/sass/app.scss', 'public/css');

// Compress JavaScript files.
if (fs.existsSync('./resources/js')) {
    if (mix.inProduction()) {
        fs.readdirSync('./resources/js', {withFileTypes: true})
            .filter(item => !item.isDirectory())
            .map(item => item.name)
            .forEach(file => mix.js('resources/js/' + file, 'public/js/' + file));
    } else {
        mix.copy('resources/js/*.*', 'public/js');
    }
}

// Copy required libraries from node_modules.
let filesFromLibraries = [
    'node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid*.*',
    'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
    'node_modules/rapidoc/dist/rapidoc-min.js',
];
mix.copy(filesFromLibraries, 'public/lib');
mix.copy('node_modules/alpinejs/dist/cdn.min.js', 'public/lib/alpinejs.min.js');

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

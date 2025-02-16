import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import path from 'path';
import fs from 'fs';
import crypto from 'crypto';

const filesFromLibraries = {
    'node_modules/alpinejs/dist/cdn.min.js': 'alpinejs',
    'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js': 'bootstrap.bundle',
    'node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.ttf': 'fa-solid-900.ttf',
    'node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.woff2': 'fa-solid-900.woff2',
};

const filesFromLibrariesForConfiguration = [];
const filesFromLibrariesForManifest = [];
for (const [sourceFilePath, fileName] of Object.entries(filesFromLibraries)) {
    let hashedFileNameWithExtension = generateHashedFileName(fileName, sourceFilePath);
    filesFromLibrariesForConfiguration.push({
        src: sourceFilePath,
        dest: 'lib',
        // rename relevant for viteStaticCopy configuration!
        rename: () => hashedFileNameWithExtension,
    });
    filesFromLibrariesForManifest[sourceFilePath] = {
        file: `lib/${hashedFileNameWithExtension}`,
        src: sourceFilePath,
        isEntry: true,
    };
}

function generateHashedFileName(fileName, fullPath) {
    const fileExtension = path.extname(fullPath);
    if (['.ttf', '.woff2'].includes(fileExtension)) {
        return fileName;
    }
    const fileBuffer = fs.readFileSync(fullPath);
    const hash = crypto.createHash('md5').update(fileBuffer).digest('hex');
    return `${fileName}.${hash}${fileExtension}`;
}

export default defineConfig({
    plugins: [
        // Compile SCSS to CSS, JavaScript files.
        laravel({
            input: [
                'resources/js/app.js',
                'resources/sass/app.scss',
            ],
            // Refresh pages when compiled assets have changed.
            refresh: true,
        }),
        // Copy static files from libraries...
        viteStaticCopy({
            targets: filesFromLibrariesForConfiguration,
        }),
        // ... and add their hash-based file names to the manifest file.
        {
            name: 'add-copied-files-to-manifest',
            closeBundle() {
                const manifestPath = path.resolve(__dirname, 'public/build/manifest.json');
                let manifest = {
                    ...JSON.parse(fs.readFileSync(manifestPath, 'utf-8')),
                    ...filesFromLibrariesForManifest
                };
                fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
            }
        },
    ],
    // Configure how SCSS is preprocessed.
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
                silenceDeprecations: [
                    // Bootstrap framework is still using deprecated syntax.
                    'import',
                    'legacy-js-api',
                ],
            }
        }
    }
});

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import path from 'path';
import fs from 'fs';
import crypto from 'crypto';

const filesFromLibraries = {
    'node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.woff2': 'fa-solid-900.woff2',
    'node_modules/alpinejs/dist/cdn.min.js': 'alpinejs',
    'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js': 'bootstrap.bundle',
    'node_modules/rapidoc/dist/rapidoc-min.js': 'rapidoc',
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
    if (fileExtension === '.woff2') {
        return fileName;
    }
    const fileBuffer = fs.readFileSync(fullPath);
    const hash = crypto.createHash('md5').update(fileBuffer).digest('hex');
    return `${fileName}.${hash}${fileExtension}`;
}

export default defineConfig(({ mode }) => {
    return {
        plugins: [
            // Compile SCSS to CSS, JavaScript files.
            laravel({
                input: [
                    'resources/js/app.js',
                    'resources/sass/app.scss',
                    ...fs.readdirSync('resources/js', {withFileTypes: true})
                        .filter(f => !f.isDirectory())
                        .map(f => f.name)
                        .map(f => `resources/js/${f}`),
                ],
                // Refresh pages when compiled assets have changed.
                refresh: true,
            }),
            // Refresh pages when Blade files have changed. See https://freek.dev/2277-using-laravel-vite-to-automatically-refresh-your-browser-when-changing-a-blade-file.
            {
                name: 'blade',
                handleHotUpdate({file, server}) {
                    if (file.endsWith('.blade.php')) {
                        server.ws.send({
                            type: 'full-reload',
                            path: '*',
                        });
                    }
                },
            },
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
                    additionalData: (content) => {
                        const rootDir = mode === 'production' ? '/build/lib' : '/lib';
                        return `$root-directory: '${rootDir}';\n${content}`;
                    },
                },
            },
        },
    };
});

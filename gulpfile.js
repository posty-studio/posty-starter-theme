'use strict';

const fs = require('fs');
const del = require('del');
const gulp = require('gulp');
const rev = require('gulp-rev');
const jsonTransform = require('gulp-json-transform');
const sourcemaps = require('gulp-sourcemaps');
const minimist = require('minimist');
const gulpif = require('gulp-if');
const sass = require('gulp-sass');
sass.compiler = require('sass');
const Fiber = require('fibers');
const notify = require('gulp-notify');
const plumber = require('gulp-plumber');
const kroket = require('kroket');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const encodeBackgroundSVGs = require('postcss-encode-background-svgs');
const imagemin = require('gulp-imagemin');
const pkg = require('./package.json');
const log = require('fancy-log');
const PluginError = require('plugin-error');
const webpack = require('webpack');
const webpackConfig = require('./webpack.config.js');
const browserSync = require('browser-sync');

browserSync.create();

const options = minimist(process.argv.slice(2));
const isProduction = options.env && options.env === 'production';

const config = {
    src: {
        js: './src/js/**/*.js',
        php: ['./*.php', './includes/**/*.php', './partials/**/*.php'],
        sass: './src/scss/**/*.scss',
        img: './src/img/**/*',
        fonts: './src/fonts/**/*',
    },
    dist: {
        base: './assets',
        js: './assets/js',
        css: './assets/css',
        img: './assets/img',
        fonts: './assets/fonts',
    },
};

// Reload BrowserSync
function reload(cb) {
    if (options.url) {
        browserSync.reload();
    }

    cb();
}

// Clean
function clean(cb) {
    del(`${config.dist.base}/**/*`);
    cb();
}

// Run Kroket
function runKroket(cb) {
    kroket();
    cb();
}

// Compile CSS
function css() {
    const onError = (err) => {
        notify.onError({
            title: 'SCSS Error',
            message: '<%= error.message %>',
        })(err);

        this.emit('end');
    };

    const postcssPlugins = isProduction
        ? [autoprefixer(), cssnano(), encodeBackgroundSVGs()]
        : [encodeBackgroundSVGs()];

    del(`${config.dist.css}/**/*`);

    return gulp
        .src(config.src.sass)
        .pipe(plumber({ errorHandler: onError }))
        .pipe(sourcemaps.init())
        .pipe(
            sass({
                outputStyle: 'expanded',
                fiber: Fiber,
            })
        )
        .pipe(sourcemaps.write())
        .pipe(postcss(postcssPlugins))
        .pipe(gulpif(isProduction, rev()))
        .pipe(gulp.dest(config.dist.css))
        .pipe(gulpif(isProduction, rev.manifest('manifest.php')))
        .pipe(
            gulpif(
                isProduction,
                jsonTransform((data, file) => `<?php return ${json2php(JSON.parse(JSON.stringify(data)))};`)
            )
        )
        .pipe(gulpif(isProduction, gulp.dest(config.dist.base)))
        .pipe(gulpif(!isProduction, browserSync.stream()))
        .pipe(notify({ title: 'SCSS', message: 'Sass compiled successfully!' }));
}

// Create default empty style.css
function createStyleCSS(cb) {
    const contents = [
        '/**',
        ` * Theme Name: ${pkg.title}`,
        ` * Theme URI: ${pkg.homepage}`,
        ` * Description: ${pkg.description}`,
        ` * Author: ${pkg.author.name}`,
        ` * Author URI: ${pkg.author.url}`,
        ` * Version: ${pkg.version}`,
        ` * Text Domain: ${pkg.name}`,
        ' */',
    ].join('\n');

    fs.writeFileSync('style.css', contents);
    cb();
}

// Compile Javascript
function js() {
    const combinedConfig = Object.assign(
        {
            mode: isProduction ? 'production' : 'development',
            watch: !isProduction,
        },
        webpackConfig
    );

    return new Promise((resolve, reject) => {
        webpack(combinedConfig, (err, stats) => {
            if (err) {
                return reject(err);
            }

            if (stats.hasErrors()) {
                return reject(new Error(stats.compilation.errors.join('\n')));
            }

            log(
                '[Webpack]\n',
                stats.toString({
                    colors: true,
                    progress: true,
                })
            );

            resolve();
        });
    });
}

// Minify images
function minifyImages() {
    return gulp
        .src(config.src.img)
        .pipe(
            imagemin(
                [
                    imagemin.gifsicle({ interlaced: true }),
                    imagemin.mozjpeg({ progressive: true }),
                    imagemin.optipng({ optimizationLevel: 7 }),
                    imagemin.svgo({
                        plugins: [
                            { removeViewBox: false },
                            { removeDimensions: true },
                            { removeUselessStrokeAndFill: false },
                            { sortAttrs: true },
                            { cleanupIDs: false },
                        ],
                    }),
                ],
                {
                    verbose: true,
                }
            )
        )
        .pipe(gulp.dest(config.dist.img))
        .pipe(gulpif(!isProduction, browserSync.stream()));
}

// Copy fonts
function copyFonts() {
    return gulp
        .src(config.src.fonts)
        .pipe(gulp.dest(config.dist.fonts))
        .pipe(gulpif(!isProduction, browserSync.stream()));
}

// Run server
function watch() {
    if (options.url) {
        browserSync.init({
            proxy: options.url,
            notify: false,
            https: true,
        });

        gulp.watch(config.src.php, reload);
        gulp.watch(`${config.dist.js}/**/*`, reload);
    }

    gulp.watch(config.src.img, minifyImages);
    gulp.watch(config.src.sass, css);
    gulp.watch(config.src.fonts, copyFonts);
    gulp.watch('./kroket.config.js', runKroket);
}

// Tasks
gulp.task(
    'default',
    gulp.series(clean, createStyleCSS, copyFonts, minifyImages, runKroket, gulp.parallel(css, js), watch)
);
gulp.task('build', gulp.series(clean, createStyleCSS, copyFonts, runKroket, css, js, minifyImages));

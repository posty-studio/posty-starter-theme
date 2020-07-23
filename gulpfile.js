'use strict';

const fs = require('fs');
const del = require('del');
const gulp = require('gulp');
const rev = require('gulp-rev');
const sourcemaps = require('gulp-sourcemaps');
const minimist = require('minimist');
const gulpif = require('gulp-if');
const sass = require('gulp-sass');
sass.compiler = require('sass');
const Fiber = require('fibers');
const notify = require('gulp-notify');
const plumber = require('gulp-plumber');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
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
        img: ['./src/img/**/*'],
    },
    dist: {
        base: './assets',
        js: './assets/js',
        css: './assets/css',
        img: './assets/img',
    },
};

// Reload BrowserSync
function reload(done) {
    if (options.url) {
        browserSync.reload();
    }

    if (done) {
        done();
    }
}

// Clean
function clean(done) {
    del(`${config.dist.base}/**/*`);
    done();
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
        .pipe(gulpif(isProduction, postcss([autoprefixer(), cssnano()])))
        .pipe(gulpif(isProduction, rev()))
        .pipe(gulpif(isProduction, gulp.dest(config.dist.css)))
        .pipe(gulpif(isProduction, rev.manifest('manifest.json')))
        .pipe(gulp.dest(config.dist.css))
        .pipe(gulpif(!isProduction, browserSync.stream()))
        .pipe(notify({ title: 'SCSS', message: 'Sass compiled successfully!' }));
}

// Create default empty style.css
function createStyleCSS(done) {
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
    done();
}

// Compile Javascript
function js(done) {
    const combinedConfig = Object.assign(
        {
            mode: isProduction ? 'production' : 'development',
            watch: !isProduction,
        },
        webpackConfig
    );

    webpack(combinedConfig, (error, stats) => {
        if (error) {
            throw new PluginError('webpack', error);
        }

        log(
            '[Webpack]\n',
            stats.toString({
                colors: true,
                progress: true,
            })
        );
    });

    done();
    reload();
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
                            { removeUselessStrokeAndFill: false },
                            { sortAttrs: true },
                            { removeDimensions: true },
                            { removeTitle: true },
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
}

// Tasks
gulp.task('default', gulp.series(clean, createStyleCSS, minifyImages, gulp.parallel(css, js), watch));
gulp.task('build', gulp.series(clean, createStyleCSS, gulp.parallel(css, js), minifyImages));

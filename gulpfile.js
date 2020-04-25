const { src, dest, parallel, series, watch } = require('gulp');
const sass         = require('gulp-sass');
const concat       = require('gulp-concat');
const rename       = require('gulp-rename');
const uglify       = require('gulp-uglify');
const del          = require('del');
const sourcemaps   = require('gulp-sourcemaps');
const postcss      = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const merge        = require('merge-stream');
const cleanCSS     = require('gulp-clean-css');
const rev          = require('gulp-rev');

let paths = {
    'assets'    : './resources/assets',
    'public'    : './public',
    'components': './node_modules'
};

function css() {
    let sassStream = src([
            paths.assets + '/styles/app.scss',
        ])
        .pipe(
            sass({
                outputStyle: 'compressed',
                includePaths: [
                    paths.components + '/bootstrap/scss',
                ],
            }).on('error', sass.logError)
        )
        .pipe(
            postcss([ autoprefixer() ])
        );

    let cssStream = src([
            paths.components + '/datatables.net-bs4/css/dataTables.bootstrap4.css',
            paths.components + '/fullcalendar/dist/fullcalendar.css',
            paths.components + '/print-js/dist/print.css',
        ])
        .pipe(cleanCSS());

    return merge(sassStream, cssStream)
        .pipe(concat('app.min.css'))
        .pipe(rev())
        .pipe(dest(paths.public + '/css'))
        .pipe(rev.manifest(paths.public + '/manifest.json', {
            base: paths.public,
            merge: true,
        }))
        .pipe(dest(paths.public));
}

function vendors() {
    return src([
        paths.components + '/jquery/dist/jquery.js',
        paths.components + '/popper.js/dist/umd/popper.js',
        paths.components + '/bootstrap/dist/js/bootstrap.js',
        paths.components + '/datatables.net/js/jquery.dataTables.js',
        paths.components + '/datatables.net-bs4/js/dataTables.bootstrap4.js',
        paths.components + '/bootstrap-datepicker/js/bootstrap-datepicker.js',
        paths.components + '/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js',
        paths.components + '/bootstrap-datepicker/js/locales/bootstrap-datepicker.pt.js',
        paths.components + '/dropzone/dist/dropzone.js',
        paths.components + '/moment/min/moment-with-locales.js',
        paths.components + '/fullcalendar/dist/fullcalendar.js',
        paths.components + '/fullcalendar/dist/locale/es.js',
        paths.components + '/fullcalendar/dist/locale/pt.js',
        paths.components + '/chart.js/dist/Chart.js',
        paths.components + '/print-js/dist/print.js',
        paths.components + '/selectize/dist/js/standalone/selectize.js',
    ])
    // .pipe(sourcemaps.init())
    .pipe(concat('vendor.js'))
    .pipe(uglify())
    .pipe(rename({suffix: '.min'}))
    .pipe(rev())
    .pipe(dest(paths.public + '/js'))
    .pipe(rev.manifest(paths.public + '/manifest.json', {
        base: paths.public,
        merge: true,
    }))
    .pipe(dest(paths.public));
};

function js() {
    return src([
        paths.assets + '/scripts/*.js',
    ])
    .pipe(concat('app.js'))
    .pipe(uglify())
    .pipe(rename({suffix: '.min'}))
    .pipe(rev())
    .pipe(dest(paths.public + '/js'))
    .pipe(rev.manifest(paths.public + '/manifest.json', {
        base: paths.public,
        merge: true,
    }))
    .pipe(dest(paths.public));
}

function clean() {
    return del([
        paths.public + '/js/**',
        paths.public + '/css/**',
        '!' + paths.public + '/css/font-awesome.min.css'
    ]);
}

function watchFiles() {
    watch(paths.assets + '/styles/*.scss', series(css, clean));
	watch(paths.assets + '/scripts/*.js', series(js, clean));
}

exports.js = js;
exports.css = css;
exports.clean = clean;
exports.watch = watchFiles;
exports.default = series(css, js, vendors);
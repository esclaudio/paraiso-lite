var gulp = require('gulp'),
	sass         = require('gulp-sass'),
	concat       = require('gulp-concat'),
	rename       = require('gulp-rename'),
	uglify       = require('gulp-uglify'),
	del          = require('del'),
	sourcemaps   = require('gulp-sourcemaps'),
	postcss      = require('gulp-postcss'),
	autoprefixer = require('autoprefixer'),
	merge        = require('merge-stream'),
	cleanCSS     = require('gulp-clean-css');
	
var paths = {
	'assets'    : './resources/assets',
	'public'    : './public',
	'components': './node_modules'
};

gulp.task('styles', function () {
	var sassStream = gulp.src([
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

	var cssStream = gulp.src([
			paths.components + '/datatables.net-bs4/css/dataTables.bootstrap4.css',
			paths.components + '/fullcalendar/dist/fullcalendar.css',
			paths.components + '/print-js/dist/print.css',
			paths.components + '/jquery-easy-loading/dist/jquery.loading.css',
			paths.components + '/daterangepicker/daterangepicker.css',
			paths.components + '/bootstrap-select/dist/css/bootstrap-select.min.css',
		])
		.pipe(cleanCSS());

	return merge(sassStream, cssStream)
		.pipe(concat('app.min.css'))
		.pipe(gulp.dest('./public/css'));
});

		
gulp.task('vendors', function () {
    return gulp.src([
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
		paths.components + '/jquery-easy-loading/dist/jquery.loading.js',
		paths.components + '/selectize/dist/js/standalone/selectize.js',
		paths.components + '/daterangepicker/daterangepicker.js',
		paths.components + '/sortablejs/Sortable.js',
		paths.components + '/bootstrap-select/dist/js/bootstrap-select.min.js',
	])
	.pipe(sourcemaps.init())
	.pipe(concat('vendor.js'))
	.pipe(gulp.dest('./public/js'))
	.pipe(uglify())
	.pipe(rename({suffix: '.min'}))
	// .pipe(sourcemaps.write())
	.pipe(gulp.dest('./public/js'));
});

gulp.task('pdf', function () {
	return gulp.src([
		paths.components + '/pdfjs-dist/build/pdf.worker.min.js',
		paths.components + '/pdfjs-dist/build/pdf.min.js',
	])
	.pipe(gulp.dest('./public/vendor/pdfjs'));
});

gulp.task('scripts', function () {
    return gulp.src([
		paths.assets + '/scripts/*.js',
	])
	.pipe(sourcemaps.init())
	.pipe(concat('app.js'))
	.pipe(gulp.dest('./public/js'))
	.pipe(uglify())
	.pipe(rename({suffix: '.min'}))
	// .pipe(sourcemaps.write())
	.pipe(gulp.dest('./public/js'));
});

gulp.task('clean', function () {
	return del([
		'./public/css/app.css',
		'./public/js/app.js',
		'./public/js/vendor.js'
	]);
});

gulp.task('watch-builder', function () {
	gulp.watch(paths.assets + '/styles/*.scss', ['styles']);
	gulp.watch(paths.assets + '/scripts/*.js', ['scripts']);
});

gulp.task('default', ['styles', 'scripts']);
gulp.task('watch', ['default', 'watch-builder']);

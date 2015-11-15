var gulp = require('gulp');
var plugins = require('gulp-load-plugins')();

gulp.task('default', function() {
    // procesar SCSS
    gulp.src('web/css/**/*.scss')
        .pipe(plugins.sass())
        .pipe(plugins.autoprefixer({
            browsers: [
                'Android 2.3',
                'Android >= 4',
                'Chrome >= 35',
                'Firefox >= 31',
                'Explorer >= 9',
                'iOS >= 7',
                'Opera >= 12',
                'Safari >= 7.1'
            ],
            cascade: false
        }))
        .pipe(plugins.concat('pack.css'))
        .pipe(plugins.minifyCss())
        .pipe(gulp.dest('web/dist/css'));

    // copiar Javascript de Bootstrap
    gulp.src('vendor/twbs/bootstrap/js/dist/*')
        .pipe(gulp.dest('web/dist/js/bootstrap'));

    // copiar fuentes
    gulp.src('vendor/fortawesome/font-awesome/fonts/*')
        .pipe(gulp.dest('web/dist/fonts'));
});

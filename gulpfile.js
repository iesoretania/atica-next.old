var gulp = require('gulp');
var plugins = require('gulp-load-plugins')();

gulp.task('default', function() {
    // procesar SCSS
    gulp.src(['web/css/**/*.scss', 'web/css/atica.css'])
        .pipe(plugins.sass())
        .pipe(plugins.autoprefixer({
            browsers: [
                'Android 2.3',
                'Android >= 4',
                'Chrome >= 20',
                'Firefox >= 24',
                'Explorer >= 8',
                'iOS >= 6',
                'Opera >= 12',
                'Safari >= 6'
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
    gulp.src('node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest('web/dist/fonts'));
});

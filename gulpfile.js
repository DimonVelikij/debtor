var gulp   = require('gulp'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglifyjs');

/**
 * сборка сторонних библиотек
 */
gulp.task('script', function () {
    return gulp.src([
            'src/AppBundle/Resources/public/vendor/angular/angular.min.js',
            'src/AppBundle/Resources/public/vendor/lodash/dist/lodash.min.js',
            'src/AppBundle/Resources/public/vendor/angular-messages/angular-messages.min.js'
        ])
        .pipe(concat('libs.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('src/AppBundle/Resources/public/js'));
});
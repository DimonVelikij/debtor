var gulp   = require('gulp'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglifyjs'),
    cssnano= require('gulp-cssnano'),
    rename = require('gulp-rename');

/**
 * сборка сторонних библиотек
 */
gulp.task('script', function () {
    return gulp.src([
            'src/AppBundle/Resources/public/vendor/angular/angular.min.js',
            'src/AppBundle/Resources/public/vendor/lodash/dist/lodash.min.js',
            'src/AppBundle/Resources/public/vendor/angular-messages/angular-messages.min.js',
            'src/AppBundle/Resources/public/vendor/angular-ui-mask/dist/mask.min.js',
            'src/AppBundle/Resources/public/vendor/angular-ui-select/dist/select.min.js',
            'src/AppBundle/Resources/public/vendor/angular-sanitize/angular-sanitize.min.js'
        ])
        .pipe(concat('libs.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('src/AppBundle/Resources/public/js'));
});

/**
 * сборка сторонних стилей
 */
gulp.task('css-ui-select', function () {
    return gulp.src([
            'src/AppBundle/Resources/public/vendor/angular-ui-select/dist/select.min.css'
        ])
        .pipe(cssnano())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('src/AppBundle/Resources/public/css'));
});
let gulp = require('gulp'),
    concat = require('gulp-concat'),
    cleanCSS = require('gulp-clean-css'),
    uglify = require('gulp-uglify'),
    count = require('gulp-count');

gulp.task('js', function () {
    return gulp.src([
        './node_modules/jquery/dist/jquery.js',
        './node_modules/bootstrap/dist/js/bootstrap.bundle.js',
        './node_modules/admin-lte/dist/js/adminlte.js',
        './node_modules/bootstrap-notify/bootstrap-notify.js',
        './node_modules/smoothscroll-for-websites/SmoothScroll.js',
        './node_modules/typed.js/lib/typed.js',
        './node_modules/flatpickr/dist/flatpickr.js',
        './node_modules/sweetalert/dist/sweetalert.min.js',
        './node_modules/datatables.net/js/jquery.dataTables.js',
        './node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js',
        './node_modules/datatables.net-responsive/js/dataTables.responsive.js',
        './node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.js',
        './node_modules/smartwizard/dist/js/jquery.smartWizard.js',
        './node_modules/jquery-validation/dist/jquery.validate.js',
        './resources/js/*.js',
    ])
        .pipe(count('## .js files selected'))
        .pipe(concat('app.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js/'))
});

gulp.task('css', function () {
    return gulp.src([
        './node_modules/@fortawesome/fontawesome-free/css/all.css',
        './node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css',
        './node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.css',
        './node_modules/animate.css/animate.css',
        './node_modules/flatpickr/dist/themes/material_blue.css',
        './node_modules/smartwizard/dist/css/smart_wizard.css',
        './node_modules/smartwizard/dist/css/smart_wizard_theme_dots.css',
        './resources/css/*.css',
    ])
        .pipe(count('## .css files selected'))
        .pipe(concat('app.css'))
        .pipe(cleanCSS())
        .pipe(gulp.dest('./public/css/'))
});

gulp.task('copy font', function () {
    gulp.src('./node_modules/@fortawesome/fontawesome-free/webfonts/*')
        .pipe(gulp.dest('./public/webfonts/'));
});

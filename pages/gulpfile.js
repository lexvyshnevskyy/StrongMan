const gulp        = require('gulp');
const browserSync = require('browser-sync').create();
const sass        = require('gulp-sass');
const watch       = require('gulp-watch');
const cssmin      = require('gulp-cssmin');
const rename      = require('gulp-rename');
gulp.task('scss', function() {
  return gulp.src('./app/scss/style.scss')
    .pipe( sass() )
    .pipe( gulp.dest('./app/css/') )
    .pipe( browserSync.stream() );
});

gulp.task('server', function() {

  browserSync.init({
    server: "./app/"
  });

});

gulp.task('watch', function() {
  watch(
    ["./app/*.html", "./app/js/*.js"],
    gulp.parallel(browserSync.reload)
  );

  watch('./app/scss/**/*.scss', function(){
    setTimeout( gulp.parallel('scss'), 1000)
  })
});

gulp.task('css-min', function() {
  gulp.src('app/css/*.css')
    .pipe(cssmin())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('app/css/'));
});
browserSync.reload;

gulp.task('default', gulp.series('scss', gulp.parallel('server', 'watch', 'css-min')));

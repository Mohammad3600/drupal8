/*
 * NOTES(Elijah):
 *
 *     This script compiles the "compiled-styles.scss" file
 *     "compiled-styles.css" should not be edited as it is automatically generated and any changes may be lost in the compilation process
 *
 * */

/*
 * Instructions:
 *     To run the scss compilation:
 *         1) navigate to the theme directory in your terminal (C:\...\sustainable_minds-dev\docroot\themes\custom\sustainable_minds)
 *         2) make sure that node modules is up to date by running "npm install"
 *               NOTE) this will require you to have nodeJS installed globally on your computer
 *         3) run gulp "gulp start"
 *               NOTE) this will require you to have gulp installed globally on your computer through node js
 *         4) gulp will watch the "scss" directory and will recompile the styles when any changes are made in the dir
 *         5) to terminate the watch process press "CTRL+C" in the terminal followed by "y"
 *
 * */

var path = require('path');
var fs = require('fs');

var gulp = require('gulp');
var browserSync = require('browser-sync');
var nunjucks = require('nunjucks');
var prettifyHTML = require('prettify-html');
var gulpSourceMaps = require('gulp-sourcemaps');
var gulpSASS = require('gulp-sass');

var browser = browserSync.create();

gulp.task("scss", function() {
    return gulp
        .src([path.join(__dirname, 'scss', 'compiled-styles.scss')])
        .pipe(gulpSourceMaps.init())
        .pipe(gulpSASS().on("error", function(err) {
            console.error(`SCSS Error: ${err.message}`);
        }))
        .pipe(gulpSourceMaps.write())
        .pipe(gulp.dest(path.join(__dirname, 'css')))
        .pipe(browser.stream());
});

gulp.task("start", gulp.series("scss", function(done) {

    // Watch files and run the appropriate tasks
    gulp.watch("./scss/**/*.scss", gulp.series("scss"));
    done();
}));
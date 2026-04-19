// dependencies
import cleanCSS from 'gulp-clean-css';
import uglify from 'gulp-uglify';
import concat from 'gulp-concat';
import order from 'gulp-order';
import gulpSass from "gulp-sass";
import * as dartSass from "sass";
import postcss from "gulp-postcss";
import autoprefixer from "autoprefixer";
import gulp from "gulp";
import noop from "gulp-noop";

const sass = gulpSass(dartSass);

/**
 * @task: gulp vendors:styles
 * concat vendor styles
 */
function buildVendorStyles() {

  if (
    typeof config.paths.styles.source.vendor !== "undefined" &&
    config.paths.styles.source.vendor.length > 0
  ) {
    return gulp
      .src(config.paths.styles.source.vendor, { allowEmpty: true, nosort: true })
      .pipe(order(config.paths.styles.source.vendor, { base: './' }))
      .pipe(sass({ outputStyle: "expanded" }).on('error', sass.logError))
      .pipe(config.run[env].autoprefixer ? postcss([autoprefixer()]) : noop())
      .pipe(config.run[env].cleancss ? cleanCSS() : noop())
      .pipe(concat("vendor.css"))
      .pipe(gulp.dest(config.paths.styles.dest));
  }

  return gulp.src(".", { allowEmpty: true }).pipe(noop());
}


/**
 * @task: gulp vendors:scripts
 * concat vendor scripts
 */
function buildVendorScripts() {

  if (
    typeof config.paths.scripts.source.vendor !== "undefined" &&
    config.paths.scripts.source.vendor.length > 0
  ) {

    return gulp
      .src(config.paths.scripts.source.vendor)
      .pipe(order(config.paths.scripts.source.vendor, { base: './' }))
      .pipe(uglify())
      .pipe(concat("vendor.js"))
      .pipe(gulp.dest(config.paths.scripts.dest));
  }

  return gulp.src(".", { allowEmpty: true }).pipe(noop());
}

export { buildVendorStyles, buildVendorScripts };

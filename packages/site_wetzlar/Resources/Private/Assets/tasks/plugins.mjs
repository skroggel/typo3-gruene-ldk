// dependencies
import cleanCSS from 'gulp-clean-css';
import uglify from 'gulp-uglify';
import postcss from 'gulp-postcss';
import autoprefixer from 'autoprefixer';
import gulpSass from "gulp-sass";
import * as dartSass from 'sass';
import order from 'gulp-order';
import gulp from 'gulp';
import noop from 'gulp-noop';

const sass = gulpSass(dartSass);

/**
 * @task: gulp plugin:styles
 * concat plugin styles
 */
function buildPluginStyles() {

  if (
    typeof config.paths.styles.source.plugins !== "undefined" &&
    config.paths.styles.source.plugins.length > 0
  ) {
    return gulp
      .src(config.paths.styles.source.plugins)
      .pipe(order(config.paths.styles.source.plugins, { base: './' }))
      .pipe(sass({ outputStyle: "expanded" }).on('error', sass.logError))
      .pipe(config.run[env].autoprefixer ? postcss([autoprefixer()]) : noop())
      .pipe(config.run[env].cleancss ? cleanCSS() : noop())
      .pipe(gulp.dest(config.paths.styles.dest));
  }

  return gulp.src(".", { allowEmpty: true }).pipe(noop());
}


/**
 * @task: gulp plugin:scripts
 * concat plugin scripts
 */
function buildPluginScripts() {
  if (
    typeof config.paths.scripts.source.plugins !== "undefined" &&
    config.paths.scripts.source.plugins.length > 0
  ) {
    return gulp
      .src(config.paths.scripts.source.plugins, { allowEmpty: true })
      .pipe(order(config.paths.scripts.source.plugins, { base: './' }))
      .pipe(config.run[env].uglify ? uglify() : noop())
      .pipe(gulp.dest(config.paths.scripts.dest));
  }

  return gulp.src(".", { allowEmpty: true }).pipe(noop());
}

export { buildPluginStyles, buildPluginScripts };

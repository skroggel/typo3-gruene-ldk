// dependencies
import uglify from 'gulp-uglify';
import concat from 'gulp-concat';
import order from 'gulp-order';

/**
 * @task: gulp scripts
 * convert stylus to css, minify, create sourcemaps
 */
function buildScripts() {

  if (
    typeof config.paths.scripts.source.default !== "undefined" &&
    config.paths.scripts.source.default.length > 0
  ) {
    return gulp
      .src(config.paths.scripts.source.default)
      .pipe(order(config.paths.scripts.source.default, { base: './' }))
      .pipe(config.run[env].uglify ? uglify() : noop())
      .pipe(concat("scripts.js"))
      .pipe(gulp.dest(config.paths.scripts.dest))
  }

  return gulp.src(".", {allowEmpty: true}).pipe(noop());
}

export {buildScripts};

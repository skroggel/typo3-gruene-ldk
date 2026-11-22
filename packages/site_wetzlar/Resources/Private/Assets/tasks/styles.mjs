// dependencies
import postcss from 'gulp-postcss';
import autoprefixer from 'autoprefixer';
import cleanCSS from 'gulp-clean-css';
import * as dartSass from 'sass';
import gulpSass from 'gulp-sass';
import order from 'gulp-order';
import gulp from 'gulp';
import noop from 'gulp-noop';

const sass = gulpSass(dartSass);

/**
 * @task: gulp styles
 *
 * Note on :placeholder-shown:
 * Autoprefixer incorrectly interprets :placeholder-shown as ::placeholder
 * and generates legacy selectors like :-moz-placeholder and :-ms-input-placeholder.
 *
 * To avoid this, we use a dummy selector :__ph-shown__ in SCSS.
 * - Autoprefixer ignores it (no unwanted vendor prefixes).
 * - After Autoprefixer runs, a custom PostCSS plugin replaces
 *   :__ph-shown__ back to :placeholder-shown.
 *
 * This ensures clean CSS output without invalid legacy selectors.
 */
function replaceCustomPlaceholder() {
  return {
    postcssPlugin: "replaceCustomPlaceholder",
    Rule(rule) {
      if (rule.selector && rule.selector.includes(":__ph-shown__")) {
        rule.selector = rule.selector.replace(/:__ph-shown__/g, ":placeholder-shown");
      }
    }
  }
}
replaceCustomPlaceholder.postcss = true;

function buildStyles() {

  if (
    typeof config.paths.styles.source.default !== "undefined" &&
    config.paths.styles.source.default.length > 0
  ) {
    return gulp
      .src(config.paths.styles.source.default)
      .pipe(order(config.paths.styles.source.default, { base: './' }))
      .pipe(sass({ outputStyle: "expanded" }).on('error', sass.logError))
      .pipe(config.run[env].autoprefixer ? postcss([autoprefixer()]) : noop())
      .pipe(postcss([replaceCustomPlaceholder()]))
      .pipe(gulp.dest(config.paths.styles.dest));
  }

  return gulp.src(".", { allowEmpty: true }).pipe(noop());
}



export { buildStyles };

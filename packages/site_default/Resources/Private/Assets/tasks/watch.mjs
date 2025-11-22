// dependencies
import {cleanScripts, cleanStyles} from "./clean.mjs";
import {buildStyles} from "./styles.mjs";
import {buildScripts} from "./scripts.mjs";
import {buildPluginStyles, buildPluginScripts} from './plugins.mjs';
import gulp from "gulp";

/**
 * @task: gulp watch
 */
function watch(which) {

  switch (which) {

    case 'scripts':
      return gulp.watch(config.paths.watch.scripts, gulp.series(cleanScripts, buildScripts, buildPluginScripts));

    default:
      return gulp.watch(config.paths.watch.styles, gulp.series(cleanStyles, buildStyles, buildPluginStyles));

  }
}


/**
 * @task: gulp watch:styles
 * watch styles
 */
function watchStyles() {
  return watch('styles');
}


/**
 * @task: gulp watch:scripts
 * watch scripts
 */
function watchScripts() {
  return watch('scripts');
}

export {watchStyles, watchScripts};

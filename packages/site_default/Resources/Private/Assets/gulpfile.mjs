/**! ==================================================
 * gulpfile.js
 * ================================================== */
// dependencies
import notify from 'gulp-notify';
import yargs from 'yargs'
import {hideBin} from 'yargs/helpers'

// global dependencies
import gulp from 'gulp';

global.gulp = gulp;

import noop from 'gulp-noop';

global.noop = noop;

//======================================================
// load config
import config from './config.json' with {type: 'json'};

global.config = config;

//======================================================
// set environment
global.env = process.env.NODE_ENV || "development";
global.args = yargs(hideBin(process.argv)).argv;

//======================================================
// error reporting
global.errorReporter = function (task, action) {
  return notify.onError({
    title: "Error in '" + task + "' task: " + action,
    message: "<%= error.message %>",
  });
};

//======================================================
// load tasks
import {cleanAll, cleanCritical} from './tasks/clean.mjs';
import {buildScripts} from './tasks/scripts.mjs';
import {buildStyles} from './tasks/styles.mjs';
import {buildPluginStyles, buildPluginScripts} from './tasks/plugins.mjs';
import {buildVendorStyles, buildVendorScripts} from './tasks/vendor.mjs';
import {watchScripts, watchStyles} from "./tasks/watch.mjs";

//======================================================
/**
 * @task: gulp build
 * build task
 */
let buildSeries = [buildScripts, buildStyles, buildPluginStyles, buildPluginScripts, buildVendorStyles, buildVendorScripts];
gulp.task(
  "build",
  gulp.series(gulp.parallel(buildSeries))
);


/**
 * @task: gulp
 * default task
 */
gulp.task(
  "default",
  gulp.series(cleanAll, "build")
);


/**
 * @task: gulp watch
 * serving proxy with browser sync
 */
gulp.task(
  "watch",
  gulp.series(cleanAll, "build", gulp.parallel(watchStyles, watchScripts))
);


/**
 * @task: gulp critical
 * rendering critical css
 */
gulp.task('critical', async () => {
  // dynamischer Import verhindert require()-Fehler bei Top-Level Await in critical.mjs
  const { buildCriticalStyles } = await import('./tasks/critical.mjs')
  await cleanCritical();
  return buildCriticalStyles();
});

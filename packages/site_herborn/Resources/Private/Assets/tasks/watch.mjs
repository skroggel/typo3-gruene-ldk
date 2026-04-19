import gulp from "gulp";
import noop from "gulp-noop";

import { cleanScripts, cleanStyles } from "./clean.mjs";
import { buildAssets } from "./assets.mjs";
import { buildScripts } from "./scripts.mjs";
import { buildStyles } from "./styles.mjs";
import { buildPluginScripts, buildPluginStyles } from "./plugins.mjs";

function watchStyles() {
  const watchPaths = config?.paths?.watch?.styles ?? [];

  if (watchPaths.length > 0) {
    return gulp.watch(
      watchPaths,
      gulp.series(cleanStyles, buildStyles, buildPluginStyles)
    );
  }

  return gulp.src(".", { allowEmpty: true }).pipe(noop());
}

function watchScripts() {
  const watchPaths = config?.paths?.watch?.scripts ?? [];

  if (watchPaths.length > 0) {
    return gulp.watch(
      watchPaths,
      gulp.series(cleanScripts, buildScripts, buildPluginScripts)
    );
  }

  return gulp.src(".", { allowEmpty: true }).pipe(noop());
}

function watchAssets() {
  const watchPaths = config?.paths?.watch?.assets ?? [];

  if (watchPaths.length > 0) {
    return gulp.watch(
      watchPaths,
      gulp.series(buildAssets)
    );
  }

  return gulp.src(".", { allowEmpty: true }).pipe(noop());
}

export { watchStyles, watchScripts, watchAssets };

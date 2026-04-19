import fs from "fs";
import gulp from "gulp";
import path from "path";
import noop from "gulp-noop";

function getAssetConfig() {
  return config?.paths?.assets ?? {};
}

function copyFolder(src, dest) {
  if (fs.existsSync(src)) {
    fs.cpSync(src, dest, {
      recursive: true,
      force: true
    });
  }
}

/**
 * @task: gulp assets
 * copy shared assets from configurable base into local public folder
 */
function buildAssets(done) {
  const assetConfig = getAssetConfig();
  const base = assetConfig.base;
  const dest = assetConfig.dest;
  const folders = assetConfig.folders ?? [];

  if (!base || !dest || folders.length === 0) {
    return gulp.src(".", { allowEmpty: true }).pipe(noop());
  }

  folders.forEach((folder) => {
    const sourcePath = path.resolve(process.cwd(), base, folder);
    const targetPath = path.resolve(process.cwd(), dest, folder);

    copyFolder(sourcePath, targetPath);
  });

  done();
}

export { buildAssets };

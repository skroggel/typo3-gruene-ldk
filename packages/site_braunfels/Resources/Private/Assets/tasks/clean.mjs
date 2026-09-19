// dependencies
import {deleteAsync} from 'del';

/**
 * @task: gulp clean
 * run all clean tasks
 */
function clean(which) {

  switch (which) {
    case 'critical':
      return deleteAsync(
        [
          config.critical.dest + '**',
          '!' + config.critical.dest,
        ],
        {force: true}
      );

    case 'styles':
      return deleteAsync(
        [
          config.paths.styles.dest + '**',
          '!' + config.paths.styles.dest,
          '!' + config.paths.styles.dest + '/vendor.css'
        ],
        {force: true}
      );

    case 'scripts':
      return deleteAsync(
        [
          config.paths.scripts.dest + '**',
          '!' + config.paths.scripts.dest,
          '!' + config.paths.scripts.dest + '/vendor.js'
        ],
        {force: true}
      );

    default:
      return deleteAsync(
        [
          config.paths.styles.dest + '**',
          '!' + config.paths.styles.dest,
          config.paths.scripts.dest + '**',
          '!' + config.paths.scripts.dest
        ],
        {force: true}
      );
  }
}

/**
 * @task: gulp cleanAll
 * clean and remove all directories
 */
function cleanAll() {
  return clean();
}

/**
 * @task: gulp cleanCritical
 * clean and remove css directory
 */
function cleanCritical() {
  return clean('critical');
}


/**
 * @task: gulp cleanStyles
 * clean and remove css directory
 */
function cleanStyles() {
  return clean('styles');
}


/**
 * @task: gulp cleanScripts
 * clean and remove script directory
 */
function cleanScripts() {
  return clean('scripts');
}


export {cleanAll, cleanCritical, cleanStyles, cleanScripts};

// dependencies
import {generate} from 'critical';
import log from 'fancy-log';
import events from 'events';

// fixing error with memory-leak
events.EventEmitter.defaultMaxListeners = 0;

/**
 * @task: gulp plugin:styles
 * concat vendor styles
 */
async function buildCriticalStyles(params, cb) {

  if (
    typeof config.critical !== "undefined" &&
    config.critical.pages.length > 0
  ) {

    // return all promises together as one
    return Promise.all( config.critical.pages.map(page => {

      const url = config.critical.baseUrl + page.url + '?type=1715339215'; // special type with no redirect if JS is missing
      log(`Generating critical CSS for template ${page.template} with URL ${url}`);

      // critical.generate returns a Promise.
      return generate({

        // Inline the generated critical-path CSS
        // - true generates HTML
        // - false generates CSS
        inline: false,

        // Your base directory
        base: './',

        // HTML source file
        src: url,

        // viewports - in ascending order!
        dimensions: config.critical.dimensions,

        // Output results to file
        target: {
          css: config.critical.dest + page.template + config.critical.suffix,
        },

        // Extract inlined styles from referenced stylesheets
        extract: true,

        // ignore CSS rules
        ignore: {
           atrule: ['@font-face'],
          // rule: [/some-regexp/],
          // decl: (node, value) => /big-image\.png/.test(value),
        },
      }, cb);
    }));
  }

  return Promise.resolve();
}

export {buildCriticalStyles};

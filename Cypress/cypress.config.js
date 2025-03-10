const { defineConfig } = require('cypress');
const fs = require('fs');

module.exports = defineConfig({
  screenshots: false,
  video: true,

  e2e: {
    pageLoadTimeout: 120000, 
    defaultCommandTimeout: 10000, 

    setupNodeEvents(on, config) {
      on('after:spec', (spec, results) => {
        if (results && results.video) {
          const failures = results.tests.some((test) =>
            test.attempts.some((attempt) => attempt.state === 'failed')
          );
          if (failures) {
            fs.unlinkSync(results.video);
          }
        }
      });
    },
  },
});

module.exports = {
    proxy: "https://crane.dune-pebbler.nl/", // Use your live site's URL
    files: ["**/*"], // Watch all files in the project
    watchOptions: {
      ignoreInitial: false, // Prevent triggering on startup
    },
    notify: true, // Show notifications in the browser
    open: false, // Don’t automatically open the browser, since you want to access the live site
    reloadDelay: 0, // Add delay to allow SFTP uploads to complete
    port: 3000, // This will still be used locally for proxying purposes
  };

  
  
  // browser-sync start --config bs-config.js
  
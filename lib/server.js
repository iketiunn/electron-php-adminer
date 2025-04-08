const { spawn } = require('child_process')
const http = require('http')
const path = require('path')
const { app } = require('electron')
const fs = require('fs')

// PHP SERVER CONFIGURATION
const SERVER_HOST = 'localhost'
const PORT_RANGE_MIN = 5000
const PORT_RANGE_MAX = 6000

// Function to get a random port within the defined range
function getRandomPort() {
  return Math.floor(Math.random() * (PORT_RANGE_MAX - PORT_RANGE_MIN + 1)) + PORT_RANGE_MIN
}

let phpProcess = null

const server = {
  host: SERVER_HOST,
  port: getRandomPort(), // Initialize with a random port
  run: function () {
    return new Promise((resolve, reject) => {
      if (phpProcess) {
        return
      }

      let phpBinary;
      const appPath = app.getAppPath(); // Get the actual app path

      // Handle different paths in development vs production
      if (app.isPackaged) {
        // In packaged app, resources are in the 'resources' directory
        let resourcesPath;
        if (process.platform === 'darwin') {
          // On macOS, avoid duplicating "Resources" in the path
          resourcesPath = path.dirname(app.getAppPath());
        } else {
          // For other platforms
          resourcesPath = path.join(path.dirname(app.getPath('exe')), 'resources');
        }

        phpBinary = process.platform === 'win32'
          ? path.join(resourcesPath, 'php', 'php.exe')
          : path.join(resourcesPath, 'php', 'php');

        console.log('Resources path:', resourcesPath);
      } else {
        // In development - look for PHP within the app directory itself
        phpBinary = process.platform === 'win32'
          ? path.join(appPath, 'php', 'php.exe')
          : path.join(appPath, 'php', 'php');
      }

      console.log('Using PHP binary at:', phpBinary);

      // Check if PHP binary exists
      try {
        if (!fs.existsSync(phpBinary)) {
          console.error(`PHP binary not found at ${phpBinary}`);
          
          // Try using system PHP as fallback
          if (process.platform !== 'win32') {
            phpBinary = '/usr/bin/php';
            console.log('Attempting to use system PHP instead:', phpBinary);
            
            if (!fs.existsSync(phpBinary)) {
              console.error('System PHP also not found. Please ensure PHP is installed.');
              reject(new Error('PHP binary not found. Please ensure PHP is installed.'));
              return;
            }
          } else {
            reject(new Error('PHP binary not found. Please ensure PHP is installed.'));
            return;
          }
        }
      } catch (err) {
        console.error('Error checking PHP binary:', err);
        reject(err);
        return;
      }

      const args = [
        '-S', `${SERVER_HOST}:${server.port}`,
        '-t', path.join(appPath, 'adminer'),
        // '-d', 'display_errors=1',
        // '-d', 'expose_php=1'
      ]

      phpProcess = spawn(phpBinary, args, { stdio: 'inherit' })

      phpProcess.on('error', (error) => {
        console.error('PHP server error:', error)
        reject(error)
      })

      phpProcess.on('close', (code) => {
        console.log(`PHP server process exited with code ${code}`)
      })

      // Check if server is ready by polling
      const checkServer = () => {
        http.get(`http://${SERVER_HOST}:${server.port}/`, (res) => {
          console.log('PHP server is ready')
          resolve() // Only resolve the promise when we get a successful response
        }).on('error', (err) => {
          console.log('Waiting for PHP server to be ready...')
          setTimeout(checkServer, 300)
        })
      }

      // Give the PHP server a moment to start up before first check
      setTimeout(checkServer, 500)
    })
  },
  close: function () {
    if (phpProcess) {
      console.log('Terminating PHP server process...')
      // Use SIGTERM for a cleaner shutdown
      const process = phpProcess;
      phpProcess = null; // Clear reference first to prevent duplicate terminations
      process.kill('SIGTERM');
      console.log('PHP server process terminate signal sent')
    }
  }
}

module.exports = server

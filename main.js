const { app, BrowserWindow } = require('electron')
const path = require('path')

// Import modules
const server = require('./lib/server')
const { createWindow } = require('./lib/window')
const { createMenu } = require('./lib/menu')
const { setupLoadingIndicator } = require('./lib/loading-indicator')

// Set PATH for macOS
if (process.platform === 'darwin') {
  const defaultPaths = [
    '/usr/local/bin',
    '/usr/bin',
    '/bin',
    '/usr/sbin',
    '/sbin'
  ];

  process.env.PATH = defaultPaths
    .concat(process.env.PATH ? process.env.PATH.split(':') : [])
    .filter((item, index, arr) => item && arr.indexOf(item) === index)
    .join(':');
}

// App lifecycle handlers
app.whenReady().then(async () => {
  const mainWindow = await createWindow()
  createMenu()

  // Set up loading indicator after window is created
  if (mainWindow) {
    setupLoadingIndicator(mainWindow);
  }

  app.on('activate', function () {
    // On macOS re-create a window when dock icon is clicked and no windows are open
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow()
    }
  })
})

// Quit when all windows are closed, except on macOS
app.on('window-all-closed', function () {
  server.close() // Always close the server when all windows are closed
  if (process.platform !== 'darwin') {
    app.quit()
  }
})

// Clean up when app is about to quit
app.on('before-quit', () => {
  server.close()
})

// PGHOST='ep-dark-cherry-a5481cvt-pooler.us-east-2.aws.neon.tech'
// PGDATABASE='neondb'
// PGUSER='neondb_owner'
// PGPASSWORD='npg_4aqVyXikSog3'
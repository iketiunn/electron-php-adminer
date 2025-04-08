const { BrowserWindow, screen, app } = require('electron')
const path = require('path')
const server = require('./server')

// Keep a global reference of the window object
let mainWindow = null

async function createWindow() {
  // Create the browser window first so we can show loading state
  const primaryDisplay = screen.getPrimaryDisplay()
  const { width, height } = primaryDisplay.workAreaSize

  // Calculate window size based on screen resolution
  let windowWidth, windowHeight;

  if (width >= 1920) {
    // For large displays (1920px or wider)
    windowWidth = Math.round(width * 0.6);
    windowHeight = Math.round(height * 0.8);
  } else if (width >= 1440) {
    // For medium-sized displays
    windowWidth = Math.round(width * 0.7);
    windowHeight = Math.round(height * 0.85);
  } else {
    // For smaller displays
    windowWidth = Math.round(width * 0.8);
    windowHeight = Math.round(height * 0.9);
  }

  // Ensure minimum reasonable size
  windowWidth = Math.max(windowWidth, 800);
  windowHeight = Math.max(windowHeight, 600);

  // Get the application root path
  const appPath = app.getAppPath()

  // Create the browser window with modern Electron settings
  mainWindow = new BrowserWindow({
    width: windowWidth,
    height: windowHeight,
    minWidth: 800,
    minHeight: 600,
    webPreferences: {
      nodeIntegration: false,
      contextIsolation: true,
      sandbox: true,
      webSecurity: true,
      preload: path.join(appPath, 'lib/preload.js'),
      devTools: true // Ensure DevTools can be opened
    },
    show: false, // Don't show window until content is ready
    center: true // Center the window on the screen
  })

  // Show loading state
  mainWindow.loadFile(path.join(appPath, 'loading.html'))
  mainWindow.show()

  try {
    // Start PHP server and wait until it's ready
    await server.run()

    // Then load the PHP server URL
    mainWindow.loadURL(`http://${server.host}:${server.port}/`)
  } catch (error) {
    console.error('Failed to start PHP server:', error)
    mainWindow.loadFile(path.join(appPath, 'error.html'))
  }

  // Handle window closed event
  mainWindow.on('closed', function () {
    // Only close the server if this is the last window or we're not on macOS
    if (BrowserWindow.getAllWindows().length <= 1 || process.platform !== 'darwin') {
      server.close()
    }
    mainWindow = null
  })

  return mainWindow
}

module.exports = {
  createWindow,
  getMainWindow: () => mainWindow
}

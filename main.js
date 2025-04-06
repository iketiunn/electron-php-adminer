const { app, BrowserWindow, Menu, shell } = require('electron')
const { spawn } = require('child_process')
const path = require('path')
const http = require('http')

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

// PHP SERVER CONFIGURATION
const SERVER_HOST = 'localhost'
const SERVER_PORT = 5555

let phpProcess = null
let server = {
  host: SERVER_HOST,
  port: SERVER_PORT,
  run: function() {
    return new Promise((resolve, reject) => {
      if (phpProcess) {
        resolve()
        return
      }
      
      const phpBinary = process.platform === 'win32' 
        ? `${__dirname}/php/php.exe` 
        : `${__dirname}/php/php`
      
      const args = [
        '-S', `${SERVER_HOST}:${SERVER_PORT}`,
        '-t', __dirname,
        '-d', 'display_errors=1',
        '-d', 'expose_php=1'
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
        http.get(`http://${SERVER_HOST}:${SERVER_PORT}/`, (res) => {
          console.log('PHP server is ready')
          resolve()
        }).on('error', (err) => {
          console.log('Waiting for PHP server to be ready...')
          setTimeout(checkServer, 300)
        })
      }
      
      // Give the PHP server a moment to start up before first check
      setTimeout(checkServer, 500)
    })
  },
  close: function() {
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

// Keep a global reference of the window object
let mainWindow

async function createWindow () {
  // Create the browser window first so we can show loading state
  const { screen } = require('electron')
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
      preload: path.join(__dirname, 'preload.js')
    },
    show: false, // Don't show window until content is ready
    center: true // Center the window on the screen
  })

  // Show loading state
  mainWindow.loadFile('loading.html')
  mainWindow.show()

  try {
    // Start PHP server and wait until it's ready
    await server.run()
    
    // Then load the PHP server URL
    mainWindow.loadURL(`http://${server.host}:${server.port}/`)
  } catch (error) {
    console.error('Failed to start PHP server:', error)
    mainWindow.loadFile('error.html')
  }

  // Handle window closed event
  mainWindow.on('closed', function () {
    // Only close the server if this is the last window or we're not on macOS
    if (BrowserWindow.getAllWindows().length <= 1 || process.platform !== 'darwin') {
      server.close()
    }
    mainWindow = null
  })
}

// Set up macOS menu for copy/paste functionality
function createMenu() {
  if (process.platform === 'darwin') {
    const template = [
      {
        label: 'Adminer',
        submenu: [
          {
            label: 'Quit',
            accelerator: 'CmdOrCtrl+Q',
            click: function () {
              app.quit()
            }
          }
        ]
      },
      {
        label: 'Edit',
        submenu: [
          { role: 'undo' },
          { role: 'redo' },
          { type: 'separator' },
          { role: 'cut' },
          { role: 'copy' },
          { role: 'paste' },
          { role: 'selectAll' }
        ]
      }
    ]
    Menu.setApplicationMenu(Menu.buildFromTemplate(template))
  }
}

// App lifecycle handlers
app.whenReady().then(() => {
  createWindow()
  createMenu()
  
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

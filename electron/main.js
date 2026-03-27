const { app, BrowserWindow, dialog, session } = require('electron');
const path = require('path');
const { spawn } = require('child_process');
const net = require('net');

let mainWindow = null;
let pythonProcess = null;
const PYTHON_PORT = 5000;
const isDev = !app.isPackaged;

function findFreePort(startPort) {
  return new Promise((resolve, reject) => {
    const server = net.createServer();
    server.listen(startPort, '127.0.0.1', () => {
      const port = server.address().port;
      server.close(() => resolve(port));
    });
    server.on('error', () => {
      resolve(findFreePort(startPort + 1));
    });
  });
}

function startPythonBackend(port) {
  return new Promise((resolve, reject) => {
    let pythonExe;
    let args;

    if (isDev) {
      // Development: run Python directly
      pythonExe = 'python';
      args = ['-c', `
import sys
sys.path.insert(0, '.')
from backend.app import create_app
app = create_app()
app.run(host='127.0.0.1', port=${port}, debug=False)
`];
    } else {
      // Production: run bundled Python executable
      const resourcesPath = process.resourcesPath;
      pythonExe = path.join(resourcesPath, 'python-backend', 'facecheck-api', 'facecheck-api.exe');
      args = ['--port', String(port)];
    }

    console.log(`Starting Python backend: ${pythonExe}`);
    pythonProcess = spawn(pythonExe, args, {
      cwd: isDev ? __dirname.replace(/[\\/]electron$/, '') : path.dirname(pythonExe),
      env: { ...process.env, FLASK_PORT: String(port) },
      stdio: ['pipe', 'pipe', 'pipe'],
    });

    pythonProcess.stdout.on('data', (data) => {
      console.log(`[Python] ${data.toString().trim()}`);
    });

    pythonProcess.stderr.on('data', (data) => {
      console.error(`[Python] ${data.toString().trim()}`);
    });

    pythonProcess.on('error', (err) => {
      console.error('Failed to start Python backend:', err);
      reject(err);
    });

    pythonProcess.on('exit', (code) => {
      console.log(`Python backend exited with code ${code}`);
      pythonProcess = null;
    });

    // Wait for the server to be ready
    const maxRetries = 30;
    let retries = 0;
    const checkReady = () => {
      const client = new net.Socket();
      client.connect(port, '127.0.0.1', () => {
        client.destroy();
        resolve(port);
      });
      client.on('error', () => {
        client.destroy();
        retries++;
        if (retries < maxRetries) {
          setTimeout(checkReady, 1000);
        } else {
          reject(new Error('Python backend failed to start'));
        }
      });
    };
    setTimeout(checkReady, 2000);
  });
}

function createWindow(port) {
  mainWindow = new BrowserWindow({
    width: 1280,
    height: 800,
    minWidth: 1024,
    minHeight: 700,
    title: 'FaceCheck - Hệ thống Điểm danh',
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      contextIsolation: true,
      nodeIntegration: false,
      additionalArguments: [`--api-port=${port}`],
    },
    icon: path.join(__dirname, '..', 'public', 'icon.png'),
    autoHideMenuBar: true,
  });

  if (isDev) {
    mainWindow.loadURL('http://localhost:5173');
    mainWindow.webContents.openDevTools();
  } else {
    // Load from Flask backend server (same-origin) to avoid file:// CORS issues
    mainWindow.loadURL(`http://127.0.0.1:${port}`);
  }

  // F12 to toggle devtools in production
  mainWindow.webContents.on('before-input-event', (event, input) => {
    if (input.key === 'F12') {
      mainWindow.webContents.toggleDevTools();
    }
  });

  mainWindow.on('closed', () => {
    mainWindow = null;
  });
}

app.whenReady().then(async () => {
  // Grant camera/microphone permissions for face registration
  session.defaultSession.setPermissionRequestHandler((webContents, permission, callback) => {
    const allowed = ['media', 'mediaKeySystem', 'clipboard-read'];
    callback(allowed.includes(permission));
  });

  try {
    const port = await findFreePort(PYTHON_PORT);
    await startPythonBackend(port);
    createWindow(port);
  } catch (err) {
    console.error('Startup error:', err);
    dialog.showErrorBox('Startup Error',
      `Failed to start the application.\n\n${err.message}`);
    app.quit();
  }
});

app.on('window-all-closed', () => {
  if (pythonProcess) {
    pythonProcess.kill();
    pythonProcess = null;
  }
  app.quit();
});

app.on('before-quit', () => {
  if (pythonProcess) {
    pythonProcess.kill();
    pythonProcess = null;
  }
});

app.on('activate', () => {
  if (mainWindow === null) {
    findFreePort(PYTHON_PORT).then(port => createWindow(port));
  }
});

const { contextBridge } = require('electron');

// Extract API port from command line arguments
const apiPortArg = process.argv.find(arg => arg.startsWith('--api-port='));
const apiPort = apiPortArg ? parseInt(apiPortArg.split('=')[1], 10) : 5000;

contextBridge.exposeInMainWorld('electronAPI', {
  platform: process.platform,
  isElectron: true,
  apiPort: apiPort,
});

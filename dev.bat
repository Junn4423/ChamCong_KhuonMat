@echo off
echo === FaceCheck Development Mode ===
echo.
echo Starting Python backend...
start "Python Backend" cmd /k "python run_backend.py --port 5000"
echo.
echo Starting React dev server...
timeout /t 3 >nul
start "React Dev" cmd /k "npm run dev:react"
echo.
echo Waiting for React to be ready...
timeout /t 5 >nul
echo.
echo Starting Electron...
npm run dev:electron

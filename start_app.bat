@echo off
title FaceCheck - Electron App
echo.
echo ============================================
echo   FaceCheck - Electron App Mode
echo ============================================
echo.

REM ---- Check node_modules ----
if not exist "node_modules" (
    echo [WARNING] Dependencies not installed. Running install.bat...
    call install.bat
)

echo [1/3] Starting Python Backend (port 5000)...
start "FaceCheck Backend" cmd /k "py -3.10 run_backend.py --port 5000"

echo [2/3] Starting React Dev Server (port 5173)...
timeout /t 3 >nul
start "FaceCheck React" cmd /k "npm run dev:react"

echo [3/3] Waiting for React, then launching Electron...
echo   Waiting for http://localhost:5173 ...
timeout /t 5 >nul

echo   Launching Electron...
npm run dev:electron

echo.
echo ============================================
echo   Electron has closed.
echo ============================================
echo   Remember to close Backend and React
echo   windows if no longer needed.
echo.
pause

@echo off
title FaceCheck - Website Mode
echo.
echo ============================================
echo   FaceCheck - Website Mode
echo ============================================
echo.

REM ---- Check node_modules ----
if not exist "node_modules" (
    echo [WARNING] Dependencies not installed. Running install.bat...
    call install.bat
)

echo [1/2] Starting Python Backend (port 5000)...
start "FaceCheck Backend" cmd /k "py -3.10 run_backend.py --port 5000"

echo [2/2] Starting React Frontend (port 5173)...
echo.
echo   Waiting for Backend to start...
timeout /t 3 >nul

start "FaceCheck Frontend" cmd /k "npm run dev:react"

echo.
echo ============================================
echo   FaceCheck is running!
echo ============================================
echo.
echo   Frontend:  http://localhost:5173
echo   Backend:   http://localhost:5000
echo.
echo   Closing this window will NOT stop servers.
echo   To stop, close "FaceCheck Backend"
echo   and "FaceCheck Frontend" windows.
echo.
pause

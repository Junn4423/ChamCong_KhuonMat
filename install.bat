@echo off
title FaceCheck - Install Dependencies
echo.
echo ============================================
echo   FaceCheck - Install Dependencies
echo ============================================
echo.

REM ---- Check Node.js ----
where node >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Node.js not found. Please install from https://nodejs.org
    pause
    exit /b 1
)
echo [OK] Node.js:
node --version

REM ---- Check Python 3.10 ----
py -3.10 --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Python 3.10 not found. Please install Python 3.10.
    pause
    exit /b 1
)
echo [OK] Python 3.10:
py -3.10 --version

echo.
echo ---- Upgrade pip ----
py -3.10 -m pip install --upgrade pip
echo.

echo ---- Install Node.js packages (Frontend) ----
echo.
call npm install
if errorlevel 1 (
    echo [ERROR] Failed to install Node.js packages!
    pause
    exit /b 1
)
echo [OK] Node.js packages installed

echo.
echo ---- Install Python packages (Backend) ----
echo.
py -3.10 -m pip install -r backend\requirements.txt
if errorlevel 1 (
    echo [ERROR] Failed to install Python packages!
    pause
    exit /b 1
)
echo [OK] Python packages installed

echo.
echo ============================================
echo   Installation complete!
echo ============================================
echo.
echo   Run start_web.bat   to open Website mode
echo   Run start_app.bat   to open Electron mode
echo.
pause

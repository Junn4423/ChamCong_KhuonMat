@echo off
echo === FaceCheck Production Build ===
echo.

REM Step 1: Build React frontend
echo [1/3] Building React frontend...
call npm run build:react
if errorlevel 1 (
    echo ERROR: React build failed
    pause
    exit /b 1
)
echo React build complete.
echo.

REM Step 2: Build Python backend with PyInstaller
echo [2/3] Building Python backend...
if exist "python-dist" rmdir /s /q "python-dist"
pyinstaller backend.spec --noconfirm --clean --distpath python-dist
if errorlevel 1 (
    echo ERROR: Python build failed
    pause
    exit /b 1
)
echo Python build complete.
echo.

REM Step 3: Build Electron app
echo [3/3] Building Electron app...

REM Fix winCodeSign cache for non-admin Windows (symlink issue)
set WINCACHE=%LOCALAPPDATA%\electron-builder\Cache\winCodeSign
set WINTARGET=%WINCACHE%\winCodeSign-2.6.0
if not exist "%WINTARGET%\rcedit-x64.exe" (
    echo Pre-populating winCodeSign cache...
    if not exist "%WINCACHE%" mkdir "%WINCACHE%"
    set WCSURL=https://github.com/electron-userland/electron-builder-binaries/releases/download/winCodeSign-2.6.0/winCodeSign-2.6.0.7z
    set WCS7Z=%WINCACHE%\winCodeSign-2.6.0.7z
    if not exist "%WCS7Z%" (
        powershell -Command "Invoke-WebRequest -Uri '%WCSURL%' -OutFile '%WCS7Z%' -UseBasicParsing"
    )
    if not exist "%WINTARGET%" mkdir "%WINTARGET%"
    node_modules\7zip-bin\win\x64\7za.exe x -bd "%WCS7Z%" "-o%WINTARGET%" -y >nul 2>&1
    echo winCodeSign cache ready.
)

set CSC_IDENTITY_AUTO_DISCOVERY=false
call npx electron-builder --win --dir
if errorlevel 1 (
    echo ERROR: Electron build failed
    pause
    exit /b 1
)
echo.
echo === Build Complete! ===
echo Output: release/
pause

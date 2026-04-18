@echo off
title FaceCheck - Website Mode
setlocal

set "PUBLIC_MODE=0"
set "NGROK_MODE=0"
set "CLOUD_MODE=0"

REM ---- Read input arguments ----
:parse_args
if "%~1"=="" goto end_args
if /I "%~1"=="--public" set "PUBLIC_MODE=1"
if /I "%~1"=="--ngrok" set "NGROK_MODE=1"
if /I "%~1"=="--cloud" set "CLOUD_MODE=1"
if /I "%~1"=="--cloudflare" set "CLOUD_MODE=1"
shift
goto parse_args
:end_args

if "%CLOUD_MODE%"=="1" set "NGROK_MODE=0"

set "SCRIPT_DIR=%~dp0"
set "NGROK_BIN="
set "NGROK_STATUS=off"
set "CLOUD_BIN="
set "CLOUD_STATUS=off"
set "CLOUD_TUNNEL_URL=http://localhost:5173"
set "CLOUD_TUNNEL_FLAGS=--edge-ip-version 4 --protocol http2 --ha-connections 1"

set "BACKEND_HOST=127.0.0.1"
set "FRONTEND_HOST_ARG="
set "FRONTEND_URL=http://localhost:5173"
set "BACKEND_URL=http://localhost:5000"

if "%PUBLIC_MODE%"=="1" (
    set "BACKEND_HOST=0.0.0.0"
    set "FRONTEND_HOST_ARG= -- --host 0.0.0.0"
)

echo.
echo ============================================
echo   FaceCheck - Website Mode
echo ============================================
echo.

if "%CLOUD_MODE%"=="1" (
    echo   Mode: CLOUDFLARE ^(Internet access enabled^)
) else (
    if "%NGROK_MODE%"=="1" (
        echo   Mode: NGROK ^(Internet access enabled^)
    ) else (
        if "%PUBLIC_MODE%"=="1" (
            echo   Mode: PUBLIC ^(LAN access enabled^)
        ) else (
            echo   Mode: LOCAL ^(localhost only^)
        )
    )
)
echo.

REM ---- Check node_modules ----
if not exist "node_modules" (
    echo [WARNING] Dependencies not installed. Running install.bat...
    call install.bat
)

echo [0/3] Checking Python dependency: openpyxl...
py -3.10 -c "import importlib.util, sys; sys.exit(0 if importlib.util.find_spec('openpyxl') else 1)" >nul 2>nul
if errorlevel 1 (
    echo [INFO] openpyxl is missing. Installing...
    py -3.10 -m pip install openpyxl
    if errorlevel 1 (
        echo [WARNING] Could not install openpyxl automatically.
        echo [WARNING] Excel export at /bao-cao may fail until openpyxl is installed.
    )
)

echo [1/3] Starting Python Backend (port 5000)...
start "FaceCheck Backend" cmd /k "py -3.10 run_backend.py --port 5000 --host %BACKEND_HOST%"

echo [2/3] Starting React Frontend (port 5173)...
echo.
echo   Waiting for Backend to start...
timeout /t 3 >nul

start "FaceCheck Frontend" cmd /k "npm run dev:react%FRONTEND_HOST_ARG%"

if "%PUBLIC_MODE%"=="1" (
    for /f "usebackq delims=" %%I in (`powershell -NoProfile -Command "Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.IPAddress -notlike '169.254*' -and $_.IPAddress -ne '127.0.0.1' } | Select-Object -ExpandProperty IPAddress -First 1"`) do set "LAN_IP=%%I"
    if defined LAN_IP (
        call set "FRONTEND_URL=http://%%LAN_IP%%:5173"
        call set "BACKEND_URL=http://%%LAN_IP%%:5000"
    )
)

REM ---- Run cloudflared if enabled ----
if "%CLOUD_MODE%"=="1" (
    if exist "%SCRIPT_DIR%cloudflared.exe" (
        set "CLOUD_BIN=%SCRIPT_DIR%cloudflared.exe"
    ) else (
        for /f "delims=" %%I in ('where cloudflared 2^>nul') do (
            if not defined CLOUD_BIN set "CLOUD_BIN=%%I"
        )
    )

    if not defined CLOUD_BIN (
        set "CLOUD_STATUS=not_found"
        echo [3/3] cloudflared not found.
        echo       Put cloudflared.exe in this folder or add cloudflared to PATH.
    ) else (
        set "CLOUD_STATUS=started"
        echo [3/3] Starting Cloudflare Tunnel ^(port 5173, IPv4 + HTTP/2^)...
        call start "Cloudflare Tunnel" cmd /k ""%%CLOUD_BIN%%" tunnel --url %CLOUD_TUNNEL_URL% %CLOUD_TUNNEL_FLAGS%"
    )
) else (
    REM ---- Run ngrok if enabled ----
    if "%NGROK_MODE%"=="1" (
        if exist "%SCRIPT_DIR%ngrok.exe" (
            set "NGROK_BIN=%SCRIPT_DIR%ngrok.exe"
        ) else (
            for /f "delims=" %%I in ('where ngrok 2^>nul') do (
                if not defined NGROK_BIN set "NGROK_BIN=%%I"
            )
        )

        if not defined NGROK_BIN (
            set "NGROK_STATUS=not_found"
            echo [3/3] Ngrok not found.
            echo       Put ngrok.exe in this folder or add ngrok to PATH.
            echo       Then run: ngrok config add-authtoken YOUR_TOKEN
        ) else (
            call "%%NGROK_BIN%%" config check >nul 2>nul
            if errorlevel 1 (
                set "NGROK_STATUS=config_error"
                echo [3/3] Ngrok config is invalid.
                echo       Opening a separate window with config error details...
                call start "Ngrok Config Error" cmd /k ""%%NGROK_BIN%%" config check"
            ) else (
                set "NGROK_STATUS=started"
                echo [3/3] Starting Ngrok Tunnel ^(port 5173^)...
                call start "Ngrok Tunnel" cmd /k ""%%NGROK_BIN%%" http 5173"
            )
        )
    )
)

echo.
echo ============================================
echo   FaceCheck is running!
echo ============================================
echo.
echo   Frontend:  %FRONTEND_URL%
echo   Backend:   %BACKEND_URL%
echo.
if "%PUBLIC_MODE%"=="1" (
    echo   Use these URLs on your phone ^(same Wi-Fi/LAN^).
    echo   If access fails, allow ports 5000 and 5173 in Windows Firewall.
    echo.
)
if "%CLOUD_MODE%"=="1" (
    if "%CLOUD_STATUS%"=="started" (
        echo   [!] Cloudflare Tunnel has been launched in a separate window.
        echo   [!] Copy the trycloudflare.com HTTPS URL from that window to access from anywhere.
        echo   [!] If the tunnel shows a timeout, run the script again ^(network to trycloudflare may be transient^).
    ) else (
        if "%CLOUD_STATUS%"=="not_found" (
            echo   [!] cloudflared executable was not found.
            echo   [!] Put cloudflared.exe in this folder or add cloudflared to PATH.
        )
    )
    echo.
) else (
    if "%NGROK_MODE%"=="1" (
        if "%NGROK_STATUS%"=="started" (
            echo   [!] Ngrok has been launched in a separate window.
            echo   [!] Copy the "Forwarding" HTTPS URL from the Ngrok window to access from anywhere.
        ) else (
            if "%NGROK_STATUS%"=="config_error" (
                echo   [!] Ngrok config is invalid.
                echo   [!] Check the "Ngrok Config Error" window, fix config, then run again.
            ) else (
                if "%NGROK_STATUS%"=="not_found" (
                    echo   [!] Ngrok executable was not found.
                    echo   [!] Put ngrok.exe in this folder or add ngrok to PATH.
                )
            )
        )
        echo.
    )
)
echo   Closing this window will NOT stop servers.
if "%CLOUD_MODE%"=="1" (
    echo   To stop, close "FaceCheck Backend", "FaceCheck Frontend", and "Cloudflare Tunnel" window.
) else (
    if "%NGROK_MODE%"=="1" (
        echo   To stop, close "FaceCheck Backend", "FaceCheck Frontend", and any "Ngrok" window.
    ) else (
        echo   To stop, close "FaceCheck Backend" and "FaceCheck Frontend" windows.
    )
)
echo.
pause

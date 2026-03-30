<#
.SYNOPSIS
    FaceCheck - Build backend (PyInstaller) + frontend (Vite + Electron)
.DESCRIPTION
    Step 1: Build React frontend with Vite
    Step 2: Build Python backend with PyInstaller
    Step 3: Build Electron app (bundles frontend + backend)
.PARAMETER SkipFrontend
    Skip the React/Vite build step
.PARAMETER SkipBackend
    Skip the PyInstaller backend build step
.PARAMETER SkipElectron
    Skip the Electron packaging step
.PARAMETER DirOnly
    Build Electron as unpacked directory (faster, no installer)
.PARAMETER Clean
    Remove all build artifacts before building
#>
param(
    [switch]$SkipFrontend,
    [switch]$SkipBackend,
    [switch]$SkipElectron,
    [switch]$DirOnly,
    [switch]$Clean
)

$ErrorActionPreference = "Stop"
$ProjectRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition
Set-Location $ProjectRoot

$VenvActivate = Join-Path $ProjectRoot "venv_build\Scripts\Activate.ps1"
$TimeStart = Get-Date

function Write-Step($step, $total, $msg) {
    Write-Host "`n[$step/$total] $msg" -ForegroundColor Cyan
    Write-Host ("=" * 60) -ForegroundColor DarkGray
}

function Write-Ok($msg) {
    Write-Host "  OK: $msg" -ForegroundColor Green
}

function Write-Fail($msg) {
    Write-Host "  FAIL: $msg" -ForegroundColor Red
}

# --- Calculate total steps ---
$totalSteps = 0
if (-not $SkipFrontend) { $totalSteps++ }
if (-not $SkipBackend)  { $totalSteps++ }
if (-not $SkipElectron) { $totalSteps++ }
if ($totalSteps -eq 0) {
    Write-Host "Nothing to build (all steps skipped)." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "========================================" -ForegroundColor White
Write-Host "  FaceCheck Production Build" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor White

# --- Clean ---
if ($Clean) {
    Write-Host "`nCleaning build artifacts..." -ForegroundColor Yellow
    foreach ($dir in @("dist", "python-dist", "release", "build")) {
        $target = Join-Path $ProjectRoot $dir
        if (Test-Path $target) {
            Remove-Item $target -Recurse -Force
            Write-Host "  Removed $dir/" -ForegroundColor DarkGray
        }
    }
    Write-Ok "Clean complete"
}

$currentStep = 0

# ============================================================
# Step 1: Build React frontend
# ============================================================
if (-not $SkipFrontend) {
    $currentStep++
    Write-Step $currentStep $totalSteps "Building React frontend (Vite)"

    # Check node_modules
    if (-not (Test-Path (Join-Path $ProjectRoot "node_modules"))) {
        Write-Host "  Installing npm dependencies..." -ForegroundColor Yellow
        npm install
        if ($LASTEXITCODE -ne 0) { Write-Fail "npm install failed"; exit 1 }
    }

    npm run build:react
    if ($LASTEXITCODE -ne 0) { Write-Fail "Vite build failed"; exit 1 }

    # Verify output
    $distIndex = Join-Path $ProjectRoot "dist\index.html"
    if (Test-Path $distIndex) {
        $fileCount = (Get-ChildItem -Path (Join-Path $ProjectRoot "dist") -Recurse -File).Count
        Write-Ok "Frontend built: dist/ ($fileCount files)"
    } else {
        Write-Fail "dist/index.html not found"; exit 1
    }
}

# ============================================================
# Step 2: Build Python backend (PyInstaller)
# ============================================================
if (-not $SkipBackend) {
    $currentStep++
    Write-Step $currentStep $totalSteps "Building Python backend (PyInstaller)"

    # Activate venv
    if (Test-Path $VenvActivate) {
        Write-Host "  Activating venv_build..." -ForegroundColor DarkGray
        & $VenvActivate
    } else {
        Write-Host "  WARNING: venv_build not found, using system Python" -ForegroundColor Yellow
    }

    # Check PyInstaller
    $pyinstaller = Get-Command pyinstaller -ErrorAction SilentlyContinue
    if (-not $pyinstaller) {
        Write-Host "  Installing PyInstaller..." -ForegroundColor Yellow
        pip install pyinstaller
        if ($LASTEXITCODE -ne 0) { Write-Fail "pip install pyinstaller failed"; exit 1 }
    }

    # Ensure dist/ exists (backend.spec bundles it as frontend_dist)
    if (-not (Test-Path (Join-Path $ProjectRoot "dist\index.html"))) {
        Write-Fail "dist/ not found. Build frontend first (remove -SkipFrontend)"; exit 1
    }

    # Clean old build
    $pythonDist = Join-Path $ProjectRoot "python-dist"
    if (Test-Path $pythonDist) {
        Remove-Item $pythonDist -Recurse -Force
    }

    pyinstaller backend.spec --noconfirm --clean --distpath python-dist
    if ($LASTEXITCODE -ne 0) { Write-Fail "PyInstaller build failed"; exit 1 }

    # Verify output
    $exePath = Join-Path $pythonDist "facecheck-api\facecheck-api.exe"
    if (Test-Path $exePath) {
        $exeSize = [math]::Round((Get-Item $exePath).Length / 1MB, 1)
        Write-Ok "Backend built: python-dist/facecheck-api/ (exe: ${exeSize}MB)"
    } else {
        Write-Fail "facecheck-api.exe not found"; exit 1
    }
}

# ============================================================
# Step 3: Build Electron app
# ============================================================
if (-not $SkipElectron) {
    $currentStep++
    Write-Step $currentStep $totalSteps "Building Electron app"

    # Verify prerequisites
    if (-not (Test-Path (Join-Path $ProjectRoot "dist\index.html"))) {
        Write-Fail "dist/ not found. Build frontend first"; exit 1
    }
    if (-not (Test-Path (Join-Path $ProjectRoot "python-dist\facecheck-api\facecheck-api.exe"))) {
        Write-Fail "python-dist/ not found. Build backend first"; exit 1
    }

    # Fix winCodeSign cache (non-admin Windows symlink issue)
    $wcsCache = Join-Path $env:LOCALAPPDATA "electron-builder\Cache\winCodeSign"
    $wcsTarget = Join-Path $wcsCache "winCodeSign-2.6.0"
    if (-not (Test-Path (Join-Path $wcsTarget "rcedit-x64.exe"))) {
        Write-Host "  Pre-populating winCodeSign cache..." -ForegroundColor DarkGray
        if (-not (Test-Path $wcsCache)) { New-Item -ItemType Directory -Path $wcsCache -Force | Out-Null }
        $wcs7z = Join-Path $wcsCache "winCodeSign-2.6.0.7z"
        if (-not (Test-Path $wcs7z)) {
            $wcsUrl = "https://github.com/electron-userland/electron-builder-binaries/releases/download/winCodeSign-2.6.0/winCodeSign-2.6.0.7z"
            Invoke-WebRequest -Uri $wcsUrl -OutFile $wcs7z -UseBasicParsing
        }
        if (-not (Test-Path $wcsTarget)) { New-Item -ItemType Directory -Path $wcsTarget -Force | Out-Null }
        $7za = Join-Path $ProjectRoot "node_modules\7zip-bin\win\x64\7za.exe"
        if (Test-Path $7za) {
            & $7za x -bd $wcs7z "-o$wcsTarget" -y | Out-Null
        }
        Write-Host "  winCodeSign cache ready" -ForegroundColor DarkGray
    }

    $env:CSC_IDENTITY_AUTO_DISCOVERY = "false"

    if ($DirOnly) {
        npx electron-builder --win --dir
    } else {
        npx electron-builder --win
    }
    if ($LASTEXITCODE -ne 0) { Write-Fail "Electron build failed"; exit 1 }

    # Verify output
    $releaseDir = Join-Path $ProjectRoot "release"
    if (Test-Path $releaseDir) {
        $outputSize = [math]::Round(((Get-ChildItem -Path $releaseDir -Recurse -File | Measure-Object -Property Length -Sum).Sum / 1MB), 0)
        Write-Ok "Electron app built: release/ (~${outputSize}MB)"
    } else {
        Write-Fail "release/ not found"; exit 1
    }
}

# ============================================================
# Summary
# ============================================================
$elapsed = (Get-Date) - $TimeStart
$mins = [math]::Floor($elapsed.TotalMinutes)
$secs = $elapsed.Seconds

Write-Host ""
Write-Host "========================================" -ForegroundColor White
Write-Host "  Build Complete!  (${mins}m ${secs}s)" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor White

if (-not $SkipElectron) {
    $installerExe = Get-ChildItem -Path (Join-Path $ProjectRoot "release") -Filter "*.exe" -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($installerExe) {
        Write-Host "  Installer: release\$($installerExe.Name)" -ForegroundColor Cyan
    }
    $unpackedDir = Join-Path $ProjectRoot "release\win-unpacked"
    if (Test-Path $unpackedDir) {
        Write-Host "  Unpacked:  release\win-unpacked\" -ForegroundColor Cyan
    }
}
Write-Host ""

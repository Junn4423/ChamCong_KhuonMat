@echo off
echo === FaceCheck: Cleanup Old Files ===
echo.
echo This script removes files from the OLD project structure.
echo The NEW structure uses: backend/, src/, electron/, package.json
echo.
echo Files to delete:
echo   - Old Python scripts (build_*.py, test_*, check_*, demo_*, etc.)
echo   - Old desktop_app.py (replaced by Electron)
echo   - Old templates/ (replaced by React src/pages/)
echo   - Old static/js, static/css (replaced by React)
echo   - Old Docker files (docker-compose*.yml, Dockerfile)
echo   - Old .spec files, build scripts (.bat, .sh)
echo   - Old build output dirs (build/, dist_build/, dist_runfix/, build_temp/, build_runfix/)
echo.
set /p CONFIRM="Are you sure? (y/N): "
if /I not "%CONFIRM%"=="y" (
    echo Cancelled.
    pause
    exit /b 0
)

echo.
echo Deleting old Python scripts...
del /q add_sample_employees.py 2>nul
del /q build_exe.py 2>nul
del /q build_minimal.py 2>nul
del /q build_optimized.py 2>nul
del /q build_simple.py 2>nul
del /q check_database.py 2>nul
del /q check_dll.py 2>nul
del /q check_tc_lv0012_structure.py 2>nul
del /q clear_database.py 2>nul
del /q convert_antispoof_to_onnx.py 2>nul
del /q convert_miniFASNet_to_onnx.py 2>nul
del /q debug_api.py 2>nul
del /q demo_erp_attendance.py 2>nul
del /q desktop_app.py 2>nul
del /q migrate_encodings.py 2>nul
del /q test_config.py 2>nul
del /q test_recognition_permissive.py 2>nul
del /q test_recognition.py 2>nul
del /q test_register.py 2>nul
del /q test_retinaface.py 2>nul
del /q test_service_stability.py 2>nul
del /q testconnect.py 2>nul

echo Deleting old build scripts...
del /q build_fast.bat 2>nul
del /q build_for_transfer.sh 2>nul
del /q BUILD_NOW.bat 2>nul
del /q build_optimized.bat 2>nul
del /q build_venv.bat 2>nul
del /q build.bat 2>nul
del /q Chay_dich_vu.bat 2>nul
del /q Chay_dich_vu.ps1 2>nul
del /q cleanup_deploy.sh 2>nul
del /q deploy.sh 2>nul
del /q docker-run.sh 2>nul
del /q export_image.sh 2>nul
del /q run.sh 2>nul
del /q run_desktop.bat 2>nul
del /q setup.sh 2>nul
del /q start_all.sh 2>nul

echo Deleting old spec files...
del /q FaceRecognition.spec 2>nul
del /q FaceRecognition_Simple.spec 2>nul
del /q desktop_app.spec 2>nul

echo Deleting old Docker files...
del /q docker-compose.yml 2>nul
del /q docker-compose.legacy.yml 2>nul
del /q docker-compose.override.yml 2>nul
del /q docker-compose.override.legacy.yml 2>nul
del /q Dockerfile 2>nul
del /q ecosystem.config.js 2>nul

echo Deleting old docs...
del /q AGENTS.md 2>nul
del /q BUILD_GUIDE.md 2>nul
del /q HUONG_DAN_ERP_INTEGRATION.md 2>nul
del /q HUONG_DAN_IMPORT.md 2>nul
del /q QUICK_GUIDE_IMPORT.md 2>nul
del /q build_log.txt 2>nul
del /q out.txt 2>nul

echo Deleting old source files (replaced by backend/)...
del /q app.py 2>nul
del /q config_import.py 2>nul
del /q import_employees.py 2>nul

echo Deleting old directories...
if exist "templates" rmdir /s /q templates
if exist "build" rmdir /s /q build
if exist "build_temp" rmdir /s /q build_temp
if exist "build_runfix" rmdir /s /q build_runfix
if exist "dist_build" rmdir /s /q dist_build
if exist "dist_runfix" rmdir /s /q dist_runfix
if exist "hooks" rmdir /s /q hooks
if exist "scripts" rmdir /s /q scripts

echo Deleting old static web files (fonts kept)...
if exist "static\js" rmdir /s /q static\js
if exist "static\css" rmdir /s /q static\css
del /q static\manifest.json 2>nul

echo.
echo Renaming files...
if exist "NEW_README.md" (
    del /q README.md 2>nul
    ren NEW_README.md README.md
)
if exist ".gitignore.new" (
    del /q .gitignore 2>nul
    ren .gitignore.new .gitignore
)

echo.
echo ========================================
echo Cleanup complete!
echo.
echo New project structure:
echo   backend/    - Python Flask API
echo   src/        - React frontend
echo   electron/   - Electron main process
echo   package.json, vite.config.js
echo.
echo Old models/ and services/ can be deleted
echo after verifying backend/ works:
echo   del /q models\*.py
echo   del /q services\*.py
echo ========================================
pause

@echo off
setlocal
cd /d "%~dp0.."

echo ============================================
echo Gut Reset - XAMPP Local Setup Helper
echo ============================================

if not exist ".env" copy ".env.xampp.example" ".env"

where composer >nul 2>nul
if errorlevel 1 (
  echo ERROR: Composer is not available on PATH.
  echo Install Composer or run the equivalent composer command manually.
  pause
  exit /b 1
)

if not exist "C:\xampp\php\php.exe" (
  echo ERROR: C:\xampp\php\php.exe not found.
  pause
  exit /b 1
)

if not exist "C:\xampp\mysql\bin\mysql.exe" (
  echo ERROR: C:\xampp\mysql\bin\mysql.exe not found.
  pause
  exit /b 1
)

echo.
echo Installing PHP dependencies...
composer install
if errorlevel 1 goto :fail

C:\xampp\php\php.exe artisan key:generate
if errorlevel 1 goto :fail

echo.
echo Importing Phase 1C MySQL schema into gut_reset...
C:\xampp\mysql\bin\mysql.exe -u root -p < database\schema\phase1c_final_production_mysql.sql
if errorlevel 1 goto :fail

echo.
echo Seeding local reference/demo data...
C:\xampp\php\php.exe artisan db:seed
if errorlevel 1 goto :fail

C:\xampp\php\php.exe artisan storage:link
if errorlevel 1 goto :fail

echo.
echo Installing frontend dependencies...
npm install
if errorlevel 1 goto :fail
npm run build
if errorlevel 1 goto :fail

echo.
echo Setup completed.
echo Read docs\local\LOCAL_XAMPP_SETUP.md for Apache VirtualHost and hosts-file setup.
pause
exit /b 0

:fail
echo.
echo Setup failed. Review the command output and docs\local\LOCAL_XAMPP_SETUP.md.
pause
exit /b 1

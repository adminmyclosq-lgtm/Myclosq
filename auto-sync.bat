@echo off
cd /D "%~dp0"
echo Starting auto-sync at %date% %time%
git add .
git commit -m "Auto-sync at %date% %time%"
git pull --no-edit
git push origin main
echo Done!

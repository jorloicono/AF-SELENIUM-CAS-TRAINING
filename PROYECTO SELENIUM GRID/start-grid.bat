@echo off
echo ========================================
echo   Iniciando Selenium Grid en modo Standalone
echo ========================================
echo.
echo Verificando drivers disponibles...
echo.
cd grid
java -jar selenium-server-standalone.jar standalone --port 4444
pause

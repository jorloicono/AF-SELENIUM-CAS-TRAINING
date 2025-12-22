@echo off
echo.
echo 🚀🚀🚀 BEHAT + SELENIUM LAB - INICIANDO 🚀🚀🚀
echo.

REM 1. Crear directorios necesarios
if not exist screenshots mkdir screenshots
if not exist reports mkdir reports

REM 2. Iniciar ChromeDriver en background
echo [1/4] Iniciando ChromeDriver...
start /min cmd /c "chromedriver.exe --port=9515 --verbose --log-path=chromedriver.log"
timeout /t 4 /nobreak >nul

REM 3. Verificar que ChromeDriver está listo
echo [2/4] Verificando ChromeDriver...
curl -s http://localhost:9515/status >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ ChromeDriver no disponible. Revisa chromedriver.exe
    pause
    exit /b 1
)

REM 4. Ejecutar todos los tests
echo [3/4] Ejecutando tests Behat...
bin\behat --format=progress --colors --stop-on-failure

REM 5. Generar reporte HTML
echo [4/4] Generando reporte HTML...
bin\behat --format=html --out=reports\report.html --no-paths

REM 6. Terminar ChromeDriver
taskkill /f /im chromedriver.exe 2>nul
echo.
echo ✅✅✅ LABORATORIO COMPLETADO ✅✅✅
echo Reporte: reports\report.html
echo Screenshots: screenshots\
pause
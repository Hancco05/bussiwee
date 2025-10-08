@echo off
chcp 65001 > nul
title Instalador PHP + MySQL + Apache para Windows 10
color 0A

echo ===============================================
echo    INSTALADOR PHP + MYSQL + APACHE
echo          Para Windows 10
echo ===============================================
echo.

:: Verificar si es administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Ejecuta como Administrador!
    echo Haz clic derecho -> Ejecutar como administrador
    pause
    exit /b 1
)

echo [1/6] Descargando XAMPP...
powershell -Command "& {Invoke-WebRequest 'https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.1.17/xampp-windows-x64-8.1.17-0-VS16-installer.exe/download' -OutFile 'xampp-installer.exe'}"

if exist "xampp-installer.exe" (
    echo [2/6] Instalando XAMPP...
    echo EJECUTANDO INSTALADOR XAMPP...
    echo Por favor completa la instalacion en la ventana que se abrira...
    echo.
    start /wait xampp-installer.exe
) else (
    echo [ERROR] No se pudo descargar XAMPP
    goto :error
)

echo [3/6] Configurando servicios...
cd C:\xampp
apache_start.bat
mysql_start.bat

timeout /t 5 /nobreak > nul

echo [4/6] Configurando PHP...
copy "php.ini" "C:\xampp\php\php.ini" /Y > nul
copy "httpd.conf" "C:\xampp\apache\conf\httpd.conf" /Y > nul

echo [5/6] Creando base de datos...
cd C:\xampp\mysql\bin
mysql -u root -e "CREATE DATABASE IF NOT EXISTS mi_proyecto;" 2>nul

echo [6/6] Configurando proyecto...
if not exist "C:\xampp\htdocs\mi_proyecto" mkdir "C:\xampp\htdocs\mi_proyecto"
xcopy /E /I /Y "%~dp0\*" "C:\xampp\htdocs\mi_proyecto\" > nul

echo.
echo ===============================================
echo    INSTALACION COMPLETADA!
echo ===============================================
echo.
echo [ACCESOS]
echo Proyecto: http://localhost/mi_proyecto/public/
echo PHPMyAdmin: http://localhost/phpmyadmin
echo XAMPP Panel: C:\xampp\xampp-control.exe
echo.
echo [BASE DE DATOS]
echo Usuario: root
echo Password: (vacio)
echo Base: mi_proyecto
echo.
echo Presiona cualquier tecla para abrir el proyecto...
pause > nul

start http://localhost/mi_proyecto/public/
goto :eof

:error
echo.
echo [ERROR] La instalacion fallo!
echo Verifica tu conexion a internet y ejecuta como administrador
pause
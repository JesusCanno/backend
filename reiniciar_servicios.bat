@echo off
echo Reiniciando los servicios...

REM Limpiando cache de Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

REM Reiniciando el servidor de Laravel
php artisan serve

echo Servicios reiniciados. Accede a la aplicación en: http://127.0.0.1:8000
echo Para el frontend, en otra ventana ejecuta: cd TfgFrontEnd-main/TfgFrontEnd-main && npm run dev 
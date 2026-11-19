@echo off
title Menjalankan Web Pemesanan Instalasi Listrik PT. Eksavindo
color 0b

echo ===================================================================
echo     MEMULAI SISTEM INFORMASI PEMESANAN INSTALASI LISTRIK
echo                 PT. EKSAVINDO MITRA MANDIRI
echo ===================================================================
echo.

cd /d "C:\pemesanan-instalasi-listrik"

echo [1/2] Menyalakan Web Server Lokal di Port 8000...
start "" "http://localhost:8000"
echo [2/2] Membuka browser otomatis ke http://localhost:8000...
echo.
echo ===================================================================
echo   SERVER AKTIF! JANGAN TUTUP JENDELA INI SELAMA MEMAKAI WEB.
echo   Buka di Browser: http://localhost:8000
echo   Untuk mematikan server: Tekan tombol silang (X) pada jendela ini.
echo ===================================================================
echo.

php -S 0.0.0.0:8000
pause


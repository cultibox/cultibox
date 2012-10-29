@ECHO OFF
ECHO * Demarage de l'application, veuillez patienter quelques secondes...
netstat -an|find ":6891"|find "LISTENING" > NUL
if %ERRORLEVEL% EQU 0 (
    echo    XAMPP semble deja demarre, redemarrage de XAMPP:
    echo         Arret de XAMPP...
    xampp\xampp_stop.exe
    echo         Demarrage de XAMPP
    xampp\xampp_start.exe > NUL
) else (
    echo      -> Demarrage de XAMPP
    xampp\xampp_start.exe > NUL
)

echo * Demarrage de l'interface...
start http://localhost:6891/cultibox/
@ECHO OFF
ECHO.
ECHO * Appuyez sur n'importe quelle touche pour terminer l'application...
pause>Nul
ECHO.
ECHO * Fermeture de l'application, veuillez patienter quelques secondes...
xampp\xampp_stop.exe
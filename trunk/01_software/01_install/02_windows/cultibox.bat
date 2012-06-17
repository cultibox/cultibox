@ECHO OFF
ECHO Demarage de l'application, veuillez patienter quelques secondes...
xampp\xampp_start.exe
start http://localhost:6891/cultibox/
@ECHO OFF
ECHO.
ECHO Appuyez sur n'importe quelle touche pour terminer l'application...
pause>Nul
ECHO.
ECHO Fermeture de l'application, veuillez patienter quelques secondes...
xampp\xampp_stop>Nul
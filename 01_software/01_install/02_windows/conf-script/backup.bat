@ECHO OFF

echo -----------------------------------------------------------------
echo               Cultibox backup database script                    
echo -----------------------------------------------------------------
echo 

echo test > "%userprofile%\cultibox\test.txt"
if exist "%userprofile%\cultibox\test.txt" (
 echo   * %userprofile%\cultibox already exists and will be used to store backup files
 del "%userprofile%\cultibox\test.txt" >nul
) else (
    mkdir "%userprofile%\cultibox"
)

echo   * Exporting your current databae...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 cultibox > "%userprofile%\cultibox\backup_cultibox.bak.new"
if %ERRORLEVEL% EQU 0  (
    echo ... cultibox: OK
) else (
    del %userprofile%\cultibox\backup_cultibox.bak.new
    echo ==== Error during the backup of the Cultibox database, exiting ====
    echo ... NOK
    pause
    exit 1
)

C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 cultibox_joomla > "%userprofile%\cultibox\backup_joomla.bak.new"
if %ERRORLEVEL% EQU 0  (
    echo ... Joomla: OK
) else (
    del "%userprofile%\cultibox\backup_joomla.bak.new"
    echo ==== Error during the backup of the Joomla database, exiting ====
    echo ... NOK
    pause
    exit 1
)


echo   * Saving previous Cultibox backup database...
If exist "%userprofile%\cultibox\backup_cultibox.bak" (
    move "%userprofile%\cultibox\backup_cultibox.bak" "%userprofile%\cultibox\backup_cultibox.bak.old"
    echo ... OK
)

echo   * Saving previous Joomla backups database...
If exist "%userprofile%\cultibox\backup_joomla.bak" (
    move "%userprofile%\cultibox\backup_joomla.bak" "%userprofile%\cultibox\backup_joomla.bak.old"
    echo ... OK
)

move "%userprofile%\cultibox\backup_cultibox.bak.new" "%userprofile%\cultibox\backup_cultibox.bak"
move "%userprofile%\cultibox\backup_joomla.bak.new" "%userprofile%\cultibox\backup_joomla.bak"
exit 0

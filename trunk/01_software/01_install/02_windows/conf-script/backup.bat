@ECHO OFF

echo -----------------------------------------------------------------
echo               Cultibox backup database script                    
echo -----------------------------------------------------------------
echo 

echo test > %HOMEPATH%\cultibox\test.txt
if exist %HOMEPATH%\cultibox\test.txt (
 echo   * %HOMEPATH%\cultibox already exists and will be used to store backup files
 del %HOMEPATH%\cultibox\test.txt >nul
) else (
    mkdir %HOMEPATH%\cultibox
)

echo   * Exporting your current databae...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 cultibox > %HOMEPATH%\cultibox\backup_cultibox.bak.new
if %ERRORLEVEL% EQU 0  (
    echo ... cultibox: OK
) else (
    del %HOMEPATH%\cultibox\backup_cultibox.bak.new
    echo ==== Error during the backup of the cultibox database, exiting ====
    echo ... NOK
    pause
    exit 1
)


C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 cultibox_joomla > %HOMEPATH%\cultibox\backup_joomla.bak.new
if %ERRORLEVEL% EQU 0  (
    echo ... cultibox: OK
) else (
    del %HOMEPATH%\cultibox\backup_cultibox.bak.new
    echo ==== Error during the backup of the cultibox database, exiting ====
    echo ... NOK
    pause
    exit 1
)


echo   * Saving previous Cultibox backup database...
If exist %HOMEPATH%\cultibox\backup_cultibox.bak (
    move %HOMEPATH%\cultibox\backup_cultibox.bak %HOMEPATH%\cultibox\backup_cultibox.bak.old
    echo ... OK
)

echo   * Saving previous Joomla backups database...
If exist %HOMEPATH%\cultibox\backup_joomla.bak (
    move %HOMEPATH%\cultibox\backup_joomla.bak %HOMEPATH%\cultibox\backup_joomla.bak.old
    echo ... OK
)

move %HOMEPATH%\cultibox\backup_cultibox.bak.new %HOMEPATH%\cultibox\backup_cultibox.bak
move %HOMEPATH%\cultibox\backup_joomla.bak.new %HOMEPATH%\cultibox\backup_joomla.bak
exit 0

@ECHO OFF

echo -----------------------------------------------------------------
echo               Cultibox backup client database script                    
echo -----------------------------------------------------------------
echo. 

echo test > "%userprofile%\cultibox\test.txt"
if exist "%userprofile%\cultibox\test.txt" (
 echo * %userprofile%\cultibox already exists and will be used to store backup
 del "%userprofile%\cultibox\test.txt" >nul
) else (
    mkdir "%userprofile%\cultibox"
)

echo.
echo ====   Saving Cultibox database ====
echo.
if exist "%userprofile%\cultibox\backup_cultibox.sql" (
    echo    * Backuping an old cultibox file
    move "%userprofile%\cultibox\backup_cultibox.sql" "%userprofile%\cultibox\backup_cultibox.sql.old"
)
echo    * Saving the database...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --no-create-db --no-create-info -h 127.0.0.1 --port=3891 cultibox > "%userprofile%\cultibox\backup_cultibox.sql"
If not %ERRORLEVEL% EQU 0 (
    del "%userprofile%\cultibox\backup_cultibox.sql"
    echo .... NOK
    if exist "%userprofile%\cultibox\backup_cultibox.sql.old" (
        echo    * Restoring old backup database
        move "%userprofile%\cultibox\backup_cultibox.sql.old" "%userprofile%\cultibox\backup_cultibox.sql"
    )
    pause
    exit 1
) else (
    echo .... OK
)

pause
exit 0

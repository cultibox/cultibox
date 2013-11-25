@ECHO OFF

echo -----------------------------------------------------------------
echo               Cultibox backup client database script                    
echo -----------------------------------------------------------------
echo 

echo test > "%userprofile%\cultibox\test.txt"
if exist "%userprofile%\cultibox\test.txt" (
 echo * %userprofile%\cultibox already exists and will be used to store backup files
 del "%userprofile%\cultibox\test.txt" >nul
) else (
    mkdir "%userprofile%\cultibox"
)

echo ====   Calendar Database ====
if exist "%userprofile%\cultibox\calendar.sql" (
    echo    * Backuping an old calendar file
    move "%userprofile%\cultibox\calendar.sql" "%userprofile%\cultibox\calendar.sql.old"
)
echo    * Saving calendar database...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --no-create-db --no-create-info -h 127.0.0.1 --port=3891 cultibox calendar > "%userprofile%\cultibox\calendar.sql"
If not %ERRORLEVEL% 0 (
    del "%userprofile%\cultibox\calendar.sql"
    echo "    .... NOK"
    if exist "%userprofile%\cultibox\calendar.sql.old" (
        echo    * Restoring old backup database
        move "%userprofile%\cultibox\calendar.sql.old" "%userprofile%\cultibox\calendar.sql"
    )
) else (
    echo "    .... OK"
)


echo ====   Logs Database ====
if exist "%userprofile%\cultibox\logs.sql" (
    echo    * Backuping an old logs file
    move "%userprofile%\cultibox\logs.sql" "%userprofile%\cultibox\logs.sql.old"
)
echo    * Saving logs database...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --no-create-db --no-create-info -h 127.0.0.1 --port=3891 cultibox logs > "%userprofile%\cultibox\logs.sql"
If not %ERRORLEVEL% 0 (
    del "%userprofile%\cultibox\logs.sql"
    echo "    .... NOK"
    if exist "%userprofile%\cultibox\logs.sql.old" (
        echo    * Restoring old backup database
        move "%userprofile%\cultibox\logs.sql.old" "%userprofile%\cultibox\logs.sql"
    )
) else (
    echo "    .... OK"
)





echo ====   Configuration Database ====
if exist "%userprofile%\cultibox\configuration.sql" (
    echo    * Backuping an old calendar file
    move "%userprofile%\cultibox\configuration.sql" "%userprofile%\cultibox\configuration.sql.old"
)
echo    * Saving calendar database...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --no-create-db --no-create-info -h 127.0.0.1 --port=3891 cultibox configuration > "%userprofile%\cultibox\configuration.sql"
If not %ERRORLEVEL% 0 (
    del "%userprofile%\cultibox\configuration.sql"
    echo "    .... NOK"
    if exist "%userprofile%\cultibox\configuration.sql.old" (
        echo    * Restoring old backup database
        move "%userprofile%\cultibox\configuration.sql.old" "%userprofile%\cultibox\configuration.sql"
    )
) else (
    echo "    .... OK"
)




echo ====   Plugs database ====
if exist "%userprofile%\cultibox\plugs.sql" (
    echo    * Backuping an old plugs file
    move "%userprofile%\cultibox\plugs.sql" "%userprofile%\cultibox\plugs.sql.old"
)
echo    * Saving plugs database...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --no-create-db --no-create-info -h 127.0.0.1 --port=3891 cultibox plugs > "%userprofile%\cultibox\plugs.sql"
If not %ERRORLEVEL% 0 (
    del "%userprofile%\cultibox\plugs.sql"
    echo "    .... NOK"
    if exist "%userprofile%\cultibox\plugs.sql.old" (
        echo    * Restoring old backup database
        move "%userprofile%\cultibox\plugs.sql.old" "%userprofile%\cultibox\plugs.sql"
    )
) else (
    echo "    .... OK"
)



echo ====   Power database ====
if exist "%userprofile%\cultibox\power.sql" (
    echo    * Backuping an old power file
    move "%userprofile%\cultibox\power.sql" "%userprofile%\cultibox\power.sql.old"
)
echo    * Saving power database...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --no-create-db --no-create-info -h 127.0.0.1 --port=3891 cultibox power > "%userprofile%\cultibox\power.sql"
If not %ERRORLEVEL% 0 (
    del "%userprofile%\cultibox\power.sql"
    echo "    .... NOK"
    if exist "%userprofile%\cultibox\power.sql.old" (
        echo    * Restoring old backup database
        move "%userprofile%\cultibox\power.sql.old" "%userprofile%\cultibox\power.sql"
    )
) else (
    echo "    .... OK"
)



echo ====   Programs database ====
if exist "%userprofile%\cultibox\programs.sql" (
    echo    * Backuping an old programs file
    move "%userprofile%\cultibox\programs.sql" "%userprofile%\cultibox\programs.sql.old"
)
echo    * Saving programs database...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --no-create-db --no-create-info -h 127.0.0.1 --port=3891 cultibox programs > "%userprofile%\cultibox\programs.sql"
If not %ERRORLEVEL% 0 (
    del "%userprofile%\cultibox\programs.sql"
    echo "    .... NOK"
    if exist "%userprofile%\cultibox\programs.sql.old" (
        echo    * Restoring old backup database
        move "%userprofile%\cultibox\programs.sql.old" "%userprofile%\cultibox\programs.sql"
    )
) else (
    echo "    .... OK"
)




echo ====   Informations database ====
if exist "%userprofile%\cultibox\informations.sql" (
    echo    * Backuping an old informations file
    move "%userprofile%\cultibox\informations.sql" "%userprofile%\cultibox\informations.sql.old"
)
echo    * Saving informations database...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --no-create-db --no-create-info -h 127.0.0.1 --port=3891 cultibox informations > "%userprofile%\cultibox\informations.sql"
If not %ERRORLEVEL% 0 (
    del "%userprofile%\cultibox\informations.sql"
    echo "    .... NOK"
    if exist "%userprofile%\cultibox\informations.sql.old" (
        echo    * Restoring old backup database
        move "%userprofile%\cultibox\informations.sql.old" "%userprofile%\cultibox\informations.sql"
    )
) else (
    echo "    .... OK"
)




echo ====   Historic database ====
if exist "%userprofile%\cultibox\historic.sql" (
    echo    * Backuping an old historic file
    move "%userprofile%\cultibox\historic.sql" "%userprofile%\cultibox\historic.sql.old"
)
echo    * Saving historic database...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --no-create-db --no-create-info -h 127.0.0.1 --port=3891 cultibox historic > "%userprofile%\cultibox\historic.sql"
If not %ERRORLEVEL% 0 (
    del "%userprofile%\cultibox\historic.sql"
    echo "    .... NOK"
    if exist "%userprofile%\cultibox\historic.sql.old" (
        echo    * Restoring old backup database
        move "%userprofile%\cultibox\historic.sql.old" "%userprofile%\cultibox\historic.sql"
    )
) else (
    echo "    .... OK"
)




echo ====   Notes database ====
if exist "%userprofile%\cultibox\notes.sql" (
    echo    * Backuping an old notes file
    move "%userprofile%\cultibox\notes.sql" "%userprofile%\cultibox\notes.sql.old"
)
echo    * Saving notes database...
C:\cultibox\xampp\mysql\bin\mysqldump.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --no-create-db --no-create-info -h 127.0.0.1 --port=3891 cultibox notes > "%userprofile%\cultibox\notes.sql"
If not %ERRORLEVEL% 0 (
    del "%userprofile%\cultibox\notes.sql"
    echo "    .... NOK"
    if exist "%userprofile%\cultibox\notes.sql.old" (
        echo    * Restoring old backup database
        move "%userprofile%\cultibox\notes.sql.old" "%userprofile%\cultibox\notes.sql"
    )
) else (
    echo "    .... OK"
)


pause

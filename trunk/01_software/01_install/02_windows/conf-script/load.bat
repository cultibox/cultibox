@ECHO OFF

echo -----------------------------------------------------------------
echo               Cultibox load client database script                    
echo -----------------------------------------------------------------
echo 

echo * Testing connection to the database...
c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 cultibox -e "SHOW TABLES;" > NUL
if NOT %ERRORLEVEL% EQU 0  (
        echo ===== Error accessing cultibox database, exiting... ====
        echo ... NOK
        pause
        exit 1
) else (
        echo ... OK"
)


echo * Loading Cultibox database...
If exist "%userprofile%\cultibox\backup_cultibox.sql" (
        c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  -e "DROP DATABASE CULTIBOX;"
        c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  -e "CREATE DATABASE cultibox;"
        c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox < "%userprofile%\cultibox\backup_cultibox.sql"
            if %ERRORLEVEL% EQU 0  (
                echo ... OK
            ) else (
                echo ===== Error loading database, exiting... ====
                echo ... NOK
                pause   
                exit 1
            )
        )
    )
)

pause
exit 0

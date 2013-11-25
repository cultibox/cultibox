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


echo * Loading calendar database...
If exist "%userprofile%\cultibox\calendar.sql" (
    for %%R in ("%userprofile%\cultibox\calendar.sql") do (
        if not %%~zR equ 0 (
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox -e "DELETE FROM calendar"
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox < "%userprofile%\cultibox\calendar.sql"
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


echo * Loading logs database...
If exist "%userprofile%\cultibox\logs.sql" (
    for %%R in ("%userprofile%\cultibox\logs.sql") do (
        if not %%~zR equ 0 (
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox -e "DELETE FROM logs"
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox < "%userprofile%\cultibox\logs.sql"
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


echo * Loading configuration database...
If exist "%userprofile%\cultibox\configuration.sql" (
    for %%R in ("%userprofile%\cultibox\configuration.sql") do (
        if not %%~zR equ 0 (
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox -e "DELETE FROM configuration"
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox < "%userprofile%\cultibox\configuration.sql"
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


echo * Loading plugs database...
If exist "%userprofile%\cultibox\plugs.sql" (
    for %%R in ("%userprofile%\cultibox\plugs.sql") do (
        if not %%~zR equ 0 (
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox -e "DELETE FROM plugs"
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox < "%userprofile%\cultibox\plugs.sql"
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


echo * Loading power database...
If exist "%userprofile%\cultibox\power.sql" (
    for %%R in ("%userprofile%\cultibox\power.sql") do (
        if not %%~zR equ 0 (
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox -e "DELETE FROM power"
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox < "%userprofile%\cultibox\power.sql"
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


echo * Loading programs database...
If exist "%userprofile%\cultibox\programs.sql" (
    for %%R in ("%userprofile%\cultibox\programs.sql") do (
        if not %%~zR equ 0 (
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox -e "DELETE FROM programs"
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox < "%userprofile%\cultibox\programs.sql"
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


echo * Loading informations database...
If exist "%userprofile%\cultibox\informations.sql" (
    for %%R in ("%userprofile%\cultibox\informations.sql") do (
        if not %%~zR equ 0 (
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox -e "DELETE FROM informations"
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox < "%userprofile%\cultibox\informations.sql"
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


echo * Loading historic database...
If exist "%userprofile%\cultibox\historic.sql" (
    for %%R in ("%userprofile%\cultibox\historic.sql") do (
        if not %%~zR equ 0 (
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox -e "DELETE FROM historic"
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox < "%userprofile%\cultibox\historic.sql"
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


echo * Loading notes database...
If exist "%userprofile%\cultibox\notes.sql" (
    for %%R in ("%userprofile%\cultibox\notes.sql") do (
        if not %%~zR equ 0 (
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox -e "DELETE FROM notes"
            c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891  cultibox < "%userprofile%\cultibox\notes.sql"
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


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

:question
SET /P ANSWER=Do you want to continue (Y/N)?
if /i {%ANSWER%}=={y} (goto :yes)
if /i {%ANSWER%}=={yes} (goto :yes)
if /i {%ANSWER%}=={n} (goto :no)
if /i {%ANSWER%}=={no} (goto :no)

goto :question
:no
exit 0 

:yes

echo * Loading Cultibox database...
If exist "%userprofile%\cultibox\backup_cultibox.sql" (
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

@ECHO OFF



echo -----------------------------------------------------------------

echo               Cultibox load database script                    

echo -----------------------------------------------------------------

echo 


set test_error=0



If exist "%userprofile%\cultibox\backup_cultibox.bak" (

    echo   * Cultibox: deletion of the current database, creation of an empty database, import of your backup database...

    c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 cultibox -e "SHOW TABLES;" > NUL

    if %ERRORLEVEL% EQU 0  (

        c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 -e "DROP DATABASE cultibox;"

        c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 -e "CREATE DATABASE cultibox;"

        c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 cultibox < "%userprofile%\cultibox\backup_cultibox.bak"    
        echo ... OK
    ) else (

        echo ===== Error accessing cultibox database, exiting... ====
 
        echo ... NOK


	pause
        exit 1

    )

) else (

    echo   * Missing %userprofile%\cultibox\backup_cultibox.bak file...

    set test_error=1

    echo ...NOK

)


If exist "%userprofile%\cultibox\backup_joomla.bak" (

    echo   * Joomla: deletion of the current database, creation of an empty database, import of your backup database...

    c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 cultibox_joomla -e "SHOW TABLES;" > NUL

    if %ERRORLEVEL% EQU 0  (

        c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 -e "DROP DATABASE cultibox_joomla;"

        c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 -e "CREATE DATABASE cultibox_joomla;"

        c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 cultibox_joomla < "%userprofile%\cultibox\backup_joomla.bak"    
        echo ... OK
    ) else (

        echo ===== Error accessing joomla database, exiting... ====
 
        echo ... NOK


	pause
        exit 1

    )

) else (

    echo   * Missing %userprofile%\cultibox\backup_joomla.bak file...
    echo ...NOK
    if not "%test_error%" == "0" (
        pause
	    exit 3
    ) else (
        pause
        exit 2
    )
)
exit 0


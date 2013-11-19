@ ECHO OFF



c:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 cultibox -e "SELECT * FROM  configuration;" > NUL

If errorlevel 0 (

    for /f "delims=" %%i in ('c:\cultibox\xampp\mysql\bin\mysql.exe "--defaults-extra-file=c:\cultibox\xampp\mysql\bin\my-extra.cnf" -h 127.0.0.1 --port=3891 --batch cultibox -e "SELECT VERSION FROM configuration;"
 ^| findstr /i /c:"noarch"') do echo %%i
    
) else (

    exit 1

)

exit 0

@ECHO OFF
netstat -an|find ":6891"|find "LISTENING" > NUL
if %ERRORLEVEL% EQU 0 (
    net start cultibox_apache
)

netstat -an|find ":3891"|find "LISTENING" > NUL
if %ERRORLEVEL% EQU 0 (
    net start cultibox_mysql
)
start http://localhost:6891/cultibox/

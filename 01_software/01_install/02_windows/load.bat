if exist ..\backup\logs (
copy ..\backup\logs ..\xampp\mysql\data\logs
..\xampp\mysql\bin\mysql.exe cultibox -u root -h localhost -pcultibox -e "DELETE FROM logs"
..\xampp\mysql\bin\mysql.exe cultibox -u root -h localhost -pcultibox -e "LOAD DATA INFILE './logs' REPLACE INTO TABLE logs FIELDS TERMINATED BY ';' IGNORE 1 LINES"
del ..\backup\logs
del  ..\xampp\mysql\data\logs
)
if exist ..\backup\plugs (
copy ..\backup\plugs ..\xampp\mysql\data\plugs
..\xampp\mysql\bin\mysql.exe cultibox -u root -h localhost -pcultibox -e "LOAD DATA INFILE './plugs' REPLACE INTO TABLE plugs FIELDS TERMINATED BY '\t' IGNORE 1 LINES"
del ..\backup\plugs
del  ..\xampp\mysql\data\plugs
)
if exist ..\backup\programs (
copy ..\backup\programs ..\xampp\mysql\data\programs
..\xampp\mysql\bin\mysql.exe cultibox -u root -h localhost -pcultibox -e "LOAD DATA INFILE './programs' REPLACE INTO TABLE programs FIELDS TERMINATED BY ';' IGNORE 1 LINES"
del ..\backup\programs
del  ..\xampp\mysql\data\programs
)
if exist ..\backup\configuration (
copy ..\backup\configuration ..\xampp\mysql\data\configuration
..\xampp\mysql\bin\mysql.exe cultibox -u root -h localhost -pcultibox -e "LOAD DATA INFILE './configuration' REPLACE INTO TABLE configuration FIELDS TERMINATED BY ';' IGNORE 1 LINES"
del ..\backup\configuration
del  ..\xampp\mysql\data\configuration
)
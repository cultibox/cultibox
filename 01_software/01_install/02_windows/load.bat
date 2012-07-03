if exist {..\backup\logs} (
copy ..\backup\logs ..\xampp\mysql\data\logs
..\xampp\mysql\bin\mysql.exe cultibox -u root -h localhost -pcultibox -e "LOAD DATA INFILE './logs' INTO TABLE logs FIELDS TERMINATED BY '\t'
)

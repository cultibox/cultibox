@ECHO OFF
mkdir backup

xampp\mysql\bin\mysqldump.exe --no-create-db --no-create-info -u root -h localhost --port=3891 -pcultibox cultibox jqcalendar > ./backup/jqcalendar.sql
If not errorlevel 0 del ./backup/jqcalendar.sql

xampp\mysql\bin\mysqldump.exe --no-create-db --no-create-info -u root -h localhost --port=3891 -pcultibox cultibox logs > ./backup/logs.sql
If not errorlevel 0 del ./backup/logs.sql

xampp\mysql\bin\mysqldump.exe --no-create-db --no-create-info -u root -h localhost --port=3891 -pcultibox cultibox configuration > ./backup/configuration.sql
If not errorlevel 0 del ./backup/configuration.sql

xampp\mysql\bin\mysqldump.exe --no-create-db --no-create-info -u root -h localhost --port=3891 -pcultibox cultibox plugs > ./backup/plugs.sql
If not errorlevel 0 del ./backup/plugs.sql

xampp\mysql\bin\mysqldump.exe --no-create-db --no-create-info -u root -h localhost --port=3891 -pcultibox cultibox power > ./backup/power.sql
If not errorlevel 0 del ./backup/power.sql

xampp\mysql\bin\mysqldump.exe --no-create-db --no-create-info -u root -h localhost --port=3891 -pcultibox cultibox programs  > ./backup/programs.sql
If not errorlevel 0 del ./backup/programs.sql

xampp\mysql\bin\mysqldump.exe --no-create-db --no-create-info -u root -h localhost --port=3891 -pcultibox cultibox informations > ./backup/informations.sql
If not errorlevel 0 del ./backup/informartions.sql

xampp\mysql\bin\mysqldump.exe --no-create-db --no-create-info -u root -h localhost --port=3891 -pcultibox cultibox historic > ./backup/historic.sql
If not errorlevel 0 del ./backup/historic.sql


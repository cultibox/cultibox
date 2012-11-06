mkdir backup
xampp\mysql\bin\mysqldump.exe --no-create-db --no-create-info -u root -h localhost -pcultibox cultibox jqcalendar logs configuration plugs power programs > ./backup/backup.sql

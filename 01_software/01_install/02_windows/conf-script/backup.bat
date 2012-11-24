mkdir backup
xampp\mysql\bin\mysqldump.exe --no-create-db --no-create-info -u root -h localhost --port=3891 -pcultibox cultibox jqcalendar logs configuration plugs power programs > ./backup/backup.sql

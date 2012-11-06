xampp\mysql\bin\mysql.exe -u root -h localhost -pcultibox cultibox -e "DELETE FROM logs"
xampp\mysql\bin\mysql.exe -u root -h localhost -pcultibox cultibox -e "DELETE FROM configuration"
xampp\mysql\bin\mysql.exe -u root -h localhost -pcultibox cultibox -e "DELETE FROM plugs"
xampp\mysql\bin\mysql.exe -u root -h localhost -pcultibox cultibox -e "DELETE FROM programs"
xampp\mysql\bin\mysql.exe -u root -h localhost -pcultibox cultibox -e "DELETE FROM jqcalendar"
xampp\mysql\bin\mysql.exe -u root -h localhost -pcultibox cultibox -e "DELETE FROM power"
xampp\mysql\bin\mysql.exe -u root -h localhost -pcultibox cultibox < ./backup/backup.sql

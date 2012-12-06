If exist "./backup/backup.sql" (
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM jqcalendar"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM logs"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM configuration"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM plugs"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM power"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM programs"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM informations"

    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/backup.sql
)

If exist "./backup/jqcalendar.sql" (
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM jqcalendar"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/jqcalendar.sql
)

If exist "./backup/logs.sql" (
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM logs"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/logs.sql
)


If exist "./backup/configuration.sql" (
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM configuration"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/configuration.sql
)


If exist "./backup/plugs.sql" (
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM plugs"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/plugs.sql
)


If exist "./backup/power.sql" (
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM power"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/power.sql
)


If exist "./backup/programs.sql" (
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM programs"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/programs.sql
)


If exist "./backup/informations.sql" (
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM informations"
    xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/informations.sql
)

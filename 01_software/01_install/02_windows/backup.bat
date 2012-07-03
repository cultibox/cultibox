mkdir backup
xampp\mysql\bin\mysql.exe  cultibox -u root -h localhost -pcultibox -e "SELECT * FROM `logs` WHERE `fake_log` = false" > backup\logs

timeout 5

xampp\mysql\bin\mysqladmin.exe -u root -h localhost password cultibox
xampp\mysql\bin\mysql.exe -u root -h localhost -pcultibox < xampp\sql_install\user_cultibox.sql
xampp\mysql\bin\mysql.exe -u root -h localhost -pcultibox < xampp\sql_install\joomla.sql
xampp\mysql\bin\mysql.exe -u root -h localhost -pcultibox < xampp\sql_install\cultibox.sql

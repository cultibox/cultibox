mkdir backup
xampp\mysql\bin\mysql.exe  cultibox -u root -h localhost -pcultibox -e "SELECT CONCAT(timestamp,';',temperature,';',humidity,';',date_catch,';',time_catch,';',fake_log) FROM `logs`" > ./backup/logs
xampp\mysql\bin\mysql.exe  cultibox -u root -h localhost -pcultibox -e "SELECT CONCAT(id,';',PLUG_ID,';',PLUG_NAME,';',PLUG_TYPE,';',PLUG_TOLERANCE,';',PLUG_POWER) FROM `plugs`" > ./backup/plugs
xampp\mysql\bin\mysql.exe  cultibox -u root -h localhost -pcultibox -e "SELECT CONCAT(plug_id,';',time_start,';',time_stop,';',value) FROM `programs`" > ./backup/programs
xampp\mysql\bin\mysql.exe  cultibox -u root -h localhost -pcultibox -e "SELECT CONCAT(id,';',COLOR_HUMIDITY_GRAPH,';',COLOR_TEMPERATURE_GRAPH,';',RECORD_FREQUENCY,';',POWER_FREQUENCY,';',NB_PLUGS,';',UPDATE_PLUGS_FREQUENCY,';',LANG,';',LOG_TEMP_AXIS,';',LOG_HYGRO_AXIS,';',SHOW_POPUP,';',ALARM_ACTIV,';',ALARM_VALUE,';',ALARM_SENSO,';',ALARM_SENSS,';',FIRST_USE) FROM `configuration`" > ./backup/configuration
pause

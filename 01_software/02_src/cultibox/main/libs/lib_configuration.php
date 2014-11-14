<?php

namespace configuration {

// {{{ update_db()
// ROLE update dabase
// RET none
function check_db() {

    // Define columns of the calendar table
    $conf_index_col = array();
    $conf_index_col["id"]                   = array ( 'Field' => "id", 'Type' => "int(11)", 'default_value' => 1, 'carac' => "NOT NULL AUTO_INCREMENT PRIMARY KEY");
    $conf_index_col["VERSION"]              = array ( 'Field' => "VERSION", 'Type' => "varchar(30)", 'default_value' => "2.0.12",'carac' => "NOT NULL");
    $conf_index_col["COLOR_HUMIDITY_GRAPH"] = array ( 'Field' => "COLOR_HUMIDITY_GRAPH", 'Type' => "varchar(30)", 'default_value' => "blue",'carac' => "NOT NULL");
    $conf_index_col["COLOR_TEMPERATURE_GRAPH"] = array ( 'Field' => "COLOR_TEMPERATURE_GRAPH", 'Type' => "varchar(30)", 'default_value' => "red",'carac' => "NOT NULL");
    $conf_index_col["COLOR_WATER_GRAPH"]    = array ( 'Field' => "COLOR_WATER_GRAPH", 'Type' => "varchar(30)", 'default_value' => "orange",'carac' => "NOT NULL");
    $conf_index_col["COLOR_LEVEL_GRAPH"]    = array ( 'Field' => "COLOR_LEVEL_GRAPH", 'Type' => "varchar(30)", 'default_value' => "pink",'carac' => "NOT NULL");
    $conf_index_col["COLOR_PH_GRAPH"]       = array ( 'Field' => "COLOR_PH_GRAPH", 'Type' => "varchar(30)", 'default_value' => "brown",'carac' => "NOT NULL");
    $conf_index_col["COLOR_EC_GRAPH"]       = array ( 'Field' => "COLOR_EC_GRAPH", 'Type' => "varchar(30)", 'default_value' => "yellow",'carac' => "NOT NULL");
    $conf_index_col["COLOR_OD_GRAPH"]       = array ( 'Field' => "COLOR_OD_GRAPH", 'Type' => "varchar(30)", 'default_value' => "red",'carac' => "NOT NULL");
    $conf_index_col["COLOR_ORP_GRAPH"]      = array ( 'Field' => "COLOR_ORP_GRAPH", 'Type' => "varchar(30)", 'default_value' => "blue",'carac' => "NOT NULL");
    $conf_index_col["COLOR_POWER_GRAPH"]    = array ( 'Field' => "COLOR_POWER_GRAPH", 'Type' => "varchar(30)", 'default_value' => "black",'carac' => "NOT NULL");
    $conf_index_col["RECORD_FREQUENCY"]     = array ( 'Field' => "RECORD_FREQUENCY", 'Type' => "int(11)", 'default_value' => 5,'carac' => "NOT NULL");
    $conf_index_col["POWER_FREQUENCY"]      = array ( 'Field' => "POWER_FREQUENCY", 'Type' => "int(11)", 'default_value' => 5,'carac' => "NOT NULL");
    $conf_index_col["NB_PLUGS"]             = array ( 'Field' => "NB_PLUGS", 'Type' => "int(11)", 'default_value' => 3,'carac' => "NOT NULL");
    $conf_index_col["UPDATE_PLUGS_FREQUENCY"] = array ( 'Field' => "UPDATE_PLUGS_FREQUENCY", 'Type' => "int(20)", 'default_value' => 1,'carac' => "NOT NULL");
    $conf_index_col["ALARM_ACTIV"]          = array ( 'Field' => "ALARM_ACTIV", 'Type' => "varchar(4)", 'default_value' => "0000",'carac' => "NOT NULL");
    $conf_index_col["ALARM_VALUE"]          = array ( 'Field' => "ALARM_VALUE", 'Type' => "varchar(5)", 'default_value' => "60.00",'carac' => "NOT NULL");
    $conf_index_col["COST_PRICE"]           = array ( 'Field' => "COST_PRICE", 'Type' => "decimal(6,4)", 'default_value' => 0.1249,'carac' => "NOT NULL");
    $conf_index_col["COST_PRICE_HP"]        = array ( 'Field' => "COST_PRICE_HP", 'Type' => "decimal(6,4)", 'default_value' => 0.1353,'carac' => "NOT NULL");
    $conf_index_col["COST_PRICE_HC"]        = array ( 'Field' => "COST_PRICE_HC", 'Type' => "decimal(6,4)", 'default_value' => 0.0926,'carac' => "NOT NULL");
    $conf_index_col["START_TIME_HC"]        = array ( 'Field' => "START_TIME_HC", 'Type' => "varchar(5)", 'default_value' => "22:30",'carac' => "NOT NULL");
    $conf_index_col["STOP_TIME_HC"]         = array ( 'Field' => "STOP_TIME_HC", 'Type' => "varchar(5)", 'default_value' => "06:30",'carac' => "NOT NULL");
    $conf_index_col["COST_TYPE"]            = array ( 'Field' => "COST_TYPE", 'Type' => "varchar(20)", 'default_value' => "standard",'carac' => "NOT NULL");
    $conf_index_col["STATISTICS"]           = array ( 'Field' => "STATISTICS", 'Type' => "varchar(5)", 'default_value' => "True",'carac' => "NOT NULL");
    $conf_index_col["ADVANCED_REGUL_OPTIONS"] = array ( 'Field' => "ADVANCED_REGUL_OPTIONS", 'Type' => "varchar(5)", 'default_value' => "False",'carac' => "NOT NULL");
    $conf_index_col["SHOW_COST"]            = array ( 'Field' => "SHOW_COST", 'Type' => "tinyint(1)", 'default_value' => 0,'carac' => "NOT NULL");
    $conf_index_col["RESET_MINMAX"]         = array ( 'Field' => "RESET_MINMAX", 'Type' => "varchar(5)", 'default_value' => "00:00",'carac' => "NOT NULL");
    $conf_index_col["WIFI"]                 = array ( 'Field' => "WIFI", 'Type' => "tinyint(1)", 'default_value' => 0,'carac' => "NOT NULL");
    $conf_index_col["WIFI_SSID"]            = array ( 'Field' => "WIFI_SSID", 'Type' => "varchar(32)");
    $conf_index_col["WIFI_KEY_TYPE"]        = array ( 'Field' => "WIFI_KEY_TYPE", 'Type' => "varchar(10)", 'default_value' => "NONE",'carac' => "NOT NULL");
    $conf_index_col["WIFI_PASSWORD"]        = array ( 'Field' => "WIFI_PASSWORD", 'Type' => "varchar(63)");
    $conf_index_col["WIFI_IP"]              = array ( 'Field' => "WIFI_IP", 'Type' => "varchar(15)", 'default_value' => "000.000.000.000",'carac' => "NOT NULL");
    $conf_index_col["WIFI_IP_REAL"]              = array ( 'Field' => "WIFI_IP_REAL", 'Type' => "varchar(15)", 'default_value' => "000.000.000.000",'carac' => "NOT NULL");
    $conf_index_col["WIFI_IP_MANUAL"]       = array ( 'Field' => "WIFI_IP_MANUAL", 'Type' => "BOOLEAN", 'default_value' => false,'carac' => "NOT NULL");
    $conf_index_col["RTC_OFFSET"]           = array ( 'Field' => "RTC_OFFSET", 'Type' => "int(11)", 'default_value' => 0,'carac' => "NOT NULL");
    $conf_index_col["REMOVE_1000_CHANGE_LIMIT"] = array ( 'Field' => "REMOVE_1000_CHANGE_LIMIT", 'Type' => "varchar(5)", 'default_value' => "False",'carac' => "NOT NULL");
    $conf_index_col["REMOVE_5_MINUTE_LIMIT"] = array ( 'Field' => "REMOVE_5_MINUTE_LIMIT", 'Type' => "varchar(5)", 'default_value' => "False",'carac' => "NOT NULL");
    $conf_index_col["DEFAULT_LANG"] = array ( 'Field' => "DEFAULT_LANG", 'Type' => "varchar(5)", 'default_value' => "fr_FR",'carac' => "NOT NULL");


    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'configuration';";
    
    $db = \db_priv_pdo_start("root");
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    // If table exists, return
    if ($res == null)
    {
        
        // Buil MySQL command to create table
        $sql = "CREATE TABLE `configuration` ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."VERSION varchar(30) NOT NULL DEFAULT '2.0.12',"
            ."COLOR_HUMIDITY_GRAPH varchar(30) NOT NULL DEFAULT 'blue',"
            ."COLOR_TEMPERATURE_GRAPH varchar(30) NOT NULL DEFAULT 'red',"
            ."COLOR_WATER_GRAPH varchar(30) NOT NULL DEFAULT 'orange',"
            ."COLOR_LEVEL_GRAPH varchar(30) NOT NULL DEFAULT 'pink',"
            ."COLOR_PH_GRAPH varchar(30) NOT NULL DEFAULT 'brown',"
            ."COLOR_EC_GRAPH varchar(30) NOT NULL DEFAULT 'yellow',"
            ."COLOR_OD_GRAPH varchar(30) NOT NULL DEFAULT 'red',"
            ."COLOR_ORP_GRAPH varchar(30) NOT NULL DEFAULT 'blue',"
            ."COLOR_POWER_GRAPH varchar(30) NOT NULL DEFAULT 'black',"
            ."RECORD_FREQUENCY int(11) NOT NULL DEFAULT '5',"
            ."POWER_FREQUENCY int(11) NOT NULL DEFAULT '5',"
            ."NB_PLUGS int(11) NOT NULL DEFAULT '3',"
            ."UPDATE_PLUGS_FREQUENCY int(20) NOT NULL DEFAULT '1',"
            ."ALARM_ACTIV varchar(4) NOT NULL DEFAULT '0000',"
            ."ALARM_VALUE varchar(5) NOT NULL DEFAULT '60.00',"
            ."COST_PRICE decimal(6,4) NOT NULL DEFAULT '0.1249',"
            ."COST_PRICE_HP decimal(6,4) NOT NULL DEFAULT '0.1353',"
            ."COST_PRICE_HC decimal(6,4) NOT NULL DEFAULT '0.0926',"
            ."START_TIME_HC varchar(5) NOT NULL DEFAULT '22:30',"
            ."STOP_TIME_HC varchar(5) NOT NULL DEFAULT '06:30',"
            ."COST_TYPE varchar(20) NOT NULL DEFAULT 'standard',"
            ."STATISTICS varchar(5) NOT NULL DEFAULT 'True',"
            ."ADVANCED_REGUL_OPTIONS VARCHAR(5) NOT NULL DEFAULT 'False',"
            ."SHOW_COST BOOLEAN NOT NULL DEFAULT 0,"
            ."RESET_MINMAX VARCHAR(5) NOT NULL DEFAULT '00:00',"
            ."WIFI BOOLEAN NOT NULL DEFAULT 0,"
            ."WIFI_SSID VARCHAR(32),"
            ."WIFI_KEY_TYPE VARCHAR(10) NOT NULL DEFAULT 'NONE',"
            ."WIFI_PASSWORD VARCHAR(63),"
            ."WIFI_IP VARCHAR(15) NOT NULL DEFAULT '000.000.000.000',"
            ."WIFI_IP_REAL VARCHAR(15) NOT NULL DEFAULT '000.000.000.000',"
            ."WIFI_IP_MANUAL BOOLEAN NOT NULL DEFAULT false,"
            ."RTC_OFFSET int(11) NOT NULL DEFAULT '0',"
            ."REMOVE_1000_CHANGE_LIMIT VARCHAR(5) NOT NULL DEFAULT 'False',"
            ."REMOVE_5_MINUTE_LIMIT VARCHAR(5) NOT NULL DEFAULT 'False',"
            ."DEFAULT_LANG VARCHAR(5) NOT NULL DEFAULT 'fr_FR');";
        
        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

         $sql = "INSERT INTO configuration (id, VERSION, COLOR_HUMIDITY_GRAPH, COLOR_TEMPERATURE_GRAPH, COLOR_WATER_GRAPH, COLOR_LEVEL_GRAPH, COLOR_PH_GRAPH, COLOR_EC_GRAPH, COLOR_OD_GRAPH, COLOR_ORP_GRAPH, COLOR_POWER_GRAPH, RECORD_FREQUENCY, POWER_FREQUENCY, NB_PLUGS, UPDATE_PLUGS_FREQUENCY, ALARM_ACTIV, ALARM_VALUE, COST_PRICE, COST_PRICE_HP, COST_PRICE_HC, START_TIME_HC, STOP_TIME_HC, COST_TYPE, STATISTICS,ADVANCED_REGUL_OPTIONS,SHOW_COST,RESET_MINMAX, WIFI, WIFI_SSID, WIFI_KEY_TYPE, WIFI_PASSWORD, WIFI_IP, WIFI_IP_REAL, WIFI_IP_MANUAL, RTC_OFFSET) VALUES (1, '2.0.12', 'blue', 'red', 'orange', 'pink', 'brown', 'yellow', 'red', 'blue', 'black', 5, 1, 3, 1, '0000', '60', 0.1225, 0.1353, 0.0926, '22:30', '06:30', 'standard', 'True', 'False', 0, '00:00',0,'','NONE','','000.000.000.000','000.000.000.000',0,0);";
        // Insert row:
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

    } else {
        // Check column
        check_and_update_column_db ("configuration", $conf_index_col);
    }
    $db=null;
    
}


}

?>

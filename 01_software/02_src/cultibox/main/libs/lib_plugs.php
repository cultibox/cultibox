<?php

namespace plugs {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {

    // Define columns of the calendar table
    $plugs_index_col = array();
    $plugs_index_col["id"]                   = array ( 'Field' => "id", 'Type' => "int(11)", 'default_value' => 1, 'carac' => "NOT NULL AUTO_INCREMENT PRIMARY KEY");
    $plugs_index_col["PLUG_ID"] = array ( 'Field' => "PLUG_ID", 'Type' => "varchar(3)", 'default_value' => "NULL");
    $plugs_index_col["PLUG_NAME"] = array ( 'Field' => "PLUG_NAME", 'Type' => "varchar(30)", 'default_value' => "NULL");
    $plugs_index_col["PLUG_TYPE"] = array ( 'Field' => "PLUG_TYPE", 'Type' => "varchar(20)", 'default_value' => 'other', 'carac' => "NOT NULL");
    $plugs_index_col["PLUG_TOLERANCE"] = array ( 'Field' => "PLUG_TOLERANCE", 'Type' => "decimal(3,1)", 'default_value' => "NULL");
    $plugs_index_col["PLUG_POWER"] = array ( 'Field' => "PLUG_POWER", 'Type' => "int(11)", 'default_value' => "NULL");
    $plugs_index_col["PLUG_POWER_MAX"] = array ( 'Field' => "PLUG_POWER_MAX", 'Type' => "varchar(10)", 'default_value' => '1000', 'carac' => "NOT NULL");
    $plugs_index_col["PLUG_REGUL"] = array ( 'Field' => "PLUG_REGUL", 'Type' => "varchar(5)", 'default_value' => 'False', 'carac' => "NOT NULL");
    $plugs_index_col["PLUG_REGUL_SENSOR"] = array ( 'Field' => "PLUG_REGUL_SENSOR", 'Type' => "varchar(7)", 'default_value' => '1', 'carac' => "NOT NULL");
    $plugs_index_col["PLUG_SENSO"] = array ( 'Field' => "PLUG_SENSO", 'Type' => "varchar(1)", 'default_value' => 'T', 'carac' => "NOT NULL");
    $plugs_index_col["PLUG_SENSS"] = array ( 'Field' => "PLUG_SENSS", 'Type' => "varchar(1)", 'default_value' => '+', 'carac' => "NOT NULL");
    $plugs_index_col["PLUG_REGUL_VALUE"] = array ( 'Field' => "PLUG_REGUL_VALUE", 'Type' => "decimal(3,1)", 'default_value' => 35.0, 'carac' => "NOT NULL");
    $plugs_index_col["PLUG_SECOND_TOLERANCE"] = array ( 'Field' => "PLUG_SECOND_TOLERANCE", 'Type' => "decimal(3,1)", 'default_value' => 0.0, 'carac' => "NOT NULL");
    $plugs_index_col["PLUG_COMPUTE_METHOD"] = array ( 'Field' => "PLUG_COMPUTE_METHOD", 'Type' => "varchar(1)", 'default_value' => 'M', 'carac' => "NOT NULL");


    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'plugs';";
    
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
        $sql = "CREATE TABLE plugs ("
                    ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
                    ."PLUG_ID varchar(3) DEFAULT NULL,"
                    ."PLUG_NAME varchar(30) DEFAULT NULL,"
                    ."PLUG_TYPE varchar(20) NOT NULL DEFAULT 'other',"
                    ."PLUG_TOLERANCE decimal(3,1) DEFAULT NULL,"
                    ."PLUG_POWER int(11) NULL DEFAULT NULL,"
                    ."PLUG_POWER_MAX varchar(10) NOT NULL DEFAULT '1000',"
                    ."PLUG_REGUL varchar(5) NOT NULL DEFAULT 'False',"
                    ."PLUG_REGUL_SENSOR VARCHAR( 7 ) NOT NULL DEFAULT '1',"
                    ."PLUG_SENSO varchar(1) NOT NULL DEFAULT 'T',"
                    ."PLUG_SENSS varchar(1) NOT NULL DEFAULT '+',"
                    ."PLUG_REGUL_VALUE decimal(3,1) NOT NULL DEFAULT '35.0',"
                    ."PLUG_SECOND_TOLERANCE DECIMAL( 3, 1 ) NOT NULL DEFAULT '0.0',"
                    ."PLUG_COMPUTE_METHOD VARCHAR( 1 ) NOT NULL DEFAULT 'M');";

        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

        $sql = "INSERT INTO plugs (PLUG_ID, PLUG_NAME, PLUG_TYPE, PLUG_TOLERANCE, PLUG_POWER, PLUG_POWER_MAX, PLUG_REGUL, PLUG_REGUL_SENSOR, PLUG_SENSO, PLUG_SENSS, PLUG_REGUL_VALUE, PLUG_SECOND_TOLERANCE,PLUG_COMPUTE_METHOD) VALUES
('1', 'Prise1', 'other', 1.0, NULL, '3500', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('2', 'Prise2', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('3', 'Prise3', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('4', 'Prise4', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('5', 'Prise5', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('6', 'Prise6', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('7', 'Prise7', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('8', 'Prise8', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('9', 'Prise9', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('10', 'Prise10', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('11', 'Prise11', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('12', 'Prise2', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('13', 'Prise13', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('14', 'Prise14', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('15', 'Prise15', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M'),
('16', 'Prise16', 'other', 1.0, NULL, '1000', 'False', '1', 'T', '+', 35.0,0.0,'M');";

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
        check_and_update_column_db ("plugs", $plugs_index_col);
    }
    $db = null;
}


}

?>

<?php

namespace program_index {

// {{{ update_db()
// ROLE update dabase
// RET none
function check_db() {

    // Define columns of the calendar table
    $conf_index_col = array();
    $index_col["id"]                   = array ( 'Field' => "id", 'Type' => "int(11)", 'default_value' => 1);
    $index_col["name"]              = array ( 'Field' => "name", 'Type' => "varchar(100)", 'default_value' => __('CURRENT_PROG_NAME'));
    $index_col["version"] = array ( 'Field' => "version", 'Type' => "varchar(100)", 'default_value' => "1.0");
    $index_col["program_idx"] = array ( 'Field' => "program_idx", 'Type' => "INT", 'default_value' => "1");
    $index_col["creation"]    = array ( 'Field' => "creation", 'Type' => "DATETIME", 'default_value' => "");
    $index_col["modification"]    = array ( 'Field' => "modification", 'Type' => "DATETIME", 'default_value' => "");
    $index_col["plugv_filename"]       = array ( 'Field' => "plugv_filename", 'Type' => "varchar(10)", 'default_value' => "00");
    $index_col["comments"]       = array ( 'Field' => "comments", 'Type' => "varchar(500)", 'default_value' => __('CURRENT_PROG_COMMENT'));


    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'program_index';";
    
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
        $sql = "CREATE TABLE program_index"
                 ."(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
                 ."name VARCHAR(100),"
                 ."version VARCHAR(100),"
                 ."program_idx INT,"
                 ."creation DATETIME,"
                 ."modification DATETIME,"
                 ."plugv_filename VARCHAR(10),"
                 ."comments VARCHAR(500));";
        
        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

        $sql = "INSERT INTO program_index (name,version,program_idx,creation,modification,plugv_filename,comments) VALUES('".__('CURRENT_PROG_NAME')."','1.0','1' , NOW(), NOW(), '00' , '".__('CURRENT_PROG_COMMENT')."');";
        // Insert row:
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }


       //INSERT INTO program_index (name,version,program_idx`,`creation`, `modification`, `plugv_filename`,`comments`) VALUES('Aktuelle','1.0','1' , NOW(), NOW(), '00' , "Aktuelles Programm");
        // CURRENT_PROG_NAME
        
    }
    
    $db = null;

    // Check column
    check_and_update_column_db ("program_index", $index_col);
    
}


}

?>

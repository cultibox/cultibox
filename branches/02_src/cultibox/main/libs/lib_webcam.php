<?php

namespace webcam {

// {{{ update_db()
// ROLE update dabase
// RET none
function check_db() {

    // Define columns of the calendar table
    $conf_index_col = array();
    $conf_index_col["id"]                   = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $conf_index_col["brightness"]           = array ( 'Field' => "brightness", 'Type' => "int(11)", 'default_value' => -1,'carac' => "NOT NULL");
    $conf_index_col["contrast"]             = array ( 'Field' => "contrast", 'Type' => "int(11)", 'default_value' => -1,'carac' => "NOT NULL");
    $conf_index_col["resolution"]           = array ( 'Field' => "resolution", 'Type' => "varchar(11)", 'default_value' => "-1",'carac' => "NOT NULL");
    $conf_index_col["palette"]              = array ( 'Field' => "palette", 'Type' => "varchar(11)", 'default_value' => "-1",'carac' => "NOT NULL");


    // Check if table webcam exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'webcam';";
    
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
        $sql = "CREATE TABLE `webcam` ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."brightness int(11) NOT NULL DEFAULT -1,"
            ."contrast int(11) NULL DEFAULT -1,"
            ."palette VARCHAR(11) NULL DEFAULT '-1',"
            ."resolution VARCHAR(11) NULL DEFAULT '-1');";
        
        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

         $sql = "INSERT INTO webcam (brightness, contrast,resolution,palette) VALUES (-1,-1,'-1','-1');";
        // Insert row:
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

    } else {
        // Check column
        check_and_update_column_db ("webcam", $conf_index_col);
    } 
    $db=null;
  }
}

?>

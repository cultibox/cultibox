<?php

namespace informations {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {

    // Define columns of the calendar table
    $info_index_col = array();
    $info_index_col["ID"]                   = array ( 'Field' => "ID", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $info_index_col["cbx_id"] = array ( 'Field' => "cbx_id", 'Type' => "int(5)", 'default_value' => 0, 'carac' => "NOT NULL");
    $info_index_col["firm_version"] = array ( 'Field' => "firm_version", 'Type' => "varchar(7)", 'default_value' => "000.000", 'carac' => "NOT NULL");
    $info_index_col["log"] = array ( 'Field' => "log", 'Type' => "mediumtext");


    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'informations';";
    
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
        $sql = "CREATE TABLE informations "
                . "(ID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, "
                . "cbx_id int(5) NOT NULL DEFAULT '0', "
                . "firm_version varchar(7) NOT NULL DEFAULT '000.000', "
                . "log mediumtext); ";

        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }

        $sql = "INSERT INTO informations (ID ,cbx_id ,firm_version,log) VALUES (NULL , '0', '', '');";
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
        check_and_update_column_db ("informations", $info_index_col);
    }
    $db = null;
}


}

?>

<?php

namespace configuration {

// {{{ update_db()
// ROLE update dabase
// RET none
function check_db() {

    // Check if table calendar have program_index field
    $sql = "show COLUMNS FROM configuration WHERE Field LIKE 'ACTIV_DAILY_PROGRAM';";
    
    $db = \db_priv_pdo_start("root");
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    // If column not exists, create them
    if ($res == null)
    {
        
        // Buil MySQL command to create column
        $sql = "ALTER TABLE configuration ADD ACTIV_DAILY_PROGRAM VARCHAR(5) NOT NULL DEFAULT 'False';";
        
        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
    
    }
        
    $db=null;
    
}


}

?>

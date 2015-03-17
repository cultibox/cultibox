<?php

namespace power {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'power';";

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
        $sql = "CREATE TABLE power ("
            ."timestamp varchar(14) NOT NULL DEFAULT '',"
            ."record int(3) DEFAULT NULL,"
            ."plug_number int(3) DEFAULT NULL,"
            ."date_catch varchar(10) DEFAULT NULL,"
            ."time_catch varchar(10) DEFAULT NULL,"
            ."KEY timestamp (timestamp));";

        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
    }
    $db = null;
}

}

?>

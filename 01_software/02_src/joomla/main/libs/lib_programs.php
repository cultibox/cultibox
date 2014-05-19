<?php

namespace program {

// {{{ update_db()
// ROLE update dabase
// RET none
function check_db() {

    // Check if table program_index exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'program_index';";
    
    $db = \db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;
    
    // If table exists, exists
    if ($res != null)
        return 0;
        
    echo "not exists";
        
    create_db();
}

function create_db()
{
    $sql = "CREATE TABLE `program_index` (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), version VARCHAR(100), program_idx INT);";
        
    $db = \db_priv_pdo_start();
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    $db=null;
        
    echo "fin";
}

}

?>

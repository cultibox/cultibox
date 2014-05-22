<?php

namespace program {

// {{{ update_db()
// ROLE update dabase
// RET none
function check_db() {

    // Check if table program_index exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'program_index';";
    
    $db = \db_priv_pdo_start("root");
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    // If table exists, return
    if ($res != null)
        return 0;
        
    // Buil MySQL command to create table
    $sql = "CREATE TABLE program_index (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), version VARCHAR(100), program_idx INT, creation DATETIME, modification DATETIME, plugv_filename VARCHAR(10));";
    
    // Create table
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    
    $db=null;
    
    // Add default line
    add_row_program_idx('Courant','1.0','1');

}

// {{{ add_row_program_idx()
// ROLE Add a row in programm_idx table
// IN $name : Name of the programm
//    $version : Version of the programm
//    $program_idx : Pointor on the the programs table
// RET none
function add_row_program_idx($name, $version, $program_idx = "") {

    // Open connection to dabase
    $db = \db_priv_pdo_start();
    // Add line
    $sql = "INSERT INTO program_index(name, version, program_idx ,creation ,modification, plugv_filename) ";
    $sql = $sql . "VALUE(\"" . $name . "\", \"" . $version . "\", \"" . $program_idx . "\", \"" . date("Y-m-d H:i:s") . "\", \"" . date("Y-m-d H:i:s") . "\", \"plugv\");";

    // Run command
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
  
    $db=null;
}

// {{{ add_row_program_idx()
// ROLE get a programm number not used in table programs
// RET program_idx
function get_programm_number_empty() {

    // Open connection to dabase
    $db = \db_priv_pdo_start();

    // Search maximum idx
    $sql = "SELECT number FROM programs ORDER BY number DESC LIMIT 1;";
    
    $program_idx = "";
    
    // Run command
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $row = $sth->fetch();
        $program_idx = $row['number'] + 1;
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    
    $db=null;
    
    return $program_idx;
}

// {{{ copy()
// ROLE Copy program $idInput into $output
// RET none
function copy($idInput, $idOutput) {

    // Open connection to dabase
    $db = \db_priv_pdo_start("root");
    
    // Select input program
    $sql = "SELECT * from programs WHERE number=\"" . $idInput . "\";";

    // Run command
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    
    // Creat insert statement
    $sql = "INSERT INTO programs(plug_id, time_start, time_stop, value, type, number) VALUES ";
    
    // For each line found
    $start = 0;
    while ($row = $sth->fetch()) 
    {
        if ($start != 0)
            $sql = $sql . ", " ;
        else
            $start = 1;
        
        // Ad same element to the next program but change field number
        $sql = $sql . "(\"" . $row['plug_id'] . "\",\"" . $row['time_start'] . "\",\"" . $row['time_stop'] . "\",\"" . $row['value'] . "\",\"" . $row['type'] . "\",\"" . $idOutput . "\")";
    }
    
    // Run command
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    
}

// {{{ get_program_index_info()
// ROLE Return infos about program_index
// RET program name
function get_program_index_info (&$tab)
{
    // Open connection to dabase
    $db = \db_priv_pdo_start();
    
    //
    $sql = "SELECT * FROM program_index;";
    
    // Run command
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $tab=$sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
}

// {{{ get_program_name()
// ROLE Return program name
// RET program name
function get_program_name ($program_idx)
{
    // Open connection to dabase
    $db = \db_priv_pdo_start();
    
    //
    $sql = "SELECT name FROM program_index WHERE id = \"$program_idx\";";
    
    // Run command
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $tab=$sth->fetch();
        return $tab['name'];
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
}

// {{{ delete_program()
// ROLE Delete a program
// IN program_idx program to delete 
// RET program name
function delete_program ($program_idx)
{
    // Open connection to dabase
    $db = \db_priv_pdo_start();
    
    //delete from program table
    $sql = "SELECT program_idx FROM program_index WHERE id = \"$program_idx\";";
    
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $tab=$sth->fetch();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    
    $prg = $tab['program_idx'];
    $sql = "DELETE FROM programs WHERE number = \"$prg\";";
    
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    
    //delete from table_index
    $sql = "DELETE FROM program_index WHERE id = \"$program_idx\";";
    
    // Run command
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
}

}

?>

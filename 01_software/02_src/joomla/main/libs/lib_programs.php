<?php

namespace program {

// {{{ update_db()
// ROLE update dabase
// RET none
function check_db() {

    // Define columns of the calendar table
    $program_index_col = array();
    $program_index_col["id"]            = array ( 'Field' => "id", 'Type' => "INT");
    $program_index_col["name"]          = array ( 'Field' => "name", 'Type' => "VARCHAR(100)");
    $program_index_col["version"]       = array ( 'Field' => "version", 'Type' => "VARCHAR(100)");
    $program_index_col["program_idx"]   = array ( 'Field' => "program_idx", 'Type' => "INT");
    $program_index_col["creation"]      = array ( 'Field' => "creation", 'Type' => "DATETIME");
    $program_index_col["modification"]  = array ( 'Field' => "modification", 'Type' => "DATETIME");
    $program_index_col["plugv_filename"] = array ( 'Field' => "plugv_filename", 'Type' => "VARCHAR(10)");
    $program_index_col["comments"]      = array ( 'Field' => "comments", 'Type' => "VARCHAR(500)");

    // Check if table program_index exists
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
        $sql = "CREATE TABLE program_index "
                . "(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, "
                . "name VARCHAR(100), version VARCHAR(100), "
                . "program_idx INT, creation DATETIME, "
                . "modification DATETIME, plugv_filename VARCHAR(10), "
                . "comments VARCHAR(500));";
        
        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
        
        // Add default line
        add_row_program_idx('Courant','1.0','1' , '00' , "Programme courant");
        
    }
    
    $db = null;

    // Check column
    check_and_update_column_db ("program_index", $program_index_col);


}
// }}}

// {{{ add_row_program_idx()
// ROLE Add a row in programm_idx table
// IN $name : Name of the programm
//    $version : Version of the programm
//    $program_idx : Pointor on the the programs table
// RET none
function add_row_program_idx($name, $version, $program_idx = "", $plugv_filename = "", $comments = "") {

    // Open connection to dabase
    $db = \db_priv_pdo_start();

    // If not defined, search first index available
    $index = 0;
    while ($plugv_filename == "")
    {
    
        if (strlen($index) < 2)
            $index = "0" . $index;
    
        $sql = "SELECT plugv_filename FROM program_index WHERE plugv_filename = \"" . $index . "\" ;";
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
            $row = $sth->fetch();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
        
        if ($row == null)
            $plugv_filename = $index;
            
        $index = $index + 1;
        
    }

    // Add line
    $sql = "INSERT INTO program_index(name, version, program_idx ,creation ,modification, plugv_filename, comments) "
        . "VALUE(\"" . $name . "\", \"" . $version . "\", \"" . $program_idx . "\", \"" 
        . date("Y-m-d H:i:s") . "\", \"" . date("Y-m-d H:i:s") . "\", \"" . $plugv_filename . "\", \"" . $comments . "\");";

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
// }}}

// {{{ get_programm_number_empty()
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
// }}}

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
// }}}

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
// }}}

// {{{ get_field_from_program_index()
// ROLE Return pluXX file name
// RET pluXX file name
function get_field_from_program_index ($variable,$program_idx)
{
    // Open connection to dabase
    $db = \db_priv_pdo_start();
    
    //
    $sql = "SELECT " . $variable . " FROM program_index WHERE id = \"$program_idx\";";
    
    // Run command
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $tab=$sth->fetch();
        return $tab[$variable];
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    
    return "";
}
// }}}

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
    
    // Delete from calendar
    $sql = "DELETE FROM calendar WHERE program_index = \"$program_idx\";";
    
    // Run command
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    
    $db = null;
}
// }}}

// {{{ get_plug_programm()
// ROLE Retrieve program of a plug
// IN plug plugNumber
// IN dateStart Date to start (write in Unix (s) format) . Time is not used
// IN dateEnd   Date to start (write in Unix (s) format) . Time is not used
// RET program name
function get_plug_programm ($plug, $dateStart, $dateEnd, $day="day")
{

    // Init return array
    $serie = array();

    // Open connection to dabase
    $db = \db_priv_pdo_start();
    

    // Format date to remove hour
    $date = strtotime(date ("Y-m-d", $dateStart));
       
    // Foreach day 
    while ($date <= $dateEnd)
    {
        // Format date to search in calendar
        $dateForCalendar = date ("Y-m-d H:i:s", $date);
        
        // Search if there is a special program this day
        $sql = "select program_index from calendar WHERE program_index != '' AND StartTime <= '${dateForCalendar}' AND EndTime >= '${dateForCalendar}' ;";
    
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
            $prg=$sth->fetch();
        } catch(\PDOException $e) {
            print_r($e->getMessage());
        }
    
        // If there is no program defined, use default
        if ($prg == NULL)
            $prgIndex = 1;
        else
            $prgIndex = $prg['program_index'];
        
        
        // Get the program number
        $programmeNumber = get_field_from_program_index ("program_idx",$prgIndex);
        
        // Retrieve data from programm
        $sql = "select * from programs WHERE number = '${programmeNumber}' AND plug_id = '${plug}' ORDER BY time_start ;";

        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            print_r($e->getMessage());
        }
        
        // If not defined, default program start at 0 and finish at 0
        // Timestamp in milliseconds Unix format
        $serie[(string)($date * 1000)] = 0;
        $serie[(string)(($date + 86399) * 1000)] = 0;
        $oldRecord = 0;

        // For each program row
        while ($row = $sth->fetch()) 
        {
        
            $timeComputeStart = substr($row['time_start'], 0 ,2) *3600
                              + substr($row['time_start'], 2 ,2) *60
                              + substr($row['time_start'], 4 ,2) ;
            
            $timeComputeStop  = substr($row['time_stop'], 0 ,2) *3600
                              + substr($row['time_stop'], 2 ,2) *60
                              + substr($row['time_stop'], 4 ,2) ;
                              
            // End previous action
            if ($row['time_start'] != 0)
                $serie[(string)(($date + $timeComputeStart - 1) * 1000)] = 0 ;
              
            // Timestamp in milliseconds Unix format
            $serie[(string)(($date + $timeComputeStart) * 1000)]     = $row['value'];
            
            // Timestamp in milliseconds Unix format
            $serie[(string)(($date + $timeComputeStop) * 1000)]      = $row['value'];
            $oldRecord = $row['value'];
            
            // Next point is by default 0
            if ($row['time_stop'] != 86399)
                $serie[(string)(($date + $timeComputeStop + 1) * 1000)]      = 0;
        
        }
    
        // Add One day to date
        $date = strtotime("+1 day", $date);
    }
        
    ksort($serie);
    
    
    $db = null;

    // Folowing code is used to have a point per minute according to actual highcart implementation
    $temp_serie = $serie;
    $serie = array();
    $OldSeconds = 0;
    $oldValue = 0;
    if ($day == "day")
        $divider = 60;
    else
        $divider = 1200;
    foreach ($temp_serie as $key => $value)
    {
        if ($OldSeconds == 0)
            $OldSeconds = ($key / 1000);
    
        // For each second between last and current, write last value
        for ($i = ceil(($OldSeconds + 1) / $divider) * $divider ; $i <= floor(($key / 1000) / $divider) * $divider ; $i = $i + $divider)
        {
            $serie[(string)((7200 + $i) * 1000)] = $oldValue;
        }
        
        if ($value == 99.9)
            $oldValue = 100;
        else
            $oldValue = $value;

        
        $OldSeconds = ($key / 1000);

    }

    return $serie ;
}
// }}}

}

?>

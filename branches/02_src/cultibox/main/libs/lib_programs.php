<?php

namespace program {

// {{{ update_db()
// ROLE update dabase
// RET none
function check_db() {

    // Define columns of the program_index table
    $program_index_col = array();
    $program_index_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $program_index_col["name"]          = array ( 'Field' => "name", 'Type' => "VARCHAR(100)");
    $program_index_col["version"]       = array ( 'Field' => "version", 'Type' => "VARCHAR(100)");
    $program_index_col["program_idx"]   = array ( 'Field' => "program_idx", 'Type' => "int(11)");
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
        
        // Build MySQL command to create table
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
        add_row_program_idx(__('CURRENT_PROG_NAME'),'1.0','1' , '00' , __('CURRENT_PROG_COMMENT'));
        
    } else {
        // Check column
        check_and_update_column_db ("program_index", $program_index_col);
    }

    $db = null;





    // Define columns of the programs table
    $program_col = array();
    $program_col["plug_id"]            = array ( 'Field' => "plug_id", 'Type' => "int(11)", 'carac' => "NOT NULL");
    $program_col["time_start"]          = array ( 'Field' => "time_start", 'Type' => "VARCHAR(6)", 'carac' => "NOT NULL");
    $program_col["time_stop"]       = array ( 'Field' => "time_stop", 'Type' => "VARCHAR(6)", 'carac' => "NOT NULL");
    $program_col["value"]   = array ( 'Field' => "value", 'Type' => "decimal(3,1)", 'carac' => "NOT NULL");
    $program_col["number"]      = array ( 'Field' => "number", 'Type' => "int(11)", 'default_value' => 1, 'carac' => "NOT NULL");
    $program_col["date_start"]  = array ( 'Field' => "date_start", 'Type' => "varchar(10)",  'default_value' => '0000-00-00', 'carac' => "NOT NULL");
    $program_col["date_end"] = array ( 'Field' => "date_end", 'Type' => "VARCHAR(10)",  'default_value' => '0000-00-00', 'carac' => "NOT NULL");
    $program_col["type"]      = array ( 'Field' => "type", 'Type' => "int(11)",  'default_value' => '0', 'carac' => "NOT NULL");

    // Check if table programs exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'programs';";

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
        // Build MySQL command to create table
        $sql = "CREATE TABLE programs ("
            ."plug_id int(11) NOT NULL,"
            ."time_start varchar(6) NOT NULL,"
            ."time_stop varchar(6) NOT NULL,"
            ."value decimal(3,1) NOT NULL,"
            ."number int(11) NOT NULL DEFAULT '1',"
            ."date_start varchar(10) NOT NULL DEFAULT '0000-00-00',"
            ."date_end varchar(10) NOT NULL DEFAULT '0000-00-00',"
            ."type int(11) NOT NULL DEFAULT '0');";

        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
    } else {
        // Check column
        check_and_update_column_db ("programs", $program_col);
    }
    $db = null;
}
// }}}

// {{{ add_row_program_idx()
// ROLE Add a row in programm_idx table
// IN $name : Name of the programm
//    $version : Version of the programm
//    $program_idx : Pointor on the the programs table
// RET id of the line added
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

    $sql = "SELECT id from program_index WHERE program_idx = $program_idx";
    try {
            $sth = $db->prepare($sql);
            $sth->execute();
            $row = $sth->fetch();
            $db=null;
            return $row['id'];
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
            $db=null;
   }
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
// IN day : Define if we want a day view or a month view
// RET array with program value
function get_plug_programm ($plug, $dateStart, $dateEnd, $day="day")
{

    // Init return array
    $serie = array();

    // Open connection to dabase
    $db = \db_priv_pdo_start();
    

    // Format date to remove hour
    date_default_timezone_set('Europe/Paris');
    $date = strtotime(date ("Y-m-d", $dateStart));
    date_default_timezone_set('UTC');
       
    // Foreach day 
    while ($date <= $dateEnd)
    {
        // Format date to search in calendar
        // Daily program event start at 02:00:00
        $dateStartForCalendar = date ("Y-m-d 03:00:00", $date);
        $dateEndForCalendar   = date ("Y-m-d 01:00:00", $date);
        
        // Search if there is a special program this day
        $sql = "select program_index from calendar WHERE program_index != '' AND StartTime <= '${dateStartForCalendar}' AND EndTime >= '${dateEndForCalendar}' ;";
    
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
        $serie[(string)(($date) * 1000)] = 0;
        $serie[(string)(($date + 86399) * 1000)] = 0;
        $oldRecord = 0;
        $oldTimeComputeStop = 0;
        $timeComputeStop = 0;

        // For each program row
        while ($row = $sth->fetch()) 
        {
        
            $timeComputeStart = substr($row['time_start'], 0 ,2) *3600
                              + substr($row['time_start'], 2 ,2) *60
                              + substr($row['time_start'], 4 ,2) ;
            
            $timeComputeStop  = substr($row['time_stop'], 0 ,2) *3600
                              + substr($row['time_stop'], 2 ,2) *60
                              + substr($row['time_stop'], 4 ,2) ;
                              
            // Caution : Timestamp for highcart are in milliseconds !

            // End previous action if not defined and not the start
            if (!array_key_exists((string)(($date + $timeComputeStart - 1) * 1000), $serie) && $row['time_start'] != 0)
            {
                $serie[(string)(($date + $timeComputeStart - 1) * 1000)] = 0;
             
                // This case is present when there is a "space" between last action and current action
                // We fill this space with zero
                for ($i = (ceil(($oldTimeComputeStop + 1) / 60) * 60) ; $i < (floor(($timeComputeStart - 1) / 60) * 60); $i = $i + 60 )
                {
                    $serie[(string)(($date + $i) * 1000)]     = 0;
                }
            }
            
            // Create start of actual Action 
            $serie[(string)(($date + $timeComputeStart) * 1000)]     = $row['value'];
            
            // If it's a day view, add points between the two actions if needed
            if ($day == "day")
            {
                for ($i = (ceil($timeComputeStart / 60) * 60) ; $i < (floor($timeComputeStop / 60) * 60); $i = $i + 60 )
                {
                    $serie[(string)(($date + $i) * 1000)]     = $row['value'];
                }
            }
            
            // Create end of actual Action 
            $serie[(string)(($date + $timeComputeStop - 1) * 1000)]      = $row['value'];
            
            
            // Next point is by default 0
            if ($timeComputeStop != 86399)
                $serie[(string)(($date + $timeComputeStop) * 1000)]      = 0;
            else
                $serie[(string)(($date + $timeComputeStop) * 1000)]      = $row['value'];
        
            // Save values for next iteration
            $oldRecord = $row['value'];
            $oldTimeComputeStop = $timeComputeStop;
        }
    
        // Every action have been registered
        // If last action doesnot end at 86399, fill with zero
        if ($day == "day")
        {
            for ($i = (ceil(($timeComputeStop + 1) / 60) * 60) ; $i < (floor(86399 / 60) * 60); $i = $i + 60 )
            {
                $serie[(string)(($date + $i) * 1000)]     = 0;
            }
        }
    
        // Add One day to date
        $date = strtotime("+1 day", $date);
    }

    ksort($serie);
    
    // Close database connexion correctly
    $db = null;

    return $serie ;
}
// }}}

// {{{ get_last_day_with_logs()
// ROLE Retrieve timestamp of last day with logs
// RET array with power value
function get_last_day_with_logs ()
{

    // Open connection to dabase
    $db = \db_priv_pdo_start();
    
    // Search if there is a special program this day
    $sql = "SELECT timestamp FROM logs ORDER BY timestamp DESC limit 1;";

    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $logs=$sth->fetch();
    } catch(\PDOException $e) {
        print_r($e->getMessage());
    }
    
    // If there is no logs defined, use default
    if ($logs == NULL)
        $retVal = "";
    else
    {
        // Format date using folowing format : date('Y')."-".date('m')."-".date('d')
        $retVal = "20" . substr($logs['timestamp'], 0 ,2) . "-"
         . substr($logs['timestamp'], 2 ,2) . "-"
         . substr($logs['timestamp'], 4 ,2) ;
    }
    
    // Close database connexion correctly
    $db = null;
    
    return $retVal;

}

// {{{ get_plug_power()
// ROLE Retrieve power of a plug
// IN plug plugNumber
// IN dateStart Date to start (write in Unix (s) format) . Time is not used
// IN dateEnd   Date to start (write in Unix (s) format) . Time is not used
// IN day : Define if we want a day view or a month view
// RET array with power value
function get_plug_power ($plug, $dateStart, $dateEnd, $day="day")
{

    // Init return array
    $serie = array();

    // Open connection to dabase
    $db = \db_priv_pdo_start();

    // Read plug power
    $sql = "SELECT PLUG_POWER FROM plugs WHERE id = {$plug};";
    
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $prg=$sth->fetch();
    } catch(\PDOException $e) {
        print_r($e->getMessage());
    }
    
    $plugPower = $prg['PLUG_POWER'];
    if ($plugPower == "" || $plugPower == null)
        $plugPower = 0;

    // Select correct divider
    if ($day == "day")
        $divider = 60;
    else
        $divider = 1200;
        
    // Get all point bewteen two dates
    $dateStartForPowerTable = date ("ymd00His", $dateStart);
    $dateEndForPowerTable = date ("ymd08His", $dateEnd);
    $sql = "SELECT timestamp , record FROM power"
            . " WHERE plug_number = {$plug}"
            . " AND timestamp >= {$dateStartForPowerTable}"
            . " AND timestamp <= {$dateEndForPowerTable}"
            . " ORDER BY timestamp ASC ;";
        
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        print_r($e->getMessage());
    }
       
    $lastTimeInS = 0;
    $oldRecord=0;
    // For each point
    while ($row = $sth->fetch()) 
    {
        // Format date
        $realDate = "20" . substr($row['timestamp'], 0 ,2) . "-"
         . substr($row['timestamp'], 2 ,2) . "-"
         . substr($row['timestamp'], 4 ,2) . " "
         . substr($row['timestamp'], 8 ,2) . ":"
         . substr($row['timestamp'], 10 ,2) . ":"
         . substr($row['timestamp'], 12 ,2);

        date_default_timezone_set('Europe/Paris');
        $realTimeInS = strtotime($realDate);
        date_default_timezone_set('UTC'); 
         
        // Don't display all point. Only if they have suffisiant diff or if the state changes:
        if (($realTimeInS >= $lastTimeInS + $divider)||($oldRecord!=$row['record']))
        {
            //If there is a change state, add a point to the same time:
            if(($oldRecord!=$row['record'])&&(!isset($serie[(string)(1000 * ($realTimeInS))-1]))) {
                $serie[(string)((1000 * ($realTimeInS))-1)] = floor($oldRecord / 9990 * $plugPower);
            }

            // WTF ! 7200
            $serie[(string)(1000 * ($realTimeInS))] = floor($row['record'] / 9990 * $plugPower);
            
            $lastTimeInS = $realTimeInS;
            $oldRecord=$row['record'];
        }
        
    }

    return $serie ;
}
// }}}

//{{{ get_curve_information()
// ROLE Retrieve curve information
// IN $curveType Define the type of curve
// IN $curveIndex Define the number of this type of curve
// RET Array with every curve informations
function get_curve_information($curveType, $curveIndex = 0) {

    // init return array
    $ret_array = array();
    
    // In function of curve information asked
    switch($curveType) {
        case 'temperature' :
        case '21':
            $ret_array['name']      = __('TEMP_SENSOR');
            $colorIndexName         = "LIST_GRAPHIC_COLOR_SENSOR_" . strtoupper(get_configuration("COLOR_TEMPERATURE_GRAPH",$main_error));
            if ($colorIndexName == "LIST_GRAPHIC_COLOR_SENSOR_")
                $colorIndexName = "LIST_GRAPHIC_COLOR_SENSOR_RED" ; 
            $ret_array['color']     = $GLOBALS[$colorIndexName][$curveIndex % 5];
            $ret_array['legend']    = __('TEMP_LEGEND');
            $ret_array['yaxis']     = 0;
            $ret_array['unit']      = "°C";
            $ret_array['curveType'] = "temperature";
            break;
        case 'humidity' :
        case '22':
            $ret_array['name']      =__('HUMI_SENSOR'); 
            $colorIndexName         = "LIST_GRAPHIC_COLOR_SENSOR_" . strtoupper(get_configuration("COLOR_HUMIDITY_GRAPH",$main_error));
            if ($colorIndexName == "LIST_GRAPHIC_COLOR_SENSOR_")
                $colorIndexName = "LIST_GRAPHIC_COLOR_SENSOR_BLUE" ;
            $ret_array['color']     = $GLOBALS[$colorIndexName][$curveIndex % 5];
            $ret_array['legend']    =__('HUMI_LEGEND');
            $ret_array['yaxis']     = 1;
            $ret_array['unit']      = "%RH";
            $ret_array['curveType'] = "humidity";
            break;  
        case 'water' :
        case '31': 
            $ret_array['name']      =__('WATER_SENSOR'); 
            $colorIndexName         = "LIST_GRAPHIC_COLOR_SENSOR_" . strtoupper(get_configuration("COLOR_WATER_GRAPH",$main_error));
            if ($colorIndexName == "LIST_GRAPHIC_COLOR_SENSOR_")
                $colorIndexName = "LIST_GRAPHIC_COLOR_SENSOR_ORANGE" ;
            $ret_array['color']     = $GLOBALS[$colorIndexName][$curveIndex % 5];
            $ret_array['legend']    =__('WATER_LEGEND');
            $ret_array['yaxis']     = 2;
            $ret_array['unit']      = "°C";
            $ret_array['curveType'] = "water";
            break;
        case 'level' :
        case '61': 
        case '71': 
            $ret_array['name']      =__('LEVEL_SENSOR'); 
            $colorIndexName         = "LIST_GRAPHIC_COLOR_SENSOR_" . strtoupper(get_configuration("COLOR_LEVEL_GRAPH",$main_error));
            if ($colorIndexName == "LIST_GRAPHIC_COLOR_SENSOR_")
                $colorIndexName = "LIST_GRAPHIC_COLOR_SENSOR_PINK" ;
            $ret_array['color']     = $GLOBALS[$colorIndexName][$curveIndex % 5];
            $ret_array['legend']    =__('LEVEL_LEGEND');
            $ret_array['yaxis']     = 3;
            $ret_array['unit']      = "cm";
            $ret_array['curveType'] = "level";
            break;
        case 'ph' :
        case '81': 
            $ret_array['name']      =__('PH_SENSOR'); 
            $colorIndexName         = "LIST_GRAPHIC_COLOR_SENSOR_" . strtoupper(get_configuration("COLOR_PH_GRAPH",$main_error));
            if ($colorIndexName == "LIST_GRAPHIC_COLOR_SENSOR_")
                $colorIndexName = "LIST_GRAPHIC_COLOR_SENSOR_BROWN" ;
            $ret_array['color']     = $GLOBALS[$colorIndexName][$curveIndex % 5] ;
            $ret_array['legend']    =__('PH_LEGEND');
            $ret_array['yaxis']     = 4;
            $ret_array['unit']      = "";
            $ret_array['curveType'] = "ph";
            break;
        case 'ec' :
        case '91': 
            $ret_array['name']      =__('EC_SENSOR'); 
            $colorIndexName         = "LIST_GRAPHIC_COLOR_SENSOR_" . strtoupper(get_configuration("COLOR_EC_GRAPH",$main_error));
            if ($colorIndexName == "LIST_GRAPHIC_COLOR_SENSOR_")
                $colorIndexName = "LIST_GRAPHIC_COLOR_SENSOR_YELLOW" ;
            $ret_array['color']     = $GLOBALS[$colorIndexName][$curveIndex % 5] ;
            $ret_array['legend']    =__('EC_LEGEND');
            $ret_array['yaxis']     = 5;
            $ret_array['unit']      = "mS";
            $ret_array['curveType'] = "ec";
            break;
        case 'od' :
        case ':1': 
            $ret_array['name']      =__('OD_SENSOR'); 
            $colorIndexName         = "LIST_GRAPHIC_COLOR_SENSOR_" . strtoupper(get_configuration("COLOR_OD_GRAPH",$main_error));
            if ($colorIndexName == "LIST_GRAPHIC_COLOR_SENSOR_")
                $colorIndexName = "LIST_GRAPHIC_COLOR_SENSOR_RED" ;
            $ret_array['color']     = $GLOBALS[$colorIndexName][$curveIndex % 5] ;
            $ret_array['legend']    =__('OD_LEGEND');
            $ret_array['yaxis']     = 6;
            $ret_array['unit']      = "mg/L";
            $ret_array['curveType'] = "od";
            break;
        case 'orp' :
        case ';1': 
            $ret_array['name']      =__('ORP_SENSOR'); 
            $colorIndexName         = "LIST_GRAPHIC_COLOR_SENSOR_" . strtoupper(get_configuration("COLOR_ORP_GRAPH",$main_error));
            if ($colorIndexName == "LIST_GRAPHIC_COLOR_SENSOR_")
                $colorIndexName = "LIST_GRAPHIC_COLOR_SENSOR_BLUE" ;
            $ret_array['color']     = $GLOBALS[$colorIndexName][$curveIndex % 5] ;
            $ret_array['legend']    =__('ORP_LEGEND');
            $ret_array['yaxis']     = 7;
            $ret_array['unit']      = "mV";
            $ret_array['curveType'] = "orp";
            break;
        case 'power': 
            $ret_array['name']      = __('POWER'); 
            $ret_array['color']     = $GLOBALS["LIST_GRAPHIC_COLOR_POWER"][$curveIndex % 10] ;
            $ret_array['legend']    = __('POWER_LEGEND');
            $ret_array['yaxis']     = 8;
            $ret_array['unit']      = "W";
            $ret_array['curveType'] = "power";
            break;   
        case 'program': 
            $ret_array['name']      = __('PROGRAM_LEGEND'); 
            $ret_array['color']     = $GLOBALS["LIST_GRAPHIC_COLOR_PROGRAM"][$curveIndex % 10] ;
            $ret_array['legend']    = __('PROGRAM_LEGEND');
            $ret_array['yaxis']     = 9;
            $ret_array['unit']      = "";
            $ret_array['curveType'] = "program";
            break;   
    }
    
    // Common parameters for each curv
    switch($ret_array['color']) {
        case 'blue': 
            $ret_array['colorgrid'] = $GLOBALS['GRAPHIC_COLOR_GRID_BLUE'];
            break;
        case 'red': 
            $ret_array['colorgrid'] = $GLOBALS['GRAPHIC_COLOR_GRID_RED'];
            break;
        case 'green': 
            $ret_array['colorgrid'] = $GLOBALS['GRAPHIC_COLOR_GRID_GREEN'];
            break;
        case 'black': 
            $ret_array['colorgrid'] = $GLOBALS['GRAPHIC_COLOR_GRID_BLACK'];
            break;
        case 'purple': 
            $ret_array['colorgrid'] = $GLOBALS['GRAPHIC_COLOR_GRID_PURPLE'];
            break;
        case 'orange': 
            $ret_array['colorgrid'] = $GLOBALS['GRAPHIC_COLOR_GRID_ORANGE'];
            break;
        case 'pink': 
            $ret_array['colorgrid'] = $GLOBALS['GRAPHIC_COLOR_GRID_PINK'];
            break;
        case 'brown': 
            $ret_array['colorgrid'] = $GLOBALS['GRAPHIC_COLOR_GRID_BROWN'];
            break;
        case 'yellow': 
            $ret_array['colorgrid'] = $GLOBALS['GRAPHIC_COLOR_GRID_YELLOW'];
            break;
        default: 
            $ret_array['colorgrid'] = "";
    }
    
    return $ret_array ;
}
//}}}

// {{{ export_program()
// ROLE export a program into a text file
// IN $id          id of the program
//    $out         error or warning message
// RET none
function export_program($id,$program_index,$file="") {
       $sql = "SELECT * FROM programs WHERE plug_id = {$id} AND number = {$program_index}";
       
       $db=\db_priv_pdo_start();
       try {
           $sth=$db->prepare("$sql");
           $sth->execute();
           $res=$sth->fetchAll(\PDO::FETCH_ASSOC);
       } catch(\PDOException $e) {
           $ret=$e->getMessage();
       }
       $db=null;
       if(strcmp("$file","")==0) {
           $file="../../../tmp/export/program_plug${id}.csv";
        }

       if($f=fopen("$file","w")) {
            fputs($f,"time_start	time_stop	value	type\r\n");
            if(count($res)>0) {
               foreach($res as $record) {
                  fputs($f,$record['time_start']."	".$record['time_stop']."	".$record['value']."	".$record['type']."\r\n");
               }
            } else {
                    fputs($f,"000000	235959	0	0\r\n");
            }
      } 
      fclose($f);
}
// }}}

}

?>

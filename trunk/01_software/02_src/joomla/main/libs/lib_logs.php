<?php

namespace logs {

// {{{ export_table_csv()
// ROLE export a program into a text file
// IN $name       name of the table to be exported
//    $out         error or warning message
// RET none
function export_table_csv($name="",&$out) {
    if($name == "")
        return 0;

    $file="tmp/$name.csv";

    if(is_file($file)) {
        unlink($file);
    }

    $os=php_uname('s');
    switch($os) {
        case 'Linux':
            exec("../../bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -B -h 127.0.0.1 --port=3891 cultibox -e 'SELECT * FROM `${name}`' > $file");
            break;
        case 'Mac':
        case 'Darwin':
            exec("../../bin/mysql --defaults-extra-file=/Applications/cultibox/xamppfiles/etc/my-extra.cnf -B -h 127.0.0.1 --port=3891 cultibox -e 'SELECT * FROM `${name}`' > $file");
            break;
        case 'Windows NT':
            exec("..\..\mysql\bin\mysql.exe --defaults-extra-file=\"C:\cultibox\\xampp\mysql\bin\my-extra.cnf\" -B -h 127.0.0.1 --port=3891 cultibox -e \"SELECT * FROM `${name}`\" > $file");
            break;
    }
}
// }}}



// {{{ check_export_table_csv()
// ROLE check that a table is empty or not
// IN $name       name of the table to be exported
//    $out         error or warning message
// RET false is table is empty, true else
function check_export_table_csv($name="",&$out) {
    if($name == "")
        return false;

    if($name == "logs") {
        $sql = "SELECT timestamp FROM {$name} WHERE fake_log LIKE 'False' LIMIT 1;";
    } else if($name == "power") {
        $sql = "SELECT timestamp FROM {$name} LIMIT 1;";
    } else {
        $sql = "SELECT * FROM {$name} LIMIT 1;";
    }

    $db = \db_priv_pdo_start();
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res=$sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if(count($res)>0) {
        if($res['timestamp'] != "")
            return true;
    }
    
    return false;
}
// }}}

// {{{ get_sensor_db_type()
// ROLE get list of sensor's type and definition from database and config file
// IN none
// RET array containing sensors type
// Type => nom abrégé => Facteur multiplicatif => unité
// '0' => 'none' => N/A => pas d'unité
// '2' => 'temp_humi' => 100 => °C ou % 
// '3' => 'water_temp' => 100 => °C
// '5' => 'wifi' => N/A => N/A
// '6' => 'water_level' => 100 => cm
// '7' => 'water_level' => 100 => cm
// '8' => 'ph' => 100 => Pas d'unité
// '9' => 'ec' => 1 => µs/cm
// ':' => 'od' => 100 => mg/l
// ';' => 'orp' => 1 => mV 

function get_sensor_db_type($sensor = "") {

    if ($sensor == "")
    {
        $sql = "SELECT * FROM sensors ORDER BY id ASC;";
    }
    else
    {
        $sql = "SELECT * FROM sensors WHERE id = '{$sensor}';";
    }

    $db = \db_priv_pdo_start();
    $res="";
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res=$sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    $nb_sens=1;
    foreach($res as $sens) {
        switch($sens['type']) {
            case '0':
            case '4':
            case '5': 
                $sensors[]=array(
                    "id" => $sens['id'],
                    "type" => 0,
                    "sensor_nb" => 0,
                    "ratio" => 0,
                    "sensorName" => "",
                    "translation" => "",
                    "unity" => ""
                ); 
                $nb_sens=$nb_sens+1;
                break;

            case '2': 
                $sensors[]=array(
                    "id" => $sens['id'],
                    "type" => $sens['type'],
                    "sensor_nb" => $nb_sens,
                    "ratio" => 100,
                    "sensorName" => "temperature",
                    "translation" => "TEMP_SENSOR",
                    "unity" => "°C/%"
                );
                $sensors[]=array(
                    "id" => $sens['id'],
                    "type" => $sens['type'],
                    "sensor_nb" => $nb_sens,
                    "ratio" => 100,
                    "sensorName" => "humidity",
                    "translation" => "HUMI_SENSOR",
                    "unity" => "°C/%"
                );
                $nb_sens=$nb_sens+1;
                break;

            case '3': 
                $sensors[]=array(
                    "id" => $sens['id'],
                    "type" => $sens['type'],
                    "sensor_nb" => $nb_sens,
                    "ratio" => 100,
                    "sensorName" => "water",
                    "translation" => "WATER_SENSOR",
                    "unity" => "°C"
                );
                $nb_sens=$nb_sens+1;
                break;

            case '6': 
            case '7': 
                $sensors[]=array(
                    "id" => $sens['id'],
                    "type" => $sens['type'],
                    "sensor_nb" => $nb_sens,
                    "ratio" => 100,
                    "sensorName" => "level",
                    "translation" => "LEVEL_SENSOR",
                    "unity" => "cm"
                );
                $nb_sens=$nb_sens+1;
                break;

            case '8': 
                $sensors[]=array(
                    "id" => $sens['id'],
                    "type" => $sens['type'],
                    "sensor_nb" => $nb_sens,
                    "ratio" => 100,
                    "sensorName" => "ph",
                    "translation" => "PH_SENSOR",
                    "unity" => " "
                );
                $nb_sens=$nb_sens+1;
                break;

            case '9': 
                $sensors[]=array(
                    "id" => $sens['id'],
                    "type" => $sens['type'],
                    "sensor_nb" => $nb_sens,
                    "ratio" => 1,
                    "sensorName" => "ec",
                    "translation" => "EC_SENSOR",
                    "unity" => "µs/cm"
                );
                $nb_sens=$nb_sens+1;
                break;

            case ':': 
                $sensors[]=array(
                    "id" => $sens['id'],
                    "type" => $sens['type'],
                    "sensor_nb" => $nb_sens,
                    "ratio" => 100,
                    "sensorName" => "od",
                    "translation" => "OD_SENSOR",
                    "unity" => "mg/l"
                );
                $nb_sens=$nb_sens+1;
                break;

            case ';': 
                $sensors[]=array(
                    "id" => $sens['id'],
                    "type" => $sens['type'],
                    "sensor_nb" => $nb_sens,
                    "ratio" => 1,
                    "sensorName" => "orp",
                    "translation" => "ORP_SENSOR",
                    "unity" => "mV"
                );
                $nb_sens=$nb_sens+1;
                break;

            default:  
                $sensors[]=array(
                    "id" => $sens['id'],
                    "type" => $sens['type'],
                    "sensor_nb" => 0,
                    "ratio" => 0,
                    "sensorName" => "",
                    "translation" => "",
                    "unity" => ""
                );
                $nb_sens=$nb_sens+1;
                break;
       }
    }
    return $sensors;
}
/// }}}

// {{{ get_sensor_log()
// ROLE Retrieve logs of a sensor
// IN sensor Sensor number
// IN dateStart Date to start (write in Unix (s) format) . Time is not used
// IN dateEnd   Date to start (write in Unix (s) format) . Time is not used
// IN day : Define if we want a day view or a month view
// IN ratio : define the diviser for each record
// RET array with logs value
function get_sensor_log ($sensor, $dateStart, $dateEnd, $day="day",$ratio=100) {

    // Init return array
    $serie = array();
    $serie[0] = array();
    $serie[1] = array();

    // Open connection to dabase
    $db = \db_priv_pdo_start();

    // Select correct divider
    if ($day == "day")
        $divider = 60;
    else
        $divider = 1200;
        
    // Get all point bewteen two dates
    $dateStartForLogsTable  = date ("ymd00His", $dateStart);
    $dateEndForLogsTable    = date ("ymd00His", $dateEnd);
    $sql = "SELECT timestamp , time_catch, date_catch, record1 , record2 FROM logs"
            . " WHERE sensor_nb = {$sensor}"
            . " AND INSERT(timestamp,7,2,'00') >= {$dateStartForLogsTable}"
            . " AND INSERT(timestamp,7,2,'00') <= {$dateEndForLogsTable}"
            . " ORDER BY date_catch,time_catch ASC;";

    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        print_r($e->getMessage());
    }

    $lastTimeInS = 0;
    // For each point
    while ($row = $sth->fetch()) 
    {
        // Format date
        $realDate = $row['date_catch'] . " " . wordwrap($row['time_catch'],"2",":");
        $realTimeInS = strtotime($realDate);

        // Don't display all point. Only if they have suffisiant diff
        $first=false;
        if ($realTimeInS >= ($lastTimeInS + $divider))
        {

            /*
            if(!$first) {
                $first=true;
                $first_date=strtotime($row['date_catch'] . " " . "00:00:01");
                $serie[0][(string)(1000 * ($first_date))] = "null"; 

                if ($row['record2'] != "" && $row['record2'] != 0) {
                    $serie[1][(string)(1000 * ($first_date))] = "null";
                } 
            }
            */

            // WTF ! 7200
            $serie[0][(string)(1000 * ($realTimeInS))] = $row['record1'] / $ratio;
            
            if ($row['record2'] != "" && $row['record2'] != null)
                $serie[1][(string)(1000 * ($realTimeInS))] = $row['record2'] / $ratio;
            
            $lastTimeInS = $realTimeInS;
        }
    }
    return $serie ;
}
// }}}

// {{{ are_fake_logs()
// ROLE Retrieve if logs are fake
// IN sensor Sensor number
// IN dateStart Date to start (write in Unix (s) format) . Time is not used
// IN dateEnd   Date to start (write in Unix (s) format) . Time is not used
// IN day : Define if we want a day view or a month view
// RET 1 if fake , 0 if not fake
function are_fake_logs ($sensor, $dateStart, $dateEnd, $day="day")
{

    // Open connection to dabase
    $db = \db_priv_pdo_start();

        
    // Get all point bewteen two dates
    $dateStartForLogsTable  = date ("ymd00His", $dateStart);
    $dateEndForLogsTable    = date ("ymd00His", $dateEnd);
    $sql = "SELECT timestamp , record1 , record2 FROM logs"
            . " WHERE sensor_nb = {$sensor}"
            . " AND INSERT(timestamp,7,2,'00') >= {$dateStartForLogsTable}"
            . " AND INSERT(timestamp,7,2,'00') < {$dateEndForLogsTable}"
            . " AND fake_log != 'False' limit 1;";



    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $row = $sth->fetch();
    } catch(\PDOException $e) {
        print_r($e->getMessage());
    }

    if ($row != null) 
    {
        return "1";
    } 
    else
    {
        return "0";
    }
}
// }}}


// {{{ reset_log()
// IN $table    table to be deleted: logs, power...
//    $start    delete logs between two specific dates, between $start and $end
//    $end      
// RET  0 is an error occured, 1 else
function reset_log($table="",$start="",$end="") {
    if($table == "") 
        return 0;
    
    $error=1;

    if($start == "" || $end == "") {
        $sql = "TRUNCATE TABLE {$table};";
    } else {
        $sql = "DELETE FROM {$table} WHERE date_catch BETWEEN '{$start}' AND '{$end}';";
    }
    
    $db=\db_priv_pdo_start();
    try {
        $db->exec($sql);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
          $error=0;
    }
    return $error;
}
// }}}



// {{{ reset_fake_log()
// IN 
// RET  0 is an error occured, 1 else
function reset_fake_log() {
    $sql = "DELETE FROM logs WHERE fake_log LIKE 'True';";

    $db=\db_priv_pdo_start();
    try {
        $db->exec($sql);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
          return $ret;
    }
    return 1;
}
// }}}


// {{{ save_log()
// IN  $file:   file to be saved
// IN  $month:  month of the file
// IN  $day:    day of the file
// IN  $type:   type of the file: logs or power
// RET  0 is an error occured, 1 else
function save_log($file="",$month=0,$day=0,$type="logs") {
    if(strcmp("$file","")==0) return 0;
    if($month==0) return 0;
    if($day==0) return 0;

    $path="";
    if(is_dir("tmp")) {
        $path="tmp";
    } else if(is_dir("../tmp")) {
        $path="../tmp";
    } else if(is_dir("../../tmp")) {
        $path="../../tmp";
    } else if(is_dir("../../../tmp")) {
        $path="../../../tmp";
    }   

    if(strcmp("$path","")==0) return 0;

    if(!is_dir("$path/saved_logs")) {
            if(!@mkdir("$path/saved_logs")) return 0;
    }

    if(!is_file("$file")) return 0;

    $name="$path/saved_logs/".date("Y")."_".$month."_".$day."_".$type."_loads_".date("Y")."_".date("m")."_".date("d")."_".date('His');
    if(!copy("$file","$name")) return 0;
    return 1;
}
// }}}



}

?>
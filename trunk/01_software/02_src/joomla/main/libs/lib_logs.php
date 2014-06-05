<?php

namespace logs {

// {{{ export_table_csv()
// ROLE export a program into a text file
// IN $name       name of the table to be exported
//    $out         error or warning message
// RET none
function export_table_csv($name="",&$out) {
       if(strcmp("$name","")==0) return 0;

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
       if(strcmp("$name","")==0) return false;

        if(strcmp("$name","logs")==0) {
            $sql = <<<EOF
SELECT `timestamp` FROM `{$name}` WHERE `fake_log` LIKE "False" LIMIT 1;
EOF;
       } else if(strcmp("$name","power")==0) {
            $sql = <<<EOF
SELECT `timestamp` FROM `{$name}` LIMIT 1;
EOF;
       } else {
            $sql = <<<EOF
SELECT * FROM `{$name}` LIMIT 1;
EOF;
}
        $db = \db_priv_pdo_start();
        try {
            $sth=$db->prepare("$sql");
            $sth->execute();
            $res=$sth->fetch(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            $ret=$e->getMessage();
        }
        $db=null;

       if(count($res)>0) {
             if(strcmp($res['timestamp'],"")!=0) {
                return true;
             }
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

}

?>

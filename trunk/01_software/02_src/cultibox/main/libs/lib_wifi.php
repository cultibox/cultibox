<?php

namespace wifi {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {

    // Define columns of the synoptic table
    $synoptic_col = array();
    $synoptic_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $synoptic_col["element"]       = array ( 'Field' => "element", 'Type' => "VARCHAR(10)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["scale"]         = array ( 'Field' => "scale", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["x"]             = array ( 'Field' => "x", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["y"]             = array ( 'Field' => "y", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["plugIndex"]     = array ( 'Field' => "plugIndex", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["sensorIndex"]   = array ( 'Field' => "sensorIndex", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["rotation"]      = array ( 'Field' => "rotation", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["image"]         = array ( 'Field' => "image", 'Type' => "VARCHAR(50)", "default_value" => "", 'carac' => "NOT NULL");
    

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM cultibox LIKE 'synoptic';";

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
        $sql = "CREATE TABLE synoptic ("
            ."id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."element varchar(10) NOT NULL DEFAULT '',"
            ."scale int(11) NOT NULL DEFAULT '100',"
            ."x int(11) NOT NULL DEFAULT '0',"
            ."y int(11) NOT NULL DEFAULT '0',"
            ."plugIndex int(11) NOT NULL DEFAULT '0',"
            ."sensorIndex int(11) NOT NULL DEFAULT '0',"
            ."rotation int(11) NOT NULL DEFAULT '0',"
            ."image varchar(50) NOT NULL DEFAULT '' );";

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
        check_and_update_column_db ("synoptic", $synoptic_col);
    }
    
    $db = null;
}


// {{{ getSensorSynoptic()
// ROLE Retrieve sensor information in db
// IN $number : Number of sensor
// RET id of the line added
function getSensorSynoptic($number) {

        // Check if table configuration exists
    $sql = "SELECT * FROM synoptic WHERE sensorIndex = '${number}' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    return $res;
}
// }}}

// {{{ addElementInSynoptic()
// ROLE Add an element in db
// IN $element : Name of the element
// IN $plugIndex : Plug index
// IN $sensorIndex : Sensor index
// IN $image : Image name
// RET 0
function addElementInSynoptic($element, $plugIndex, $sensorIndex, $image, $x=0, $y="") {
    
    if ($plugIndex != 0 && $x == 0) 
    {
        $x = 700;
    } elseif ($x == 0)
    {
        $x = 600;
    }
    
    if ($y == "") 
    {
        $y = ($plugIndex + $sensorIndex + 3) * 100;
    }

    // Check if table configuration exists
    $sql = "INSERT INTO synoptic (element, plugIndex, sensorIndex, image, x, y) VALUES('${element}' , '${plugIndex}' , '${sensorIndex}' , '${image}' , '${x}' , '${y}') ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    return 0;
}
// }}}


function getSensorOfSynoptic () {

    $ret_array = array();

    // Read sensors in db
    $sensorList = \sensors\getDB();
    
    // foreach sensor, get type and position
    foreach ($sensorList as $index => $sensor){

        // Read parameters in db and add it to return array
        $sensorParameters = getSensorSynoptic($sensor["id"]);

        // If empty create them 
        if (empty($sensorParameters) && $sensor["type"] != "0") {

            addElementInSynoptic("sensor", 0, $sensor["id"], "capteur.png");
            
            $ret_array[] = getSensorSynoptic($sensor["id"]);
            
        } elseif ($sensor["type"] != "0") {
            $ret_array[] = $sensorParameters;
        }

    }
    
    
    return $ret_array;
}


// {{{ add_row_program_idx()
// ROLE Add a row in programm_idx table
// IN $name : Name of the programm
//    $version : Version of the programm
//    $program_idx : Pointor on the the programs table
// RET id of the line added
function getSensorValue($number) {

    $ret = "";

    try {
        $ret = exec('tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\cultiPi\cultiPiviewRepere.tcl" serverAcqSensor "::sensor(1,value)"');
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }

    $arr = explode (" ", $ret);
    
    return $arr[3];
}
// }}}

// {{{ updatePosition()
// ROLE Update position of an element
// IN $name :
// RET id of the line added
function updatePosition($elem,$x,$y) {

    // Update position conf
    $sql = "UPDATE synoptic SET x='${x}' ,y='${y}' WHERE id='${elem}' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    return 0;
    
    return $arr[3];
}
// }}}


}

?>

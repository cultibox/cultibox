<?php

namespace cultipi {

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
    $synoptic_col["z"]             = array ( 'Field' => "z", 'Type' => "int(11)", "default_value" => 1, 'carac' => "NOT NULL");
    $synoptic_col["indexElem"]     = array ( 'Field' => "indexElem", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
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
            ."z int(11) NOT NULL DEFAULT '100',"
            ."indexElem int(11) NOT NULL DEFAULT '0',"
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


// {{{ getSynopticDBElemByname()
// ROLE Retrieve sensor information in db with this name
// IN $element : Type of element (sensor, plug, other)
// IN $indexElem : Index of this element
// RET Every information about this element in DB
function getSynopticDBElemByname ($element, $indexElem) {


    // Check if table configuration exists
    $sql = "SELECT * FROM synoptic WHERE element = '${element}' AND indexElem = '${indexElem}' ;";
    
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

// {{{ getSynopticDBElemByID()
// ROLE Retrieve sensor information in db with this ID
// IN $id : id of element 
// RET Every information about this element in DB
function getSynopticDBElemByID ($id) {


    // Check if table configuration exists
    $sql = "SELECT * FROM synoptic WHERE id = '${id}' ;";
    
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
function addElementInSynoptic($element, $indexElem, $image, $x=0, $y="") {
    
    if ($x == 0 || $x == "") 
    {
        switch ($element) {
            case "plug":
                $x = 300;
                break;
            case "sensor":
                $x = 1100;
                break;
            default:
                $x = 500;
                break;
    }
    }
     
    if ($y == 0 || $y == "") 
    {
        $y = ($indexElem + 2) * 100;
    }

    // Check if table configuration exists
    $sql = "INSERT INTO synoptic (element, indexElem, image, x, y) VALUES('${element}' , '${indexElem}' , '${image}' , '${x}' , '${y}') ;";
    
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
        $sensorParameters = getSynopticDBElemByname("sensor",$sensor["id"]);

        // If empty create them 
        if (empty($sensorParameters) && $sensor["type"] != "0") {

            addElementInSynoptic("sensor", $sensor["id"], "capteur.png");
            
            $ret_array[] = getSynopticDBElemByname("sensor",$sensor["id"]);
            
        }
        elseif ($sensor["type"] != "0") 
        {
            $ret_array[] = $sensorParameters;
        }

    }

    return $ret_array;
}

function getPlugOfSynoptic () {

    $ret_array = array();

    // Read nb plug in database
    $plugNB = \configuration\getConfElem("NB_PLUGS");
    
    // foreach sensor, get type and position
    for ($i = 1; $i <= $plugNB["NB_PLUGS"] && $i <= 100; $i++) {

        // Read parameters in db and add it to return array
        $sensorParameters = getSynopticDBElemByname("plug",$i);

        // If empty create them 
        if (empty($sensorParameters)) {

            addElementInSynoptic("plug", $i, "prise_100W.png", "", "", 65);
            
            $ret_array[] = getSynopticDBElemByname("plug",$i);
            
        } 
        else
        { 
            $ret_array[] = $sensorParameters;
        }

    }

    return $ret_array;
}

// {{{ getSensorSynoptic()
// ROLE Retrieve sensor information in db
// IN $indexElem : Number of sensor
// RET id of the line added
function getOtherOfSynoptic () {


    // Check if table configuration exists
    $sql = "SELECT * FROM synoptic WHERE element != 'sensor' AND element != 'plug' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    return $res;
}
// }}}

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

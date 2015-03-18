<?php

namespace cultipi {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {

    // Define columns of the synoptic table
    $synoptic_col = array();
    $synoptic_col["id"]            = array ( 'Field' => "id", 'Type' => "int(11)", 'carac' => "NOT NULL AUTO_INCREMENT");
    $synoptic_col["element"]       = array ( 'Field' => "element", 'Type' => "VARCHAR(10)", "default_value" => "other", 'carac' => "NOT NULL");
    $synoptic_col["scale"]         = array ( 'Field' => "scale", 'Type' => "int(11)", "default_value" => 100, 'carac' => "NOT NULL");
    $synoptic_col["x"]             = array ( 'Field' => "x", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["y"]             = array ( 'Field' => "y", 'Type' => "int(11)", "default_value" => 0, 'carac' => "NOT NULL");
    $synoptic_col["z"]             = array ( 'Field' => "z", 'Type' => "int(11)", "default_value" => 100, 'carac' => "NOT NULL");
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
            ."element varchar(10) NOT NULL DEFAULT 'other',"
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
        
        // Add one tente and one CBX
        addElementInSynoptic("other", 1, "cultipi.png", 850, 450, 2, 74);
        addElementInSynoptic("other", 2, "tente_1_espace.png", 600, 350, 1, 250);
        
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
function addElementInSynoptic($element, $indexElem, $image, $x=0, $y="", $z=100, $scale = 100) {
    
    if ($x == 0 || $x == "") 
    {
        switch ($element) {
            case "plug":
                if((isset($_COOKIE['CONTENT_LEFT']))&&(!empty($_COOKIE['CONTENT_LEFT']))) {
                    $x=(int)($_COOKIE['CONTENT_LEFT']+$_COOKIE['CONTENT_LEFT']*25/100);
                } else {
                    $x = 300;
                }
                break;
            case "sensor":
                if((isset($_COOKIE['CONTENT_RIGHT']))&&(!empty($_COOKIE['CONTENT_RIGHT']))) {
                    $x=(int)($_COOKIE['CONTENT_RIGHT']-$_COOKIE['CONTENT_RIGHT']*10/100);
                } else {
                    $x = 1100;
                }
                break;
            case "other":
                if((isset($_COOKIE['CONTENT_RIGHT']))&&(!empty($_COOKIE['CONTENT_RIGHT'])) && isset($_COOKIE['CONTENT_LEFT']) && !empty($_COOKIE['CONTENT_LEFT'])) {
                    $x=(int)(( $_COOKIE['CONTENT_RIGHT'] - $_COOKIE['CONTENT_LEFT']) / 2 + $_COOKIE['CONTENT_LEFT']);
                } else {
                    $x = 700;
                }
                break;
            default:
                $x = 500;
                break;
        }
    }
     
    if ($y == 0 || $y == "") 
    {
        $step = ($indexElem + 1 ) * 150;
        if((isset($_COOKIE['CONTENT_TOP']))&&(!empty($_COOKIE['CONTENT_TOP']))) {
            $y=(int)($_COOKIE['CONTENT_TOP']+$step);
        } else {
            $y=$step;
        }
    }

    // Check if table configuration exists
    $sql = "INSERT INTO synoptic (element, indexElem, image, x, y, z, scale) VALUES('${element}' , '${indexElem}' , '${image}' , '${x}' , '${y}' , '${z}' , '${scale}') ;";
    
    $db = \db_priv_pdo_start("root");
    
    $res = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    
    // Retrieve ID of this element
    $sql = "SELECT * FROM synoptic WHERE element = '${element}' AND indexElem = '${indexElem}' AND image =  '${image}' AND x = '${x}' AND y = '${y}';";
    
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

// {{{ deleteElementInSynoptic()
// ROLE Remove an element
// IN $id : Name of the element
// RET 0
function deleteElementInSynoptic($id) {
    

    // Check if table configuration exists
    $sql = "DELETE FROM synoptic WHERE id='${id}';"; 
    
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

            switch ($sensor["type"])
            {
                case '2' :
                    $image = "T_RH_sensor.png";
                    break;
                case '3': 
                    $image = "water_T_sensor.png";
                    break;
                case '6': 
                case '7': 
                    $image = "level_sensor.png";
                    break;
                case '8': 
                    $image = "pH-sensor.png";
                    break;
                case '9': 
                    $image = "conductivity-sensor.png";
                    break;
                case '10': 
                    $image = "dissolved-oxygen-sensor.png";
                    break;
                case '11': 
                    $image = "ORP-sensor.png";
                    break;
                default :
                    $image = "T_RH_sensor.png";
                    break;
            }
        
            addElementInSynoptic("sensor", $sensor["id"], $image);
            
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

    // Read plug parameters
    $plugParam = \plugs\getDB();
        
    // foreach sensor, get type and position
    for ($i = 1; $i <= $plugNB["NB_PLUGS"] && $i <= 100; $i++) {

    
        // Read parameters in db and add it to return array
        $sensorParameters = getSynopticDBElemByname("plug",$i);

        // If empty create them 
        if (empty($sensorParameters)) {
            switch ($plugParam[$i - 1]["PLUG_TYPE"]) 
            {
                case "lamp" :
                    $image = "lampe_OFF.png";
                    break;
                case "extractor" :
                case "intractor" :
                case "ventilator" :
                    $image = "lampe_OFF.png";
                    break;
                case "pump" :
                case "pumpfiling" :
                case "pumpempting" :
                    $image = "pompe_OFF.png";
                    break;
                default :
                    if ($plugParam[$i - 1]["PLUG_POWER_MAX"] ==  "1000") 
                    {
                        $image = "1000W_OFF.png";
                    }
                    else
                    {
                        $image = "3500W_OFF.png";
                    }
                    break;
            }
        
            addElementInSynoptic("plug", $i, $image);
            
            $sensorParameters = getSynopticDBElemByname("plug",$i);
            $sensorParameters["PLUG_NAME"] = $plugParam[$i - 1]["PLUG_NAME"];
            
            $ret_array[] = $sensorParameters;
            
        } 
        else
        { 
            $sensorParameters["PLUG_NAME"] = $plugParam[$i - 1]["PLUG_NAME"];
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

// {{{ getAllSensorLiveValue()
// ROLE Retrieve value of each sensor
// RET plug value
function getAllSensorLiveValue() {

    $return_array = array();
    $return_array["error"] = "";
    
    $commandLine = 'tclsh "/opt/cultipi/cultiPi/get.tcl" serverAcqSensor ';
    for ($i = 1; $i <= 6; $i++) {
        $commandLine = $commandLine . ' "::sensor(' . $i . ',value)"';
    }
    
    $ret = "";
    try {
        $ret = exec($commandLine);
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }

    $arr = explode ("\t", $ret);

    for ($i = 0; $i <= 5; $i++) {
        if (array_key_exists($i, $arr)) {
            if ($arr[$i] != "") {
                $return_array[$i + 1] = $arr[$i];
            } else {
                $return_array[$i + 1] = "DEFCOM";
            }
        } else {
            $return_array[$i + 1] = "DEFCOM";
        }
    }

    return $return_array;
}
// }}}

// {{{ getSensorLiveValue()
// ROLE Retrieve value of a sensor
// IN $number : Index of a sensor
// RET Sensor value
function getSensorLiveValue($number) {

    $ret = "";

    $return_array = array();
    $return_array["val1"] = "DEFCOM"; 
    $return_array["val2"] = "DEFCOM";
    $return_array["error"] = "";
    
    try {
        $ret = exec('tclsh "/opt/cultipi/cultiPi/get.tcl" serverAcqSensor "::sensor(' . $number . ',value)"');
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }

    $arr = explode (" ", $ret);
    
    if (array_key_exists(0, $arr)) {
        $return_array["val1"] = $arr[0];
    }
    if (array_key_exists(1, $arr)) {
        $return_array["val2"] = $arr[1];
    }
    
    return $return_array;
}
// }}}

// {{{ getPlugLiveValue()
// ROLE Retrieve value of each plug
// RET plug value
function getAllPlugLiveValue() {

    $return_array = array();
    $return_array["error"] = "";
    
    $commandLine = 'tclsh "/opt/cultipi/cultiPi/get.tcl" serverPlugUpdate ';
    for ($i = 1; $i <= 16; $i++) {
        $commandLine = $commandLine . ' "::plug(' . $i . ',value)"';
    }
    
    $ret = "";
    try {
        $ret = exec($commandLine);
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }

    $arr = explode ("\t", $ret);
    
    for ($i = 0; $i <= 15; $i++) {
        if (array_key_exists($i, $arr)) {
            if ($arr[$i] != "") {
                $return_array[$i + 1] = $arr[$i];
            } else {
                $return_array[$i + 1] = "DEFCOM";
            }
        } else {
            $return_array[$i + 1] = "DEFCOM";
        }
    }

    return $return_array;
}
// }}}

// {{{ getPlugLiveValue()
// ROLE Retrieve value of a plug
// IN $number : Index of a plug
// RET plug value
function getPlugLiveValue($number) {

    $ret = "";

    $return_array = array();
    $return_array["val1"] = "DEFCOM"; 
    $return_array["error"] = "";
    
    try {
        $ret = exec('tclsh "/opt/cultipi/cultiPi/get.tcl" serverPlugUpdate "::plug(' . $number . ',value)"');
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }

    $arr = explode ("\t", $ret);
    
    if (array_key_exists(0, $arr)) {
        $return_array["val1"] = $arr[0];
    }
    
    return $return_array;
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

// {{{ updatePosition()
// ROLE Update position of an element
// IN $name :
// RET id of the line added
function updateZScaleImageRotation($elem,$z,$scale,$image,$rotation) {

    // Update position conf
    $sql = "UPDATE synoptic SET z='${z}' ,scale='${scale}' ,image='${image}' ,rotation='${rotation}' WHERE id='${elem}' ;";
    
    $db = \db_priv_pdo_start("root");
    
    $ret = array();
    
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    
    return $ret;
}
// }}}

// {{{ forcePlug()
// ROLE Force a plug
// IN $number : Index of plug
// IN $value : Value to force
// IN $time : Time to force
// RET NA
function forcePlug($number,$value,$time) {

    $ret = "";

    $return_array = array();
    $return_array["val1"] = "DEFCOM"; 
    $return_array["val2"] = "DEFCOM";
    $return_array["error"] = "";
    
    try {
        $ret = exec('tclsh "/opt/cultipi/cultiPi/set.tcl" serverPlugUpdate ' . $number . ' ' . $value . ' ' . $time);
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $return_array["error"] = $e->getMessage();
    }

    return "";
}
// }}}

}

?>

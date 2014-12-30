<?php 

    // Include libraries
    if (file_exists('../../libs/db_get_common.php') === TRUE)
    {
        // Script call by Ajax
        require_once('../../libs/config.php');
        require_once('../../libs/db_get_common.php');
        require_once('../../libs/db_set_common.php');
        require_once('../../libs/utilfunc.php');
        require_once('../../libs/utilfunc_sd_card.php');
        require_once('../../libs/debug.php');
    }
    
    $action = "";
    if((isset($_GET['action'])) && (!empty($_GET['action']))) {
        $action=$_GET['action'];
    }
    if((isset($_POST['action'])) && (!empty($_POST['action']))) {
        $action=$_POST['action'];
    }
    
    
    
    $ret_array = array();
    
    switch ($action) {
        case "getSensors" :
       
            cultipi\check_db();
            
            //cultipi\addElementInSynoptic("sensor", 1, "capteur.png");
            
            $ret_array = configuration\getConfElem("NB_PLUGS");
            
            break;
        case "getPlugs" :
            echo "i égal 1";
            break;
        case "getOtherItems" :
            echo "i égal 2";
            break;
        case "updatePosition" :
            $elem = "";
            if((isset($_POST['elem'])) && (!empty($_POST['elem']))) {
                $elem=$_POST['elem'];
            }
            $x = "";
            if((isset($_POST['x'])) && (!empty($_POST['x']))) {
                $x=$_POST['x'];
            }
            $y = "";
            if((isset($_POST['y'])) && (!empty($_POST['y']))) {
                $y=$_POST['y'];
            }
            cultipi\updatePosition($elem,$x,$y);
            break;
        case "updateZScaleImageRotation" :
            $id = "";
            if((isset($_POST['id'])) && (!empty($_POST['id']))) {
                $id=$_POST['id'];
            }
            $scale = "";
            if((isset($_POST['scale'])) && (!empty($_POST['scale']))) {
                $scale=$_POST['scale'];
            }
            $z = "";
            if((isset($_POST['z'])) && (!empty($_POST['z']))) {
                $z=$_POST['z'];
            }
            $image = "";
            if((isset($_POST['image'])) && (!empty($_POST['image']))) {
                $image=$_POST['image'];
            }
            $rotation = "";
            if((isset($_POST['rotation'])) && (!empty($_POST['rotation']))) {
                $rotation=$_POST['rotation'];
            }
            echo "$id , $z , $scale , $image , $rotation";
            cultipi\updateZScaleImageRotation($id,$z,$scale,$image,$rotation);
            break;
        case "getParam" :
            $elem = "";
            if((isset($_POST['id'])) && (!empty($_POST['id']))) {
                $ret_array = cultipi\getSynopticDBElemByID($_POST['id']);
            }
            break;
        case "getAllSensorLiveValue" :
            $ret_array = cultipi\getAllSensorLiveValue();
            break;
        case "getAllPlugLiveValue" :
            $ret_array = cultipi\getAllPlugLiveValue();
            break;
        case "getSensorLiveValue" :
            $index = "";
            if((isset($_POST['index'])) && (!empty($_POST['index']))) {
                $index = $_POST['index'];
            }
            
            $ret_array = cultipi\getSensorLiveValue($index);

            break;
        case "getPlugLiveValue" :
            $index = "";
            if((isset($_POST['index'])) && (!empty($_POST['index']))) {
                $index = $_POST['index'];
            }
            
            $ret_array = cultipi\getPlugLiveValue($index);

            break;
        default:
            break;
    }
    
    // Return the array
    echo json_encode($ret_array);
 
?>

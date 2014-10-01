<?php 

if((isset($_GET['POSITION_X']))&&(!empty($_GET['POSITION_X']))&&(isset($_GET['POSITION_Y']))&&(!empty($_GET['POSITION_Y']))&&(isset($_GET['WIDTH']))&&(!empty($_GET['WIDTH']))) {
    if((isset($_GET['REDUCED']))&&(!empty($_GET['REDUCED']))) {
        setcookie("POSITION", $_GET['POSITION_X'].",".$_GET['POSITION_Y'].",".$_GET['WIDTH'].",".$_GET['REDUCED'], time()+(86400 * 30),"/",false,false);
    } else {
        setcookie("POSITION", $_GET['POSITION_X'].",".$_GET['POSITION_Y'].",".$_GET['WIDTH'].",False", time()+(86400 * 30),"/",false,false);
    }
} else if((isset($_COOKIE['POSITION']))&&(!empty($_COOKIE['POSITION']))) {
    echo json_encode($_COOKIE['POSITION']);
} else {
    echo json_encode("15,15,325,False"); 
}

?>

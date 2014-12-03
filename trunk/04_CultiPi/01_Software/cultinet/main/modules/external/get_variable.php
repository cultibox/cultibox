<?php 

if((isset($_GET['name']))&&(!empty($_GET['name']))) {
    $name=strtoupper($_GET['name']);
}

if((!isset($name))||(empty($name))) {
    echo json_encode("0");
} else {
    switch($name) {
        case 'LANG' : if((isset($_COOKIE['LANG']))&&(!empty($_COOKIE['LANG']))) {
                        echo json_encode($_COOKIE['LANG']);
                      } else {
                            echo json_encode("0");
                      }
                    break;
        default:
            echo json_encode("0");
    }
}

?>

<?php 

//Affiche la valeur d'une variable PHP, script appelé par Ajax par le fichier cultibox.js pour récupérer des informations
// stockées côté serveur:

require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');
require_once('../../libs/db_get_common.php');

$error=array();


//Récupération du nom de la variable, par convention interne, les noms de COOKIE  sont 
//toujours en majuscule, on capitalise donc le nom récupéré:
if((isset($_GET['name']))&&(!empty($_GET['name']))) {
    $name=strtoupper($_GET['name']);
}

if((!isset($name))||(empty($name))) {
    //On affiche 0 si la fonction est appelée sans le nom de la variable:
    echo json_encode("0");
} else {
    switch($name) {
        case 'LOAD_LOG':
            if((isset($_COOKIE['LOAD_LOG']))&&(!empty($_COOKIE['LOAD_LOG']))) {
                echo json_encode($_COOKIE['LOAD_LOG']);
            } else {
                 echo json_encode("0");
            }
            break;
        case 'SD_CARD':
            $sd_card=get_sd_card();
            if(!$sd_card) {
                echo "";
            } else {
                echo $sd_card;
            }
            break;
        case 'IMPORTANT':
            if((isset($_COOKIE['IMPORTANT']))&&(!empty($_COOKIE['IMPORTANT']))) {
                echo json_encode($_COOKIE['IMPORTANT']);
            } else {
                 echo json_encode("0");
            }
            break;
        case 'TOOLTIP_MSG_BOX':
            if((isset($_COOKIE['TOOLTIP_MSG_BOX']))&&(!empty($_COOKIE['TOOLTIP_MSG_BOX']))) {
                echo json_encode($_COOKIE['TOOLTIP_MSG_BOX']);
            } else {
                 echo json_encode("0");
            }
            break;
        case 'CHECK_PROGRAM':
            if((isset($_GET['value']))&&(!empty($_GET['value']))) {
                    $value=strtoupper($_GET['value']);
            } 

            if((!isset($value))||(empty($value))) {
                echo json_encode("0");
            } else {
                if(!check_programs("",$value)) {
                     echo json_encode("0");
                } else {
                    echo json_encode("1");
                }
            }
            break;
        case 'COST' : echo json_encode(get_configuration("SHOW_COST",$error));
                     break;
        case 'WEBCAM': echo json_encode(get_configuration("SHOW_WEBCAM",$error));
                        break;
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

<?php 
//Affiche la valeur d'une variable PHP, script appelé par Ajax par le fichier cultibox.js pour récupérer des informations
// stockées côté serveur:

require_once('../../libs/utilfunc.php');

//On démarre une SESSION si ce n'est pas déja le cas:
if (!isset($_SESSION)) {
    session_start();
}

//Récupération du nom de la variable, par convention interne, les noms de variable de SESSION sont 
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
            if((isset($_SESSION['LOAD_LOG']))&&(!empty($_SESSION['LOAD_LOG']))) {
                echo json_encode($_SESSION['LOAD_LOG']);
            }
            break;
        case 'SD_CARD':
            echo get_sd_card();
            break;
        case 'IMPORTANT':
            if((isset($_SESSION['IMPORTANT']))&&(!empty($_SESSION['IMPORTANT']))) {
                echo json_encode($_SESSION['IMPORTANT']);
            }
            break;
        case 'TOOLTIP_MSG_BOX':
            if((isset($_SESSION['TOOLTIP_MSG_BOX']))&&(!empty($_SESSION['TOOLTIP_MSG_BOX']))) {
                echo json_encode($_SESSION['TOOLTIP_MSG_BOX']);
            }
            break;
        default:
            echo json_encode("0");
    }
}

?>

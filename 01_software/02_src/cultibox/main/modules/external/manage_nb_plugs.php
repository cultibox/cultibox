<?php 

require_once('../../libs/config.php');
require_once($GLOBALS['BASE_PATH'].'main/libs/db_get_common.php');
require_once($GLOBALS['BASE_PATH'].'main/libs/db_set_common.php');


if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
}

if((isset($_GET['nb_plugs']))&&(!empty($_GET['nb_plugs']))) {
    $nb_plugs=$_GET['nb_plugs'];
}

if((!isset($type))||(empty($type))||(!isset($nb_plugs))||(empty($nb_plugs))) {
    echo json_encode("0");
} else {
    if(strcmp("$type","add")==0) {
        if($nb_plugs<$GLOBALS['NB_MAX_PLUG']) {
            insert_configuration("NB_PLUGS",$nb_plugs+1,$main_error);
        }
    }

    if(strcmp("$type","remove")==0) { 
        if($nb_plugs>3) {
            insert_configuration("NB_PLUGS",$nb_plugs-1,$main_error);
        }
    }
}


/*
  if((empty($main_error))||(!isset($main_error))) {
                //Si tout s'est bien passé:
                // On incrémente le nombre de prises définit:
                $nb_plugs=$nb_plugs+1;

                //On positionne les listes déroulantes à la valeur de la nouvelle prise:
                $selected_plug=$nb_plugs;
                $reset_selected=$nb_plugs;
                $export_selected=$nb_plugs;
                $import_selected=$nb_plugs;

                //Affichage des messages d'ajout: 
                $pop_up_message=$pop_up_message.popup_message(__('PLUG_ADDED'));
                $main_info[]=__('PLUG_ADDED');
            }
        } else {
            //Sinon affichage du message de limite de prises atteinte:
            $main_error[]=__('PLUG_MAX_ADDED');
        }
        $nb_plugs=get_configuration("NB_PLUGS",$main_error);



// Suppression d'une prise pour configurer un nouveau programme, la variable définissant le nombre de prise maximale
// est configurée dans le fichier config.php: $GLOBALS['NB_MAX_PLUG'] (même chose pour le nombre minimale: $GLOBALS['NB_MIN_PLUG'])
if((isset($remove_plug))&&(!empty($remove_plug))) {
    if((isset($nb_plugs))&&(!empty($nb_plugs))) {
        if($nb_plugs>3) {
            //Si le nombre de prise minimale n'est pas encore atteind:
            // Suppression d'une nouvelle prise dans la base de données:
            insert_configuration("NB_PLUGS",$nb_plugs-1,$main_error);
            if((empty($main_error))||(!isset($main_error))) {
                //Si tout s'est bien passé:
                // On décrémente le nombre de prises définit:
                $nb_plugs=$nb_plugs-1;
                if($selected_plug>$nb_plugs) { // Si la prise actuellement sélectionnée était la dernière prise, on change l'affichage:
                    $selected_plug=$nb_plugs;
                    $reset_selected=$nb_plugs;
                    $export_selected=$nb_plugs;
                    $import_selected=$nb_plugs;
                }

                //Affichage des messages de suppression:
                $pop_up_message=$pop_up_message.popup_message(__('PLUG_REMOVED'));
                $main_info[]=__('PLUG_REMOVED');
            }
        } else {
            //Sinon affichage du message de limite de prises atteinte:
            $main_error[]=__('PLUG_MIN_ADDED');
        }
        $nb_plugs=get_configuration("NB_PLUGS",$main_error);
    }
}

*/






?>

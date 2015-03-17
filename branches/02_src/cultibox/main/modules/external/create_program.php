<?php

require_once('../../libs/config.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');


if(!isset($selected_plug)) {
    $selected_plug=getvar('selected_plug');
}

if((empty($selected_plug))||(!isset($selected_plug))) {
    $selected_plug=1;
}

$chinfo=true;
$chtime="";
$pop_up_message="";
$pop_up_error_message="";
$resume=array();

$index_info=array();
program\get_program_index_info($index_info);


$cyclic=getvar("cyclic"); 
$apply=getvar('apply'); 
$action_prog=getvar('action_prog'); 
$reset_old_program=getvar("reset_old_program"); 
$value_program  = getvar('value_program'); 
$regul_program=getvar("regul_program"); 
$start_time=getvar("start_time"); 
$end_time=getvar("end_time");

$start="";
$end="";
$rep="";
$resume_regul=array();
$type="0";
$tmp_prog="";


// Var used to choose programm to display and modify 
$program_index_id = getvar("program_index_id");
if($program_index_id == "") $program_index_id = 1;

// Get "number" field of program table
$program_index = program\get_field_from_program_index ("program_idx",$program_index_id);

// Get number of daily program recorded:
$nb_daily_program = get_nb_daily_program($main_error);


if(isset($cyclic)&&(!empty($cyclic))) {
    //Dans le cas d'un programme cyclique on récupère les champs correspondant:
    $repeat_time=getvar("repeat_time"); //La fréquence de répétition
    $start_time_cyclic=getvar('start_time_cyclic'); //L'heure de départ du programme
    $end_time_cyclic=getvar('end_time_cyclic'); //L'heure de fin du programme
    $cyclic_duration=getvar('cyclic_duration'); //La durée d'un cycle
    $cyclic_start=$start_time_cyclic;   //On sauvegarde les valeurs de départ et de fin qui vont être modifié dans le programme
    $final_cyclic_end=$end_time_cyclic; //pour l'affichage dans les input text
}


//Vérification et mise en place de la value du programme en fonction du type de prise entre autre:
if($regul_program == "on") {
    //Valeur de 99.9 pour un programme en marche forcé:
    $value_program="99.9";
    //Type de programme pour différencier les programmes variateurs des programmes de prises classiques: 0 pour classique, 2 pour les variateurs
    $type="0";
    $check="1";
} else if("$regul_program"=="off") {
    $value_program="0";
    $check="1";
} else if("$regul_program"=="dimmer") {
    $type="2";
    $check="1";
} else {
    $check="1";
    $type="1";
}



if($check == "1") { //Si la valeur du programme est correcte:
    if(isset($cyclic)&&(!empty($cyclic))&&(strcmp("$repeat_time","00:00:00")!=0)) {
        date_default_timezone_set('UTC'); //Pour le calcul des timestamps
        //Calcul de l'heure de fin du premier cycle: départ + durée du cycle 
        $tmpstamp_start=strtotime($cyclic_start);
        list($hours, $mins, $secs) = explode(':', $cyclic_duration);
        $tmpstamp_end=($hours * 3600 )+($mins * 60 )+$secs;
        $cyclic_end=date('H:i:s', $tmpstamp_start+$tmpstamp_end);

        $start_time=$cyclic_start;
        $end_time=$cyclic_end;

        list($hs, $ms, $ss) = explode(':', $cyclic_start);
        list($he, $me, $se) = explode(':', $cyclic_end);
        $chk_start=mktime($hs,$ms,$ss,1,1,1970);
        $chk_stop=mktime($he, $me, $se,1,1,1970);
    } 

    //Vérification du type de programme, les traitements ne sont pas les même si le programme reboucle (départ>fin) ou s'il ne reboucle pas (départ<fin)
    $chtime=check_times($start_time,$end_time);

    if($chtime==2) { //Dans le cas d'un programme ou la première action depasse sur le début de la journée: 
        $prog[]= array(
            "start_time" => "$start_time",
            "end_time" => "23:59:59",
            "value_program" => "$value_program",
            "selected_plug" => "$selected_plug",
            "type" => "$type",
            "number" => $program_index
        );

        $prog[]= array(
            "start_time" => "00:00:00",
            "end_time" => "$end_time",
            "value_program" => "$value_program",
            "selected_plug" => "$selected_plug",
            "type" => "$type",
            "number" => $program_index
        );
    } else {
        $prog[]= array(
            "start_time" => "$start_time",
            "end_time" => "$end_time",
            "value_program" => "$value_program",
            "selected_plug" => "$selected_plug",
            "type" => "$type",
            "number" => $program_index
        );
    }

    $start=$start_time;
    $end=$end_time;

    ///Pour vérifier si une erreur à eu lieu dans l'insertion du/des programmes: true -> pas d'erreur, false -> une erreur
    $ch_insert=true;


    //Dans le cas d'une action ponctuel on s'arrête la, dans le cas d'une action cyclique il faut calculer les cycles: 
    if( isset($cyclic) && !empty($cyclic)
        && $repeat_time != "00:00:00") {
            
        //Récupération des heures, minutes et secondes du temps de répétition
        list($rephh, $repmm, $repss) = explode(':', $repeat_time); 
         
        //Calcul du temps de répétition d'un nouveau cycle en seconde
        $step=$rephh*3600+$repmm*60+$repss; 

        //On insère pas le premier événement, il a été ajouté précédemment:
        $chk_first=false;


        //Variables de vérification du rebouclage des cycles:
        $start_check=str_replace(":","",$start_time);
        $stop_check=str_replace(":","",$end_time);
        $repeat_check=str_replace(":","",$repeat_time);
        $duration_check=str_replace(":","",$cyclic_duration);
        $optimize=false;

        $chtime=check_times($start_time_cyclic,$end_time_cyclic);
        if($duration_check>=$repeat_check) {
            if($chtime!=2) {
                //Dans le cas d'un programme qui ne reboucle pas, on vérifie si les cycles ne se chevauchent pas,
                // si c'est le cas un seul événement couvre la journée:
                $optimize=true;
                unset($prog);
                $prog[]= array(
                        "start_time" => "$cyclic_start",
                        "end_time" => "$end_time_cyclic",
                        "value_program" => "$value_program",
                        "selected_plug" => "$selected_plug",
                        "type" => "$type",
                        "number" => $program_index
                    );
            } else {
            //Dans le cas d'un programme qui reboucle, on vérifie si les cycles ne se chevauchent pas,
            // si c'est le cas deux événements couvrent la journée:
                $optimize=true;
                unset($prog);
                $prog[]= array(
                        "start_time" => "$cyclic_start",
                        "end_time" => "23:59:59",
                        "value_program" => "$value_program",
                        "selected_plug" => "$selected_plug",
                        "type" => "$type",
                        "number" => $program_index
                );

                $prog[]= array(
                        "start_time" => "00:00:00",
                        "end_time" => "$end_time_cyclic",
                        "value_program" => "$value_program",
                        "selected_plug" => "$selected_plug",
                        "type" => "$type",
                        "number" => $program_index
                    );
            }
        }

        if(!$optimize) {    
            //Dans le cas ou les cycles ne rebouclent pas entre eux, on va les calculer et les insérer:
            $chtime=check_times($cyclic_start,$final_cyclic_end);
            if($chtime==2) {
                //Calcul de la plage de durée des actions, si la durée reboucle, la plage de durée = 86400 (1 jour en seconde) - (temps de départ - temps de fin) car cyclic_start > final_cyclic_end dans un rebouclage
                $elapsed_time=86400-(mktime(substr($cyclic_start,0,2),substr($cyclic_start,3,2),substr($cyclic_start,6,2))-mktime(substr($final_cyclic_end,0,2),substr($final_cyclic_end,3,2),substr($final_cyclic_end,6,2)));
                if(date('His', $chk_stop)>date('H:i:s', $chk_start)) {
                    $chk_while=$chk_stop-$chk_start;
                } else {
                    $chk_while=(mktime(23,59,59,1,1,1970)-$chk_start)+($chk_stop-86400);
                }
            } else {
                $elapsed_time=mktime(substr($final_cyclic_end,0,2),substr($final_cyclic_end,3,2),substr($final_cyclic_end,6,2))-mktime(substr($cyclic_start,0,2),substr($cyclic_start,3,2),substr($cyclic_start,6,2));
                $chk_while=$chk_stop-$chk_start; 
            }

            $reboucle=false;
            while($chk_while<$elapsed_time) {
                //On ne veut pas enregistrer le premier événement qui a déja été enregistré plus haut - utilisation de la variable chk_first:
                if($chk_first) {
                    //Dans le cas ou l'on n'est pas dans le premier cycle on enregistre le programme:
                    if(strcmp("$cyclic_end","00:00:00")==0) $cyclic_end="23:59:59"; //La fin de la journée est définit comme 23:59:59

                    if(str_replace(":","",$cyclic_start)>str_replace(":","",$cyclic_end)) {
                        $prog[]= array(
                            "start_time" => "$cyclic_start",
                            "end_time" => "23:59:59",
                            "value_program" => "$value_program",
                            "selected_plug" => "$selected_plug",
                            "type" => "$type",
                            "number" => $program_index
                        );
                        $prog[]= array(
                            "start_time" => "00:00:00",
                            "end_time" => "$cyclic_end",
                            "value_program" => "$value_program",
                            "selected_plug" => "$selected_plug",
                            "type" => "$type",
                            "number" => $program_index
                        );

                    } else {
                        $prog[]= array(
                            "start_time" => "$cyclic_start",
                            "end_time" => "$cyclic_end",
                            "value_program" => "$value_program",
                            "selected_plug" => "$selected_plug",
                            "type" => "$type",
                            "number" => $program_index
                        );
                    }
                }

                //Récupération en heure, minute seconde des temps:
                list($hh, $mm, $ss) = explode(':', $cyclic_start);
                if(strcmp("$cyclic_end","23:59:59")==0) $cyclic_end="00:00:00"; //Le début de la journée est définit comme 00:00:00
                list($shh, $smm, $sss) = explode(':', $cyclic_end);


                //Ajout du nouveau cycle au cycle précédent:
                $val_start=mktime($hh,$mm,$ss,1,1,1970)+$step;
                $val_stop=mktime($shh,$smm,$sss,1,1,1970)+$step;

                if($chtime==2) {
                    if($val_stop>86400) $val_stop=$val_stop-86400;
                    if($val_start>86400) $val_start=$val_start-86400;
                } else {
                    if($val_stop>86400) {
                        $cyclic_start=date('H:i:s', $val_start);
                        $cyclic_end=date('H:i:s', $val_stop);
                        break;
                    }
                }

                //Retransformation des temps en hh:mm:ss:
                $cyclic_start=date('H:i:s', $val_start);
                $cyclic_end=date('H:i:s', $val_stop);

                if($chk_first) {
                    if($val_stop==86400) {  
                        $val_stop=85399;
                        $cyclic_end=date('H:i:s', $val_stop);
                    }
                }

                //Pour eviter que la partie cyclique reboucle:
                if(($chtime==2)&&($reboucle)&&(str_replace(":","",$cyclic_end)>str_replace(":","",$start_time))) {
                    break;
                } elseif(($chtime!=2)&&(str_replace(":","",$cyclic_end)>str_replace(":","",$end_time_cyclic))) {    
                    break;
                }
                   

                //Mise à jour de la valeur de vérification de fin de boucle en fonction du nouveau cycle:
                if($chtime==2) {
                    //Dans le cas d'un rebouclage, le calcul du temps écoulé est le suivant:
                    // Si la valeur de fin de cycle n'a pas rebouclé, le temps écoulé est: fin du cycle courant - début du premier cycle
                    $chk_stop=$val_stop;
                    if(date('His', $chk_stop)>date('His', $chk_start)) {
                        $chk_while=$chk_stop-$chk_start;
                    } else {
                        $reboucle=true;
                        //Si le cycle reboucle, le temps écoulé est: 
                        if(strcmp(date('H:i:s',($chk_stop)),"00:00:00")!=0) {
                            $chk_while=mktime(0,0,0,1,2,1970)-$chk_start+$chk_stop;
                        } else {
                            //Si le temps de fin est à 00:00:00 on est en bordure du rebouclage, on considère qu'on a pas encore rebouclé: 
                            //le temps écoulé est: fin du cycle courant - début du premier cycle
                            $chk_while=86400-$chk_start;
                        }
                    }
                } else {
                    //Dans le cas d'un non rebouclage, on se base sur la valeur de fin de cycle:
                    $chk_stop=$val_stop;
                    $chk_while=$chk_stop-$chk_start;
                }

                //Variable pour ne pas enregistrer le premier élément:
                $chk_first=true;
            }

            // Si le dernier événement cyclique dépasse, on le raccourcis pour qu'il finisse a 23:59:59
            if((strcmp("$cyclic_end","00:00:00")==0)||(str_replace(":","",$cyclic_end)<str_replace(":","",$cyclic_start))) {
                $prog[]= array(
                        "start_time" => "$cyclic_start",
                        "end_time" => "23:59:59",
                        "value_program" => "$value_program",
                        "selected_plug" => "$selected_plug",
                        "type" => "$type",
                        "number" => $program_index
                    );
            } else if((str_replace(":","",$cyclic_end)>str_replace(":","",$final_cyclic_end))&&(str_replace(":","",$cyclic_start)<str_replace(":","",$final_cyclic_end))) {
                $prog[]= array(
                        "start_time" => "$cyclic_start",
                        "end_time" => "$final_cyclic_end",
                        "value_program" => "$value_program",
                        "selected_plug" => "$selected_plug",
                        "type" => "$type",
                        "number" => $program_index
                    );
            }
                
        }
        $rep=$repeat_time;
        $start_time="00:00:00";
        $end_time="00:00:00";
    }

    //If the reset checkbox is checked
    if((isset($reset_old_program)) && (!empty($reset_old_program)) && (strcmp("$reset_old_program","Yes")==0)) {
        clean_program($selected_plug,$program_index,$main_error);
        unset($reset_old_program);
    } 

    $ch_insert=true;
    if(count($prog)>0) {
         if(!insert_program($prog,$main_error,$program_index)) 
            $ch_insert=false;
    } else {
        $ch_insert=false;
    }

    if($ch_insert) {
        echo json_encode("1");
    }  else {
        echo json_encode("0");
    }
}

?>

<?php

// Compute page time loading for debug option
$start_load = getmicrotime();


// Language for the interface, using a COOKIE and the function __('$msg') from utilfunc.php library to print messages
$main_error=array();
$main_info=array();
$version=get_configuration("VERSION",$main_error);

// ================= VARIABLES ================= //
$nb_plugs=get_configuration("NB_PLUGS",$main_error);

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
$plug_type=get_plug_conf("PLUG_TYPE",$selected_plug,$main_error);

$index_info=array();
program\get_program_index_info($index_info);



//Valeur du radio bouton qui définit si le programme sera cyclic ou non:
if(!isset($cyclic)) {
    $cyclic=getvar("cyclic");
}

if(!isset($apply)) {
    $apply=getvar('apply');
}

if(!isset($action_prog)) {
    $action_prog=getvar('action_prog');
}

if(!isset($reset_old_program)) {
    $reset_old_program=getvar("reset_old_program");
}

if(!isset($value_program)) {
    $value_program  = getvar('value_program');
}

if(!isset($regul_program)) {
    $regul_program=getvar("regul_program");
}

if(!isset($start_time)) {
    $start_time=getvar("start_time");
}


if(!isset($end_time)) {
    $end_time=getvar("end_time");
}
    

// Get configuration value
$second_regul        = get_configuration("SECOND_REGUL",$main_error);
$remove_1000_change_limit = get_configuration("REMOVE_1000_CHANGE_LIMIT",$main_error);
$remove_5_minute_limit    = get_configuration("REMOVE_5_MINUTE_LIMIT",$main_error);


$start="";
$end="";
$rep="";
$resume_regul=array();
$tmp="";

if(!isset($submit_progs)) {
    $submit_progs=getvar("submit_progs",$main_error);
}

$type="0";
$tmp_prog="";

// Var used to choose programm to display and modify 
if(!isset($program_index_id)) {
     $program_index_id = getvar("program_index_id");
}

if($program_index_id == "") $program_index_id = 1;


// Get "number" field of program table
$program_index = program\get_field_from_program_index ("program_idx",$program_index_id);


// Get number of daily program recorded:
$nb_daily_program = get_nb_daily_program($main_error);
    
// Var used to define if program file must be rebuild
$rebuildProgamFile = false;
    
$error_value[0]="";
$error_value[1]="";
$error_value[2]=__('ERROR_VALUE_PROGRAM','html');
$error_value[3]=__('ERROR_VALUE_PROGRAM_TEMP','html');
$error_value[4]=__('ERROR_VALUE_PROGRAM_HUMI','html');
$error_value[5]=__('ERROR_VALUE_PROGRAM_CM','html');
$error_value[6]=__('ERROR_VALUE_PROGRAM','html');

for($i=1;$i<=$nb_plugs;$i++) {
    $resume_regul[$i]=format_regul_sumary("$i",$main_error);
}

if((!isset($export_selected)) || empty($export_selected)) {
    $export_selected=$selected_plug;
} 

if((!isset($import_selected)) || empty($import_selected)) {
    $import_selected=$selected_plug;
}

if(isset($cyclic)&&(!empty($cyclic))) {
    //Dans le cas d'un programme cyclique on récupère les champs correspondant:
    if(!isset($repeat_time)) {
        $repeat_time=getvar("repeat_time"); //La fréquence de répétition
    }

    if(!isset($start_time_cyclic)) {
        $start_time_cyclic=getvar('start_time_cyclic'); //L'heure de départ du programme
    }

    if(!isset($end_time_cyclic)) {
        $end_time_cyclic=getvar('end_time_cyclic'); //L'heure de fin du programme
    }

    if(!isset($cyclic_duration)) {
        $cyclic_duration=getvar('cyclic_duration'); //La durée d'un cycle
    }

    $cyclic_start=$start_time_cyclic;   //On sauvegarde les valeurs de départ et de fin qui vont être modifié dans le programme
    $final_cyclic_end=$end_time_cyclic; //pour l'affichage dans kes input text
}

if(empty($apply)||(!isset($apply))) {
    $value_program="";
    $regul_program="on";
}

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

if((!isset($sd_card))||(empty($sd_card))) {
   $main_error[]=__('ERROR_SD_CARD');
}

// Retrieve plug's informations from the database
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);

// Add user information : He can use wizard
$main_info[]=__('WIZARD_ENABLE_FUNCTION').": <a href='/cultibox/index.php?menu=wizard' class='href-wizard-msgbox'><img src='main/libs/img/wizard.png' alt='".__('WIZARD')."' title='' id='wizard' /></a>";


//Create a new program:
if(!empty($apply) && isset($apply))
{ 

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
            $chk_start=mktime($hs,$ms,$ss);
            $chk_stop=mktime($he, $me, $se);
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
            $optimize=false;


            if($chtime!=2) {
                //Dans le cas d'un programme qui reboucle, on vérifie si les cycles ne se chevauchent pas,
                // si c'est le cas un seul événement couvre la journée:
                if($stop_check-$start_check>=$repeat_check) {
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
                }
            } else {
                //Dans le cas d'un programme qui ne reboucle pas, on vérifie si les cycles ne se chevauchent pas,
                // si c'est le cas un seul événement couvre la journée:
                if((235959-$start_check)+$stop_check>=$repeat_check) {
                    $optimize=true;
                    unset($prog);
                    $prog[]= array(
                            "start_time" => "00:00:00",
                            "end_time" => "23:59:59",
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
                        $chk_while=(mktime(23,59,59)-$chk_start)+($chk_stop-mktime(0,0,0));
                    }
                } else {
                    $elapsed_time=mktime(substr($final_cyclic_end,0,2),substr($final_cyclic_end,3,2),substr($final_cyclic_end,6,2))-mktime(substr($cyclic_start,0,2),substr($cyclic_start,3,2),substr($cyclic_start,6,2));
                    $chk_while=$chk_stop-$chk_start; 
                }

                while($chk_while<$elapsed_time) {
                    //On ne veut pas enregistrer le premier événement qui a déja été enregistré plus haut - utilisation de la variable chk_first:
                    if($chk_first) {
                        //Dans le cas ou l'on n'est pas dans le premier cycle on enregistre le programme:
                        if(strcmp("$cyclic_end","00:00:00")==0) $cyclic_end="23:59:59"; //La fin de la journée est définit comme 23:59:59
                        $prog[]= array(
                            "start_time" => "$cyclic_start",
                            "end_time" => "$cyclic_end",
                            "value_program" => "$value_program",
                            "selected_plug" => "$selected_plug",
                            "type" => "$type",
                            "number" => $program_index
                        );
                    }

                    //Récupération en heure, minute seconde des temps:
                    list($hh, $mm, $ss) = explode(':', $cyclic_start);
                    if(strcmp("$cyclic_end","23:59:59")==0) $cyclic_end="00:00:00"; //Le début de la journée est définit comme 00:00:00
                    list($shh, $smm, $sss) = explode(':', $cyclic_end);


                    //Ajout du nouveau cycle au cycle précédent:
                    $val_start=mktime($hh,$mm,$ss)+$step;
                    $val_stop=mktime($shh,$smm,$sss)+$step;

                    //Retransformation des temps en hh:mm:ss:
                    $cyclic_start=date('H:i:s', $val_start);
                    $cyclic_end=date('H:i:s', $val_stop);


                    //Pour eviter que la partie cyclique reboucle:
                    if(($chtime==2)&&(str_replace(":","",$cyclic_end)>str_replace(":","",$start_time))) {
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
                            //Si le cycle a déja rebouclé, le temps écoulé est: 
                            if(strcmp(date('H:i:s',($chk_stop)),"00:00:00")!=0) {
                                $chk_while=(mktime(0,0,0,1,2,1970)-$chk_start)+($chk_stop-mktime(0,0,0,1,1,1970));
                            } else {
                                //Si le temps de fin est à 00:00:00 on est en bordure du rebouclage, on considère qu'on a pas encore rebouclé: 
                                //le temps écoulé est: fin du cycle courant - début du premier cycle
                                $chk_while=(mktime(0,0,0)-$chk_start);
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
        if(isset($reset_old_program) && $reset_old_program == "Yes") {
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
            $main_info[]=__('INFO_VALID_UPDATE_PROGRAM');
            $pop_up_message=$pop_up_message.popup_message(__('INFO_VALID_UPDATE_PROGRAM'));                    

            if((isset($sd_card))&&(!empty($sd_card))) {
                $main_info[]=__('INFO_PLUG_CULTIBOX_CARD');
                $pop_up_message=$pop_up_message.popup_message(__('INFO_PLUG_CULTIBOX_CARD'));
            }

            // Check if user has not removed the limit of 1000 change
            if ($remove_1000_change_limit == "False")
            {
                $tmp_prog=create_program_from_database($main_error);
                if(count($tmp_prog)>$GLOBALS['PLUGV_MAX_CHANGEMENT']-1) {
                    $last_action=substr($tmp_prog[$GLOBALS['PLUGV_MAX_CHANGEMENT']-1],0,5);
                    $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_MAX_PROGRAM')." ".date('H:i:s',$last_action));
                }
            }

        } 
    }
}

// Check if user has not removed the limit of 1000 change
if ($remove_1000_change_limit == "False")
{
    //Pour vérifier que l'on ne dépasse pas la limite de changement d'état des prises:
    //On génère le fichier plugv depuis la base de données et on compte le nombre de ligne,
    //Si cela dépasse la limite, on affiche une erreur/warning après calcul de l'heure de la dernière action
    $tmp_prog=create_program_from_database($main_error);
    if (count($tmp_prog) > $GLOBALS['PLUGV_MAX_CHANGEMENT']-1)
    {
        $last_action=substr($tmp_prog[$GLOBALS['PLUGV_MAX_CHANGEMENT']-1],0,5);
        $main_error[]=__('ERROR_MAX_PROGRAM')." ".date('H:i:s', $last_action);
    }
}


// For each plug gets pogramm
for($i=0;$i<$nb_plugs;$i++) {
    $data_plug = get_data_plug($i+1,$main_error,$program_index);
    $plugs_infos[$i]["data"] = format_program_highchart_data($data_plug,"");

    // Translate
    $plugs_infos[$i]['translate'] = translate_PlugType($plugs_infos[$i]['PLUG_TYPE']);
    
}

// Create summary for tooltip
$resume=format_data_sumary($plugs_infos);

$tmp_resume[]="";
foreach($resume as $res) {
    $tmp_res=explode("<br />",$res);
    if(count($tmp_res)>40) {
        $tmpr=array_chunk($tmp_res,39);
        $tmpr[0][]="[...]";
        $tmp_resume[]=implode("<br />", $tmpr[0]);
    } else {
        $tmp_resume[]=$res;
    }
}

if(count($tmp_resume)>0) {
    unset($resume);
    $resume=$tmp_resume;
}



// If it's an submit entry, rebuild program
if(isset($submit_progs) && !empty($submit_progs))
{
    $rebuildProgamFile = true;
}

// If needed, rebuild program
if ($rebuildProgamFile 
    && !empty($sd_card) 
    && check_sd_card($sd_card))
{
    // Get field number of program
    $fieldNumber = program\get_field_from_program_index("program_idx",$program_index_id);

    // Read from database program
    $program = create_program_from_database($main_error,$fieldNumber);
    
    // Check if different from SD cards
    if(!compare_program($program,$sd_card)) {
    
        // Get pluXX filename
        $plu_fileName = "plu" . program\get_field_from_program_index("plugv_filename",$program_index_id);
    
        // Save the program on SD card
        if(!save_program_on_sd($sd_card,$program,$plu_fileName)) {
        
            // If there is an error, display it
            $main_error[]=__('ERROR_WRITE_PROGRAM');
        }
    }
}



sd_card_update_log_informations($sd_card);
if((strcmp($regul_program,"on")==0)||(strcmp($regul_program,"off")==0)) {
    $value_program="";
} 

//Compute time loading for debug option
$end_load = getmicrotime();

if($GLOBALS['DEBUG_TRACE']) {
    echo __('GENERATE_TIME').": ".round($end_load-$start_load, 3) ." secondes.<br />";
    echo "---------------------------------------";
    aff_variables();
    echo "---------------------------------------<br />";
    memory_stat();
}

?>

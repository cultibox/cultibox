<?php

// Compute page time loading for debug option
$start_load = getmicrotime();


// Language for the interface, using a COOKIE and the function __('$msg') from utilfunc.php library to print messages
$main_error=array();
$main_info=array();

// Load config cultipi 
if(is_file("main/libs/config_cultipi.php")) {
   require_once 'main/libs/config_cultipi.php';
} else if(is_file("../libs/config_cultipi.php")) {
   require_once '../libs/config_cultipi.php';
} else {
   require_once '../../libs/config_cultipi.php';
}


// ================= VARIABLES ================= //
$nb_plugs=get_configuration("NB_PLUGS",$main_error);

if(!isset($selected_plug)) {
    $selected_plug=getvar('selected_plug');
}

if((empty($selected_plug))||(!isset($selected_plug))) {
    $selected_plug=1;
}


$version=get_configuration("VERSION",$main_error);
$second_regul=get_configuration("ADVANCED_REGUL_OPTIONS",$main_error);
$plug_count_sensor=array();


if(!isset($submit_plugs)) {
    $submit_plugs=getvar("submit_plugs");
}

$plug_count_sensor=array();


if(!isset($submenu)) {
    $submenu=getvar("submenu",$main_error);
}

if(!isset($reccord)) {
    $reccord=getvar('reccord');
}


// By default the expanded menu is the plug1 menu
if((!isset($submenu))||(empty($submenu))) {
    $submenu="1";
}


if((!isset($selected_plug))||(empty($selected_plug))) {
    $selected_plug="1";
}


for($nb=1;$nb<=$nb_plugs;$nb++) {
   $plug_name{$nb}  =get_plug_conf("PLUG_NAME",$nb,$main_error);
   $plug_type{$nb}  =get_plug_conf("PLUG_TYPE",$nb,$main_error);
   $plug_power{$nb} =get_plug_conf("PLUG_POWER",$nb,$main_error);
   $plug_regul{$nb} =get_plug_conf("PLUG_REGUL",$nb,$main_error);
   $plug_senso{$nb} =get_plug_conf("PLUG_SENSO",$nb,$main_error);
   $plug_senss{$nb} =get_plug_conf("PLUG_SENSS",$nb,$main_error);
   $plug_regul_value{$nb}   =get_plug_conf("PLUG_REGUL_VALUE",$nb,$main_error);
   $plug_power_max{$nb}     =get_plug_conf("PLUG_POWER_MAX",$nb,$main_error);
   $plug_tolerance{$nb}     =get_plug_conf("PLUG_TOLERANCE",$nb,$main_error);
   $plug_second_tolerance{$nb}  =get_plug_conf("PLUG_SECOND_TOLERANCE",$nb,$main_error); 
   $plug_compute_method{$nb}    =get_plug_conf("PLUG_COMPUTE_METHOD",$nb,$main_error);
   $plug_regul_sensor{$nb}      =get_plug_conf("PLUG_REGUL_SENSOR",$nb,$main_error);
   $plug_module{$nb}            =get_plug_conf("PLUG_MODULE",$nb,$main_error);
   if ($plug_module{$nb} == "") {$plug_num_module{$nb} = "wireless";}
   $plug_num_module{$nb}        =get_plug_conf("PLUG_NUM_MODULE",$nb,$main_error);
   if ($plug_num_module{$nb} == "") {$plug_num_module{$nb} = 1;}
   $plug_module_options{$nb}    =get_plug_conf("PLUG_MODULE_OPTIONS",$nb,$main_error);
   $plug_module_output{$nb}     =get_plug_conf("PLUG_MODULE_OUTPUT",$nb,$main_error);
   if ($plug_module_output{$nb} == "") {$plug_module_output{$nb} = 1;}   
   
   $plug_sensor[$nb]    =get_plug_regul_sensor($nb,$main_error);
   $plug_count_sensor[$nb]  =count(explode("-",$plug_regul_sensor{$nb}));
}


// Write file plug01 plug02...
if((isset($sd_card))&&(!empty($sd_card))) {
    if((isset($submit_plugs))&&(!empty($submit_plugs))) {
   // build conf plug array
       $plugconf=create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$main_error);
       if(count($plugconf)>0) {
            if(!check_sd_card($sd_card)) {
                $main_error[]=__('ERROR_WRITE_SD_PLUGCONF');
            } else {    
                if(!write_plugconf($plugconf,$sd_card)) {
                    $main_error[]=__('ERROR_WRITE_SD_PLUGCONF');    
                }
            } 
        }

        //write pluga file
        if(!check_sd_card($sd_card)) {
            $main_error[]=__('ERROR_WRITE_SD_PLUGA');
        } else {
            if(!write_pluga($sd_card,$main_error)) {
                $main_error[]=__('ERROR_WRITE_SD_PLUGA');
            }
        }
   }
}


// Retrieve plug's informations from the database
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);
$status=get_canal_status($main_error);


$chinfo=true;
$chtime="";
$resume=array();
$export_selected=1;

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
$remove_1000_change_limit = get_configuration("REMOVE_1000_CHANGE_LIMIT",$main_error);
$remove_5_minute_limit    = get_configuration("REMOVE_5_MINUTE_LIMIT",$main_error);

$resume_regul=array();
$tmp_prog="";
$start="";
$end="";

// Var used to choose programm to display and modify 
if(!isset($program_index_id)) {
     $program_index_id = getvar("program_index_id");
}
if($program_index_id == "") $program_index_id = 1;

// Get "number" field of program table
$program_index = program\get_field_from_program_index ("program_idx",$program_index_id);


// Get number of daily program recorded:
$nb_daily_program = get_nb_daily_program($main_error);
    
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
    $final_cyclic_end=$end_time_cyclic; //pour l'affichage dans les input text
} 

if((isset($ponctual))&&(!empty($ponctual))) {
    $start=$start_time;
    $end=$end_time;
}

if(empty($apply)||(!isset($apply))) {
    $value_program="";
    $regul_program="on";
}

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) { 
    if((!isset($sd_card))||(empty($sd_card))) {
        $sd_card=get_sd_card();
    }
} else {
    $sd_card = $GLOBALS['CULTIPI_CONF_TEMP_PATH'];
}


if((!isset($sd_card))||(empty($sd_card))) {
    setcookie("CHECK_SD", "False", time()+1800,"/",false,false);
}


// Check if user has not removed the limit of 1000 change
$limit=false;
$last_action=0;

if ($remove_1000_change_limit == "False")
{
    //Pour vérifier que l'on ne dépasse pas la limite de changement d'état des prises:
    //On génère le fichier plugv depuis la base de données et on compte le nombre de ligne,
    //Si cela dépasse la limite, on affiche une erreur/warning après calcul de l'heure de la dernière action
    $tmp_prog=create_program_from_database($main_error,$program_index);
    if (count($tmp_prog) > $GLOBALS['PLUGV_MAX_CHANGEMENT']-1)
    {
        $last_action=substr($tmp_prog[$GLOBALS['PLUGV_MAX_CHANGEMENT']-1],0,5);
        $main_error[]=__('ERROR_MAX_PROGRAM')." ".date('H:i:s', $last_action);
        $limit=true;
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

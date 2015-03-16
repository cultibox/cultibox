<?php

// Compute page time loading for debug option
$start_load = getmicrotime();


// Language for the interface, using a COOKIE variable and the function __('$msg') from utilfunc.php library to print messages
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
$version=get_configuration("VERSION",$main_error);
$second_regul=get_configuration("ADVANCED_REGUL_OPTIONS",$main_error);
$plug_count_sensor=array();


if(!isset($submit_plugs)) {
    $submit_plugs=getvar("submit_plugs");
}

$plug_count_sensor=array();


if(!isset($selected_plug)) {
    $selected_plug=getvar('selected_plug');
}

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

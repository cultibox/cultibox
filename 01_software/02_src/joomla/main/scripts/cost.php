<?php

if (!isset($_SESSION)) {
   session_start();
}

/* Libraries requiered: 
        db_*_common.php : manage database requests
        utilfunc.php  : manage variables and files manipulations
        debug.php     : functions for PHP and SQL debugs
        utilfunc_sd_card.php : functions for SD card management
*/

require_once('main/libs/config.php');
require_once('main/libs/db_get_common.php');
require_once('main/libs/db_set_common.php');
require_once('main/libs/utilfunc.php');
require_once('main/libs/debug.php');
require_once('main/libs/utilfunc_sd_card.php');


// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$error=array();
$main_error=array();
$main_info=array();
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');

$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');

// ================= VARIABLES ================= //
$startday=getvar('startday');
$endday=getvar('endday');
$cost_price=getvar('cost_price');
$cost_price_hp=getvar('cost_price_hp');
$cost_price_hc=getvar('cost_price_hc');
$cost_type=getvar('cost_type');
$start_hc=getvar("start_hc",$main_error);
$stop_hc=getvar("stop_hc",$main_error);
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$price=get_configuration("COST_PRICE",$main_error);
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);
$select_plug=getvar('select_plug');
$pop_up_error_message="";
$pop_up_message="";
$pop_up=get_configuration("SHOW_POPUP",$main_error);
$version=get_configuration("VERSION",$main_error);
$submit=getvar("submit_cost");
$resume="";
$lang=$_SESSION['LANG'];

// Check database consistency
check_database();

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}
// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
check_and_update_sd_card($sd_card,$main_info,$main_error);

// Search and update log information form SD card
sd_card_update_log_informations($sd_card);

//Setting some default value if they are not configured
if((!isset($select_plug))||(empty($select_plug))) {
   $select_plug="all";
}

if((!isset($startday))||(empty($startday))) {
   $startday=date('Y')."-".date('m')."-".date('d');
} 
$startday=str_replace(' ','',"$startday");

if((!isset($endday))||(empty($endday))) {
   $endday=date('Y')."-".date('m')."-".date('d');
} 
$endday=str_replace(' ','',"$endday");


//Save cost configuration or retrieve it:
if(!empty($cost_type)) {
    insert_configuration("COST_TYPE","$cost_type",$main_error);
} else {
    $cost_type = get_configuration("COST_TYPE",$main_error);
}


if(strcmp($cost_type,"standard")==0) {
    if(!empty($cost_price)&&($cost_price!=0)) {
         $cost_price=str_replace(",",".","$cost_price");
         insert_configuration("COST_PRICE","$cost_price",$main_error);
    } else {
        $cost_price = get_configuration("COST_PRICE",$main_error);
    }
    $cost_price_hc = get_configuration("COST_PRICE_HC",$main_error);
    $cost_price_hp = get_configuration("COST_PRICE_HP",$main_error);
    $start_hc = get_configuration("START_TIME_HC",$main_error);
    $stop_hc = get_configuration("STOP_TIME_HC",$main_error);

} else {
    if(!empty($cost_price_hc)&&($cost_price_hc!=0)) {
         $cost_price_hc=str_replace(",",".","$cost_price_hc");
         insert_configuration("COST_PRICE_HC","$cost_price_hc",$main_error);
    } else {
        $cost_price_hc = get_configuration("COST_PRICE_HC",$main_error);
    }

    if(!empty($cost_price_hp)&&($cost_price_hp!=0)) {
         $cost_price_hp=str_replace(",",".","$cost_price_hp");
         insert_configuration("COST_PRICE_HP","$cost_price_hp",$main_error);
    } else {
        $cost_price_hp = get_configuration("COST_PRICE_HP",$main_error);
    }


    if((isset($start_hc))&&(!empty($start_hc))) {
        insert_configuration("START_TIME_HC","$start_hc",$main_error);
    } else {
        $start_hc = get_configuration("START_TIME_HC",$main_error);
    }


    if((isset($stop_hc))&&(!empty($stop_hc))) {
        insert_configuration("STOP_TIME_HC","$stop_hc",$main_error);
    } else {
        $stop_hc = get_configuration("STOP_TIME_HC",$main_error);
    }
    $cost_price = get_configuration("COST_PRICE",$main_error);
}



if((strcmp($select_plug,"all")!=0)&&(strcmp($select_plug,"distinct_all")!=0)) {
        if(!check_configuration_power($select_plug,$main_error)) {
            $main_error[]=__('ERROR_POWER_PLUG')." ".$select_plug." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='plugs-".$_SESSION['SHORTLANG']."?selected_plug=".$select_plug."'>".__('HERE')."</a>";
        }
} else {
        $nb=array();
        for($plugs=1;$plugs<=$nb_plugs;$plugs++) {
            if(!check_configuration_power($plugs,$main_error)) {
               $nb[]=$plugs; 
            }
        }

        if(count($nb)>0) {
            if(count($nb)==1) {
                $main_error[]=__('ERROR_POWER_PLUG')." ".$nb[0]." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='plugs-".$_SESSION['SHORTLANG']."?selected_plug=".$nb[0]."'>".__('HERE')."</a>";
            } else {
                $tmp_number="";     
                foreach($nb as $number) {
                    if(strcmp($tmp_number,"")!=0) {
                        $tmp_number=$tmp_number.", ";
                    }
                    $tmp_number=$tmp_number.$number;
                }
                $main_error[]=__('ERROR_POWER_PLUGS')." ".$tmp_number." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='plugs-".$_SESSION['SHORTLANG']."?selected_plug=all'>".__('HERE')."</a>";
            }
        }
}



//Computing cost value:
if(strcmp($select_plug,"distinct_all")!=0) {
    if((isset($submit))&&(!empty($submit))) {
        $theorical_power="0";
        $real_power="0";
    } else {
        $theorical_power=get_theorical_power($select_plug,$cost_type,$main_error,$check);
        $nb=get_nb_days($startday,$endday)+1;
        $theorical_power=$theorical_power*$nb;

        $startTime = strtotime("$startday 12:00");
        $endTime = strtotime("$endday 12:00");
        $real_power=0;

        for ($i = $startTime; $i <= $endTime; $i = $i + 86400) {
            $thisDate = date('Y-m-d', $i); // 2010-05-01, 2010-05-02, etc
            $data_power=get_data_power($thisDate,$thisDate,$select_plug,$main_error);
            $real_power=get_real_power($data_power,$cost_type,$main_error)+$real_power;
            unset($data_power);
        }
    }

    if(strcmp($select_plug,"all")==0) {
        $title=__('PRICE_SELECT_ALL_PLUG');
        $color_cost = get_configuration("COLOR_COST_GRAPH",$main_error);
    } else {
        $title=$plugs_infos[$select_plug-1]['PLUG_NAME'];
        $color_cost=$GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'][$select_plug-1];
    }


    $data_price[]= array( 
        "number" => "$select_plug",
        "theorical" => "$theorical_power",
        "real" => "$real_power", 
        "title" => "$title",
        "color" => "$color_cost"
    );
} else {
    $nb=get_nb_days($startday,$endday)+1;
    for($plugs=1;$plugs<=$nb_plugs;$plugs++) { 
        if((isset($submit))&&(!empty($submit))) {
            $theorical_power="0";
            $real_power="0";
        } else {
            $theorical_power=get_theorical_power($plugs,$cost_type,$main_error,$check);
            $theorical_power=$theorical_power*$nb;

            $startTime = strtotime("$startday 12:00");
            $endTime = strtotime("$endday 12:00");
            $real_power=0;

            for ($i = $startTime; $i <= $endTime; $i = $i + 86400) {
                $thisDate = date('Y-m-d', $i); // 2010-05-01, 2010-05-02, etc
                $data_power=get_data_power($thisDate,$thisDate,$plugs,$main_error);
                $real_power=get_real_power($data_power,$cost_type,$main_error)+$real_power;
                unset($data_power);
            }
        }

        $title=$plugs_infos[$plugs-1]['PLUG_NAME'];
        $data_price[]= array(
            "number" => $plugs,
            "real" => "$real_power",
            "theorical" => "$theorical_power",
            "title" => "$title",
            "color" => $GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'][$plugs-1]
       ); 
    }
}

//Get and format resume for cost configuration
$resume=get_cost_summary($main_error);

// Include in html pop up and message
include('main/templates/post_script.php');

//Display the cost template
include('main/templates/cost.html');

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

<?php

// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a COOKIE variable and the function __('$msg') from utilfunc.php library to print messages
$main_error=array();
$main_info=array();

// ================= VARIABLES ================= //
if(!isset($startday)) {
    $startday=getvar('startday');
}

if(!isset($endday)) {
    $endday=getvar('endday');
}

if(!isset($cost_price)) {
    $cost_price=getvar('cost_price');
}

if(!isset($cost_price_hp)) {
    $cost_price_hp=getvar('cost_price_hp');
}

if(!isset($cost_price_hc)) {
    $cost_price_hc=getvar('cost_price_hc');
}

if(!isset($cost_type)) {
    $cost_type=getvar('cost_type');
}

if(!isset($start_hc)) {
    $start_hc=getvar("start_hc",$main_error);
}

if(!isset($stop_hc)) {
    $stop_hc=getvar("stop_hc",$main_error);
}

if(!isset($select_plug)) {
    $select_plug=getvar('select_plug');
}

if(!isset($submit_cost)) {
    $submit_cost=getvar("submit_cost");
}


$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$price=get_configuration("COST_PRICE",$main_error);
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);
$resume="";
$lang=$_COOKIE['LANG'];

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


//Computing cost value:
if((strcmp($select_plug,"distinct_all")!=0)&&(strcmp($select_plug,"all")!=0)) {
    if((isset($submit_cost))&&(!empty($submit_cost))) {
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

    $title=$plugs_infos[$select_plug-1]['PLUG_NAME'];
    $color_cost=$GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'][$select_plug-1];


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
        if((isset($submit_cost))&&(!empty($submit_cost))) {
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
       ) ; 
    }


    if(strcmp($select_plug,"all")==0) {
        $title=__('PRICE_SELECT_ALL_PLUG');
        $color_cost = "purple";

        $cost_real=0;
        $cost_theo=0;
        foreach($data_price as $data) {
            $cost_real=$data['real']+$cost_real;
            $cost_theo=$data['theorical']+$cost_theo;
        }

        unset($data_price);
        $data_price[]= array(
            "number" => "$select_plug",
            "theorical" => "$cost_theo",
            "real" => "$cost_real",
            "title" => "$title",
            "color" => "$color_cost"
        );
    } 

}

//Get and format resume for cost configuration
$resume=get_cost_summary($main_error);

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

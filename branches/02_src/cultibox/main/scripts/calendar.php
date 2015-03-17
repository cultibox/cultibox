<?php

// Compute page time loading for debug option
$start_load = getmicrotime();


// ================= VARIABLES ================= //
$main_error=array();
$main_info=array();
$informations = Array(); //Aray containing data from the informations table or the log.txt file

$calendar_start=getvar('calendar_startdate'); //Variable used when user add a grown calendar to a specific date
if((!isset($calendar_start))||(empty($calendar_start))) {
    $calendar_start=date('Y')."-".date('m')."-".date('d'); //If user didn't had a grown calendar, today's date is used for the form
}

$title_list = calendar\get_title_list(); //Get list of titles available from the database to be used in the calendar form
    
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


// Read program index
$program_index = array();
program\get_program_index_info($program_index);

$count_program_index=false;
foreach ($program_index as $key => $value) {
    // Don'y display current event
    if ($value['id'] != 1) {
        $count_program_index=true;
        break;
    }
}

//Get the important event list for the previous and next week to display:
$important_list = array();
$important_list = calendar\get_important_event_list($main_error); //Get import event list from database

$substrat=array();
$product=array();

// List XML file find in folder
foreach(glob('../../xml/*.{xml}', GLOB_BRACE) as $entry) {
    $rss_file = file_get_contents($entry);
    $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);

    foreach ($xml as $tab) {
        if(is_array($tab)) {
            if((array_key_exists('substrat', $tab))
                &&(array_key_exists('marque', $tab))
                &&(array_key_exists('periode', $tab)))
            {
                $substrat[]=ucwords(strtolower($tab['substrat']));
                $product[]= array(
                    "marque" => ucwords(strtolower($tab['marque'])),
                    "periode" => ucwords(strtolower($tab['periode'])),
                    "substrat" => ucwords(strtolower($tab['substrat']))
                );
            }
        }
    }   
}

$substrat=array_unique($substrat);
asort($substrat);

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

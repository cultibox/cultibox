<?php

require_once('../../libs/config.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');

// Retrieve start and end param (Unix time format (s))
$sd_card = $_GET['sd_card'];

// Create all plugXX programm       
// Read program index       
$program_index = array();       
program\get_program_index_info($program_index);         
        
// Foreach programm, create the programm        
foreach ($program_index as $key => $value)      {       
    // Read from database program       
    $program = create_program_from_database($main_error,$value['program_idx']);         
         
    // SAve programm on SD      
    save_program_on_sd($sd_card,$program,"plu" . $value['plugv_filename']);         
}       
  

$data = array();
calendar\read_event_from_db($data);

// Read event from XML
foreach (calendar\get_external_calendar_file() as $fileArray)
{
    if ($fileArray['activ'] == 1)
    {
        calendar\read_event_from_XML($fileArray['filename'],$data);
    }
}

if(!check_sd_card($sd_card)) {
    $main_error[]=__('ERROR_WRITE_CALENDAR');
} else {
    if(!write_calendar($sd_card,$data,$main_error)) {
        $main_error[]=__('ERROR_WRITE_CALENDAR');
    }

    $plgidx=create_plgidx($data);
    if(count($plgidx)>0) {
        write_plgidx($plgidx,$sd_card);
    }
} 

?>

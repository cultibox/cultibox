<?php

require_once('../../libs/config.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');

// Retrieve start and end param (Unix time format (s))
$start = $_GET['start'];
$end = $_GET['end'];

// Initializes a container array for all of the calendar events
$jsonArray = array();

// Initialise ID index
$id = 0 ;
$event=array();


// Gets event from db (Must be in first and before read_event_from_XML else id are not correctly incremented) !!!!!!!
$id = calendar\read_event_from_db ($event,$start,$end);

// List XML file find in folder
$files = glob('../../xml/permanent/*.{xml}', GLOB_BRACE);
foreach($files as $file) {
    // Gets element from XML
    $id = calendar\read_event_from_XML ($file,$event ,$id + 1,$start,$end);
}

echo json_encode($event);

?>

<?php

require_once('../../libs/config.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');

// Retrieve start and end param (Unix time format (s))
$sd_card = $_GET['sd_card'];

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

if(!write_calendar($sd_card,$data,$main_error)) {
    $main_error[]=__('ERROR_WRITE_CALENDAR');
}

?>

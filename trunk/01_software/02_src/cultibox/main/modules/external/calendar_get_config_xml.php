<?php

require_once('../../libs/config.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');

// After creating the XML available file list, checking that each file is an external file like moon calendar
$xml_list = calendar\get_external_calendar_file(); //Get the list of the xml file is the mail/xml directory to add event from those files

echo json_encode($xml_list);

?>

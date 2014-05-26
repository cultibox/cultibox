<?php

require_once('../../libs/config.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');

echo calendar\set_external_calendar_file($_GET['filename'], $_GET['checked']);

?>

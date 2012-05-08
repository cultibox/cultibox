<?php

if (!isset($_SESSION)) {
	session_start();
}

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$error="";
$info="";

if((isset($lang))&&(!empty($lang))) {
	insert_configuration("LANG",$lang,$error);
} else {
	$lang=get_configuration("LANG",$error);
}

set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');

if((!isset($sd_card))||(empty($sd_card))) {
        $sd_card=get_sd_card();
}

include('main/templates/calendar.html');

?>

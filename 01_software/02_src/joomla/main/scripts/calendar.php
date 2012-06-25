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

if(!isset($pop_up)) {
        $pop_up = get_configuration("SHOW_POPUP",$error);
}


if((isset($sd_card))&&(!empty($sd_card))) {
	check_and_copy_firm($sd_card,$error);
        $data=create_calendar_from_database($error);
	if(count($data)>0) {
		write_calendar($sd_card,$data,$error);
	}
} 
include('main/templates/calendar.html');

?>

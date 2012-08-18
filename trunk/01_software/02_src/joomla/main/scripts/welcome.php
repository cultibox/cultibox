<?php


if (!isset($_SESSION)) {
	session_start();
}


require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$error="";
$info="";
$program="";
$sd_card="";

$lang=get_configuration("LANG",$error);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');


if((!isset($sd_card))||(empty($sd_card))) {
        $sd_card=get_sd_card();
}

if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($error);
   save_program_on_sd($sd_card,$program,$error,$info);
   check_and_copy_firm($sd_card,$error);
} 

include('main/templates/welcome.html');

?>

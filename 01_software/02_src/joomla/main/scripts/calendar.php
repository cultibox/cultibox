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

if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($error);
   if(!compare_program($program,$sd_card)) {
      $info=$info.__('UPDATED_PROGRAM');
      $pop_up_message=clean_popup_message(__('UPDATED_PROGRAM'));
      save_program_on_sd($sd_card,$program,$error,$info);
   }
   check_and_copy_firm($sd_card,$error);
} else {
        $error=$error.__('ERROR_SD_CARD_CONF');
}

if(!isset($pop_up)) {
   $pop_up = get_configuration("SHOW_POPUP",$error);
}


if((isset($sd_card))&&(!empty($sd_card))) {
   $data=create_calendar_from_database($error);
   if(count($data)>0) {
      write_calendar($sd_card,$data,$error);
   }
   $info=$info.__('INFO_SD_CARD').": $sd_card";
}


include('main/templates/calendar.html');

?>

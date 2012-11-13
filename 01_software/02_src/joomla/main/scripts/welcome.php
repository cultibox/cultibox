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
$update=get_configuration("CHECK_UPDATE",$error);
$version=get_configuration("VERSION",$error);
$wizard=true;
$nb_plugs = get_configuration("NB_PLUGS",$error);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');

if(isset($nb_plugs)&&(!empty($nb_plugs))) {
    if(check_programs($nb_plugs)) {
        $wizard=false;  
    }
}


if((!isset($sd_card))||(empty($sd_card))) {
        $sd_card=get_sd_card();
}

if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($error);
   save_program_on_sd($sd_card,$program,$error);
   check_and_copy_firm($sd_card,$error);
   check_and_copy_log($sd_card,$error);
} 


if(strcmp("$update","True")==0) {
      $ret=array();
      check_update_available($ret,$error);
      foreach($ret as $file) {
         if(count($file)==4) {
               if(strcmp("$version","$file[1]")==0) {
                  $tmp="";
                  $tmp=__('INFO_UPDATE_AVAILABLE');
                  $tmp=str_replace("</li>","<a href=".$file[3]." target='_blank'>".$file[2]."</a></li>",$tmp);
                  $info=$info.$tmp;
               }
            }
      }
}

include('main/templates/welcome.html');

?>

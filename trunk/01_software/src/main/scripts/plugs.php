<?php

if (!isset($_SESSION)) {
	session_start();
}

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$lang=get_configuration("LANG",$return);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');

$return="";
$info="";
$nb_plugs=get_configuration("NB_PLUGS",$return);
$update_program=false;

if((!isset($sd_card))||(empty($sd_card))) {
	$sd_card=get_sd_card();
}

if((!empty($sd_card))&&(isset($sd_card))) {
	$program=create_program_from_database();
	save_program_on_sd($sd_card,$program,$return,$info);
} else {
        $return=$return.__('ERROR_SD_CARD_CONF');
}

for($nb=1;$nb<=$nb_plugs;$nb++) {
	$name=getvar("plug_name${nb}");
	$type=getvar("plug_type${nb}");
	$tolerance=getvar("plug_tolerance{$nb}");

	if((!empty($name))&&(isset($name))) {
		insert_plug_conf("PLUG_NAME",$nb,$name,$return);
		$update_program=true;
	}
	

	if((!empty($type))&&(isset($type))) {
		insert_plug_conf("PLUG_TYPE",$nb,$type,$return);
		$update_program=true;
        }

	if((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0)) {
		if((!empty($tolerance))&&(isset($tolerance))) {
			if(check_tolerance_value($type,$tolerance,$return)) {
				insert_plug_conf("PLUG_TOLERANCE",$nb,$tolerance,$return);
				$update_program=true;
			}
		}
	} else {
		insert_plug_conf("PLUG_TOLERANCE",$nb,0,$return);
	} 

	$plug_name{$nb}=get_plug_conf("PLUG_NAME",$nb,$return);
	$plug_type{$nb}=get_plug_conf("PLUG_TYPE",$nb,$return);
	$plug_tolerance{$nb}=get_plug_conf("PLUG_TOLERANCE",$nb,$return);
}
if(($update_program)&&(empty($return))) {
        $info=$info.__('VALID_UPDATE_CONF');
}


if((isset($sd_card))&&(!empty($sd_card))) {
        $info=$info.__(INFO_SD_CARD).": $sd_card";
       	$plugconf=create_plugconf_from_database($nb_plugs);
	if(count($plugconf)>0) {
		write_plugconf($plugconf,$sd_card,$out);
	}
}


include('main/templates/plugs.html');

?>

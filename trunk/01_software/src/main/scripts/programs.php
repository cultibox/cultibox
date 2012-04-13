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
$nb_plugs=get_configuration("NB_PLUGS",$return);

for($nb=1;$nb<=$nb_plugs;$nb++) {
	$name=getvar("plug_name${nb}");
	$type=getvar("plug_type${nb}");
	$tolerance=getvar("plug_tolerance{$nb}");

	if((!empty($name))&&(isset($name))) {
		insert_plug_conf("PLUG_NAME",$nb,$name,$return);
	}
	

	if((!empty($type))&&(isset($type))) {
		insert_plug_conf("PLUG_TYPE",$nb,$type,$return);
        }

	if((!empty($tolerance))&&(isset($tolerance))) {
		insert_plug_conf("PLUG_TOLERANCE",$nb,$tolerance,$return);
        }

	$plug_name{$nb}=get_plug_conf("PLUG_NAME",$nb,$return);
	$plug_type{$nb}=get_plug_conf("PLUG_TYPE",$nb,$return);
	$plug_tolerance{$nb}=get_plug_conf("PLUG_TOLERANCE",$nb,$return);
}

include('main/templates/programs.html');

?>

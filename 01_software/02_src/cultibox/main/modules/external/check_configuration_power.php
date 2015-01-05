<?php 

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/config.php');

$message="";
if((isset($_GET['list_power']))&&(!empty($_GET['list_power']))) {
    $list=explode("-",$_GET['list_power']);
    $main_error=array();
    $nb=array();
    foreach($list as $plug) {
        if(!check_configuration_power($plug,$main_error)) {
              $nb[]=$plug;
        }
    }   

    if(count($nb)>0) {
        if(count($nb)==1) {
            $message=__('ERROR_POWER_PLUG')." ".$nb[0]." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='/cultibox/index.php?menu=plugs&selected_plug=".$nb[0]."' class='note_link' id='plug_pwr_link'>".__('HERE')."</a>";
        } else {
            $tmp_number="";
            foreach($nb as $number) {
               if(strcmp($tmp_number,"")!=0) {
                   $tmp_number=$tmp_number.", ";
                }
                $tmp_number=$tmp_number.$number;
            }
            $message=__('ERROR_POWER_PLUGS')." ".$tmp_number." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='/cultibox/index.php?menu=plugs' class='note_link'>".__('HERE')."</a>";
        }
    }
}
echo json_encode("$message");

?>

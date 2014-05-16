<?php

//Pour mettre à jour la table sensors depuis le fichier index
//de la carte SD. FOnction appelée lors de 

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');
require_once('../../libs/utilfunc_sd_card.php');

if((isset($_GET['sd_card']))&&(!empty($_GET['sd_card']))) {
    $sd_card=$_GET['sd_card'];
}

if((!isset($sd_card))||(empty($sd_card))) {
    echo "NOK";
    return 0;
} 


$current_index=get_sensor_type($sd_card,date('m'),date('d'));
$chk_value=false;
foreach($current_index as $tst_index) {
    if(strcmp("$tst_index","0")!=0) {
        $chk_value=true;
        break;
    }
}

if($chk_value) {
    update_sensor_type($current_index);
    clean_index_file($sd_card);
}

echo "0";

?>

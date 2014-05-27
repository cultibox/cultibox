<?php

//Pour mettre à jour la table sensors depuis le fichier index
//de la carte SD. Fonction appelée lors de 

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

// Read index file and update sensors type
$sensor_type = array(); 
if (get_sensor_type($sd_card,$sensor_type))
{
    // Update database with sensors
    update_sensor_type($sensor_type);
}

// Clean index file
clean_index_file($sd_card);

echo "0";

?>

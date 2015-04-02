<?php

$wifi_list=array();

exec("sudo /usr/sbin/iw wlan0 scan ap-force|/bin/grep SSID|/usr/bin/awk -F\": \" '{print $2}'",$wifi_net_list,$error);
$wifi_list=array_unique($wifi_net_list);


foreach($wifi_list as $list) {
    if((strpos(trim($list),"cultipi_")!=0)&&(strcmp(str_replace(" ","",trim($list)),"")!=0)) {
        $wifi_list[]=trim($list);
    }
}
echo json_encode($wifi_list);

?>

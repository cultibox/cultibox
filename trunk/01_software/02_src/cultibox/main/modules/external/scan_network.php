<?php

$wifi_list=array();

exec("sudo /sbin/iwlist wlan0 scan |/bin/grep ESSID|/usr/bin/awk -F \"\\\"\" '{print $2}'",$wifi_net_list,$error);
foreach($wifi_net_list as $list) {
    if((strpos("$list","cultipi_")!==0)&&(strcmp("$list","")!=0)) {
        $wifi_list[]=$list;
    }
}
echo json_encode($wifi_list);

?>

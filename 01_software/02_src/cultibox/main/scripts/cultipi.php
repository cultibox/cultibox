<?php

// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a COOKIE variable and the function __('$msg') from utilfunc.php library to print messages
$main_error=array();
$main_info=array();

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) {
    if((!isset($sd_card))||(empty($sd_card))) {
        $sd_card=get_sd_card();
    }
} else {
    $sd_card = $GLOBALS['CULTIPI_CONF_TEMP_PATH'];
    if((!is_file($sd_card."/cnf/plg/pluga"))||(!is_file($sd_card."/cnf/prg/plugv"))) {
        if(strpos($_SERVER['REMOTE_ADDR'],"10.0.0.")!==false) {
            check_and_update_sd_card($sd_card,$info,$error,false);
        }
    }
}


// By default the expanded menu is the user interface menu
if((!isset($submenu))||(empty($submenu))) {
    $submenu="synoptic_ui";
}


if((!isset($sd_card))||(empty($sd_card))) {
    setcookie("CHECK_SD", "False", time()+1800,"/",false,false);
}


$net_config=parse_network_config();

$wire_enable=find_config($net_config,"eth0","iface eth0","bool");
$wire_dhcp=find_config($net_config,"eth0","iface eth0 inet dhcp","bool");
$wire_static=find_config($net_config,"eth0","iface eth0 inet static","bool");
$wire_address=find_config($net_config,"eth0","address","val");
$wire_mask=find_config($net_config,"eth0","netmask","val");
$wire_gw=find_config($net_config,"eth0","gateway","val");
if(strcmp("$wire_mask","")==0) $wire_mask="255.255.255.0";
if(strcmp("$wire_gw","")==0) $wire_gw="0.0.0.0";


$wifi_enable=find_config($net_config,"wlan0","iface wlan0","bool");
$wifi_dhcp=find_config($net_config,"wlan0","iface wlan0 inet dhcp","bool");
$wifi_static=find_config($net_config,"wlan0","iface wlan0 inet static","bool");

$wifi_address=find_config($net_config,"wlan0","address","val");
$wifi_mask=find_config($net_config,"wlan0","netmask","val");
$wifi_gw=find_config($net_config,"wlan0","gateway","val");

if(strcmp("$wifi_address","10.0.0.100")==0) {
    $wifi_address="";
    $wifi_mask="255.255.255.0";
}

if(strcmp("$wifi_mask","")==0) $wifi_mask="255.255.255.0";
if(strcmp("$wifi_gw","")==0) $wifi_gw="0.0.0.0";


$eth_phy=get_phy_addr("eth0");
$wlan_phy=get_phy_addr("wlan0");

exec("sudo /sbin/iwlist wlan0 scan |/bin/grep ESSID|/usr/bin/awk -F \"\\\"\" '{print $2}'",$wifi_net_list,$error);


if(find_config($net_config,"wlan0","wpa-psk","bool")) {
    $wifi_key_type="WPA AUTO";
    $wifi_password=find_config($net_config,"wlan0","wpa-psk ","val");
    $wifi_ssid=find_config($net_config,"wlan0","wpa-ssid","val");
} else if(find_config($net_config,"wlan0","wireless-key","val")) {
    $wifi_key_type="WEP";
    $wifi_password=find_config($net_config,"wlan0","wireless-key","val");
    $wifi_ssid=find_config($net_config,"wlan0","wireless-essid","val");
} else {
    $wifi_key_type="NONE";
    $wifi_ssid=find_config($net_config,"wlan0","wireless-essid","val");
}


if(strpos("$wifi_ssid","cultipi_")===0) $wifi_ssid="";


//Compute time loading for debug option
$end_load = getmicrotime();

if($GLOBALS['DEBUG_TRACE']) {
    echo __('GENERATE_TIME').": ".round($end_load-$start_load, 3) ." secondes.<br />";
    echo "---------------------------------------";
    aff_variables();
    echo "---------------------------------------<br />";
    memory_stat();
}

?>

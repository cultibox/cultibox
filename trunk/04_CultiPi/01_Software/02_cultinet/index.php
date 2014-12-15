<?php

//Set lang:
if((isset($_COOKIE['LANG']))&&(!empty($_COOKIE['LANG']))) {
    $lang=$_COOKIE['LANG'];
} else {
    $lang="fr_FR";
    setcookie("LANG", "$lang", time()+(86400 * 365),"/",false,false);
    header('Location: /cultinet/');
}

//Minimal library required:
require_once 'main/libs/utilfunc.php';
require_once 'main/libs/l10n.php';
require_once 'main/libs/config.php';

__('LANG');

$net_config=parse_network_config(); 

$wire_enable=find_config($net_config,"eth0","iface eth0","bool");
$wire_dhcp=find_config($net_config,"eth0","iface eth0 inet dhcp","bool");
$wire_static=find_config($net_config,"eth0","iface eth0 inet static","bool");
$wire_address=find_config($net_config,"eth0","address","val");
$wire_mask=find_config($net_config,"eth0","netmask","val");

$wifi_enable=find_config($net_config,"wlan0","iface wlan0","bool");
$wifi_dhcp=find_config($net_config,"wlan0","iface wlan0 inet dhcp","bool");
$wifi_static=find_config($net_config,"wlan0","iface wlan0 inet static","bool");

$wifi_address=find_config($net_config,"wlan0","address","val");
$wifi_mask=find_config($net_config,"wlan0","netmask","val");

$eth_phy=get_phy_addr("eth0");
$wlan_phy=get_phy_addr("wlan0");

exec("sudo /sbin/iwlist wlan0 scan |/bin/grep ESSID|/usr/bin/awk -F \"\\\"\" '{print $2}'",$wifi_net_list,$error);


if(find_config($net_config,"wlan0","wpa-psk","bool")) {
    $wifi_key_type="WPA (TKIP + AES)";
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

?>
<!DOCTYPE HTML>
<head>
    <title>Cultinet</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link href="/cultinet/favicon.ico" rel="shortcut icon"/>
    <link rel="stylesheet" href="/cultinet/css/base.css?v=<?=@filemtime('css/base.css')?>" />
    <link rel="stylesheet" href="/cultinet/css/layout.css?v=<?=@filemtime('css/layout.css')?>" />
    <link rel="stylesheet" href="/cultinet/fonts/opensans.css?v=<?=@filemtime('fonts/opensans.css')?>" />

    <link rel="stylesheet" media="all" type="text/css" href="/cultinet/main/libs/css/jquery-ui-1.8.19.custom.css?v=<?=@filemtime('main/libs/css/jquery-ui-1.8.19.custom.css')?>" />
    <link rel="stylesheet" media="all" type="text/css" href="/cultinet/main/libs/css/cultibox.css?v=<?=@filemtime('main/libs/css/cultibox.css')?>" />

    <script type="text/javascript" src="/cultinet/main/libs/js/jquery-1.8.3.js?v=<?=@filemtime('main/libs/js/jquery-1.8.3.js')?>"></script>
    <script type="text/javascript" src="/cultinet/main/libs/js/jquery-ui-1.9.2.custom.js?v=<?=@filemtime('main/libs/js/jquery-ui-1.9.2.custom.js')?>"></script>
    <script type="text/javascript" src="/cultinet/main/libs/js/jquery-ui-1.9.2.custom.min.js?v=<?=@filemtime('main/libs/js/jquery-ui-1.9.2.custom.min.js')?>"></script>
    <script type="text/javascript" src="/cultinet/main/libs/js/jquery.blockUI.js?v=<?=@filemtime('main/libs/js/jquery.blockUI.js')?>"></script>
</head>


<body id="page" class="page">
    <?php include_once 'main/libs/js/cultibox.js'; ?>


    <div id="page-bg">
        <div>
            <div class="wrapper grid-block">
                <header id="header">
                    <div id="headerbar" class="grid-block">
                        <div class="mod-languages">
                            <ul class="lang-inline">
                                <li class="translate"><a href="/cultinet/index.php?lang=en_GB" id="en_GB"><img src="/cultinet/main/libs/img/en.gif" alt="Translate all texts on the site in English (UK)" title="Translate all texts on the site in English (UK)" /></a></li>

                                <li class="translate"><a href="/cultinet/index.php?lang=fr_FR" id="fr_FR"><img src="/cultinet/main/libs/img/fr.gif" alt="Traduire tous les textes du site en Français (FR)" title="Traduire tous les textes du site en Français (FR)" /></a></li>

                                <li class="translate"><a href="/cultinet/index.php?lang=it_IT" id="it_IT"><img src="/cultinet/main/libs/img/it.gif" alt="Tradurre tutti i testi del sito in Italiano (IT)" title="Tradurre tutti i testi del sito in Italiano (IT)" /></a> </li>

                               <li class="translate"><a href="/cultinet/index.php?lang=es_ES" id="es_ES"><img src="/cultinet/main/libs/img/es.gif" alt="Traducir los textos del sitio en Español (ES)" title="Traducir los textos del sitio en Español (ES)" /></a></li>

                                <li class="translate"><a href="/cultinet/index.php?lang=de_DE" id="de_DE"><img src="/cultinet/main/libs/img/de.gif" alt="Übersetzen alle Texte auf der Website Deutsch (DE)" title="Übersetzen alle Texte auf der Website Deutsch (DE)" /></a></li>
                            </ul>
                        </div>
                    </div>
                </header>

                <div id="main" class="grid-block">
                    <div id="maininner" class="grid-box">
                        <div id="content" class="grid-block">

<br />
<table class="table_width">
    <tr>
        <td><label class="title_conf"><?php echo __('CONFIGURE_TITLE_CULTIBOX'); ?></label></td>
    </tr>
    <tr>
        <td class="p_center_subtitle">
            <?php echo __('CONFIGURE_SUBTITLE'); ?>
        </td>
    </tr>
</table>
<br /><br />
<noscript>
    <div id="compat-js" class="text_info">
       <p><?php echo __('ENABLE_JAVASCRIPT'); ?></p>
    </div>
    <br />
</noscript>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="configform" id="configform">

<!-- Start of wire conf tab -->
<table class="table_width">
    <tr>
        <td class="conf-marge"></td>
        <td class="conf-title"><?php echo __('WIRE_CONF'); ?>:<img src="main/libs/img/infos.png" alt="" title="<?php if(strcmp("$eth_phy","")==0) { echo __('NO_IFACE_FOUND'); } else { echo __('PHY_ADDR').": ".strtoupper($eth_phy); } ?>" /></td>
        <td class="conf-field">
            <select name="activ_wire" id="activ_wire" <?php if(strcmp("$eth_phy","")==0) { echo " disabled"; } ?>>
            <option value="True" <?php if($wire_enable) echo "selected"; ?>><?php echo __('ACTIVATED'); ?></option>
            <option value="False" <?php if((!$wire_enable)||(strcmp("$eth_phy","")==0)) echo "selected"; ?>><?php echo __('DEACTIVATED'); ?></option>
            </select>
        </td>
    </tr>
</table>

<div id="wire_interface" <?php if((!$wire_enable)||(strcmp("$eth_phy","")==0)) echo 'style="display:none"'; ?>>
        <p class="p_center">
           <input type="radio" name="wire_type" value="static" <?php if($wire_static) echo 'checked'; ?>><?php echo __('MANUAL_IP_ADDR'); ?><img src="main/libs/img/infos.png" alt="" title="<?php echo __('TOOLTIP_MANUAL_IP'); ?>" /><input type="radio" name="wire_type" value="dhcp" <?php if($wire_dhcp) echo 'checked'; ?>><?php echo __('DYNAMIC_IP_ADDR'); ?><img src="main/libs/img/infos.png" alt="" title="<?php echo __('TOOLTIP_DYNAMIC_IP'); ?>" /></p>

        <div id="wire_static" <?php if(!$wire_static) echo 'style="display:none"'; ?>>
        <table class="table_width">
        <tr>
            <td class="conf-marge-field"></td>
            <td class="conf-field-title"><?php echo __('IP_ADDR'); ?>:<img src="main/libs/img/infos.png" alt="" title="<?php echo __('TOOLTIP_IP_ADDR'); ?>" /></td>
            <td class="conf-field-field"><input type="text" size="15" id="wire_address" name="wire_address" maxlength=15 value="<?php echo $wire_address; ?>"></td>
            <td><div id="error_wire_ip" style="display:none" class="error_field"><img src='/cultinet/main/libs/img/arrow_error.png' alt=''><?php echo __('ERROR_WIFI_IP'); ?></div></td>
         </tr>
         <tr>
            <td></td>
            <td><?php echo __('IP_MASK'); ?>:<img src="main/libs/img/infos.png" alt="" title="<?php echo __('TOOLTIP_MASK_ADDR'); ?>" /></td>
            <td><input type="text" size=15 maxlength=15 id="wire_mask" name="wire_mask" value="<?php echo $wire_mask; ?>"></td>
            <td><div id="error_wire_mask" style="display:none" class="error_field"><img src='/cultinet/main/libs/img/arrow_error.png' alt=''><?php echo __('ERROR_WIFI_IP_MASK'); ?></div></td>
         </tr>
         </table>
        </div>
</div>
<br />
<hr />
<br /><br />
<table class="table_width">
    <tr>
        <td class="conf-marge"></td>
        <td class="conf-title"><?php echo __('WIRELESS_CONF'); ?>:<img src="main/libs/img/infos.png" alt="" title="<?php if(strcmp("$wlan_phy","")==0) { echo __('NO_IFACE_FOUND'); } else { echo __('PHY_ADDR').": ".strtoupper($wlan_phy); } ?>" /></td>
        <td>
            <select name="activ_wifi" id="activ_wifi">
                <option value="True" <?php if(strcmp("$wifi_enable","True")==0) echo "selected"; ?>><?php echo __('ACTIVATED'); ?></option>
                <option value="False" <?php if(strcmp("$wifi_enable","False")==0) echo "selected"; ?>><?php echo __('DEACTIVATED'); ?></option>
            </select>
        </td>
    </tr>
</table>
<br />
<div id="wifi_interface" <?php if((!$wifi_enable)||(strcmp("$wlan_phy","")==0)) { echo 'style="display:none"'; } ?>>
    <table class="table_width">
        <tr>
            <td class="conf-marge-field-wifi"></td>
            <td class="conf-field-title-wifi"><?php echo __('CONFIGURE_WIFI_SSID'); ?>:<img src="main/libs/img/infos.png" alt="" title="<?php echo __('TOOLTIP_WIFI_SSID'); ?>" /></td>
             <td class="conf-field-field-wifi">
                <input  type="text" size="15" name="wifi_ssid" id="wifi_ssid" value="<?php echo $wifi_ssid; ?>" /><img src="/cultinet/main/libs/img/wifi.ico" id="wifi_scan" alt="" title="<?php echo __('WIFI_SCAN_ESSID'); ?>" />
            </td>
                <td><div id="error_wifi_ssid" style="display:none" class="error_field"><img src='/cultinet/main/libs/img/arrow_error.png' alt=''><?php echo __('ERROR_SSID_VALUE'); ?></div></td>
            </tr>
           <tr>
              <td></td>
              <td><?php echo __('CONFIGURE_WIFI_KEY_TYPE'); ?>:<img src="main/libs/img/infos.png" alt="" title="<?php echo __('TOOLTIP_WIFI_KEY_TYPE'); ?>" /></td>
              <td>
                    <select name="wifi_key_type" id="wifi_key_type">
                        <?php foreach( $GLOBALS['WIFI_KEY_TYPE_LIST'] as $key) { ?>
                            <option value="<?php echo $key; ?>" <?php if($key == $wifi_key_type) echo 'selected'; ?>><?php echo $key; ?></option>
                        <?php } ?>
                    </select>
              </td>
              <td></td>
           </tr>
           <tr>
                <td></td>
                <td><?php echo __('CONFIGURE_WIFI_PASSWORD'); ?>:</td>
                <td>
                    <input  type="password" size="15" 
                            name="wifi_password" id="wifi_password" value="" <?php if(strcmp("$wifi_key_type","NONE")==0) { echo 'disabled'; } ?> />
                <img src='/cultinet/main/libs/img/eye.png' alt='' title="<?php echo __('SHOW_CURRENT_PWD'); ?>" id="eyes" <?php if(strcmp("$wifi_key_type","NONE")==0) echo 'style="display:none"';  ?>></td>
                <td>
                    <div id="error_wifi_password" style="display:none" class="error_field"><img src='/cultinet/main/libs/img/arrow_error.png' alt=''><?php echo __('ERROR_PASSWORD_VALUE'); ?></div>
                    <div id="error_empty_password" style="display:none" class="error_field"><img src='/cultinet/main/libs/img/arrow_error.png' alt=''><?php echo __('ERROR_EMPTY_PASSWORD'); ?></div>
                    <div id="error_password_wep" style="display:none" class="error_field"><img src='/cultinet/main/libs/img/arrow_error.png' alt=''><?php echo __('ERROR_WEP_KEY_TYPE'); ?></div>
                    <div id="error_password_wpa" style="display:none" class="error_field"><img src='/cultinet/main/libs/img/arrow_error.png' alt=''><?php echo __('ERROR_WPA_KEY_TYPE'); ?></div>
                </td>
           </tr>
           <tr>
                <td></td>
                <td><?php echo __('CONFIGURE_WIFI_PASSWORD_CONFIRM'); ?>:</td>
                <td>
                    <input type="password" size="15" 
                           name="wifi_password_confirm" id="wifi_password_confirm" value="" <?php if(strcmp("$wifi_key_type","NONE")==0) { echo 'disabled'; } ?>/>
                </td>
                <td><div id="error_wifi_password_confirm" style="display:none" class="error_field"><img src='/cultinet/main/libs/img/arrow_error.png' alt=''><?php echo __('ERROR_PASSWORD_VALUE'); ?></div></td>
           </tr>
           <tr>
                <td></td>
                <td><?php echo __('ADDR_TYPE'); ?>:</td>
                <td>
                    <input type="radio" name="wifi_type" value="static" <?php if($wifi_static) echo 'checked'; ?>><?php echo __('MANUAL_IP_ADDR'); ?><img src="main/libs/img/infos.png" alt="" title="<?php echo __('TOOLTIP_MANUAL_IP'); ?>" /><br /><input type="radio" name="wifi_type" value="dhcp" <?php if($wifi_dhcp) echo 'checked'; ?>><?php echo __('DYNAMIC_IP_ADDR'); ?><img src="main/libs/img/infos.png" alt="" title="<?php echo __('TOOLTIP_DYNAMIC_IP'); ?>" /></td>
                <td></td>   
           </tr>
</table>

<div id="manual_ip_wifi" <?php if(!$wifi_static) echo "style='display:none'"; ?>>
 <table class="table_width">
        <tr>
            <td class="conf-marge-field-wifi"></td>
            <td class="conf-field-title-wifi"><?php echo __('CONFIGURE_WIFI_IP'); ?>:<img src="main/libs/img/infos.png" alt="" title="<?php echo __('TOOLTIP_WIFI_IP'); ?>" /></td>
            <td class="conf-field-field-wifi"><input type="text" size="15" name="wifi_ip" id="wifi_ip" value="<?php echo $wifi_address; ?>" />
            <td><div id="error_wifi_ip" style="display:none" class="error_field"><img src='/cultinet/main/libs/img/arrow_error.png' alt=''><?php echo __('ERROR_WIFI_IP'); ?></div></td>
        </tr>
         <tr>
              <td></td>
              <td><?php echo __('CONFIGURE_WIFI_IP_MASK'); ?>:<img src="main/libs/img/infos.png" alt="" title="<?php echo __('TOOLTIP_MASK_ADDR'); ?>" /></td>
              <td><input type="text" size="15" name="wifi_mask" id="wifi_mask" value="<?php echo $wifi_mask; ?>" /></td>
              <td><div id="error_wifi_mask" style="display:none" class="error_field"><img src='/cultinet/main/libs/img/arrow_error.png' alt=''><?php echo __('ERROR_WIFI_IP_MASK'); ?></div></td>
        </tr>
</table>
</div>
</div>
</div>
<br />
<hr />
<br />
<p class="p_center">
    <input type="submit" id="submit_conf" name="submit_conf" value="<?php echo __('SAVE_CONF'); ?>">
</p>
</form>
<br />
<div id="empty_network_conf" style="display:none">
    <p><?php echo __('EMPTY_NETWORK_CONFIG'); ?></p>
</div>

<div id="error_network_file" style="display:none">
    <p><?php echo __('ERROR_NET_FILE_CREATION'); ?></p>
</div>

<div id="network_new_addr_set" style="display:none">
    <p><?php echo __('AVAILABLE_MODULE_SET'); ?></p>
</div>

<div id="error_restore_conf" style="display:none">
    <p><?php echo __('RESTORED_CONF'); ?></p>
</div>

<div id="wifi_essid_list" style="display:none" title="<?php echo __('WIFI_ESSID_SCAN_TITLE'); ?>">
    <?php if(count($wifi_net_list)>0) { 
            echo '<p>'.__('WIFI_SCAN_SUBTITLE').'</p>';
            $checked="checked";
            foreach($wifi_net_list as $essid) {
                echo '<b>'.$essid.' : </b><input type="radio" name="wifi_essid" value="'.$essid.'" '.$checked.' /><br />';
                $checked="";
            }
        }
    ?>
</div>



                        <div class="shortlogo"><img src="main/libs/img/shortlogo2.png" alt=""></div>
                    </div> 
                </div>
            </div>
            
            <footer id="footer" class="grid-block">
                    <p class="p_center">
                        <?php
                            echo "GPL-V3<br />";
                        ?>
                    </p>
            </footer>
        </div>
    </div>
</body>
</html>

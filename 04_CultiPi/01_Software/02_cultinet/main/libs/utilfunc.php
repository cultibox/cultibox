<?php

// {{{ __($msgkey, ...)
// ROLE get the translated message corresponding to $msgkey, the parameters may
// be used in $msgkey by using the sprintf syntax (%s)
// IN $msgkey
// RET string translated
static $__translations;
static $__translations_fallback;
static $string_lang;

function __() {
   global $__translations;
   global $__translations_fallback;
   global $string_lang;

   if (func_num_args() < 1) {
      die("ERROR: __() called without arguments");
      return "";
   }
   $args = func_get_args();

  
   if (!isset($__translations)) {
      $__translations = __translations_get($_COOKIE['LANG']);
      $__translations_fallback = __translations_get("fr_FR");

      if (empty($__translations_fallback)) {
         die("No translation file");
      }
      
      $string_lang = array($_COOKIE['LANG'] => $__translations);
   }
   
   $msg = $args[0];
   if (isset($__translations["$msg"])) {
      $msg = $__translations["$msg"];
   } elseif (isset($__translations_fallback["$msg"])) {
      $msg = $__translations_fallback["$msg"];
   } else {
      die("WARNING:L10N: no translation for '$msg'");
   }
   
   $args[0] = $msg;
   $ret = call_user_func_array('sprintf', $args);


   if(isset($args[1])) {
        return $ret;
   } else {
        return htmlentitiesOutsideHTMLTags($ret);
   }
}
//}}}

// {{{ htmlentitiesOutsideHTMLTags()
// ROLE encode a string in HTML and preserve HTML tags
// IN  $htmltext        text to encode
// RET text encoded in HTML
function htmlentitiesOutsideHTMLTags($htmlText)
{
    $matches = Array();
    $sep = '###HTMLTAG###';

    preg_match_all(":</{0,1}[a-z]+[^>]*>:i", $htmlText, $matches);

    $tmp = preg_replace(":</{0,1}[a-z]+[^>]*>:i", $sep, $htmlText);
    $tmp = explode($sep, $tmp);

    for ($i=0; $i<count($tmp); $i++) $tmp[$i] = htmlentities($tmp[$i]);
    $tmp = join($sep, $tmp);
    for ($i=0; $i<count($matches[0]); $i++) $tmp = preg_replace(":$sep:", $matches[0][$i], $tmp, 1);

    return $tmp;
}
// }}}


// {{{ parse_network_config()
// ROLE decode /etc/network/interfaces
// IN  
// RET array containing datas from /etc/network/interfaces ordered by interfaces
function parse_network_config() {
    $file="/etc/network/interfaces";
    $current=false; 
    $myArray=array();
    $myArray["first"]=array();

    if(file_exists($file)) {
        $config=file("$file");

        foreach($config as $conf) {
            $conf=trim($conf);
            $pos = strpos($conf, "#IFACE ");
            if($pos !== false) {
                  $current=substr($conf, 7,strlen($conf));
                  $myArray["${current}"]=array();
            } else {
                if($current) { 
                    $myArray["${current}"][]=$conf;
                } else {
                    $myArray["first"][]=$conf;
                }
            }
        }
    }
    return $myArray;
}
// }}}


// {{{ find_config()
// ROLE find configuration key in /Etc/netork/interface for a specific interface
// IN  $maArray     array containing value of the /etc/network/interfaces
//     $iface       interface to search
//     $key         key sentence to search
//     $type        val or bool to return the value or a boolean of the jey
// RET val or true or false depending configuration
function find_config($myArray,$iface,$key,$type="val") {
    if(count($myArray)<=1) return false;

    if(!array_key_exists(strtoupper($iface), $myArray)) return false;

    foreach($myArray[strtoupper($iface)] as $tab) {
        $pos=strpos($tab,$key);
        if($pos !== false) {
            if(strcmp($type,"val")==0) {
                $val=substr($tab, strlen($key)+1,strlen($tab)); 
                return $val;
            } else { 
                return true;
            }
        }

    }

    if(strcmp($type,"val")==0) {
        return "";
    } else {
        return false;
    }
}
//}}}


// {{{ get_phy_addr()
// ROLE get physical addr (MAC)
// IN  $iface   interface to check
// RET iface physical address
function get_phy_addr($iface) {
    $phy=exec("sudo /sbin/ifconfig ${iface}|/bin/grep HWaddr|/usr/bin/awk -F \" \" '{print $5}'");
    return trim($phy);
}
//}}}



// {{{ create_network_file()
// ROLE create the network configuration file
// IN  $myConf  array containing informations
// RET error number or -1 else
function create_network_file($myConf) {
    if(count($myConf)==0) return 2;
    
    $myArray=array();
    $myArray[]="# interfaces(5) file used by ifup(8) and ifdown(8)";
    $myArray[]="";
    $myArray[]="#IFACE LO";
    $myArray[]="auto lo";
    $myArray[]="iface lo inet loopback";
    $myArray[]="";

    if(strcmp($myConf['activ_wire'],"True")==0) {
        $myArray[]="#IFACE ETH0";
        $myArray[]="allow-hotplug eth0";
        $myArray[]="auto eth0";

        if(strcmp($myConf['wire_type'],"static")==0) {
            $myArray[]="iface eth0 inet static";
            $myArray[]="address ".$myConf['wire_ip'];
            $myArray[]="netmask ".$myConf['wire_mask'];
        } else {
            $myArray[]="iface eth0 inet dhcp";

        }
        $myArray[]="";

    }


    if(strcmp($myConf['activ_wifi'],"True")==0) {
        $myArray[]="#IFACE WLAN0";
        $myArray[]="allow-hotplug wlan0";
        $myArray[]="auto wlan0";

        if(strcmp($myConf['wifi_type'],"static")==0) {
            $myArray[]="iface wlan0 inet static";
            $myArray[]="address ".$myConf['wifi_ip'];
            $myArray[]="netmask ".$myConf['wifi_mask'];
        } else {
            $myArray[]="iface wlan0 inet dhcp";
        }

        switch($myConf['wifi_key_type']) {
            case "NONE":
                    $myArray[]="wireless-essid ".$myConf['wifi_ssid'];
                    break;
            case "WEP":
                    $myArray[]="wireless-essid ".$myConf['wifi_ssid'];
                    $myArray[]="wireless-key ".$myConf['wifi_password'];
                    break;

            case "WPA (TKIP + AES)":
                    $myArray[]="wpa-scan-ssid 1";
                    $myArray[]="wpa-ssid ".$myConf['wifi_ssid'];
                    $myArray[]="wpa-ap-scan 1";
                    $myArray[]="wpa-key-mgmt WPA-PSK";
                    $pwd=exec("/usr/bin/wpa_passphrase ".$myConf['wifi_ssid']." ".$myConf['wifi_password']."|/bin/grep psk=|/bin/grep -v \"#psk\"|/usr/bin/awk -F \"=\" '{print $2}'",$output,$err);
                    if(count($output)!=1) return 3;
                    $myArray[]="wpa-psk ".$output[0];
                    break;  
        }
    }

    //"WPA (TKIP + AES)","WPA (TKIP)","WPA (AES/CCMP)"


    if($f=fopen("/tmp/interfaces","w")) {
        foreach($myArray as $myInf) {
           fputs($f,"$myInf\n");
        }
    } else {
        return 4;
    }
    fclose($f);
    return 1;
}
// }}}


?>

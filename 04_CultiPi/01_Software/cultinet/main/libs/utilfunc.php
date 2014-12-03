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
    if(count($myConf)==0) return false;
    
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

        $myArray[]="wireless-essid ".$myConf['wifi_ssid'];

        $tmp_cmd=exec('sudo /sbin/iwlist wlan0 scan 2>&1 | grep -v "^wlan0" | grep -v "^$" | \
    sed -e "s/^\ *//" \
        -e "s/^Cell [0-9]\+ - //" \
        -e "s/^Address: /AP=/" \
        -e "s/^Quality:\([0-9]\+\)\/.*$/QUALITY=\1/" \
        -e "s/^.*Channel \([0-9]\+\).*$/CHANNEL=\1/" \
        -e "s/^ESSID:/ESSID=/" \
        -e "s/^Mode:/MODE=/" \
        -e "s/^Encryption key:/ENC=/" \
        -e "s/^[^#].*:.*//" | \
    tr "\n" "|" ',$output,$err);
            
        if(count($output)!=1) return false;
        $wifi_info=explode("||||||",$output[0]);

        if(count($tmp_cmd)==0) return false;

        $chan="";
        $mode="Master";
        foreach($wifi_info as $inf) {
                $pos = strpos($inf, $myConf['wifi_ssid']);
                if($pos !== false) {
                    $winf=explode("|",$inf);

                    if(count($winf)==0) return false;

                    foreach($winf as $data) {
                        $pos=strpos($data, "CHANNEL=");
                        if($pos !== false) {
                            $chan=substr($data, 8,strlen($data));
                        } else {
                            $pos=strpos($data, "MODE=");
                            if($pos !== false) {
                                $mode=substr($data, 5,strlen($data));
                             }
                        }
                    }
                }
        }

        if(strcmp("$chan","")!=0) {
            $myArray[]="wireless-channel ".$chan;
        }

        if(strcmp("$mode","Master")==0) {
            $mode="managed";
        } else {
            $mode="ad-hoc";
        }
        $myArray[]="wireless-mode ".$mode;

        switch($myConf['wifi_key_type']) {
            case "NONE":
                    break;
            case "WEP":
                    $myArray[]="wireless-key ".$myConf['wifi_password'];
                    break;

            case "WPA2":
                    $myArray[]="wpa-psk ".$myConf['wifi_password'];
                    break;  
        }
    }


    if($f=fopen("/tmp/interfaces","w")) {
        foreach($myArray as $myInf) {
           fputs($f,"$myInf\n");
        }
    }
    fclose($f);
    return -1;
}
// }}}


?>

<?php

// Define constant
define("ERROR_COPY_FILE", "2");
define("ERROR_WRITE_PROGRAM", "3");
define("ERROR_COPY_FIRM", "4");
define("ERROR_COPY_PLUGA", "5");
define("ERROR_COPY_PLUG_CONF", "6");
define("ERROR_COPY_TPL", "7");
define("ERROR_COPY_INDEX", "8");
define("ERROR_COPY_WIFI_CONF", "10");
define("ERROR_WRITE_SD_CONF", "11");
define("ERROR_WRITE_SD", "12");
define("ERROR_SD_NOT_FOUND", "13");

// {{{ check_and_update_sd_card()
// ROLE If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
// IN   $sd_card    sd card path 
// RET 0 if the sd card is updated, 1 if the sd card has been updated, return > 1 if an error occured
function check_and_update_sd_card($sd_card="",&$main_info_tab,&$main_error_tab) {

    // Check if SD card has been found
    if(empty($sd_card) || !isset($sd_card)  || $sd_card == "")
    {
        $main_error_tab[]=__('ERROR_SD_CARD');
        return ERROR_SD_NOT_FOUND;
    }

    // Alert user than an SD card was found
    $main_info[]=__('INFO_SD_CARD').": $sd_card";
    
    // Check if SD card can be writable
    if(!check_sd_card($sd_card))
        return ERROR_WRITE_SD;
        
    $program="";
    $conf_uptodate=true;

    /* TO BE DELETED */
    if(!compat_old_sd_card($sd_card)) {
        $main_error_tab[]=__('ERROR_COPY_FILE');
        return ERROR_COPY_FILE;
    }
    /* ************* */


    $program=create_program_from_database($main_error);
    if(!compare_program($program,$sd_card)) {
        $conf_uptodate=false;
        if(!save_program_on_sd($sd_card,$program)) {
            $main_error_tab[]=__('ERROR_WRITE_PROGRAM');
            return ERROR_WRITE_PROGRAM;
        }
    }

    $ret_firm=check_and_copy_firm($sd_card);
    if(!$ret_firm) {
        $main_error_tab[]=__('ERROR_COPY_FIRM'); 
        return ERROR_COPY_FIRM;
    } else if($ret_firm==1) {
        $conf_uptodate=false;
    }

    if(!compare_pluga($sd_card)) {
        $conf_uptodate=false;
        if(!write_pluga($sd_card,$main_error)) {
            $main_error_tab[]=__('ERROR_COPY_PLUGA');
            return ERROR_COPY_PLUGA;
        }
    }

    $plugconf=create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$main_error);
    if(count($plugconf)>0) {
        if(!compare_plugconf($plugconf,$sd_card)) {
            $conf_uptodate=false;
            if(!write_plugconf($plugconf,$sd_card)) {
                $main_error_tab[]=__('ERROR_COPY_PLUG_CONF');
                return ERROR_COPY_PLUG_CONF;
            }
        }
    }

    if(!check_and_copy_index($sd_card)) {
        $main_error_tab[]=__('ERROR_COPY_INDEX');
        return ERROR_COPY_INDEX;
    }

    if(!check_and_copy_plgidx($sd_card)) {
        $main_error_tab[]=__('ERROR_COPY_TPL');
        return ERROR_COPY_TPL;
    }

    $wifi_conf=create_wificonf_from_database($main_error,get_ip_address());
    if(!compare_wificonf($wifi_conf,$sd_card)) {
        $conf_uptodate=false;
        if(!write_wificonf($sd_card,$wifi_conf,$main_error)) {
            $main_error_tab[]=__('ERROR_COPY_WIFI_CONF');
            return ERROR_COPY_WIFI_CONF;
        }
    }

    $recordfrequency = get_configuration("RECORD_FREQUENCY",$main_error);
    $powerfrequency = get_configuration("POWER_FREQUENCY",$main_error);
    $updatefrequency = get_configuration("UPDATE_PLUGS_FREQUENCY",$main_error);
    $alarmenable = get_configuration("ALARM_ACTIV",$main_error);
    $alarmvalue = get_configuration("ALARM_VALUE",$main_error);
    $resetvalue= get_configuration("RESET_MINMAX",$main_error);
    $rtc=get_rtc_offset(get_configuration("RTC_OFFSET",$main_error));
    if("$updatefrequency"=="-1") {
        $updatefrequency="0";
    }

    if(!compare_sd_conf_file($sd_card,$recordfrequency,$updatefrequency,$powerfrequency,$alarmenable,$alarmvalue,"$resetvalue","$rtc")) {
        $conf_uptodate=false;
        if(!write_sd_conf_file($sd_card,$recordfrequency,$updatefrequency,$powerfrequency,"$alarmenable","$alarmvalue","$resetvalue","$rtc",$main_error)) {
            $main_error_tab[]=__('ERROR_WRITE_SD_CONF');
            return ERROR_WRITE_SD_CONF;
        }
    }

    if(!$conf_uptodate) {
        // Infor user that programms have been updated
        $main_info_tab[]=__('UPDATED_PROGRAM');
        return 0;
    }
    
    // Conf was up to date
    return 1; 
}
// }}}

// {{{ get_error_sd_card_update_message()
// ROLE transoform a check sd card configuration code into a an error message
// IN   $id    id of the message
// RET "" if there is no message to display, the message else
function get_error_sd_card_update_message($id=0) {
    switch($id) { //Id to check to get the current error message:
        case ERROR_COPY_FILE:  return __('ERROR_COPY_FILE');
        case ERROR_WRITE_PROGRAM:  return __('ERROR_WRITE_PROGRAM');
        case ERROR_COPY_FIRM:  return __('ERROR_COPY_FIRM');
        case ERROR_COPY_PLUGA:  return __('ERROR_COPY_PLUGA');
        case ERROR_COPY_PLUG_CONF:  return __('ERROR_COPY_PLUG_CONF');
        case ERROR_COPY_TPL:  return __('ERROR_COPY_TPL');
        case ERROR_COPY_INDEX:  return __('ERROR_COPY_INDEX');
        case ERROR_COPY_WIFI_CONF: return __('ERROR_COPY_WIFI_CONF');
        case ERROR_WRITE_SD_CONF: return __('ERROR_WRITE_SD_CONF');
        case ERROR_WRITE_SD: return __('ERROR_WRITE_SD');
        default: return "";
    }
}
// }}}

// {{{ copy_empty_big_file()
// ROLE copy an empty file to a new file destination
// IN $file     destination of the file
// RET true if the copy is errorless, false else
function sd_card_update_log_informations ($sd_card="") {

    if(empty($sd_card) || !isset($sd_card) || $sd_card == "")
        return ERROR_SD_NOT_FOUND;

    // The informations part to send statistics to debug the cultibox: 
    //      if the 'STATISTICS' variable into the configuration table from the database is set to 'True' informations will be send for debug
    $informations["cbx_id"]="";
    $informations["firm_version"]="";
    $informations["log"]="";
    
    // Read log.txt file and clear it
    find_informations("$sd_card/log.txt",$informations);
    copy_empty_big_file("$sd_card/log.txt");
    
    // If informations are defined in log.txt copy them into database
    if(strcmp($informations["cbx_id"],"")!=0) 
        insert_informations("cbx_id",$informations["cbx_id"]);
    if(strcmp($informations["firm_version"],"")!=0) 
        insert_informations("firm_version",$informations["firm_version"]);
    if(strcmp($informations["log"],"")!=0) 
        insert_informations("log",$informations["log"]);

    return 1;
}


// {{{ copy_empty_big_file()
// ROLE copy an empty file to a new file destination
// IN $file     destination of the file
// RET true if the copy is errorless, false else
function copy_empty_big_file($file) {
   //Trying to find the template file from the current path:
   if(is_file('main/templates/data/empty_file_big.tpl')) {
        $filetpl = 'main/templates/data/empty_file_big.tpl';
   } else if(is_file('../main/templates/data/empty_file_big.tpl')) {
        $filetpl = '../templates/data/empty_file_big.tpl';
   } else {
        $filetpl = '../../templates/data/empty_file_big.tpl';
   }

   if(strcmp("$filetpl","")==0) return false;

   //Copying the template file if one has been found:
   if(!@copy($filetpl, $file)) return false;
   return true;
}
//}}}


// {{{ get_sd_card()
// ROLE get the sd card place to record configuration
// IN  $hdd     list of hdd available which could be configured as cultibox SD card
// RET false if nothing is found, the sd card place else
function get_sd_card(&$hdd="") {
    //For Linux
    $ret=false;
    $dir="";
    $os=php_uname('s');
    //Retrieve SD path depnding of the current OS:
    switch($os) {
        case 'Linux':
            //In Ubuntu Quantal mounted folders are now in /media/$USER directory
            $user=get_current_user();
            if((isset($user))&&(!empty($user))) {
                $dir="/media/".$user;
                if(is_dir($dir)) {
                    $rep = @opendir($dir);
                    if($rep) {
                        while ($f = @readdir($rep)) {
                            if(is_dir("$dir/$f")) {
                                if((strcmp("$f",".")!=0)&&(strcmp("$f","..")!=0)) {
                                    $hdd[]="$dir/$f";
                                    if(check_cultibox_card("$dir/$f")) {
                                        $ret="$dir/$f";
                                    }
                                }
                            }
                        }
                        closedir($rep);
                    }
                }
            }
            break;

        case 'Mac':
        case 'Darwin':
            $dir="/Volumes";
            if(is_dir($dir)) {
                $rep=@opendir($dir);
                if($rep) {
                    while ($f=@readdir($rep)) {
                        if(is_dir("$dir/$f")) {
                            if((strcmp("$f",".")!=0)&&(strcmp("$f","..")!=0)) {
                                $hdd[]="$dir/$f";
                                if(check_cultibox_card("$dir/$f")) {
                                    $ret="$dir/$f";
                                }
                            }
                        }
                    }
                    closedir($rep);
                }
            }
            break;

        case 'Windows NT':
            $vol=`MountVol`;
            $vol=explode("\n",$vol);
            $dir=Array();
            foreach($vol as $value) {
                // repérer les deux derniers segments du nom de l'hôte
                preg_match('/[D-Z]:/', $value,$matches);
                foreach($matches as $val) {
                    $dir[]=$val;
                }
            }

            foreach($dir as $disque) {
                $check=`dir $disque`;
                if(strlen($check)>0) {
                    $hdd[]="$disque";
                    if(check_cultibox_card("$disque")) {
                        $ret="$disque";
                    }
                }
            }
            break;
    }
    return $ret;
}
// }}}


// {{{ check_cultibox_card()
// ROLE check if the directory is a cultibox directory to write configuration
// IN   $dir         directory to check
// RET true if it's a cultibox directory, false else
function check_cultibox_card($dir="") {
/* TO BE DELETED */
   if((is_file("$dir/plugv"))&&(is_file("$dir/pluga"))&&(is_dir("$dir/logs"))) {
       if((is_file("$dir/plugv"))&&(is_file("$dir/pluga"))&&(is_dir("$dir/logs"))) {
            return true;
        }
   }
/* ********* */

    if((is_file("$dir/cnf/prg/plugv"))&&(is_file("$dir/cnf/plg/pluga"))&&(is_dir("$dir/logs"))) {
        return true;
    }
    
    return false;
}
// }}}


// {{{ save_program_on_sd()
// ROLE write programs into the sd card
// IN   $sd_card        path to the sd card to save datas
//      $program        the program to be save in the sd card 
// RET true if data correctly written, false else
function save_program_on_sd($sd_card,$program) {
    if(is_file("${sd_card}/cnf/prg/plugv")) {
        $file="${sd_card}/cnf/prg/plugv";
        $prog="";
        $nbPlug=count($program);
        $shorten=false;

        if($nbPlug>0) {
            if($nbPlug>$GLOBALS['PLUGV_MAX_CHANGEMENT']) {
                $nbPlug=$GLOBALS['PLUGV_MAX_CHANGEMENT'];
                $shorten=true;
            }

         while(strlen($nbPlug)<3) $nbPlug="0$nbPlug";
         $prog=$nbPlug."\r\n";


         if($shorten) {
            for($i=0; $i<$nbPlug-1; $i++) $prog=$prog."$program[$i]"."\r\n";
         } else {
            for($i=0; $i<$nbPlug; $i++) $prog=$prog."$program[$i]"."\r\n";
         }

         if($shorten) {
            $last=count($program)-1;
            $prog=$prog."$program[$last]"."\r\n";
         }

         if($f=@fopen("$sd_card/cnf/prg/plugv","w+")) {
            if(!@fwrite($f,"$prog")) { fclose($f); return false; }
            fclose($f);
         }
      } else {
         return false;
      }
   } else {
      return false;
   }
   return true;
}
// }}}


// {{{ compare_program()
// ROLE compare programs and data to check if they are up to date
// IN   $data         array containing datas to check
//      $sd_card      sd card path to save data
// RET false is there is something to write, true else
function compare_program($data,$sd_card) {
    if(is_file("${sd_card}/cnf/prg/plugv")) {
        $nb=0;
        //On compte le nombre d'entrée dans la base des programmes:
        $nbdata=count($data);

        //Si les changements de la base dépassent ceux de maximum définit, on coupe le tableau des programmes pour le faire
        //correspondre au nombre maximal
        if($nbdata>$GLOBALS['PLUGV_MAX_CHANGEMENT']) {
            $tmp_array=array_slice($data, 0, $GLOBALS['PLUGV_MAX_CHANGEMENT']-1);
            $tmp_array[]=$data[$nbdata-1];
            $data=$tmp_array;
            $nbdata=count($data);
        }

        $file="${sd_card}/cnf/prg/plugv";

        if(count($data)>0) {
            //On récupère les informations du fichier courant plugv
            $buffer_array=@file("$file");
            foreach($buffer_array as $buffer) {
                  $buffer=trim($buffer); //On supprime les caractères invisibles
                  if(!empty($buffer)) {
                     if($nb==0) {
                        if($nbdata!=$buffer) { //S'il s'agit de la première ligne, qui contient le nombre d'entrée, on compare le nombre d'entrée du fichier avec le nombre d'entrée du tableau
                         return false;
                        }
                     } else {
                        if(strcmp($data[$nb-1],$buffer)!=0) { //Sinon on compare le contenu du fichier et celui de la ligne correspondante dans le tableau
                           return false;
                        }
                     }
                     $nb=$nb+1;
                  } else if($nb==0) {
                    return false;
                  }
            }
            return true; //Tout est égal, on renvoie true
        }
    }
    return false;
}
// }}}


// {{{ compare_pluga()
// ROLE compare pluga and data from databases to check if the file is up to date
// IN   $sd_card      sd card path to save data
// RET false is there is something to write, true else
function compare_pluga($sd_card) {
    if(is_file("${sd_card}/cnf/plg/pluga")) {
         $nb=0;
         $file="${sd_card}/cnf/plg/pluga";

         $pluga=Array();
         $pluga[]=$GLOBALS['NB_MAX_PLUG'];
         for($i=0;$i<$GLOBALS['NB_MAX_PLUG'];$i++) {
            $tmp_power_max=get_plug_conf("PLUG_POWER_MAX",$i+1,$out);
            if(strcmp("$tmp_power_max","1000")==0) {
                $tmp_pluga=$GLOBALS['PLUGA_DEFAULT'][$i];
            } elseif(strcmp("$tmp_power_max","3500")==0) {
                $tmp_pluga=$GLOBALS['PLUGA_DEFAULT_3500W'][$i];
            } else if(intval(rtrim($tmp_power_max))<10) {
                $tmp_pluga=99+intval(rtrim($tmp_power_max));
            }
            $pluga[]="$tmp_pluga";
        }

        $nbdata=count($pluga);

        if(count($pluga)>0) {
            $buffer_array=@file("$file");
            foreach($buffer_array as $buffer) {
                $buffer=trim($buffer);

                if(!empty($buffer)) {
                  if(strcmp($pluga[$nb],$buffer)!=0) {
                     return false;
                  }
                  $nb=$nb+1;

                } elseif($nb==$nbdata) {
                  return true;
                } else {
                  return false;
                }
            }
            return true;
       }
    }
    return false;
}
// }}}


// {{{ compare_wificonf()
// ROLE compare wifi configuration if the file is up to date
// IN    $data         array containing datas to check
//       $sd_card      sd card path to check data
// RET false is there is something to write, true else
function compare_wificonf($data,$sd_card) {
    $file="$sd_card/cnf/wifi";
    if(!is_file($file)) return false;

    $wifi_array=@file("$file");
    if((count($data))!=(count($wifi_array))) return false;

    for($i=0;$i<count($data);$i++) {
        if(strcmp(trim(html_entity_decode($data[$i])),trim(html_entity_decode($wifi_array[$i])))!=0) return false;
    }
    return true;
}


// {{{ write_pluga()
// ROLE write plug_a into the sd card
// IN   $sd_card        the sd card to be written
//      $out            error or warning messages
// RET false is an error occured, true else
function write_pluga($sd_card,&$out) {
   $file="$sd_card/cnf/plg/pluga";

   if($f=@fopen("$file","w+")) {
      $pluga=Array();
      $pluga=$GLOBALS['NB_MAX_PLUG']."\r\n";
      for($i=0;$i<$GLOBALS['NB_MAX_PLUG'];$i++) {
        $tmp_power_max=get_plug_conf("PLUG_POWER_MAX",$i+1,$out);
        if(strcmp("$tmp_power_max","1000")==0) {
            $tmp_pluga=$GLOBALS['PLUGA_DEFAULT'][$i];
        } elseif(strcmp("$tmp_power_max","3500")==0) {
            $tmp_pluga=$GLOBALS['PLUGA_DEFAULT_3500W'][$i];
        //Dimmer plug:
        } else if(intval(rtrim($tmp_power_max))<10) {
            $tmp_pluga=99+intval(rtrim($tmp_power_max));
        }
        $pluga=$pluga."$tmp_pluga"."\r\n";
      }

      if(!@fwrite($f,"$pluga")) {
          fclose($f);
          return false;
      }
   } else {
        return false;
   }
   fclose($f);
   return true;
}
// }}}


// {{{ write_wificonf()
// ROLE write wifi configuration into the sd card
// IN   $sd_card        the sd card to be written
//      $wificonf       string containing data to be written
//      $out            error or warning messages
// RET false is an error occured, true else
function write_wificonf($sd_card,$wificonf="",&$out) {
   $data="";
   $file="$sd_card/cnf/wifi";

   foreach($wificonf as $conf) {
        $data=$data.html_entity_decode($conf)."\r\n";
   }

   if($f=@fopen("$file","w+")) {
      if(!@fwrite($f,"$data")) {
        fclose($f);
        return false;
      }
   } else {
        return false;
   }
   fclose($f);
   return true;
}
// }}}


// {{{ write_plugconf()
// ROLE write plug_configuration into the sd card
// IN   $data           array containing datas to write
//      $sd_card        the sd card to be written
// RET false is an error occured, true else
function write_plugconf($data,$sd_card) {
   for($i=0;$i<count($data);$i++) {
      $nb=$i+1;
      if($nb<10) {
         $file="$sd_card/cnf/plg/plug0$nb";
      } else {
         $file="$sd_card/cnf/plg/plug$nb";
      }

      if($f=@fopen("$file","w+")) {
         if(!@fputs($f,"$data[$i]"."\r\n")) {
            fclose($f);
            return false;
         }
      } else {
         return false;
      }
      fclose($f);
   }
   return true;
}
// }}}


// {{{ compare_plugconf()
// ROLE compare plug's configuration with the database
// IN   $data    array containing plugconf datas
//      sd_card     path to the sd_card
// OUT false is there is a difference, true else
function compare_plugconf($data, $sd_card="") {
   for($i=0;$i<count($data);$i++) {
        $nb=$i+1;
        if($nb<10) {
            $file="$sd_card/cnf/plg/plug0$nb";
        } else {
            $file="$sd_card/cnf/plg/plug$nb";
        }

        if(!is_file($file)) return false;
        $tmp=explode("\r\n",$data[$i]);
        foreach($tmp as $dt) {
           $new_tmp[]=trim($dt);
        }

        $tmp=$new_tmp;

        $buffer=@file("$file");
        $buffer=array_filter($buffer);

        foreach($buffer as $bf) {
           $new_buffer[]=trim($bf);
        }

        $buffer=$new_buffer;

        if(count($buffer)!=count($tmp)) return false;

        for($j=0;$j<count($buffer);$j++) {
            if(strcmp($tmp[$j],$buffer[$j])!=0) {
                    return false;
            }
        }

        unset($tmp);
        unset($buffer);
   }
   return true;
}
// }}}


// {{{ compare_sd_conf_file()
// ROLE  compare conf file data with the database
// IN   $sd_card      location of the sd card to save data
//      $record_frequency   record frequency value
//      $update_frequency   update frequency value
//      $power_frequency    record of the power frequency value
//      $alarm_enable       enable or disable the alarm system
//      $alarm_value        value to trigger the alarm
//      $reset_value        value for the sensor's reset min/max
//      $rtc                RTC_OFFSET value
// RET false if there is a difference, true else
function compare_sd_conf_file($sd_card="",$record_frequency,$update_frequency,$power_frequency,$alarm_enable,$alarm_value,$reset_value,$rtc) {
    if(!is_file($sd_card."/cnf/conf")) return false;

    $file="${sd_card}/cnf/conf";

    $record=$record_frequency*60;
    $power=$power_frequency*60;
    $update="000$update_frequency";


    while(strlen($alarm_enable)<4) {
        $alarm_enable="0$alarm_enable";
    }

    $alarm_value=$alarm_value*100;
    while(strlen($alarm_value)<4) {
        $alarm_value="0$alarm_value";
    }

    while(strlen($record)<4) {
        $record="0$record";
    }

   while(strlen($power)<4) {
      $power="0$power";
   }

   while(strlen($rtc)<4) {
      $rtc="0$rtc";
   }

    $reset_value=str_replace(":","",$reset_value);
    if((strlen($reset_value)!=4)||($reset_value<0)) {
        $reset_value="0000";
    } 
    
    $conf[]="PLUG_UPDATE:$update";
    $conf[]="LOGS_UPDATE:$record";
    $conf[]="POWR_UPDATE:$power";
    $conf[]="ALARM_ACTIV:$alarm_enable";
    $conf[]="ALARM_VALUE:$alarm_value";
    $conf[]="ALARM_SENSO:000T";
    $conf[]="ALARM_SENSS:000+";
    $conf[]="RTC_OFFSET_:$rtc";
    $conf[]="RESET_MINAX:$reset_value";
    $conf[]="PRESSION___:0000";

    $buffer=@file("$file");

    if(count($conf)!=count($buffer)) return false;
    for($nb=0;$nb<count($conf);$nb++) {
        if(strcmp(trim($conf[$nb]),trim($buffer[$nb]))!=0) {
            return false;
        }
    }
    return true;
}
// }}}


// {{{ write_sd_conf_file()
// ROLE   save configuration into the SD card
// IN   $sd_card      location of the sd card to save data
//   $record_frequency   record frequency value
//   $update_frequency   update frequency value
//   $power_frequency    record of the power frequency value
//   $alarm_enable       enable or disable the alarm system
//   $alarm_value        value to trigger the alarm
//   $reset_value        min/max reset value
//   $rtc                value for the RTC_OFFSET
//   $out                error or warning messages
// RET false if an error occured, true else  
function write_sd_conf_file($sd_card,$record_frequency=1,$update_frequency=1,$power_frequency=1,$alarm_enable="0000",$alarm_value="50.00",$reset_value,$rtc="0000",$out="") {
   $alarm_senso="000T";
   $alarm_senss="000+";
   $record=$record_frequency*60;
   $power=$power_frequency*60;

   
   while(strlen($alarm_enable)<4) {
      $alarm_enable="0$alarm_enable";
   }

   $alarm_value=$alarm_value*100;
   while(strlen($alarm_value)<4) {
      $alarm_value="0$alarm_value";
   }


   while(strlen($record)<4) {
      $record="0$record";
   }

   while(strlen($power)<4) {
      $power="0$power";
   }

   while(strlen($rtc)<4) {
      $rtc="0$rtc";
   }

   $reset_value=str_replace(":","",$reset_value);
   if((strlen($reset_value)!=4)||($reset_value<0)) {
        $reset_value="0000";
   } 

   $update="000$update_frequency";
   $file="$sd_card/cnf/conf";
   $check=true;
   if($f=@fopen("$file","w+")) {
      if(!@fputs($f,"PLUG_UPDATE:$update\r\n")) $check=false;
      if(!@fputs($f,"LOGS_UPDATE:$record\r\n")) $check=false;
      if(!@fputs($f,"POWR_UPDATE:$power\r\n")) $check=false; 
      if(!@fputs($f,"ALARM_ACTIV:$alarm_enable\r\n")) $check=false;
      if(!@fputs($f,"ALARM_VALUE:$alarm_value\r\n")) $check=false;
      if(!@fputs($f,"ALARM_SENSO:$alarm_senso\r\n")) $check=false;
      if(!@fputs($f,"ALARM_SENSS:$alarm_senss\r\n")) $check=false;
      if(!@fputs($f,"RTC_OFFSET_:$rtc\r\n")) $check=false;
      if(!@fputs($f,"RESET_MINAX:$reset_value\r\n")) $check=false;
      if(!@fputs($f,"PRESSION___:0000\r\n")) $check=false;
      fclose($f);

      if(!$check) {
        return false;
      }
   } else {
      return false;
   }
   return true;
}
//}}}


// {{{ clean_calendar()
// ROLE delete all calc_XX files 
// IN $sd_card         sd card location
//    $start           start and end: to clean just a part of the calendar
//    $end             if empty: clean all the calendar
// RET false if an error occured, true else
function clean_calendar($sd_card="",$start="",$end="") {
    if(strcmp("$sd_card","")==0) return false;

    $path="$sd_card/logs";
    if(is_dir($path)) {
        if((strcmp("$start","")==0)&&(strcmp("$end","")==0)) {
            for($i=1;$i<=12;$i++) {
                if(strlen("$i")<2) {
                    $i="0".$i;
                }
        
                $sq=@opendir($path."/".$i); 
                while ($f=@readdir($sq)) {
                    if("$f" != "." && "$f" != "..") {
                        if(preg_match('/^cal_/', $f)) {
                            @unlink($path."/".$i."/".$f);
                        }
                    }
                }
            }
        } elseif((strcmp("$start","")!=0)&&(strcmp("$end","")==0)) {
            $stmon=substr($start,5,2);
            $stday=substr($start,8,2);

            if(is_file($sd_card."/logs/".$stmon."/cal_".$stday)) {
                @unlink($sd_card."/logs/".$stmon."/cal_".$stday);
            }
        } elseif((strcmp("$start","")!=0)&&(strcmp("$end","")!=0)) {
            $stmon=substr($start,5,2);
            $stday=substr($start,8,2);
            $edmon=substr($end,5,2);
            $edday=substr($end,8,2);

            if(strlen("$stday")<2) {
                $stday="0".$stday;
            }

            if(strlen("$stmon")<2) {
                $stmon="0".$stmon;
            }

            if(is_file($sd_card."/logs/".$stmon."/cal_".$stday)) {
                @unlink($sd_card."/logs/".$stmon."/cal_".$stday);
            }

            while(($stday!=$edday)||($stmon!=$edmon)) {
                $stday=$stday+1;
                if($stday>31) {
                    $stmon=$stmon+1;
                    $stday=1;
                }

                if($stmon>12) {
                    $stmon=1;
                }

                if(strlen("$stday")<2) {
                    $stday="0".$stday;
                }

                if(strlen("$stmon")<2) {
                    $stmon="0".$stmon;
                }

                if(is_file($sd_card."/logs/".$stmon."/cal_".$stday)) {
                    @unlink($sd_card."/logs/".$stmon."/cal_".$stday);
                }
            }
        }
    }
    return true;
}
// }}}


// {{{ write_calendar()
// ROLE save calendar informations into the SD card
// IN $sd_card         sd card location
//    $data            data to write into the sd card (come from create_calendar_from_database )
//    $out             error or warning messages
//    $start           write calendar between two dates
//    $end             if start and end are not set: write full calendar
// RET false if an error occured, true else
function write_calendar($sd_card,$data,&$out,$start="",$end="") {

    $status=true;

    if(isset($sd_card)&&(!empty($sd_card))) {
   
        if(count($data)>0) {
            if((strcmp("$start","")==0)&&(strcmp("$end","")==0)) {
                $month_end=12;
                $month_start=1;
                $day_end=31;
                $day_start=1;
             } else if((strcmp("$start","")!=0)&&(strcmp("$end","")==0)) {
                $month_end=substr($start,5,2);
                $month_start=$month_end;
                $day_end=substr($start,8,2);
                $day_start=$day_end;
            } else {
                $month_start=substr($start,5,2);
                $day_start=substr($start,8,2);
                $month_end=substr($end,5,2);
                $day_end=substr($end,8,2);
            }

            $month=$month_start;
            $day=$day_start;
            do {
                $val=concat_calendar_entries($data,$month,$day);
                
                // If there is something to write
                if($val) {
                    
                    // Create correct day number for filename
                    while(strlen($day)<2) {
                        $day="0$day";
                    }
                    
                    // Create correct month number for filename
                    while(strlen($month)<2) {
                        $month="0$month";
                    }

                    // Create filename
                    $file="$sd_card/logs/$month/cal_$day";
                    
                    // If file can be opened
                    if($f=@fopen("$file","w+")) {
                    
                        // Foreach event to write
                        foreach($val as $value) {
                            $sub=$value["subject"];
                            $desc=$value["description"];

                            if(!@fputs($f,"$sub"."\r\n")) $status=false;
                            if(!@fputs($f,"$desc"."\r\n\r\n")) $status=false;
                        }
                        fclose($f);
                    } else {  
                        $status=false;
                    }
                }

                // Change day number if end of the month is reached
                if($day==31) {
                    $day="01";
                    $month=$month+1;
                    if($month>12) {
                        $month="01";
                    }
                } else {
                    $day=$day+1;
                }
                unset($val);
            } while(($month!=$month_end)||($day!=$day_end)); 

            $val=concat_calendar_entries($data,$month,$day);

            // If there is something to write in SD card
            if(!empty($val)) {
            
                // Create correct day number for filename
                while(strlen($day)<2) {
                    $day="0$day";
                }

                // Create correct month number for filename
                while(strlen($month)<2) {
                    $month="0$month";
                }
                
                // Create filename
                $file="$sd_card/logs/$month/cal_$day";
                
                // Open It
                if($f=@fopen("$file","w+")) {
                
                    // Foreach event to write
                    foreach($val as $value) {
                        $sub=$value["subject"];
                        $desc=$value["description"];

                        if(!@fputs($f,"$sub"."\r\n")) $status=false;
                        if(!@fputs($f,"$desc"."\r\n")) $status=false;
                    }
                    
                    fclose($f);
                } else {
                     $status=false;
                }
           }
       }
    }
    return $status;
}
//}}}


// {{{ check_and_copy_firm()
// ROLE check if firmwares (firm.hex,emetteur.hex) has to be copied and do the copy into the sd card
// IN  $sd_card     the sd card pathname 
// RET 1 if at least one firmware has been copied, 0 if an error occured, -1 else
function check_and_copy_firm($sd_card) {
   $new_firm="";
   $current_firm="";
   $new_file="";
   $copy=-1;


   //Liste des firmawares à vérifier et à copier:
   $firm_to_test[]="firm.hex";
   $firm_to_test[]="bin/emetteur.hex";
   $firm_to_test[]="bin/sht.hex";
   $firm_to_test[]="bin/wlevel_5.hex";
   $firm_to_test[]="bin/wlevel_6.hex";
   $firm_to_test[]="bin/ec_2.hex";
   $firm_to_test[]="bin/ec_3.hex";
   $firm_to_test[]="bin/ec_4.hex";
   $firm_to_test[]="bin/ec_5.hex";
   $firm_to_test[]="bin/ec_6.hex";
   $firm_to_test[]="bin/ph_2.hex";
   $firm_to_test[]="bin/ph_3.hex";
   $firm_to_test[]="bin/ph_4.hex";
   $firm_to_test[]="bin/ph_5.hex";
   $firm_to_test[]="bin/ph_6.hex";
   $firm_to_test[]="bin/or_2.hex";
   $firm_to_test[]="bin/or_3.hex";
   $firm_to_test[]="bin/or_4.hex";
   $firm_to_test[]="bin/or_5.hex";
   $firm_to_test[]="bin/or_6.hex";
   $firm_to_test[]="bin/od_2.hex";
   $firm_to_test[]="bin/od_3.hex";
   $firm_to_test[]="bin/od_4.hex";
   $firm_to_test[]="bin/od_5.hex";
   $firm_to_test[]="bin/od_6.hex";


   //Pour chaque firmware on procède de la même façon:
   foreach($firm_to_test as $firm) { 
        //Vérification de la présence du firmware:
        if(is_file("tmp/$firm")) {
            $new_file="tmp/$firm";
        } else if(is_file("../tmp/$firm")) {
            $new_file="../tmp/$firm";
        } else if(is_file("../../tmp/$firm")) {
            $new_file="../../tmp/$firm";
        } else {
            $new_file="../../../tmp/$firm";
        } 

        //Chemin du firmware à comparer sur la carte SD:
        $current_file="$sd_card/$firm";

        //Si on trouve le firmware de référence on récupère le contenue de la première ligne ou la version est présente:
        if(is_file("$new_file")) {
            $handle = @fopen("$new_file", 'r');
            if($handle) {
                $new_firm = fgets($handle);
            } else {
                $copy=0;
            }
            fclose($handle);
        } 

        //Même chose avec le firmware sur la carte SD:
        if(is_file("$current_file")) {
            $handle=@fopen("$current_file", 'r');
            if($handle) {
                $current_firm = fgets($handle);
            } else {
                $copy=0;
            }
            fclose($handle);
        } 


        //Si le firmware sur la carte SD et le firmware de référence ont été trouvé, on compare le numéro de version du firmware
        //Si les numéro diffèrent (numéro firmware de référence > numéro firmware sur la carte SD) on copiera le firmware de référence sur la carte SD
        if((isset($new_firm))&&(!empty($new_firm))&&(isset($current_firm))&&(!empty($current_firm))) {
                $current_firm=trim("$current_firm");
                $new_firm=trim("$new_firm");

                if((strlen($current_firm)==43)&&(strlen($new_firm)==43)) {   
                    $new_firm=substr($new_firm,9,4); 
                    $current_firm=substr($current_firm,9,4);

                    if(hexdec($new_firm) > hexdec($current_firm)) {
                        copy($new_file, $current_file);
                        if($copy) $copy=1;
                    } 
                } else {
                    $copy=0;
                }
        } elseif((!is_file("$current_file"))&&(is_file("$new_file"))) {
        //S'il n'y a pas de firmware sur la carte SD, on copie le firmware de référence:
                copy($new_file, $current_file);
                if($copy) $copy=1;
        } else {
            $copy=0;
        }

        unset($new_file);
        unset($current_file);
        unset($handle);
        unset($current_firm);
        unset($new_firm); 
    }
    return $copy;
}
// }}}


// {{{ check_and_copy_plgidx()
// ROLE check if cnf/prg/plgidx file has to be updated
// IN  $sd_card     the sd card pathname 
// RET false if an error occured, true else
function check_and_copy_plgidx($sd_card="") {
    $path="";

    //On essaye de déterminer le chemin du fichier de référence:
    if(is_file("tmp/cnf/prg/plgidx")) {
        $path="tmp/cnf/prg/plgidx";
    } else if(is_file("../tmp/cnf/prg/plgidx")) {
        $path="../tmp/cnf/prg/plgidx";
    } else if(is_file("../../tmp/cnf/prg/plgidx")) {
        $path="../../tmp/cnf/prg/plgidx";
    } else if(is_file("../../../tmp/cnf/prg/plgidx")) {
        $path="../../../tmp/cnf/prg/plgidx";
    }

    //Si le fichier sur la carte SD n'existe pas:
    if(!is_file("$sd_card/cnf/prg/plgidx")) {
        //Si le chemin de référence a été trouvé:
        if(strcmp("$path","")!=0) {
            if(!@copy("$path", "$sd_card/cnf/prg/plgidx")) {
                //Si la copie n'a pas réussie:
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    } else {
        if(strcmp("$path","")==0) {
           //Si on ne trouve pas le fichier de référence:
           return false;
        } else {
           //On compare si le fichier de référence et le fichier sur la carte SD sont différents:
           if(filesize("$path")!=filesize("$sd_card/cnf/prg/plgidx")) {
               if(!@copy("$path", "$sd_card/cnf/prg/plgidx")) {
                   return false;
                } else {
                   return true;
                }
           } else {
               return true;
           }
        }
    }
}
// }}}


// {{{ check_and_copy_id()
// ROLE check if cnf/id file has to be updated
// IN  $sd_card     the sd card pathname 
//     $id          the saved id from the database
// RET false if the id file has to be updated, true else
function check_and_copy_id($sd_card,$id="") {
    if(strcmp("$id","")==0) return true;

    if(is_file("$sd_card/cnf/id")) {
        $id_file=file("$sd_card/cnf/id");
        if(count($id_file)==1) {
            $id_file=trim($id_file[0]);
        } else {
            $id_file=0;
        }
    } else {
        $id_file=0;
    }

    if($id_file!=$id) {
        while(strlen($id)<5) $id="0$id";
        $handle=fopen("$sd_card/cnf/id",'w');
        fwrite($handle,"$id");
        fclose($handle);
        return false;
    }
    return true;
}
// }}}


// {{{ check_and_copy_index()
// ROLE check if the index file exists and if not, create it 
// IN  $sd_card     the sd card pathname 
// RET false if an error occured, true else
function check_and_copy_index($sd_card) {
    $path="";

    if(is_file("tmp/logs/index")) {
        $path="tmp/logs/index";
    } else if(is_file("../tmp/logs/index")) {
        $path="../tmp/logs/index";
    } else if(is_file("../../tmp/logs/index")) {
        $path="../../tmp/logs/index";
    } else if(is_file("../../../tmp/logs/index")) {
        $path="../../../tmp/logs/index";
    }

    if(!is_file("$sd_card/logs/index")) {
        if(strcmp("$path","")!=0) {
            if(!@copy("$path", "$sd_card/logs/index")) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    } else {
        if(strcmp("$path","")==0) {
           return false;
        } else {
           if(filesize("$path")!=filesize("$sd_card/logs/index")) {
               if(!@copy("$path", "$sd_card/logs/index")) {
                   return false;
                } else {
                   return true;
                }
           } else {
               return true;
           }
        }
    }
}
// }}}


// {{{ clean_index_file()
// ROLE force the cleaning of the index file
// IN  $sd_card     the sd card pathname 
// RET false if an error occured, true else
function clean_index_file($sd_card) {
    $path="";

    if(is_file("tmp/logs/index")) {
        $path="tmp/logs/index";
    } else if(is_file("../tmp/logs/index")) {
        $path="../tmp/logs/index";
    } else if(is_file("../../tmp/logs/index")) {
        $path="../../tmp/logs/index";
    } else if(is_file("../../../tmp/logs/index")) {
        $path="../../tmp/logs/index";
    }

    if(strcmp("$path","")==0) return false;
    if(!is_dir("$sd_card/logs/")) return false;

    if(!@copy("$path", "$sd_card/logs/index")) return false;
    return true;
}
// }}}


// {{{ find_informations()
// ROLE find some informations from the log.txt file
// IN    $ret       array to return containing informations
//       $log_file  path to the log file
// RET   none
function find_informations($log_file,&$ret) {
if(!file_exists("$log_file")) return $ret;
   $ret["cbx_id"]="";
   $ret["firm_version"]="";
   $ret["log"]="";

   $buffer_array=file("$log_file");
   foreach($buffer_array as $buffer) {
        $buffer=trim($buffer);

        if(strcmp($buffer,"")==0) break;

        if(strcmp($ret["log"],"")==0) {
            $ret["log"]=$buffer;
        } else {
            $ret["log"]=$ret["log"]."#".$buffer;
        }

        switch (substr($buffer,14,1)) {
            case 'I':
                $ret["cbx_id"]=substr($buffer,16,5);
                break;
            case 'V':
                $ret["firm_version"]=substr($buffer,16,7); 
                break;
        }
   }
   return $ret;
}
// }}}


// {{{ check_sd_card()
// ROLE check if the soft can write on a sd card
//  IN      $sd        the sd_card path to be checked
// RET true if we can, false else
function check_sd_card($sd="") {

    // Check to open in write mode
    if($f=@fopen("$sd/test.txt","w+")) {
        // Close file
        fclose($f);
        
        // Delete file
        if(!@unlink("$sd/test.txt")) 
            return false;
        
        // SD card is writable
        return true;
    } else {
        // Not openable in write mode
        return false;
    }
}
// }}}


// {{{ get_rtc_from_sd()
// ROLE get the rtc offset value of the sd card
//  IN      $sd        the sd_card path 
// RET rtc value
function get_rtc_from_sd($sd="") {
   $file="$sd/cnf/conf";
   if(!is_file("$file")) return false;
   
   $conf=file("$file"); 
   if(strcmp($conf[7],"RTC_OFFSET_:\r\n")==0) return false;

   return "$conf[7]";
}
// }}}



/* TO BE DELETED */
function compat_old_sd_card($sd_card="") {
    if((isset($sd_card))&&(!empty($sd_card))) {
        $logs="$sd_card/logs";
        $cnf="$sd_card/cnf";
        $plg="$cnf/plg";
        $prg="$cnf/prg";
        $bin="$sd_card/bin";

        $error_copy=false;

        if(!is_dir($logs)) if(!@mkdir("$logs")) { $error_copy=true; };
        if(!is_dir($cnf)) if(!@mkdir("$cnf")) { $error_copy=true; };
        if(!is_dir($plg)) if(!@mkdir("$plg")) { $error_copy=true; }; 
        if(!is_dir($prg)) if(!@mkdir("$prg")) { $error_copy=true; };
        if(!is_dir($bin)) if(!@mkdir("$bin")) { $error_copy=true; };

        if(!is_file("$sd_card/cnf/prg/plugv")) {
            if(!@copy("main/templates/data/empty_file.tpl","$sd_card/cnf/prg/plugv")) { $error_copy=true; };
        }

        if($error_copy) {
            return false;
        }
    }
    return true;
}

/* **************** */


?>

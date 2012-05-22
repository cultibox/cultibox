<?php

// {{{ __($msgkey, ...)
// ROLE get the translated message corresponding to $msgkey, the parameters may
// be used in $msgkey by using the sprintf syntax (%s)
// IN $msgkey
// IN (optional)
// RET string translated
static $__translations;
static $__translations_fallback;
static $string_lang;

if(is_file("main/libs/l10n.php")) {
	require_once 'main/libs/l10n.php';
} else {
	require_once '../libs/l10n.php';
}

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
                $__translations = __translations_get($_SESSION['LANG']);
                $__translations_fallback = __translations_get(LANG_FALLBACK);
                if (empty($__translations_fallback)) {
                        die("No translation file");
                }
                $string_lang = array($_SESSION['LANG'] => $__translations);
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
        return $ret;
}
//}}}


// {{{ getvar()
// ROLE Used to get access to GET or POST values for '$varname'
// IN $varname as a string
// RET the string value as read in $_GET[] or $_POST[]
function getvar($varname) {
        $tmp = false;

        // The mess with the *x appended is a workaround for some browser
        // versions that append it when you want to get information for some
        // image buttons

        if (isset($_GET["$varname"])) {
                $tmp = $_GET["$varname"];
        } elseif (isset($_GET["$varname"."x"])) {
                $tmp = true;
        } elseif (isset($_GET["$varname"."_x"])) {
                $tmp = true;
        } elseif (isset($_POST["$varname"])) {
                $tmp = $_POST["$varname"];
        } elseif (isset($_POST["$varname"."x"])) {
                $tmp = true;
        } elseif (isset($_POST["$varname"."_x"])) {
                $tmp = true;
        }

        // FIXME: simple html cleanup
        if (is_array($tmp)) {
                return $tmp;
        } else {
                return stripslashes(htmlentities($tmp));
        }
}
// }}}

// {{{ check_empty_string($value)
// ROLE check is a string is empty or only composed with CR
// IN $value	string to check
// RET false if the string is empty, true else
function check_empty_string($value="") {
   $value=str_replace(' ','',$value);
   $value=str_replace(CHR(13).CHR(10),'',$value); 
   
   if("$value" == "") {
      return 0;
   } else {
      return 1;
   }
}
//}}}


// {{{ get_log_value($file,&$array_line)
// ROLE get log's values from files and clean it
// IN $file      file to explode
//    $array_line   array to store log's values
// RET none
function get_log_value($file,&$array_line) {
   $handle = fopen("$file", 'r');
   $index=0;
   if ($handle)
   {
      while (!feof($handle))
      {
         $buffer = fgets($handle);
         if(!check_empty_string($buffer)) {
            break;
         } else {
            $temp = explode("\t", $buffer);
            $date_catch="20".substr($temp[0], 0, 2)."-".substr($temp[0],2,2)."-".substr($temp[0],4,2);
            $time_catch=substr($temp[0], 8,6);
            $array_line[] = array(
               "timestamp" => $temp[0],
               "temperature" => $temp[1],
               "humidity" => $temp[2],
               "date_catch" => $date_catch,
               "time_catch" => $time_catch
            );
            $index=$index+1;
         }
      }
      fclose($handle);
      if("$index" != "0") {
         clean_log_file($file);
      }
   }
}
//}}}


// {{{ clean_log_file($file)
// ROLE copy an empty file to clean a log file
// IN $file             file to clean
// RET none
function clean_log_file($file) {
   $filetpl = 'main/templates/data/empty_file.tpl';
   copy($filetpl, $file);
}
//}}}


// {{{ get_format_month($data)
// ROLE using a graphics data string containing value to make an average for month graphics
// IN $data   datas from a graphics containing values for a month
// RET return datas string value containing an average of the input fiel data
function get_format_month($data) {
   $arr = explode(" ", $data);
   $count=0;
   $moy=0;
   $data_month="";
   foreach($arr as $value) {
      if("$value"=="null") {
         $value=0;
      }
      $moy=round(($moy + $value)/2,2);
      $count=$count+1;
      if($count==20) {
         if("$data_month"=="") {
            $data_month="$moy";
         } else {
            $data_month="$data_month, $moy";
         }
         $count=0;
         $moy=0;
      }
   }
   return $data_month;
}
//}}}


// {{{ get_current_lang()
// ROLE get the current language selected for the interface
// IN none
// RET current lang using the l10n format, ex: en-GB with joomla format is replaced by en_GB
function get_current_lang() {
   $lang =& JFactory::getLanguage();
   return str_replace("-","_",$lang->getTag());
}
//}}}


// {{{ set_current_lang()
// ROLE set the Joomla language for the interface
// IN $lang   lang to set using the l10n format (ex: en_GB)
// RET true
function set_lang($lang) {
   $lang=str_replace("_","-",$lang);
   $language =& JFactory::getLanguage();
   $language->setLanguage("${lang}");
   $language->load();

   //FIXME check error
   return true;
}


// }}}


// {{{ get_format_graph($arr)
// ROLE get datas for the highcharts graphics
// IN $arr   array containing datas
// RET $data   data at the highcharts format (a string)
function get_format_graph($arr) {
   $data="";
   $last_mm="";
   $last_hh="";
   foreach($arr as $value) {
      $hh=substr($value['time_catch'], 0, 2);
      $mm=substr($value['time_catch'], 2, 2);

      if(("$hh:$mm" != "00:00")&&(empty($data))&&(empty($last_value))) {
         $data=fill_data("00","00","$hh","$mm","null","$data");
      } else if((check_empty_record("$last_hh","$last_mm","$hh","$mm"))&&("$hh:$mm" != "00:00")) {
         $data=fill_data("$last_hh","$last_mm","$hh","$mm","$last_value","$data");
      } else {
         if("$hh:$mm" != "00:00") {
            $data=fill_data("$last_hh","$last_mm","$hh","$mm","null","$data");
         }
      }
      $last_value="$value[record]";
      $last_hh=$hh;
      $last_mm=$mm;
   }
   if("$last_hh:$last_mm" != "23:59") {
      $data=fill_data("$last_hh","$last_mm","24","00","$last_value","$data");
   }
   return $data;
}
//}}}


// {{{ fill_data($fhh,$fmm,$lhh,$lmm,$val,$data)
// ROLE fill highcharts data,between two time spaces, using a specific value
// IN $fhh   start hours
//    $fmm   start minutes
//    $lhh   end hours
//    $lmm   end minutes
//    $val   value used to fill time spaces
//    $data   data at the highcharts format (a string)
// RET none
function fill_data($fhh,$fmm,$lhh,$lmm,$val,$data) {
   while(strcmp("$fhh:$fmm","$lhh:$lmm")<0) {
      if("$data" == "") {
         $data="$val";
      } else {
         $data=$data.", $val";
      }
      $fmm=$fmm+1;
      if($fmm<10) {
         $fmm="0$fmm";
      }
      
      if("$fmm" == "60") {
         $fmm="00";
         $fhh=$fhh+1;
         if($fhh<10) {
            $fhh="0$fhh";
         }
      }
   }
   return $data;
}
//}}}



// {{{ check_empty_record($last_hh,$last_mm,$hh,$mm)
// ROLE check if there is an empty record. An empty reccord is defined is the time spaces
// between two values is greatan than 30minutes
// IN $last_hh   last record hours
//    $last_mm   last record minutes
//    $hh   first record hours
//    $mm   first record minutes
// RET true is there isn't an empty record, false else.
function check_empty_record($last_hh,$last_mm,$hh,$mm) {
      $lhh= 60 * $last_hh + $last_mm;
      $chh= 60 * $hh + $mm;

      if($lhh-$chh<=30) {
         return true;
      } else {
         return false;
      }
}
//}}}


// {{{ check_format_date($date,$type)
// ROLE check date format (month with the format MM ou complete date: YYYY-MM-DD)
// IN $date   date to check
//    $type   the type: month or days
// RET true is the format match the type, false else
function check_format_date($date="",$type,&$out="") {
   $date=str_replace(' ','',"$date");
   if("$type"=="days") {
      if(strlen("$date")!=10) {
         $out=$out.__('ERROR_FORMAT_DATE_DAY');
         return 0;
      }

      if(!preg_match('#^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$#', $date)) {
         $out=$out.__('ERROR_FORMAT_DATE_DAY');
                        return 0;
                }
      return 1;
   }

   if("$type" == "month") {
      if(strlen("$date")!=2) {
         $out=$out.__('ERROR_FORMAT_DATE_MONTH');
         return 0;
      }

      if(!preg_match('#^[0-9][0-9]$#', $date)) {
         $out=$out.__('ERROR_FORMAT_DATE_MONTH');
         return 0;
      }

      if(($date < 1)||($date > 12)) {
         $out=$out.__('ERROR_FORMAT_DATE_MONTH');
         return 0;
      }
      return 1;
   }
   return 0;
}
//}}}


// {{{ check_numeric_value($value)
// ROLE check if a value is numeric or not
// IN $value   value to check
// RET true is $value is numeric, false else
function check_numeric_value($value="") {
   if((empty($value))||(!isset($value))) {
      return false;
   }

   if(!is_numeric($value)) {
      return false;
   }
   return true;
}
//}}}


// {{{ get_sd_card()
// ROLE get the sd card place to record configuration
// IN   none 
// RET false if nothing is found, the sd card place else
function get_sd_card() {
   //For Linux
   $dir="/media";
   if(is_dir($dir)) {
      $rep = opendir($dir); 
      while ($f = readdir($rep)) {
            if(is_dir("$dir/$f")) {
            if(check_cultibox_card("$dir/$f")) {
               return "$dir/$f";
            }
            }
      }
   } else {
      //For Mac
      $dir="/Volumes";
      if(is_dir($dir)) {
         $rep = opendir($dir);
         while ($f = readdir($rep)) {
                           if(is_dir("$dir/$f")) {
                                   if(check_cultibox_card("$dir/$f")) {
                                           return "$dir/$f";
                                   }
                           }
                   }
      } else {
         //For windows
         $dir=array("c:","d:","e:","f:","g:","h:","i:","j:","k:","l:","m:","n:","o:","p:","q:","r:","s:","t:","u:","v:","w:","x:","y:","z");
         foreach($dir as $disque) {
            if(is_dir("$disque")) {
               if(check_cultibox_card("$disque")) {
                                                return "$disque";
               }
                           }
         }
      }
   }
   return false;
}
// }}}


// {{{ check_cultibox_card()
// ROLE check if the directory is a cultibox directory to write configuration
// IN   directory to check
// RET true if it's a cultibox directory, false else
function check_cultibox_card($dir="") {
   if((is_dir("$dir/logs"))&&(is_file("$dir/plugv"))&&(is_file("$dir/pluga"))) {
      return true;
   } else {
      return false;
   }
}
// }}}


// {{{ check_cultibox_card()
// ROLE check times send by user to reccord a plug behaviour
// IN   $start_time   time starting the event
//   $end_time   time ending the event
//   $out      string for error or warning messages
// RET true if ok, false else
function check_times($start_time="",$end_time="",&$out="") {
   if((!empty($start_time))&&(isset($start_time))&&(!empty($end_time))&&(isset($end_time))) {
      $start_time=str_replace(' ','',"$start_time");
      if(strlen("$start_time")!=8) {
         $out=$out.__('ERROR_FORMAT_TIME');
         return 0;
      }

      if(!preg_match('#^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$#', $start_time)) {
         $out=$out.__('ERROR_FORMAT_TIME');
         return 0;
      }

      $end_time=str_replace(' ','',"$end_time");
      if(strlen("$end_time")!=8) {
         $out=$out.__('ERROR_FORMAT_TIME');
         return 0;
      }

      if(!preg_match('#^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$#', $end_time)) {
         $out=$out.__('ERROR_FORMAT_TIME');
         return 0;
      }

      $sth=substr($start_time, 0, 2);
      $stm=substr($start_time, 3, 2);
      $sts=substr($start_time, 6, 2);

      $enh=substr($end_time, 0, 2);
      $enm=substr($end_time, 3, 2);
      $ens=substr($end_time, 6, 2);
   
      $start_time= mktime($sth, $stm, $sts);
      $end_time= mktime($enh, $enm, $ens);

      if($start_time >= $end_time) {
         $out=$out.__('ERROR_WRONG_TIME_VALUE');
                        return 0;
      }   

      return 1;         
   }
   $out=$out.__('ERROR_MISSING_VALUE_TIME');
   return 0;
}
// }}}



// {{{ format_program_highchart_data()
// ROLE format data to be used by highchart for the programs part
// IN   $arr      an array containing datas
//      $date_start     
//   $type      the type of the plug
// RET data for highchart and cultibox programs
function format_program_highchart_data($arr,$date_start="",$type="") {
   $data="";
   if(empty($date_start)) {
      $ref_day=1;
      $ref_month=1;
      $ref_year=1970;
   } else {
       $ref_year=substr($date_start, 0, 4);
                $ref_month=substr($date_start, 5, 2);
                $ref_day=substr($date_start, 8, 2);
   }
   date_default_timezone_set('UTC');
   if(count($arr)>0) {
      if(is_array($arr)) {
      foreach($arr as $value) {
         if(("$type"=="lamp")&&($value['value']=="99.9")) {
            $value['value']=1;   
         } else if(("$type"=="log-lamp")&&($value['value']=="1")) {
                                $value['value']=99.9;
                        }
         if((empty($data))&&(strcmp($value['time_start'],"000000")!=0)) {
            $first=mktime(0,0,0,$ref_month,$ref_day,$ref_year)*1000;
            $data="[".$first.",0]";
            $last_time=$first;
            $last_value="0";
         } else if((empty($data))&&(strcmp($value['time_start'],"000000")==0)) {
                                $val_start=mktime(0,0,0,$ref_month,$ref_day,$ref_year)*1000;
                                $ehh=substr($value['time_stop'],0,2);
                                $emm=substr($value['time_stop'],2,2);
                                $ess=substr($value['time_stop'],4,2);
                                $val_end=mktime($ehh,$emm,$ess,$ref_month,$ref_day,$ref_year)*1000;
            if(strcmp($value['time_stop'],"235959")==0) {
                                   $data="[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
            } else {
               $data="[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
               $last_time=$val_end;
               $last_value=$value['value'];
            }
         }

         if((!empty($data))&&($value['value']!=0)&&(strcmp($value['time_start'],"000000")!=0)) {
               $shh=substr($value['time_start'],0,2);
               $smm=substr($value['time_start'],2,2);
               $sss=substr($value['time_start'],4,2);
               $val_start=mktime($shh,$smm,$sss,$ref_month,$ref_day,$ref_year)*1000;
               $ehh=substr($value['time_stop'],0,2);
                                        $emm=substr($value['time_stop'],2,2);
                                        $ess=substr($value['time_stop'],4,2);
                                        $val_end=mktime($ehh,$emm,$ess,$ref_month,$ref_day,$ref_year)*1000;
               if(strcmp($value['time_stop'],"235959")!=0) {
                  if("$last_time"!="$val_start") {
                     $data=$data.",[".$last_time.",0],[".$val_start.",0],[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
                  } else {
                     $data=$data.",[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
                  }
                  $last_time=$val_end;
                  $last_value=$value['value'];
               } else {
                                           $data=$data.",[".$last_time.",0],[".$val_start.",0],[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
               }
         }
         $last_val=$value;
      }
         }
      if((!empty($data))&&(strcmp($last_val['time_stop'],"235959")!=0)) {
         if("$ref_year"=="1970") {
               $last=mktime(0,0,0,$ref_month,$ref_day+1,$ref_year)*1000;
         } else {
               $last=mktime(23,59,59,$ref_month,$ref_day,$ref_year)*1000;
               
         }
         $data=$data.",[".$last_time=$val_end.",0],[".$last.",0]";
      } 
   } else {
      $first=mktime(0,0,0,$ref_month,$ref_day,$ref_year)*1000;
      $last=mktime(0,0,0,$ref_month,$ref_day+1,$ref_year)*1000;
      $data="[".$first.",0],[".$last.",0]";
   }
   return $data;
}
// }}}


// {{{ save_program_on_sd($sd_card,&$out,&$info)
// ROLE format data to be used by highchart for the programs part
// IN   $arr    an array containing datas
// RET data for highchart and cultibox programs
function save_program_on_sd($sd_card,$program,&$out,&$info) {
   if(is_file("${sd_card}/plugv")) {
         $file="${sd_card}/plugv";
         if(count($program)>0) {
            write_program($program,"$sd_card/plugv",$out);
         }
   } else {
      return false;
   }
}
// }}}


// {{{ write_program($data,$sd_card,$out)
// ROLE write programs into the sd card
// IN   $data      array containing datas to write
//   $file      file path to save data
//   $out      error or warning messages
// RET false is an error occured, true else
function write_program($data,$file,&$out="") {
   if($f=fopen("$file","w+")) {
      for($i=0; $i<count($data); $i++) {
         fputs($f,"$data[$i]"."\r\n");
      }
      fclose($f);
   } else {
      $out=$out.__('ERROR_WRITE_SD');
   }
}
// }}}


// {{{ write_plugconf($data,ŝd_card,$out)
// ROLE write plug_configuration into the sd card
// IN   $data           array containing datas to write
//      $sd_card        the sd card to be written
//      $out            error or warning messages
// RET false is an error occured, true else
function write_plugconf($data,$sd_card,&$out="") {
   for($i=0;$i<count($data);$i++) {
      $nb=$i+1;
      if($nb<10) {
         $file="$sd_card/plug0$nb";
      } else {
         $file="$sd_card/plug$nb";
      }

      if($f=fopen("$file","w+")) {
         fputs($f,"$data[$i]"."\r\n");
      }
      fclose($f);
   }
}
// }}}


// {{{ write_sd_conf_file()
// ROLE   save configuration into the SD card
// IN   $sd_card      location of the sd card to save data
//   $record_frequency   record frequency value
//   $update_frequency   update frequency value
//   $out         error or warning message
// RET none   
function write_sd_conf_file($sd_card,$record_frequency=1,$update_frequency=1,&$out="") {
   $record=$record_frequency*60;
   while(strlen($record)<4) {
      $record="0$record";
   }
   $update="000$update_frequency";
   $file="$sd_card/conf";
   if($f=fopen("$file","w+")) {
      fputs($f,"PLUG_UPDATE:$record\r\n");
      fputs($f,"LOGS_UPDATE:$update\r\n");
      fclose($f);
   } else {
      $out=$out.__('ERROR_WRITE_SD');
   }
}
//}}}


// {{{ write_calendar()
// ROLE save calendar informations into the SD card
// IN   $sd_card   sd card location
//   $data      data to write into the sd card
//   $out      error or warning messages
// RET none
function write_calendar($sd_card,$data,&$out="") {
   if(count($data)>0) {
      foreach($data as $val) {
         $file="$sd_card/logs/$val[month]/cal_$val[day]";
              if($f=fopen("$file","w+")) {
            fputs($f,"$val[number]");
            foreach($val['subject'] as $sub) {
                              fputs($f,"\r\n"."$sub");
            }
            fclose($f);
         }
      }
   }
}
//}}}


// {{{ check_tolerance_value()
// ROLE check the tolerance value
// IN   $type      the type of the plug 
//   $tolerancesave   the value to check
//   $out      error or warning message
// RET false is there is a wrong value, true else
function check_tolerance_value($type,$tolerance=0,&$out="") {
   if((strcmp($type,"heating")==0)||(strcmp($type,"ventilator")==0)) {
         if(($tolerance >= 0)&&($tolerance <= 25.5)) {
            return true;
         } else {
            $out=$out.__('ERROR_TOLERANCE_VALUE_DEGREE');
            return false;
         }
   } else if((strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)) {
         if(($tolerance >= 0)&&($tolerance <= 25.5)) {
                                return true;
                        } else {
            $out=$out.__('ERROR_TOLERANCE_VALUE_POURCENT');
                                return false;
                        }   
   }
   return true;
}
// }}}


// {{{ check_format_values_program($value)
// ROLE check AND format value of a program 
// IN   $value   value to check and format
// RET false is there is a wrong value, true else
function check_format_values_program($value="0") {
   $value=str_replace(',','.',$value);
   $value=str_replace(' ','',$value);
   if(($value>100)||($value<0)) return false; 
   if(!is_numeric($value)) return false; 
   return true;
}
// }}}


?>

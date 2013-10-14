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
} else if(is_file("../libs/l10n.php")) {
   require_once '../libs/l10n.php';
} else {
   require_once '../../libs/l10n.php';
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


   if(isset($args[1])) {
        return $ret;
   } else {
        return htmlentitiesOutsideHTMLTags($ret);
   }
}
//}}}


// {{{ htmlentitiesOutsideHTMLTags()
// ROLE encode a string in HTML and preserve HTML tags
// IN $htmltext         text to encode
// RET text encoded in HTML
function htmlentitiesOutsideHTMLTags($htmlText)
{
    $matches = Array();
    $sep = '###HTMLTAG###';

    preg_match_all(":</{0,1}[a-z]+[^>]*>:i", $htmlText, $matches);

    $tmp = preg_replace(":</{0,1}[a-z]+[^>]*>:i", $sep, $htmlText);
    $tmp = explode($sep, $tmp);

    for ($i=0; $i<count($tmp); $i++)
        $tmp[$i] = htmlentities($tmp[$i]);

    $tmp = join($sep, $tmp);

    for ($i=0; $i<count($matches[0]); $i++)
        $tmp = preg_replace(":$sep:", $matches[0][$i], $tmp, 1);

    return $tmp;
}
// }}}


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
   
   if (is_array($tmp)) {
      return $tmp;
   } else {
      return stripslashes(htmlentities($tmp));
   }
}
// }}}

// {{{ check_empty_string()
// ROLE check is a string is empty or only composed with CR
// IN $value         string to check
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


// {{{ get_log_value()
// ROLE get log's values from files and clean it
// IN $sd_card       path to the sd card
//    $month         month to be proceed
//    $day           day to be proceed
//    $array_line    array to store log's values
// RET none
function get_log_value($sd_card,$month,$day,&$array_line) {
   $file="$sd_card/logs/$month/$day";

   if(!file_exists("$file")) return false;
   $handle = fopen("$file", 'r');
   if ($handle)
   {
      while (!feof($handle))
      {
         $buffer = fgets($handle);
         if(check_empty_string($buffer)) {
            $temp = explode("\t", $buffer);
            if(count($temp)==($GLOBALS['NB_MAX_SENSOR']*2+1)) { 
                for($i=0;$i<count($temp);$i++) {
                    $temp[$i]=rtrim($temp[$i]);
                    $temp[$i]=str_replace(" ","",$temp[$i]);
                    $temp[$i]=str_replace("0000","",$temp[$i]);
                }

                $date_catch="20".substr($temp[0], 0, 2)."-".substr($temp[0],2,2)."-".substr($temp[0],4,2);
                $date_catch=rtrim($date_catch);
                $time_catch=substr($temp[0], 8,6);
                $time_catch=rtrim($time_catch);


                if((!empty($date_catch))&&(!empty($time_catch))&&(!empty($temp[0]))&&(strlen($date_catch)==10)&&(strlen($time_catch)==6)&&(strlen($temp[0])==14)) {
                        for($i=0;$i<$GLOBALS['NB_MAX_SENSOR'];$i++) {
                            $sensor_type=get_sensor_type($i,"$sd_card","$month","$day");
                            if(empty($sensor_type)) {
                                $sensor_type="2";
                            }

                            if((!empty($temp[2*$i+1]))||(!empty($temp[2*$i+2]))) {
                                        $array_line[] = array(
                                            "timestamp" => $temp[0],
                                            "temperature" => $temp[1+2*$i],
                                            "humidity" => $temp[2+2*$i],
                                            "date_catch" => $date_catch,
                                            "time_catch" => $time_catch,
                                            "sensor_nb" => $i+1,    
                                            "sensor_type" => $sensor_type
                                        );
                            } 
                        }
                }
            }
         }
      }
      fclose($handle);
   }
}
//}}}


// {{{ get_power_value()
// ROLE get powers values from files and clean it
// IN $file             file to explode
//    $array_line       array to store log's values
// RET none
function get_power_value($file,&$array_line) {
   $check=true;
   if(!file_exists("$file")) return false;
   $handle = fopen("$file", 'r');
   if ($handle)
   {
      while (!feof($handle))
      {
         $buffer = fgets($handle);
         $buffer=trim($buffer);
         if(!check_empty_string($buffer)) {
            if($check) {
                $check=false;
            } else {
                break;
            }
         } else {
            if(!$check) $check=true;
            $temp=explode("\t", $buffer);

            if(count($temp)==17) {
                for($i=0;$i<count($temp);$i++) {
                    $temp[$i]=rtrim($temp[$i]);
                }

                $date_catch="20".substr($temp[0], 0, 2)."-".substr($temp[0],2,2)."-".substr($temp[0],4,2);
                $date_catch=rtrim($date_catch);
                $time_catch=substr($temp[0], 8,6);
                $time_catch=rtrim($time_catch);

                if((!empty($date_catch))&&(!empty($time_catch))) {
                  for($i=1;$i<count($temp);$i++) {
                     if(strlen($temp[$i])!=4) {
                        return false;
                     }
                  }


                  for($i=1;$i<count($temp);$i++) {
                        if(is_numeric($temp[$i])) {
                            $array_line[] = array(
                                "timestamp" => $temp[0],
                                "power" => $temp[$i],
                                "plug_number" => $i,
                                "date_catch" => $date_catch,
                                "time_catch" => $time_catch
                            );    
                        }
                  }
                }
            }
         }
      }
      fclose($handle);
   }
}
//}}}

// {{{ clean_log_file()
// ROLE copy an empty file to clean a log file
// IN $file             file to clean
// RET true if the copy is errorless, false else
function clean_log_file($file) {
   if(is_file('main/templates/data/empty_file_big.tpl')) {
        $filetpl = 'main/templates/data/empty_file_big.tpl';
   } else if(is_file('../main/templates/data/empty_file_big.tpl')) {
        $filetpl = '../templates/data/empty_file_big.tpl';
   } else {
        $filetpl = '../../templates/data/empty_file_big.tpl';
   }

   if(!@copy($filetpl, $file)) return false;
   return true;
}
//}}}


// {{{ get_format_month($data)
// ROLE using a graphics data string containing value to make an average for month graphics
// IN $data   datas from a graphics containing values for a month
// RET return datas string value containing an average of the input fiel data
function get_format_month($data) {
   $arr = explode(",", $data);
   $count=0;
   $moy=0;
   $data_month="";
   foreach($arr as $value) {
      $count=$count+1;
      if($count==20) {
         if("$data_month"=="") {
            $data_month="$value";
         } else {
            $data_month="$data_month, $value";
         }
         $count=0;
      }
   }
   return $data_month;
}
//}}}


// {{{ get_current_lang()
// ROLE get the current language selected for the interface
// IN none
// RET current lang using the l10n format, ex: fr in url for joomla format is replaced by fr_FR
function get_current_lang() {
    $url=$_SERVER["REQUEST_URI"];
    $url=str_replace("/"," ",$url);
    $tab_url=explode(" ",$url);
    $lang="";
    if(count($tab_url)>0) {
        foreach($tab_url as $val) {
            $val=strtolower($val);
            switch($val) {  
                case 'fr':
                         $lang="fr_FR";
                         $_SESSION['TIMEZONE']="Europe/Paris";
                         break;
                case 'en':
                         $lang="en_GB";
                         $_SESSION['TIMEZONE']="Europe/London";
                         break;
                case 'it':
                         $lang="it_IT";
                         $_SESSION['TIMEZONE']="Europe/Rome";
                         break;
                case 'de':
                         $lang="de_DE";
                         $_SESSION['TIMEZONE']="Europe/Berlin";
                         break;
                case 'es':
                         $lang="es_ES";
                         $_SESSION['TIMEZONE']="Europe/Madrid";
                         break;
            }    
            
            if(strcmp($lang,"")!=0) {
                return $lang;
            }
        }
    } else {
          return "en_GB";
    }
}
//}}}


// {{{ get_short_lang()
// ROLE get the current language selected for the interface
// IN none
// RET current lang in short format example: fr_FR is replaced by fr
function get_short_lang($lang="") {
    if(strcmp("$lang","")==0) return "en";

    switch($lang) {
        case 'fr_FR': return "fr";
        case 'en_GB': return "en";
        case 'it_IT': return "it";
        case 'de_DE': return "de";
        case 'es_ES': return "es";
    }
    return "en";
}
//}}}



// }}}


// {{{ get_format_graph()
// ROLE get datas for the highcharts graphics
// IN $arr           array containing datas
//    $type          type to compute a hole
// RET $data         data at the highcharts format (a string)
function get_format_graph($arr,$type="log") {
   $err="";
   if(strcmp("$type","log")==0) {
        $hole=get_configuration("RECORD_FREQUENCY",$err)*4;
   } elseif(strcmp("$type","power")==0) {
        $hole=get_configuration("POWER_FREQUENCY",$err)*4;
   } 
   if(strcmp("$hole","")==0) $hole=10;
   $data="";
   $last_mm="";
   $last_hh="";
   $last_value="";

   if(count($arr)>0) {
   foreach($arr as $value) {
      $hh=substr($value['time_catch'], 0, 2);
      $mm=substr($value['time_catch'], 2, 2);

      if(("$hh:$mm" != "00:00")&&(empty($data))&&(empty($last_value))) {
         $data=fill_data("00","00","$hh","$mm","null","$data");
      } else if((check_empty_record("$last_hh","$last_mm","$hh","$mm",$hole))&&("$hh:$mm" != "00:00")) {
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
      if((check_empty_record("$last_hh","$last_mm","24","00",$hole))&&("$hh:$mm" != "00:00")) {
               $data=fill_data("$last_hh","$last_mm","24","00","$last_value","$data");
      } else {
         $data=fill_data("$last_hh","$last_mm","24","00","null","$data");
      }
   } 
   } else {
          $data=fill_data("00","00","24","00","null","$data");
   }
   return $data;
}
//}}}


// {{{ format_data_power()
// ROLE format data power like programs graph, using space time
// IN $data       array containing power values
// RET array containing formated datas
function format_data_power($data) {
         $arr=array();
         if(count($data)==0) return $arr;
         foreach($data as $datap) {
                        $arr[]=array(
                            "time_catch" => $datap['time_catch'],
                            "record" => $datap['record']
                        );
          }
          return $arr;
}
// }}}



// {{{ fill_data()
// ROLE fill highcharts data,between two time spaces, using a specific value
// IN $fhh        start hours
//    $fmm        start minutes
//    $lhh        end hours
//    $lmm        end minutes
//    $val        value used to fill time spaces
//    $data       data at the highcharts format (a string)
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



// {{{ check_empty_record()
// ROLE check if there is an empty record. An empty reccord is defined if the time spaces
// between two values is greatan than $hole variable
// IN $last_hh       last record hours
//    $last_mm       last record minutes
//    $hh            first record hours
//    $mm            first record minutes
//    $hole          number of minutes to determine if there is a hole beetween two values
// RET true is there isn't an empty record, false else.
function check_empty_record($last_hh,$last_mm,$hh,$mm,$hole=10) {
      $lhh= 60 * $last_hh + $last_mm;
      $chh= 60 * $hh + $mm;

      if($chh-$lhh<=$hole) {
         return true;
      } else {
         return false;
      }
}
//}}}


// {{{ check_format_date()
// ROLE check date format (month with the format MM ou complete date: YYYY-MM-DD)
// IN $date       date to check
//    $type       the type: month or days
// RET true is the format match the type, false else
function check_format_date($date="",$type) {
   $date=str_replace(' ','',"$date");
   if("$type"=="days") {
      if(strlen("$date")!=10) {
         return 0;
      }

      $tmp=explode("-","$date");
      if(count($tmp)!=3) return 0;

      if(!preg_match('#^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$#', $date)) {
         return 0;
      }

      return checkdate($tmp[1], $tmp[2], $tmp[0]);
   }

   if("$type" == "month") {
      if(strlen("$date")!=7) {
         return 0;
      }

      $tmp=explode("-","$date");
      if(count($tmp)!=2) return 0;

      if(!preg_match('#^[0-9][0-9][0-9][0-9]-[0-9][0-9]$#', $date)) {
         return 0;
      }

      return checkdate($tmp[1], "1", $tmp[0]);
   }
   return 0;
}
//}}}


// {{{ check_numeric_value()
// ROLE check if a value is numeric or not
// IN $value         value to check
// RET true is $value is numeric, false else
function check_numeric_value($value="") {
   if("$value"=="0") {
         return true;
   }

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
// IN  $hdd     list of hdd available which could be configured as cultibox SD card
// RET false if nothing is found, the sd card place else
function get_sd_card(&$hdd="") {
        //For Linux
        $ret=false;
        $dir="/media";
        $os=php_uname('s');
        switch($os) {
                case 'Linux':
                        //In Ubuntu Quantal mounted folders are now in /media/$USER directory
                        $user=get_current_user();
                        if((isset($user))&&(!empty($user))) {
                            $dir="/media/".$user;
                            if(is_dir($dir)) {
                                $rep = opendir($dir);
                                while ($f = readdir($rep)) {
                                        if(is_dir("$dir/$f")) {
                                                if((strcmp("$f",".")!=0)&&(strcmp("$f","..")!=0)) {
                                                    $hdd[]="$dir/$f";
                                                    if(check_cultibox_card("$dir/$f")) {
                                                        $ret="$dir/$f";
                                                    }
                                                }
                                        }
                                }
                            }
                        }
                        break;

                case 'Mac':
                case 'Darwin':
                        $dir="/Volumes";
                        if(is_dir($dir)) {
                                $rep = opendir($dir);
                                        while ($f = readdir($rep)) {
                                        if(is_dir("$dir/$f")) {
                                                if((strcmp("$f",".")!=0)&&(strcmp("$f","..")!=0)) {
                                                    $hdd[]="$dir/$f";
                                                    if(check_cultibox_card("$dir/$f")) {
                                                        $ret="$dir/$f";
                                                    }
                                                }
                                        }
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


// {{{ check_times()
// ROLE check times send by user to reccord a plug behaviour
// IN   $start_time      time starting the event
//      $end_time        time ending the event
// RET 1 if ok, 0 if there is an error or 2 if start time > end time
function check_times($start_time="",$end_time="") {
   if((!empty($start_time))&&(isset($start_time))&&(!empty($end_time))&&(isset($end_time))) {
      $start_time=str_replace(' ','',"$start_time");

      if(strcmp($start_time,$end_time)==0) {
         return 0;
      }

      $end_time=str_replace(' ','',"$end_time");

      $sth=substr($start_time, 0, 2);
      $stm=substr($start_time, 3, 2);
      $sts=substr($start_time, 6, 2);

      $enh=substr($end_time, 0, 2);
      $enm=substr($end_time, 3, 2);
      $ens=substr($end_time, 6, 2);
   
      $start_time= mktime($sth, $stm, $sts);
      $end_time= mktime($enh, $enm, $ens);

      if($start_time >= $end_time) {
              return 2;
      }   

      return 1;         
   }
   return 0;
}
// }}}



// {{{ format_program_highchart_data()
// ROLE format data to be used by highchart for the programs part
// IN   $arr               an array containing datas
//      $date_start          
// RET data for highchart and cultibox programs
function format_program_highchart_data($arr,$date_start="") {
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
   if($GLOBALS['DEBUG_TRACE']) {
         echo "<br />Debug Trace Highchart Data:<br />";
   }
   if(count($arr)>0) {
      if(is_array($arr)) {
      foreach($arr as $value) {
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
                   if("$last_time"!="$val_start") {
                        $data=$data.",[".$last_time.",0],[".$val_start.",0],[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
                   } else {
                        $data=$data.",[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
                   }
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


// {{{ save_program_on_sd()
// ROLE format data to be used by highchart for the programs part
// IN   $sd_card        path to the sd card to save datas
//      $program        the program to be save in the sd card 
//      $out            error or warning messages
// RET data for highchart and cultibox programs
function save_program_on_sd($sd_card,$program,&$out) {
   if(is_file("${sd_card}/cnf/prg/plugv")) {
      $file="${sd_card}/cnf/prg/plugv";
      if(count($program)>0) {
         if($f=fopen("$sd_card/cnf/prg/plugv","w+")) {
            $nbPlug = count($program);
            while(strlen($nbPlug)<3) {
                $nbPlug="0$nbPlug";
            }
       
            fputs($f,$nbPlug."\r\n");
            for($i=0; $i<count($program); $i++) {
                fputs($f,"$program[$i]"."\r\n");
            }
            fclose($f); 
        } else {
            return false;
        }
      }
   } else {
      return false;
   }
   return true;
}
// }}}


// {{{ write_program()
// ROLE write programs into the sd card
// IN   $data         array containing datas to write
//      $file         file path to save data
//      $out          error or warning messages
// RET false is an error occured, true else
function write_program($data,$file,&$out) {
   if($f=@fopen("$file","w+")) {
      $nbPlug = count($data);
      while(strlen($nbPlug)<3) {
         $nbPlug="0$nbPlug";
      }
      fputs($f,$nbPlug."\r\n");
      for($i=0; $i<count($data); $i++) {
         fputs($f,"$data[$i]"."\r\n");
      }
      fclose($f);
   } else {
      $out[]=__('ERROR_WRITE_SD');
   }
}
// }}}


// {{{ compare_program()
// ROLE compare programs and data to check if they are up to date
// IN   $data         array containing datas to write
//      $sd_card      sd card path to save data
// RET false is there is something to write, true else
function compare_program($data,$sd_card) {
    if(is_file("${sd_card}/cnf/prg/plugv")) {

         $nb=0;
         $nbdata=count($data);
         $file="${sd_card}/cnf/prg/plugv";

         if(count($data)>0) {
            $handle = fopen($file, 'r');
            if ($handle) {
               while(!feof($handle)) {
                  $buffer = fgets($handle);
                  $buffer=rtrim($buffer);

                  if(!empty($buffer)) {
                     if($nb==0) {
                        if($nbdata!=$buffer) { 
                         return false; 
                        } 
                     } else {
                        if(strcmp($data[$nb-1],$buffer)!=0) { 
                           return false;
                        }  
                     }
                     $nb=$nb+1;
                  } else if($nb==0) {
                    return false;
                  } 
               }
               fclose($handle);
            }
            return true;
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
            }
            $pluga[]="$tmp_pluga";
        }

        $nbdata=count($pluga);

        if(count($pluga)>0) {
            $handle = fopen($file, 'r');
            if ($handle) {
               while(!feof($handle)) {
                  $buffer=fgets($handle);
                  $buffer=rtrim($buffer);

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
               fclose($handle);
            }
            return true;
       }
   }
   return false;
}
// }}}


// {{{ write_pluga()
// ROLE write plug_a into the sd card
// IN   $sd_card        the sd card to be written
//      $out            error or warning messages
// RET false is an error occured, true else
function write_pluga($sd_card,&$out) {
   $file="$sd_card/cnf/plg/pluga";

   if($f=fopen("$file","w+")) {
      $pluga=Array();
      $pluga[]=$GLOBALS['NB_MAX_PLUG'];
      for($i=0;$i<$GLOBALS['NB_MAX_PLUG'];$i++) {
        $tmp_power_max=get_plug_conf("PLUG_POWER_MAX",$i+1,$out);
        if(strcmp("$tmp_power_max","1000")==0) {
            $tmp_pluga=$GLOBALS['PLUGA_DEFAULT'][$i];
        } elseif(strcmp("$tmp_power_max","3500")==0) {
            $tmp_pluga=$GLOBALS['PLUGA_DEFAULT_3500W'][$i];   
        }
        $pluga[]="$tmp_pluga";
      }

      foreach($pluga as $val) {
                fputs($f,"$val"."\r\n");
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

      if($f=fopen("$file","w+")) {
         fputs($f,"$data[$i]"."\r\n");
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

        if($handle=@fopen($file,"r")) {
            while (!feof($handle)) {
               $buffer[] = fgets($handle);
            }
        }
        fclose($handle);
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
// RET false if there is a difference, true else
function compare_sd_conf_file($sd_card="",$record_frequency,$update_frequency,$power_frequency,$alarm_enable,$alarm_value,$reset_value) {
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
    $conf[]="RTC_OFFSET_:0000";
    $conf[]="RESET_MINAX:$reset_value";
    $conf[]="PRESSION___:0000";

    $nb=0;


    $handle = fopen($file, 'r');
    if ($handle) {
        while(!feof($handle)) {
            $buffer=fgets($handle);
            $buffer=rtrim($buffer);

            if(!empty($buffer)) {
                if(strcmp($conf[$nb],$buffer)!=0) {
                       return false;
                }
                $nb=$nb+1;
            }
        }
        fclose($handle);
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
//   $out                error or warning message
// RET false if an error occured, true else  
function write_sd_conf_file($sd_card,$record_frequency=1,$update_frequency=1,$power_frequency=1,$alarm_enable="0000",$alarm_value="50.00",$reset_value,&$out) {
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

   $reset_value=str_replace(":","",$reset_value);
   if((strlen($reset_value)!=4)||($reset_value<0)) {
        $reset_value="0000";
   } 

   $update="000$update_frequency";
   $file="$sd_card/cnf/conf";
   if($f=@fopen("$file","w+")) {
      fputs($f,"PLUG_UPDATE:$update\r\n");
      fputs($f,"LOGS_UPDATE:$record\r\n");
      fputs($f,"POWR_UPDATE:$power\r\n"); 
      fputs($f,"ALARM_ACTIV:$alarm_enable\r\n");
      fputs($f,"ALARM_VALUE:$alarm_value\r\n");
      fputs($f,"ALARM_SENSO:$alarm_senso\r\n");
      fputs($f,"ALARM_SENSS:$alarm_senss\r\n");
      fputs($f,"RTC_OFFSET_:0000\r\n");
      fputs($f,"RESET_MINAX:$reset_value\r\n");
      fputs($f,"PRESSION___:0000\r\n");
      fclose($f);
   } else {
      $out[]=__('ERROR_WRITE_SD_CONF');
      return false;
   }
   return true;
}
//}}}


// {{{ concat_calendar_entries()
// ROLE concat calendar entries to use several comments for the same day
// IN   $data        data to be proccessed
// IN   $month       month to be checked
// IN   $day         day to be checked
// RET return an array containing all datas for the day checked
function concat_calendar_entries($data,$month,$day) {
         $new_data=array();

         foreach($data as $val) {
            $current=strtotime($month."/".$day);
            $start=strtotime($val['start_month']."/".$val['start_day']);
            $end=strtotime($val['end_month']."/".$val['end_day']);


            if(($start<=$current)&&($end>=$current)) {
               if(empty($new_data)) {
                  $new_data=$val;
               } else {
                  if($new_data['number']==18) break;

                  $new_data['description'][]="             ";
                  $new_data['number']=$new_data['number']+1;
                  if($new_data['number']==18) break;

                  foreach($val['subject'] as $sub) {
                     $new_data['description'][]=$sub;
                     $new_data['number']=$new_data['number']+1;
                     if($new_data['number']==18) break;
                  }

                  foreach($val['description'] as $desc) {
                     $new_data['description'][]=$desc;
                     $new_data['number']=$new_data['number']+1;
                     if($new_data['number']==18) break;
                  }
               }
            }
         }
   return $new_data;
}
// }}}

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
        
                $sq = opendir($path."/".$i); 
                while ($f = readdir($sq)) {
                    if("$f" != "." && "$f" != "..") {
                        if(preg_match('/^cal_/', $f)) {
                            unlink($path."/".$i."/".$f);
                        }
                    }
                }
            }
        } elseif((strcmp("$start","")!=0)&&(strcmp("$end","")==0)) {
            $stmon=substr($start,5,2);
            $stday=substr($start,8,2);

            if(is_file($sd_card."/logs/".$stmon."/cal_".$stday)) {
                unlink($sd_card."/logs/".$stmon."/cal_".$stday);
            }
        } elseif((strcmp("$start","")!=0)&&(strcmp("$end","")!=0)) {
            $stmon=substr($start,5,2);
            $stday=substr($start,8,2);
            $edmon=substr($end,5,2);
            $edday=substr($end,8,2);

           for($i=$stmon;$i<=$edmon;$i++) {
               while(1) {
                    if(strlen("$i")<2) {
                        $i="0".$i;
                    } 
                
                    if(strlen("$stday")<2) {
                        $stday="0".$stday;
                    }

                    if(is_file($sd_card."/logs/".$i."/cal_".$stday)) {
                        unlink($sd_card."/logs/".$i."/cal_".$stday);
                    }
                   
                    if(($stday==31)||(($stday==$edday)&&($i==$edmon))) {
                        $stday=1;
                        break;
                    }
                    $stday=$stday+1;
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
//    $data            data to write into the sd card
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
        while(1) {
               $val=concat_calendar_entries($data,$month,$day);
               if(!empty($val)) {
                  while(strlen($day)<2) {
                     $day="0$day";
                  }
                  while(strlen($month)<2) {
                     $month="0$month";
                  }
                  $file="$sd_card/logs/$month/cal_$day";
                  if($f=fopen("$file","w+")) {
                     // format number of line to show. Must be 3 caractere width
                     
                     $number_to_show  = "$val[number]";
                     while(strlen($number_to_show)<3) {
                        $number_to_show="0$number_to_show";
                     }

                     fputs($f,"$number_to_show"."\r\n");
                     foreach($val['subject'] as $sub) {
                        fputs($f,"$sub"."\r\n");
                     }
                     foreach($val['description'] as $desc) {
                        fputs($f,"$desc"."\r\n");
                     }
                     fclose($f);
                  } else {  
                    $status=false;
                  }
               }

               if(($month==$month_end)&&($day==$day_end)) break;

               if($day==31) {
                 $day="01";
                 $month=$month+1;
               } else {
                 $day=$day+1;
               }
               unset($val);
        } 
       }

    }
    return $status;
}
//}}}


// {{{ check_date()
// ROLE check the an interval of dates is correct
// IN   $datestart      first date
//      $dateend        second date
// RET false if $datestart => $dateend, true else
function check_date($datestart="",$dateend="") {
         $year_start=substr($datestart,0,4); 
         $month_start=substr($datestart,5,2);
         $day_start=substr($datestart,8,2);

         $year_end=substr($dateend,0,4);
         $month_end=substr($dateend,5,2);
         $day_end=substr($dateend,8,2);

         if($year_start<$year_end) {
               return true;
         }

         if($year_start>$year_end) {
               return false;
         } 

         if($month_start<=$month_end) {
              if($month_start==$month_end) {
                  if($day_start<=$day_end) {
                      return true;
                  }
              } else {
                  return true;
              }
         }
         return false;

}
// }}}


// {{{ check_tolerance_value()
// ROLE check the tolerance value
// IN $type               the type of the plug 
//    $tolerance          the value to check
// RET false is there is a wrong value, true else
function check_tolerance_value($type,&$tolerance=0) {
   $tolerance=str_replace(",",".",$tolerance);

    if(!is_numeric($tolerance)) return false;

   if((strcmp($type,"heating")==0)||(strcmp($type,"ventilator")==0)) {
      if(($tolerance >= 0)&&($tolerance <= 10)) {
         return true;
      } else {
         return false;
      }
   } else if((strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)) {
      if(($tolerance >= 0)&&($tolerance <= 25.5)) {
         return true;
      } else {
         return false;
      }   
   }
   return true;
}
// }}}


// {{{ check_format_values_program()
// ROLE check AND format value of a program 
// IN   $value       value to check and format
// IN   $type        temp or humi - type to check
// RET error message if there is a wrong value, true else
function check_format_values_program(&$value="0",$type="temp") {
   $value=str_replace(',','.',$value);
   $value=str_replace(' ','',$value);

    if(!is_numeric($value)) {   
                return 2;
   }

   if(strcmp($type,"temp")==0) {
      if(($value>60)||($value<5)) {
        return 3;
      }
   } elseif(strcmp($type,"humi")==0) {
      if(($value>95)||($value<10)) {
                return 4;
      }
   } else {
   if(($value>99.9)||($value<0)) {
                return 5;
       }
   }
   return 1;
}
// }}}

// {{{ check_alarm_value()
// ROLE check is a value for the alarm is correct
// IN   $value       value to check
// OUT  false is there is a wrong value, true else
function check_alarm_value($value="0") {
   $value=str_replace(',','.',$value);
   $value=str_replace(' ','',$value);
   if(($value>=100)||($value<=0)) return false;
   if(!is_numeric($value)) return false;
   return true;
}
// }}}

// {{{ check_regul_value()
// ROLE check is a value for the regulation is correct
// IN   $value       value to check
// OUT  false is there is a wrong value, true else
function check_regul_value($value="0") {
   $value=str_replace(',','.',$value);
   $value=str_replace(' ','',$value);
   if(($value>99.99)||($value<0)) return false;
   if(!is_numeric($value)) return false;
   return true;
}
// }}}


// {{{ check_and_copy_firm()
// ROLE check if firmwares (firm.hex,emetteur.hex) has to be copied and do the copy into the sd card
// IN  $sd_card     the sd card pathname 
// RET true if if as least one firmware has been copied, false else
function check_and_copy_firm($sd_card) {
   $new_firm="";
   $current_firm="";
   $copy=false;

   $firm_to_test[]="firm.hex";
   $firm_to_test[]="bin/emetteur.hex";
   $firm_to_test[]="bin/sht.hex";

   foreach($firm_to_test as $firm) { 
        if(is_file("tmp/$firm")) {
            $new_file="tmp/$firm";
        } else if(is_file("../tmp/$firm")) {
            $new_file="../tmp/$firm";
        } else if(is_file("../../tmp/$firm")) {
            $new_file="../../tmp/$firm";
        } else {
            $new_file="../../../tmp/$firm";
        }

        $current_file="$sd_card/$firm";

        if(is_file("$new_file")) {
            $handle = fopen("$new_file", 'r');
            if ($handle) {
                $new_firm = fgets($handle);
            }
            fclose($handle);
        }


        if(is_file("$current_file")) {
            $handle = fopen("$current_file", 'r');
            if ($handle) {
                $current_firm = fgets($handle);
            }
            fclose($handle);
        }

        if((isset($new_firm))&&(!empty($new_firm))&&(isset($current_firm))&&(!empty($current_firm))) {
                $current_firm=trim("$current_firm");
                $new_firm=trim("$new_firm");

                if((strlen($current_firm)==43)&&(strlen($new_firm)==43)) {   
                    $new_firm=substr($new_firm,9,4); 
                    $current_firm=substr($current_firm,9,4);

                    if(hexdec($new_firm) > hexdec($current_firm)) {
                        copy($new_file, $current_file);
                        $copy=true;
                    }
                }
        } elseif((!is_file("$current_file"))&&(is_file("$new_file"))) {
                copy($new_file, $current_file);
                $copy=true;
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


// {{{ check_and_copy_log()
// ROLE check if the log.txt exists and if not, create it from empty_file64.tpl
// IN  $sd_card     the sd card pathname 
// RET false if an error occured, true else
function check_and_copy_log($sd_card) {
    if(!is_file("$sd_card/log.txt")) {
        if(is_file("main/templates/data/empty_file_64.tpl")) {
            copy("main/templates/data/empty_file_big.tpl", "$sd_card/log.txt");   
        } else if(is_file("../templates/data/empty_file_64.tpl")) {
            copy("../templates/data/empty_file_big.tpl", "$sd_card/log.txt");
        } else if(is_file("../../templates/data/empty_file_64.tpl")) {
            copy("../../templates/data/empty_file_big.tpl", "$sd_card/log.txt");
        } else {
            return false;
        }
    }
    return true;
}
// }}}


// {{{ check_and_copy_index()
// ROLE check if the index file exists and if not, create it 
// IN  $sd_card     the sd card pathname 
// RET false if an error occured, true else
function check_and_copy_index($sd_card) {
    if(!is_file("$sd_card/logs/index")) {
        if(is_file("tmp/logs/index")) {
            copy("tmp/logs/index", "$sd_card/logs/index");
        } else if(is_file("../tmp/logs/index")) {
            copy("../tmp/logs/index", "$sd_card/logs/index");
        } else if(is_file("../../tmp/logs/index")) {
            copy("../../tmp/logs/index", "$sd_card/logs/index");
        } else if(is_file("../../../tmp/logs/index")) {
            copy("../../../tmp/logs/index", "$sd_card/logs/index");
        } else {
            return false;
        }
    }
    return true;
}
// }}}



// {{{ clean_popup_message()
// ROLE clean highchart message by removing non-appropriate char for javascript
// IN  $message         message to be cleaned
// RET   new message cleaned 
function clean_highchart_message($message="") {
   $old = array("'","&eacute;","&agrave;","&egrave;","&ecirc;","&deg;","&ucirc;","&ocirc;");
   $new   = array("\'","é","à","è","ê","°","û","ô");
   return str_replace($old, $new, $message)."\\n\\n";
}
// }}}


// {{{ clean_calendar_message()
// ROLE clean calendar field to be formated for the cultibox that doesn't manage accents
// IN  $message         message to be cleaned
// RET new message cleaned 
function clean_calendar_message($message="") {
   $search = array('À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ');
   $replace = array('A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y');
   $res=str_replace($search, $replace, $message);

   $tmp="";
   for($i=0;$i<strlen($res);$i++) { 
       if((ord($res[$i])>=32)&&(ord($res[$i])<=122)) {
          $tmp=$tmp.$res[$i];
       } else {
         $tmp=$tmp." ";
       }
   }
   return $tmp;
}
// }}}


// {{{ popup_message()
// ROLE popup message formatting
// IN  $message         message to be formatted
// RET new message cleaned 
function popup_message($message="") {
   return $message."<br /><br />";
}
// }}}



// {{{ get_nb_days()
// ROLE get number of days beetwen two dates
// IN   $start_date       first date
//      $end_date         second date
// RET   return number of days beetwen dates or -1
function get_nb_days($start_date="",$end_date="") {
         if((!isset($start_date))||(empty($start_date))||(!isset($end_date))||(empty($end_date))) { return -1; }

          $year_start=substr($start_date,0,4); 
          $month_start=substr($start_date,5,2);
          $day_start=substr($start_date,8,2);
          $year_end=substr($end_date,0,4);
          $month_end=substr($end_date,5,2);
          $day_end=substr($end_date,8,2);

          $first=mktime(0,0,0,$month_start,$day_start,$year_start);
          $second=mktime(0,0,0,$month_end,$day_end,$year_end);

          if($first>$second) { return -1; }

          return round(($second-$first)/86400);
}
// }}}


// {{{ check_update_available()
// ROLE get the update file from a distant site and check available updates
// IN    $version version of the current software
//       $out     errors or warnings messages
// RET   none  
function check_update_available($version,&$out) {
         $version=str_replace(".","",$version);
         $temp=explode("-", $version);
         $version=$temp[0];

         if(isset($GLOBALS['UPDATE_FILE'])&&(!empty($GLOBALS['UPDATE_FILE']))) {
               $file=$GLOBALS['UPDATE_FILE'];
               if($handle=@fopen($file,"r")) {
                  $val=fgets($handle);
                  fclose($handle);
                  if(!empty($val)) { 
                    $tmp_version=str_replace(".","","$val");
                    $tmp_version=trim($tmp_version);
                    if(($version<$tmp_version)) {
                        return true;
                    }
                  }
               } else {
                  $out[]=__('ERROR_REMOTE_UPDATE_FILE');
               }
         }
         return false;
}
// }}}


// {{{ find_informations()
// ROLE find some informations from the log.txt file
// IN    $ret       array to return containing informations
//       $log_file  path to the log file
// RET   none
function find_informations($log_file,&$ret) {
if(!file_exists("$log_file")) return $ret;
   $handle = fopen("$log_file", 'r');
   $ret["nb_reboot"]=0;
   $ret["last_reboot"]="";
   $ret["cbx_id"]="";
   $ret["firm_version"]="";
   $ret["emeteur_version"]="";
   $ret["sensor_version"]="";
   $ret["log"]="";

   if ($handle) {
      while (!feof($handle)) {
         $buffer=fgets($handle);
         $buffer=trim($buffer);

        if(strcmp($buffer,"")==0) break;

        if(strcmp($ret["log"],"")==0) {
            $ret["log"]=$buffer;
         } else {
            $ret["log"]=$ret["log"]."#".$buffer;
         }

        switch (substr($buffer,14,1)) {
            case 'B':
                $ret["nb_reboot"]=$ret["nb_reboot"]+1;
                $ret["last_reboot"]=substr($buffer,0,14);
                break;
            case 'I':
                $ret["cbx_id"]=substr($buffer,16,5);
                break;
            case 'V':
                $ret["firm_version"]=substr($buffer,15,7); 
                break;
            case 'S':
                $ret["emeteur_version"]=substr($buffer,15,7);
                break;
            case 'E':
                $ret["sensor_version"]=substr($buffer,15,7);
                break;
        }

      }
      fclose($handle);
   }

   return $ret;
}
// }}}


// {{{ format_data_sumary()
// ROLE format actions of a plug to be displayed in a sumary
// IN    $data       plug's informations array
// RET   sumary formated 
function format_data_sumary($data) {
    $resume=array();
    $unity="";
    $number=1;
    foreach($data as $plugs_info) {
        $resume[$number]="";
        if((empty($plugs_info["data"]))||(empty($plugs_info['PLUG_NAME']))||(empty($number))|(empty($plugs_info['PLUG_TYPE']))) {
            $resume[]="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.":<br /> ".$plugs_info['PLUG_NAME']."</i></b></p><p align='center'>".__('EMPTY_ACTION')."</p>";
        } else {
            switch($plugs_info['PLUG_TYPE']) {
                    case 'lamp': $unity=""; break;
                    case 'other': $unity=""; break;
                    case 'ventilator': $unity="°C"; break;
                    case 'heating': $unity="°C"; break;
                    case 'humidifier': $unity="%"; break; 
                    case 'dehumidifier': $unity="%"; break;
            }
            $actions=array();
            $actions=explode('[',$plugs_info["data"]);
            $prev_value="0";
            $value=array();
            foreach($actions as $action) {
                $action=str_replace('],','',$action);
                $action=str_replace(']','',$action);
                $action=explode(',',$action);
                if((isset($action[0]))&&(isset($action[1]))&&(strcmp($action[0],"")!=0)&&(strcmp($action[1],"")!=0)) {
                      $heure=date ("H:i:s", $action[0]/1000);

                      if(strcmp("$prev_value","$action[1]")!=0) {
                          if(strcmp($action[1],"0")!=0) {
                            if(strcmp($resume[$number],"")==0) {
                                        if(strcmp($action[1],"99.9")==0) {
                                            $resume[$number]="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.":<br /> ".$plugs_info['PLUG_NAME']."</i></b></p><p align='left'>".__('SUMARY_ON')." ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        } else {
                                            $resume[$number]="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.":<br /> ".$plugs_info['PLUG_NAME']."</i></b></p><p align='left'>".__('SUMARY_REGUL_ON')." (".$action[1].$unity.") ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        }
                                    } else {
                                        if(strcmp($action[1],"99.9")==0) {
                                            $resume[$number]=$resume[$number].__('SUMARY_ON')." ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        } else {
                                            $resume[$number]=$resume[$number].__('SUMARY_REGUL_ON')." (".$action[1].$unity.") ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        }
                                    }
                                    $prev_value=$action[1];
                            } else if(strcmp($prev_value,"0")!=0) {
                                        if(strcmp("$prev_value","99.9")==0) {
                                            $resume[$number]=$resume[$number].__('SUMARY_OFF')." ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        } else {
                                            $resume[$number]=$resume[$number].__('SUMARY_REGUL_OFF')." ".__('SUMARY_HOUR')." ".$heure."<br />";

                                        }
                                            $prev_value=0;
                            } 
                        }
                    } 
                }

                if(strcmp($resume[$number],"")==0) { 
                        $resume[$number]="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.": <br />".$plugs_info['PLUG_NAME']."</i></b></p><p align='center'>".__('EMPTY_ACTION')."</p>";
                } else {
                        $resume[$number]=$resume[$number]."</p>";
                }

            }
       $number=$number+1;
    }
    return $resume;
}
// }}}


// {{{ format_axis_data();
// ROLE format data from a program to be displayed with a diffrent axis on highchart graphics
// IN    $data       program to be formatted
//       $max        max of the axis
//       $out        error or warning message
// RET   data formated 
function format_axis_data($data,$max=0,&$out) {
           if($max==0) return $data;
           if(count($data)<=0) return $data;

           $prog_max=99.9;
           $new_data=array();

           foreach($data as $val) {
                $val['value']=($val['value']/$prog_max)*$max;
                $new_data[]=array(
                                    "time_start" => $val['time_start'],
                                    "time_stop" => $val['time_stop'],
                                    "value" => $val['value']
                                );
           }
           return $new_data;
}


// }}}


// {{{ check_format_time()
// ROLE check time format: HH:MM:SS
// IN   $time      time to be checked
// RET 1 if ok, 0 if there is an error 
function check_format_time($time="") {
    if(strlen("$time")!=8) {
         return 0;
    }

    if(!preg_match('#^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$#', $time)) {
         return 0;
    }

    return 1;
}


// }}}


// {{{ get_browser_infos()
// ROLE get client browser informations
// RET array containing browser datas
function get_browser_infos() {
 $browser = array(
    'version'   => '0.0.0',
    'majorver'  => 0,
    'minorver'  => 0,
    'build'     => 0,
    'name'      => 'unknown',
    'useragent' => ''
  );

  $browsers = array(
    'firefox', 'msie', 'opera', 'chrome', 'safari', 'mozilla', 'seamonkey', 'konqueror', 'netscape',
    'gecko', 'navigator', 'mosaic', 'lynx', 'amaya', 'omniweb', 'avant', 'camino', 'flock', 'aol'
  );

  if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $browser['useragent'] = $_SERVER['HTTP_USER_AGENT'];
    $user_agent = strtolower($browser['useragent']);
    foreach($browsers as $_browser) {
      if (preg_match("/($_browser)[\/ ]?([0-9.]*)/", $user_agent, $match)) {
        $browser['name'] = $match[1];
        $browser['version'] = $match[2];
        @list($browser['majorver'], $browser['minorver'], $browser['build']) = explode('.', $browser['version']);
        break;
      }
    }
  }
  return $browser;
}
// }}}


// {{{ check_browser_compat()
// ROLE check if the client browser in compatible with the cultibox
// RET true if compat, false else
function check_browser_compat($tab) {
    if(count($tab)>0) {
        switch($tab['name']) {
            case 'firefox':
                //Do not support firefox 1.0 and 2.0
                if($tab['majorver']<=2) return false;
                return true;
                break;
            case 'msie':
                //Do not support IE 6.0 or earlier
                if($tab['majorver']<=6) return false;
                return true;
                break;
            case 'chrome':
                //Support every version of chrome
                return true;
                break;
            case 'safari':
		        //Support for Mac Os X Safari
		        if($tab['majorver']<500) return false;
		        return true;
		        break;
        }
        return false;
    }
    return true;
}
// }}}


// {{{ check_sd_card()
// ROLE check if the soft can write on a sd card
//  IN      $sd        the sd_card path to be checked
// RET true if we can, false else
function check_sd_card($sd="") {
    if(@$f=fopen("$sd/test.txt","w+")) {
       fclose($f);
       unlink("$sd/test.txt");
       return true;
   } else {
       return false;
   }
}
// }}}


// {{{  getmicrotime()
// ROLE    send a time to compute page loading
//  IN     none
// RET     time elapsed 
/* USAGE
    $debut = getmicrotime();
    $fin = getmicrotime();
    echo "Page générée en ".round($fin-$debut, 3) ." secondes.<br />";
*/
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
// }}}



// {{{  convert_SIZE()
// ROLE    convert octet into kB,MB,GB
//  IN     size to be converted
// RET     size converted 
function convert_SIZE($size)
{
    $unite = array('B','kB','MB','GB');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unite[$i];
}
// }}}


// {{{  aff_variables()
// ROLE    display all variables used and its memory
//  IN     none
// RET     none
function aff_variables()
{
   echo '<br/>';
   global $datas ;
   foreach($GLOBALS as $Key => $Val)
   {
      if ($Key != 'GLOBALS')
      {   
         echo' <br/>'. $Key .' &asymp; '.sizeofvar( $Val );
      }
   }
    echo' <br/>';
}
// }}}


//{{{  Same as aff_variables but for a single variable
function sizeofvar($var)
{

  $start_memory = memory_get_usage();   
  $temp =unserialize(serialize($var ));   
  $taille = memory_get_usage() - $start_memory;
  return convert_SIZE($taille) ;
}
// }}}


// {{{  memory_stat()
// ROLE    display memory usage for a PHP script
//  IN     none
// RET     none
function memory_stat()
{
   echo  'Mémoire -- Utilisé : '. convert_SIZE(memory_get_usage(false)) .
   ' || Alloué : '.
   convert_SIZE(memory_get_usage(true)) .
   ' || MAX Utilisé  : '.
   convert_SIZE(memory_get_peak_usage(false)).
   ' || MAX Alloué  : '.
   convert_SIZE(memory_get_peak_usage(true)).
   ' || MAX autorisé : '.
   ini_get('memory_limit') ;  ;
}


// {{{ find_new_ine()
// ROLE    check if a start or end time for a program will add a new line into the plugv file
//  IN     $tab     array containing data from already recorded program
// RET     $time    time to check
function find_new_line($tab, $time="") {
    if(strcmp($time,"")==0) return false;

    $ret=true;
    $time=str_replace(":","",$time);
    foreach($tab as $line) {
        $base_time=substr(0,5,$line);
        $hh=substr(0,2,$time);
        $mm=substr(2,2,$time);
        $ss=substr(4,2,$time);
        $new_time=(3600*$hh)+(60*$mm)+$ss;
        
        if(strcmp($base_time,$new_time)==0) $ret=false;
    }
    return $ret;
}
// }}}


// {{{ get_sensor_type()
// ROLE get sensor type from the index file
// IN $nmb      number of the sensor 
//    $sd_card  path to the SD card plugged
//    $month    month to be checked
//    $day      day to be checked
// RET sensor type number
function get_sensor_type($nmb,$sd_card,$month,$day) {
   $file="$sd_card/logs/index";
   if(!file_exists("$file")) return false;

   $handle = fopen("$file", 'r');
   if($handle) {
      while(!feof($handle)) {
         $buffer = fgets($handle);
         $tmp=substr($buffer, 0, 4);

         if(strcmp("$tmp","$month$day")==0) {
            $sensor=substr("$buffer",4+$nmb,1);
            fclose($handle);
            return "$sensor";     
         }
      }
      fclose($handle);
      return false;
   }
   return false;
}
// }}}


// {{{ check_config_xml_file()
// ROLE check if an external xml file should be displayed for the calendar
// IN file     file to be checked
// RET true if the file has to be displayed, false else
function check_config_xml_file($file) {
    if(!is_file('../../xml/config')) {
        if(!is_file('main/xml/config')) {    
             return true;
        } else {
            $config="main/xml/config";
        }
    } else {
        $config="../../xml/config";
    }

    $handle = fopen("$config", 'r');
    if($handle) {
      while (!feof($handle)) {
        $buffer = fgets($handle);
        if(strcmp(rtrim($buffer),"$file")==0) return false;
      }
    } else {    
        return true;
    }
    return true;
}
// }}}

// {{{ get_external_calendar_file()
// ROLE get an array containing list of xml external files available for calendar (like moon calendar)
// RET array containing datas
function get_external_calendar_file() {
    $ret=array();

    if(!is_dir('main/xml')) {
        if(!is_dir('../../xml')) {
            return $ret;
        } else {
            $dir='../../xml';
        }
    } else {
        $dir='main/xml';
    }


    if($handle = @opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if(($entry!=".")&&($entry!="..")&&(strcmp(pathinfo($entry,PATHINFO_EXTENSION),"xml")==0)) {
                $rss_file = file_get_contents($dir."/".$entry);
                $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);

                $check=true;
                foreach ($xml as $tab) {
                    if(is_array($tab)) {
                        if((array_key_exists('substrat', $tab))&&(array_key_exists('marque', $tab))) {
                            $check=false;
                        }   
                    }
                }

                if($check) {
                     $ret[]=$entry;
                }
            }
        }
    }
    return $ret;
}
// }}}


// {{{ get_xml_file()
// ROLE get usable others xml files 
// RET array containing datas
function get_xml_file() {
    $ret=array();

    if(!is_dir('main/xml')) {
        if(!is_dir('../../xml')) {
            return $ret;
        } else {
            $dir='../../xml';
        }
    } else {
        $dir='main/xml';
    }

    if($handle = @opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if(($entry!=".")&&($entry!="..")&&(strcmp(pathinfo($entry,PATHINFO_EXTENSION),"xml")==0)) {
                $rss_file = file_get_contents($dir."/".$entry);
                $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);

                foreach ($xml as $tab) {
                    if(is_array($tab)) {
                        if((array_key_exists('substrat', $tab))&&(array_key_exists('marque', $tab))) {
                            $ret[]=$entry;
                            break;
                        }   
                    }
                }
            }
        }
    }
    return $ret;
}
// }}}


// {{{ check_xml_calendar_file()
// ROLE check if a file is an external calendar (like moon calendar)
// RET  true if it's the case, false else
function check_xml_calendar_file($file) {
    if(!is_dir('main/xml')) {
        if(!is_dir('../../xml')) {
            return false;
        } else {
            $dir='../../xml';
        }
    } else {
        $dir='main/xml';
    }

    if($handle = @opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if(($entry!=".")&&($entry!="..")&&(strcmp(pathinfo($entry,PATHINFO_EXTENSION),"xml")==0)&&(strcmp("$entry","$file")==0)) {
            $rss_file = file_get_contents($dir."/".$entry);
            $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);

            $check=true;
            foreach ($xml as $tab) {
                if(is_array($tab)) {
                    if((array_key_exists('substrat', $tab))&&(array_key_exists('marque', $tab))) {
                        $check=false;
                    }
                }
            }

            if($check) {
                return true;
            }
            }
        }
    }
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

        if(!is_dir($logs)) mkdir("$logs");
        if(!is_dir($cnf)) mkdir("$cnf");
        if(!is_dir($plg)) mkdir("$plg");
        if(!is_dir($prg)) mkdir("$prg");
        if(!is_dir($bin)) mkdir("$bin");

        if(!is_file("$sd_card/cnf/prg/plugv")) {
            copy("main/templates/data/empty_file.tpl","$sd_card/cnf/prg/plugv");  
        }
    }
}

/* **************** */


?>
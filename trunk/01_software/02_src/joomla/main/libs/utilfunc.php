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


   return htmlentitiesOutsideHTMLTags($ret);
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
// IN $file          file to explode
//    $array_line    array to store log's values
// RET none
function get_log_value($file,&$array_line) {
   if(!file_exists("$file")) return false;
   $handle = fopen("$file", 'r');
   if ($handle)
   {
      while (!feof($handle))
      {
         $buffer = fgets($handle);
         if(!check_empty_string($buffer)) {
            break;
         } else {
            $temp = explode("\t", $buffer);
            for($i=0;$i<count($temp);$i++) {
               $temp[$i]=rtrim($temp[$i]);
            }

            $date_catch="20".substr($temp[0], 0, 2)."-".substr($temp[0],2,2)."-".substr($temp[0],4,2);
            $date_catch=rtrim($date_catch);
            $time_catch=substr($temp[0], 8,6);
            $time_catch=rtrim($time_catch);
           
            if((!empty($date_catch))&&(!empty($time_catch))&&(!empty($temp[0]))&&(!empty($temp[1]))&&(!empty($temp[2]))) {
        if((strlen($temp[1])<=4)&&(strlen($temp[2])<=4)&&(strlen($temp[0])==14)) {
                     $array_line[] = array(
                        "timestamp" => $temp[0],
                        "temperature" => $temp[1],
                        "humidity" => $temp[2],
                        "date_catch" => $date_catch,
                        "time_catch" => $time_catch
                     );
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
   if(!file_exists("$file")) return false;
   $handle = fopen("$file", 'r');
   if ($handle)
   {
      while (!feof($handle))
      {
         $buffer = fgets($handle);
         $buffer=trim($buffer);
         if(!check_empty_string($buffer)) {
            break;
         } else {
            $temp = explode("\t", $buffer);

            if(count($temp)!=17) {
               return false;
            }

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
                        if(strcmp($temp[$i],"0000")!=0) {
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

      fclose($handle);
   }
}
//}}}

// {{{ clean_log_file()
// ROLE copy an empty file to clean a log file
// IN $file             file to clean
// RET none
function clean_log_file($file) {
   $filetpl = 'main/templates/data/empty_file_64.tpl';
   copy($filetpl, $file);
}
//}}}


// {{{ clean_big_file()
// ROLE copy an empty file to clean a power file
// IN $file             file to clean
// RET none
function clean_big_file($file) {
   $filetpl = 'main/templates/data/empty_file_big.tpl';
   copy($filetpl, $file);
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
// RET current lang using the l10n format, ex: en-GB with joomla format is replaced by en_GB
function get_current_lang() {
   $lang =& JFactory::getLanguage();
   return str_replace("-","_",$lang->getTag());
}
//}}}


// {{{ set_current_lang()
// ROLE set the Joomla language for the interface
// IN $lang       lang to set using the l10n format (ex: en_GB)
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


// {{{ get_format_graph()
// ROLE get datas for the highcharts graphics
// IN $arr           array containing datas
// RET $data         data at the highcharts format (a string)
function get_format_graph($arr) {
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
      if((check_empty_record("$last_hh","$last_mm","24","00"))&&("$hh:$mm" != "00:00")) {
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
// ROLE check if there is an empty record. An empty reccord is defined is the time spaces
// between two values is greatan than 30minutes
// IN $last_hh       last record hours
//    $last_mm       last record minutes
//    $hh            first record hours
//    $mm            first record minutes
// RET true is there isn't an empty record, false else.
function check_empty_record($last_hh,$last_mm,$hh,$mm) {
      $lhh= 60 * $last_hh + $last_mm;
      $chh= 60 * $hh + $mm;

      if($chh-$lhh<=30) {
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

      if(!preg_match('#^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$#', $date)) {
         return 0;
      }
      return 1;
   }

   if("$type" == "month") {
      if(strlen("$date")!=2) {
         return 0;
      }

      if(!preg_match('#^[0-9][0-9]$#', $date)) {
         return 0;
      }

      if(($date < 1)||($date > 12)) {
         return 0;
      }
      return 1;
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
// IN   none 
// RET false if nothing is found, the sd card place else
function get_sd_card() {
        //For Linux
        $dir="/media";
        $os=php_uname('s');
        switch($os) {
                case 'Linux':
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
                        }

                        //In Ubuntu Quantal mounted folders are now in /media/$USER directory
                        $user=get_current_user();
                        if((isset($user))&&(!empty($user))) {
                            $dir="/media/".$user;
                            if(is_dir($dir)) {
                                $rep = opendir($dir);
                                while ($f = readdir($rep)) {
                                        if(is_dir("$dir/$f")) {
                                                if(check_cultibox_card("$dir/$f")) {
                                                        return "$dir/$f";
                                                }
                                        }
                                }
                            }
                        }
                        break;

                case 'Mac':
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
                        }
                        break;

                case 'Windows NT':
                  $vol=`MountVol`;
                  $vol=explode("\n",$vol);
                  $dir=Array();
                  foreach($vol as $value) {
                     // repérer les deux derniers segments du nom de l'hôte
                     preg_match('/[C-Z]:/', $value,$matches);
                     foreach($matches as $val) {
                        $dir[]=$val;
                     }
                  }
                  
                        foreach($dir as $disque) {
                              $check=`dir $disque`;
                              if(strlen($check)>0) {
                                 if(check_cultibox_card("$disque")) {
                                         return "$disque";
                                 }
                              }
                        }
                        break;
        }
        return false;
}
// }}}


// {{{ check_cultibox_card()
// ROLE check if the directory is a cultibox directory to write configuration
// IN   $dir         directory to check
// RET true if it's a cultibox directory, false else
function check_cultibox_card($dir="") {
   if((is_file("$dir/plugv"))&&(is_file("$dir/pluga"))&&(is_dir("$dir/logs"))) {
         return true;
   } 
   return false;
}
// }}}


// {{{ check_times()
// ROLE check times send by user to reccord a plug behaviour
// IN   $start_time      time starting the event
//      $end_time        time ending the event
//      $out             string for error or warning messages
// RET 1 if ok, 0 if there is an error or 2 if start time > end time
function check_times($start_time="",$end_time="",&$out="") {
   if((!empty($start_time))&&(isset($start_time))&&(!empty($end_time))&&(isset($end_time))) {
      $start_time=str_replace(' ','',"$start_time");
      if(strlen("$start_time")!=8) {
         $out=$out.__('ERROR_FORMAT_TIME');
         return 0;
      }

      if(strcmp($start_time,$end_time)==0) {
         $out=$out.__('ERROR_SAME_TIME');
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
              return 2;
      }   

      return 1;         
   }
   $out=$out.__('ERROR_MISSING_VALUE_TIME');
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
            if($GLOBALS['DEBUG_TRACE']) {
                echo "<br />Case 1:  <br/>Current:";
                print_r($value);
                echo "<br />Last:".$last_time." - ".$last_value ;
            }

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
                       if($GLOBALS['DEBUG_TRACE']) {
                          echo "<br />Case 1-1:  <br/>Current:";
                          print_r($value);
                          echo "<br />Last:".$last_time." - ".$last_value ;
                       }
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
                    if($GLOBALS['DEBUG_TRACE']) {
                          echo "<br />Case 1-2:  <br/>Current:";
                          print_r($value);
                          echo "<br />Last:".$last_time." - ".$last_value ;
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


// {{{ write_program()
// ROLE write programs into the sd card
// IN   $data         array containing datas to write
//      $file         file path to save data
//      $out          error or warning messages
// RET false is an error occured, true else
function write_program($data,$file,&$out="") {
   if($f=fopen("$file","w+")) {
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
      $out=$out.__('ERROR_WRITE_SD');
   }
}
// }}}


// {{{ compare_program()
// ROLE write programs into the sd card
// IN   $data         array containing datas to write
//      $sd_card      sd card path to save data
// RET false is there is nothing to write, true else
function compare_program($data,$sd_card) {
    if(is_file("${sd_card}/plugv")) {

         $nb=0;
         $nbdata=count($data);
         $file="${sd_card}/plugv";

         if(count($data)>0) {
            $handle = fopen($file, 'r');
            if ($handle) {
               while (!feof($handle)) {
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
function write_pluga($sd_card,&$out="") {

   $file="$sd_card/pluga";

   if($f=fopen("$file","w+")) {
      $pluga=Array();
      $pluga[]="16";
      for($i=0;$i<16;$i++) {
      $tmp_pluga=get_plug_conf("PLUG_ID",$i+1,$out);
      if((empty($tmp_pluga))||(!isset($tmp_pluga))) {
                      $tmp_pluga=$GLOBALS['PLUGA_DEFAULT'][$i];
                }
                $pluga[]="$tmp_pluga";
      }

      foreach($pluga as $val) {
                fputs($f,"$val"."\r\n");
      }
   }
   fclose($f);
}
// }}}

// {{{ write_plugconf()
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
//   $power_frequency    record of the power frequency value
//   $alarm_enable       enable or disable the alarm system
//   $alarm_value        value to trigger the alarm
//   $alarm_senso        humidity or temperature to use to trigger the alarm
//   $alarm_senss        configure if the alarm have to be triggered above or under the value
//   $out         error or warning message
// RET none   
function write_sd_conf_file($sd_card,$record_frequency=1,$update_frequency=1,$power_frequency=1,$alarm_enable="0000",$alarm_value="50.00",$alarm_senso="000T",$alarm_senss="000+",&$out="") {
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

   $update="000$update_frequency";
   $file="$sd_card/conf";
   if($f=fopen("$file","w+")) {
      fputs($f,"PLUG_UPDATE:$update\r\n");
      fputs($f,"LOGS_UPDATE:$record\r\n");
      fputs($f,"POWR_UPDATE:$power\r\n"); 
      fputs($f,"ALARM_ACTIV:$alarm_enable\r\n");
      fputs($f,"ALARM_VALUE:$alarm_value\r\n");
      fputs($f,"ALARM_SENSO:$alarm_senso\r\n");
      fputs($f,"ALARM_SENSS:$alarm_senss\r\n");
      fputs($f,"LOG_MIN_TMP:0000\r\n");
      fputs($f,"LOG_MAX_TMP:3000\r\n");
      fputs($f,"LOG_MIN_HMI:0000\r\n");
      fputs($f,"LOG_MAX_HMI:9000\r\n");
      fputs($f,"RTC_OFFSET_:0000\r\n");
      fclose($f);
   } else {
      $out=$out.__('ERROR_WRITE_SD');
   }
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
            if(($val['start_month']<=$month)&&($val['end_month']>=$month)&&($val['start_day']<=$day)&&($val['end_day']>=$day)) {
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


// {{{ write_calendar()
// ROLE save calendar informations into the SD card
// IN $sd_card         sd card location
//    $data            data to write into the sd card
//    $out             error or warning messages
// RET none
function write_calendar($sd_card,$data,&$out="") {
   if(isset($sd_card)&&(!empty($sd_card))) {
      if(count($data)>0) {
         for($month=1;$month<=12;$month++) {
            for($day=1;$day<=31;$day++) {
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

                     fputs($f,"$number_to_show");
                     foreach($val['subject'] as $sub) {
                        fputs($f,"\r\n"."$sub");
                     }
                     foreach($val['description'] as $desc) {
                        fputs($f,"\r\n"."$desc");
                     }

                     fputs($f,"\r\n");
                     fclose($f);
                  } 

                  if($day==31) {
                     $day="01";
                  } 
                  unset($val);
               }
          }
        }
     }
    }
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
               if($day_start<=$day_end) {
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
//    $out                error or warning message
// RET false is there is a wrong value, true else
function check_tolerance_value($type,&$tolerance=0,&$out="") {
   $tolerance=str_replace(",",".",$tolerance);
   if((strcmp($type,"heating")==0)||(strcmp($type,"ventilator")==0)) {
      if(($tolerance > 0)&&($tolerance <= 10)) {
         return true;
      } else {
         $out=$out.__('ERROR_TOLERANCE_VALUE_DEGREE');
         return false;
      }
   } else if((strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)) {
      if(($tolerance > 0)&&($tolerance <= 25.5)) {
         return true;
      } else {
         $out=$out.__('ERROR_TOLERANCE_VALUE_POURCENT');
         return false;
      }   
   }
   return true;
}
// }}}


// {{{ check_format_values_program()
// ROLE check AND format value of a program 
// IN   $value       value to check and format
// IN   $out         error or warning message
// IN   $type        temp or humi - type to check
// RET false is there is a wrong value, true else
function check_format_values_program(&$value="0",&$out="",$type="temp") {
   $value=str_replace(',','.',$value);
   $value=str_replace(' ','',$value);

    if(!is_numeric($value)) {
                $out.__('ERROR_VALUE_PROGRAM');
                return false;
   }

   if(strcmp($type,"temp")==0) {
      if(($value>60)||($value<5)) {
      $out=$out.__('ERROR_VALUE_PROGRAM_TEMP');
      return false; 
      }
   } elseif(strcmp($type,"humi")==0) {
      if(($value>95)||($value<10)) {
                $out=$out.__('ERROR_VALUE_PROGRAM_HUMI');
                return false; 
      }
   } else {
   if(($value>99.9)||($value<0)) {
                $out=$out.__('ERROR_VALUE_PROGRAM');
                return false; 
       }
   }
 
   return true;
}
// }}}

// {{{ check_power_value()
// ROLE check AND format power value of a plug
// IN   $value   value to check and format
// IN   $out     error or warning message
// RET false is there is a wrong value, true else
function check_power_value($value="0",&$out="") {
   if($value<0) {
                $out=$out.__('ERROR_POWER_VALUE');
                return false;
   }
   if(!is_numeric($value)) {
                $out.__('ERROR_POWER_VALUE');
                return false;
   }
   return true;
}
// }}}

// {{{ check_alarm_value()
// ROLE check is a value for the alarm is correct
// IN   $value       value to check
// OUT  false is there is a wrong value, true else
function check_alarm_value($value="0") {
   $value=str_replace(',','.',$value);
   $value=str_replace(' ','',$value);
   if(($value>99.99)||($value<0)) return false;
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
// ROLE check if firmwares (firm.hex,emmeteur.hex) has to be copied and do the copy into the sd card
// IN  $sd_card     the sd card pathname 
//     $out         error or warning message
// RET none
function check_and_copy_firm($sd_card,&$out="") {
   $new_firm="";
   $current_firm="";

   $firm_to_test[]="firm.hex";
   $firm_to_test[]="emmeteur.hex";


   foreach($firm_to_test as $firm) { 
        $new_file="tmp/$firm";
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

        if((isset($new_firm))&&(!empty($new_firm))) {
            if((!isset($current_firm))||(empty($current_firm))) {
                copy($new_file, $current_file);
            } else {
                $current_firm=trim("$current_firm");
                $new_firm=trim("$new_firm");

                if((strlen($current_firm)==43)&&(strlen($new_firm)==43)) {   
                    $new_firm=substr($new_firm,9,4); 
                    $current_firm=substr($current_firm,9,4);

                    if(hexdec($new_firm) > hexdec($current_firm)) {
                        copy($new_file, $current_file);
                    }
                }
           }
        }
    }
}
// }}}


// {{{ check_and_copy_log()
// ROLE check if the log.txt exists and if not, create it from empty_file64.tpl
// IN  $sd_card     the sd card pathname 
//     $out         error or warning message
// RET none
function check_and_copy_log($sd_card,&$out="") {
    if(is_file("$sd_card/log.txt")) {
    } else {
        if(is_file("main/templates/data/empty_file_64.tpl")) {
            copy("main/templates/data/empty_file_64.tpl", "$sd_card/log.txt");   
        }else {
            $out=$out.__('ERROR_COPY_TPL');
        }
    }
}
// }}}


// {{{ clean_popup_message()
// ROLE clean popup message by removing non-appropriate char for javascript
// IN  $message         message to be cleaned
// RET   new message cleaned 
function clean_popup_message(&$message="") {
   $old = array("'","<li>", "</li>", "&eacute;","&agrave;","&egrave;","&ecirc;","&deg;");
   $new   = array("\'","", "\\n", "é","à","è","ê","°");
   
   return str_replace($old, $new, $message);
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
// IN    $ret     array to return containing updates informations
//       $out     errors or warnings messages
// RET   none  
function check_update_available(&$ret,&$out="") {
         if(isset($GLOBALS['UPDATE_FILE'])&&(!empty($GLOBALS['UPDATE_FILE']))) {
               $buffer=array();
               $tmp=array();
               $file=$GLOBALS['UPDATE_FILE'];
               if($handle=fopen($file,"r")) {
                  while (!feof($handle)) {
                     $buffer[] = fgets($handle);
                  }
                  fclose($handle);
                  $os=php_uname('s');
                  foreach($buffer as $val) {
                                    $tmp=explode("*", $val);
                                    if(strcmp($tmp[0],"$os")==0) {
                                         $ret[]=$tmp; 
                                    }
                 } 
               } else {
                  $out=$out.__('ERROR_REMOTE_UPDATE_FILE');
               }
         }
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
// IN    $data       actions of the plug
//       $name       name of the plug
//       $number     plug's number
// RET   sumary formated 
function format_data_sumary($data="",$name="",$number="",$type="") {
    $resume="";
    $unity="";
    if((empty($data))||(empty($name))||(empty($number))|(empty($type))) {
            $resume="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.":<br /> ".$name."</i></b></p><p align='center'>".__('EMPTY_ACTION')."</p>";
    } else {
                switch($type) {
                    case 'lamp': $unity="";
                                 break;
                    case 'unknown': $unity="";
                                break;
                    case 'ventilator': $unity="°C";
                                break;
                    case 'heating': $unity="°C";
                                break;
                    case 'humidifier': $unity="%";
                                break; 
                    case 'deshumidifier': $unity="%";
                                break;
                }

               
                $actions=array();
                $actions=explode('[',$data);
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
                                    if(strcmp($resume,"")==0) {
                                        if(strcmp($action[1],"99.9")==0) {
                                            $resume="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.":<br /> ".$name."</i></b></p><p align='left'>".__('SUMARY_ON')." ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        } else {
                                            $resume="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.":<br /> ".$name."</i></b></p><p align='left'>".__('SUMARY_REGUL_ON')." (".$action[1].$unity.") ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        }
                                    } else {
                                        if(strcmp($action[1],"99.9")==0) {
                                            $resume=$resume.__('SUMARY_ON')." ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        } else {
                                            $resume=$resume.__('SUMARY_REGUL_ON')." (".$action[1].$unity.") ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        }
                                    }
                                    $prev_value=$action[1];
                            } else if(strcmp($prev_value,"0")!=0) {
                                        if(strcmp("$prev_value","99.9")==0) {
                                            $resume=$resume.__('SUMARY_OFF')." ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        } else {
                                            $resume=$resume.__('SUMARY_REGUL_OFF')." ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        }
                                            $prev_value=0;
                            } 
                        }
                    } 
                }

                if(strcmp($resume,"")==0) { 
                        $resume="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.": <br />".$name."</i></b></p><p align='center'>".__('EMPTY_ACTION')."</p>";
                } else {
                        $resume=$resume."</p>";
                }
    }
    return $resume;
}
// }}}
?>

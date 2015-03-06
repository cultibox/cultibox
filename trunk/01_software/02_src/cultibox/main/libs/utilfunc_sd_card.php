<?php

// Define constant
define("ERROR_COPY_FILE", "2");
define("ERROR_WRITE_PROGRAM", "3");
define("ERROR_COPY_FIRM", "4");
define("ERROR_COPY_PLUGA", "5");
define("ERROR_COPY_PLUG_CONF", "6");
define("ERROR_COPY_TPL", "7");
define("ERROR_COPY_INDEX", "8");
define("ERROR_WRITE_SD_CONF", "11");
define("ERROR_WRITE_SD", "12");
define("ERROR_SD_NOT_FOUND", "13");
define("ERROR_COPY_PLGIDX", "14");

// {{{ check_and_update_sd_card()
// ROLE If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
// IN   $sd_card    sd card path 
// RET 0 if the sd card is updated, 1 if the sd card has been updated, return > 1 if an error occured
function check_and_update_sd_card($sd_card="",&$main_info_tab,&$main_error_tab,$force_rtc_offset=false) {

    // Check if SD card has been found
    if(empty($sd_card) || !isset($sd_card)  || $sd_card == "")
    {
        $main_error_tab[]=__('ERROR_SD_CARD');
        return ERROR_SD_NOT_FOUND;
    }

    // Alert user than an SD card was found
    $main_info_tab[]=__('INFO_SD_CARD').": $sd_card";
    
    // Check if SD card can be writable
    if(!check_sd_card($sd_card)) return ERROR_WRITE_SD;

    // Check and update path
    $logs = "$sd_card/logs";
    $cnf  = "$sd_card/cnf";
    $plg  = "$cnf/plg";
    $prg  = "$cnf/prg";
    $bin  = "$sd_card/bin";

    if(!is_dir($logs)) mkdir($logs);
    if(!is_dir($cnf)) mkdir($cnf);
    if(!is_dir($plg)) mkdir($plg);
    if(!is_dir($prg)) mkdir($prg);
    if(!is_dir($bin)) mkdir($bin);

    // If we are in cultipi mode, create file systeme structure
    if ($GLOBALS['MODE'] == "cultipi") {
        if(!is_dir($sd_card . "/cultiPi"))          mkdir($sd_card . "/cultiPi");
        if(!is_dir($sd_card . "/serverAcqSensor"))  mkdir($sd_card . "/serverAcqSensor");
        if(!is_dir($sd_card . "/serverHisto"))      mkdir($sd_card . "/serverHisto");
        if(!is_dir($sd_card . "/serverLog"))        mkdir($sd_card . "/serverLog");
        if(!is_dir($sd_card . "/serverPlugUpdate")) mkdir($sd_card . "/cultiPi");
        
        // Create conf file
        $paramList["verbose"] = "debug";
        $paramList["simulator"] = "off";
        create_conf_XML($sd_card . "/serverAcqSensor/conf.xml" , $paramList);
        
    }
    
    $program = "";
    $conf_uptodate = true;

    $program_index = array();
    program\get_program_index_info($program_index);
   

    $confsave_prog=true;
    foreach ($program_index as $key => $value) {
        // Read from database program
        $program = create_program_from_database($main_error,$value['program_idx']);

        if(!compare_program($program,$sd_card,"plu" . $value['plugv_filename'])) {
            $conf_uptodate=false;

            if(!save_program_on_sd($sd_card,$program,"plu" . $value['plugv_filename'])) {  
                $confsave_prog=false;
            }
        }
    }

    //For plugv
    $program = create_program_from_database($main_error);
    if(!compare_program($program,$sd_card)) {
        $conf_uptodate=false;

        if(!save_program_on_sd($sd_card,$program)) {
            $confsave_prog=false;
        }
    }

    if(!$confsave_prog) {
        $main_error_tab[]=__('ERROR_WRITE_PROGRAM');
        return ERROR_WRITE_PROGRAM;
    }

    if(!isset($GLOBALS['MODE']) || $GLOBALS['MODE'] != "cultipi") { 
        $ret_firm=check_and_copy_firm($sd_card);
        if(!$ret_firm) {
            $main_error_tab[]=__('ERROR_COPY_FIRM'); 
            return ERROR_COPY_FIRM;
        } else if($ret_firm==1) {
            $conf_uptodate=false;
        }
    }

    if(!compare_pluga($sd_card)) {
        $conf_uptodate=false;
        if(!write_pluga($sd_card,$main_error)) {
            $main_error_tab[]=__('ERROR_COPY_PLUGA');
            return ERROR_COPY_PLUGA;
        }
    }

    $plugconf = create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$main_error);
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

    $data=array();
    calendar\read_event_from_db($data);
    $plgidx=create_plgidx($data);
    if(count($plgidx)>0) {
        if(!compare_plgidx($plgidx,$sd_card)) {
            $conf_uptodate=false;
            if(!write_plgidx($plgidx,$sd_card)) {
                $main_error_tab[]=__('ERROR_COPY_PLGIDX');
                return ERROR_COPY_PLGIDX;
            }
        }
    } else {
        if(!check_and_copy_plgidx($sd_card)) {
             $main_error_tab[]=__('ERROR_COPY_TPL');
             return ERROR_COPY_TPL;
        }
    }

    // Read value on sd Card
    if(!$force_rtc_offset) {
        $sdConfRtc = read_sd_conf_file($sd_card,"rtc_offset");
        $sdConfRtc = get_decode_rtc_offset($sdConfRtc);

        // Update database
        insert_configuration("RTC_OFFSET",$sdConfRtc,$main_error);
    }


    $recordfrequency = get_configuration("RECORD_FREQUENCY",$main_error);
    $powerfrequency = get_configuration("POWER_FREQUENCY",$main_error);
    $updatefrequency = get_configuration("UPDATE_PLUGS_FREQUENCY",$main_error);
    $alarmenable    = get_configuration("ALARM_ACTIV",$main_error);
    $alarmvalue     = get_configuration("ALARM_VALUE",$main_error);
    $resetvalue     = get_configuration("RESET_MINMAX",$main_error);
    $rtc            = get_rtc_offset(get_configuration("RTC_OFFSET",$main_error));
    $enableled      = get_configuration("ENABLE_LED",$main_error);
    if($updatefrequency == "-1") {
        $updatefrequency="0";
    }

    if(!compare_sd_conf_file($sd_card,
                             $recordfrequency,
                             $updatefrequency,
                             $powerfrequency,
                             $alarmenable,
                             $alarmvalue,
                             $resetvalue,
                             $rtc,
                             $enableled))
    {
        $conf_uptodate=false;
        if(!write_sd_conf_file($sd_card,
                               $recordfrequency,
                               $updatefrequency,
                               $powerfrequency,
                               $alarmenable,
                               $alarmvalue,
                               $resetvalue,
                               $rtc,
                               $enableled,
                               $main_error))
        {
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
        case ERROR_WRITE_SD_CONF: return __('ERROR_WRITE_SD_CONF');
        case ERROR_WRITE_SD: return __('ERROR_WRITE_SD');
        case ERROR_COPY_PLGIDX: return __('ERROR_COPY_PLGIDX');
        default: return "";
    }
}
// }}}

// {{{ sd_card_update_log_informations()
// ROLE copy an empty file to a new file destination
// IN $sd_card     destination path
// RET true if the copy is errorless, false else
function sd_card_update_log_informations ($sd_card="") {

    if(empty($sd_card) || !isset($sd_card) || $sd_card == "") return ERROR_SD_NOT_FOUND;


    // The informations part to send statistics to debug the cultibox: 
    //      if the 'STATISTICS' variable into the configuration table from the database is set to 'True' informations will be send for debug
    $informations["cbx_id"]="";
    $informations["firm_version"]="";
    $informations["log"]="";

    
    // Read log.txt file and clear it
    find_informations("$sd_card/log.txt",$informations);
    copy_template_file("empty_file_big.tpl", "$sd_card/log.txt");

    // If informations are defined in log.txt copy them into database
    if($informations["cbx_id"] != "")  
        insert_informations("cbx_id",$informations["cbx_id"]);
        
    if($informations["firm_version"] != "") 
        insert_informations("firm_version",$informations["firm_version"]);
        
    if($informations["log"] != "") 
        insert_informations("log",$informations["log"]);

    return 1;
}


// {{{ copy_template_file()
// ROLE copy an empty file to a new file destination
// IN  $name     name of the file to be copied
//     $file     destination of the file
// RET true if the copy is errorless, false else
function copy_template_file($name="", $file) {
   if(strcmp("$name","")==0) return false;
   //Trying to find the template file from the current path:
   if(is_file("main/templates/data/$name")) {
        $filetpl = "main/templates/data/$name";
   } else if(is_file("../main/templates/data/$name")) {
        $filetpl = "../main/templates/data/$name";
   } else if(is_file("../../main/templates/data/$name")) {
        $filetpl = "../../main/templates/data/$name";
   } else {
        $filetpl = "../../../main/templates/data/$name";
   }

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
    //Retrieve SD path depending of the current OS:
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
        
            // There is a bug in php, this is why we stop and restart session
            // For mor information, see :
            // http://php.net/manual/fr/function.exec.php : Comment write by  "elwiz at 3e dot pl"
            // https://bugs.php.net/bug.php?id=44942
            session_write_close();
            $vol=`MountVol`;
            session_start();

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
function save_program_on_sd($sd_card,$program,$filename = "plugv") {
    $out=array();

    // Init file name
    $file = "${sd_card}/cnf/prg/${filename}";
    
    // Init out program file contants
    $prog="";
    $nbPlug=count($program);
    $shorten=false;

    // Check if there are some plugs
    if($nbPlug == 0)
        return false;
   
    // Limit nb plugs to max allowed
    if(get_configuration("REMOVE_1000_CHANGE_LIMIT",$out)=="False") {
        if($nbPlug>$GLOBALS['PLUGV_MAX_CHANGEMENT']) {
            $nbPlug=$GLOBALS['PLUGV_MAX_CHANGEMENT'];
            $shorten=true;
        }
    } 

    // Complet nbPlug variable up to 3 digits
    while(strlen($nbPlug)<5)
        $nbPlug="0$nbPlug";
    
    // Add header of the file
    $prog=$nbPlug."\r\n";

    // If we have to reduce number of change
    if($shorten) {
        // For each line of the program add it to file
        for($i=0; $i<$nbPlug-1; $i++) 
            $prog=$prog."$program[$i]"."\r\n";
    } else {
        for($i=0; $i<$nbPlug; $i++) 
            $prog=$prog."$program[$i]"."\r\n";
    }

    // If the programm has been cut (too many change) add an last entry
    if($shorten) {
        $last=count($program)-1;
        $prog=$prog."$program[$last]"."\r\n";
    }

    // Write it on SD card
    if($f = @fopen($file,"w+")) {
        if(!@fwrite($f,"$prog")) 
        { 
            fclose($f);
            return false;
        }
            fclose($f);
    }

   return true;
}
// }}}


// {{{ compare_program()
// ROLE compare programs and data to check if they are up to date
// IN   $data         array containing datas to check
//      $sd_card      sd card path to save data
//      $file         file to be compared to
// RET false is there is something to write, true else
function compare_program($data,$sd_card,$file="plugv") {
    $file="${sd_card}/cnf/prg/".$file;
    $out=array();

    if(is_file("${file}")) {
        $nb=0;
        //On compte le nombre d'entrée dans la base des programmes:
        $nbdata=count($data);

        //Si les changements de la base dépassent ceux de maximum définit, on coupe le tableau des programmes pour le faire
        //correspondre au nombre maximal
        if(get_configuration("REMOVE_1000_CHANGE_LIMIT",$out)=="False") {
            if($nbdata>$GLOBALS['PLUGV_MAX_CHANGEMENT']) {
                $tmp_array=array_slice($data, 0, $GLOBALS['PLUGV_MAX_CHANGEMENT']-1);
                $tmp_array[]=$data[$nbdata-1];
                $data=$tmp_array;
                $nbdata=count($data);
            }
        }

        
         while(strlen($nbdata)<5) {
            $nbdata="0$nbdata";
         }

        if(count($data)>0) {
            //On récupère les informations du fichier courant plugv
            $buffer_array=@file("$file");
            foreach($buffer_array as $buffer) {
                  $buffer=trim($buffer); //On supprime les caractères invisibles
                  if(!empty($buffer)) {
                     if($nb==0) {
                        if(strcmp("$nbdata","$buffer")!=0) { //S'il s'agit de la première ligne, qui contient le nombre d'entrée, on compare le nombre d'entrée du fichier avec le nombre d'entrée du tableau
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
    $out  = array();
    $file = "${sd_card}/cnf/plg/pluga";
    
    // Check if the file exists
    if(is_file($file)) {
        $nb=0;

        $pluga = Array();
        $nb_plug=get_configuration("NB_PLUGS",$out);
        while(strlen($nb_plug)<2) {
            $nb_plug = "0$nb_plug";
        }

         $pluga[] = $nb_plug;
         for($i=0;$i<$nb_plug;$i++) {
         
            // Get power of the plug
            $tmp_power_max = get_plug_conf("PLUG_POWER_MAX",$i+1,$out);
            
            // Get module of the plug
            $tmp_MODULE = get_plug_conf("PLUG_MODULE",$i+1,$out);
            if ($tmp_MODULE == "") 
                $tmp_MODULE = "wireless";
            
            // Get module number of the plug
            $tmp_NUM_MODULE = get_plug_conf("PLUG_NUM_MODULE",$i+1,$out);
            if ($tmp_NUM_MODULE == "")
                $tmp_NUM_MODULE = 1;

            // Get module options of the plug
            $tmp_MODULE_OPTIONS = get_plug_conf("PLUG_MODULE_OPTIONS",$i+1,$out);

            // Get module output used
            $tmp_MODULE_OUPUT = get_plug_conf("PLUG_MODULE_OUPUT",$i+1,$out);
            if ($tmp_MODULE_OUPUT == "") 
                $tmp_MODULE_OUPUT = 1;

            // Create adress for this plug
            $tmp_pluga = 0;
            switch ($tmp_MODULE) {
                case "wireless":
                    if ($tmp_power_max == "3500") {
                        $tmp_pluga = $GLOBALS['PLUGA_DEFAULT_3500W'][$i];
                    } else {
                        $tmp_pluga = $GLOBALS['PLUGA_DEFAULT'][$i];
                    }
                    break;
                case "direct":
                    // Direct plug case (Adresse 50 --> 58)
                    $tmp_pluga = $tmp_MODULE_OUPUT + 49;
                    break;
                case "mcp230xx":
                    // MCP plug case 
                    // Module 1 : (Adresse 60 --> 67)
                    // Module 2 : (Adresse 70 --> 77)
                    // Module 3 : (Adresse 80 --> 87)
                    $tmp_pluga = 60 + 10 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUPUT - 1;
                    break;
                case "dimmer":
                    // Dimmer plug case 
                    // Module 1 : (Adresse 90 --> 93)
                    // Module 2 : (Adresse 95 --> 98)
                    // Module 3 : (Adresse 100 --> 103)
                    $tmp_pluga = 90 + 5 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUPUT - 1;
                    break;
                case "network":
                    $tmp_pluga = 1000 + 16 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUPUT - 1;
                    break;
                case "xmax":
                    // xmax plug case 
                    // Module 1 : (Adresse 105 --> 108)
                    $tmp_pluga = 105 + $tmp_MODULE_OUPUT - 1;
                    break;                    
            }

            while(strlen($tmp_pluga)<3) {
                $tmp_pluga = "0$tmp_pluga";
            }

            $pluga[] = $tmp_pluga;
        }

        $nbdata = count($pluga);

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


// {{{ create_plgidx()
// ROLE create plgidx file
// IN  $data            data to write into the sd card (come from calendar\read_event_from_db )
// RET array containing plgidx
function create_plgidx($data) {
    $plgidx = array();
    $return=array();

    // If there is not event , return false
    if(count($data) == 0) 
        return $return;

    // Open database connexion
    $db = \db_priv_pdo_start();
    
    // Foreach event
    foreach($data as $event)
    {
        // If this is a program index event
        if ($event['program_index'] != "")
        {

            // Query plugv filename associated
            try {
                $sql = "SELECT plugv_filename FROM program_index WHERE id = \"" . $event['program_index'] . "\";";
                $sth = $db->prepare($sql);
                $sth->execute();
                $res = $sth->fetch();
            } catch(\PDOException $e) {
                $ret=$e->getMessage();
            }
        
            //
            $today = strtotime(date("Y-m-d"));
            $nextYear  = strtotime("+1 year", strtotime(date("Y-m-d")));
        
            // Start date
            $date = $event['start_year'] . "-" . $event['start_month']  . "-" . $event['start_day'];
            // End date
            $end_date = $event['end_year'] . "-" . $event['end_month']  . "-" . $event['end_day'];
            
            while (strtotime($date) <= strtotime($end_date)) {

                // Save only for futur element
                if (strtotime($date) >= $today && strtotime($date) < $nextYear)
                    $plgidx[$date] = $res['plugv_filename'];
                  
                // Incr date                  
                $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
            }
        }
    }

    // Close connexion
    $db = null;
    
    // For each day
    for ($month = 1; $month <= 13; $month++) 
    {
        for ($day = 1; $day <= 31; $day++) 
        {
            // Format day and month
            $monthToWrite = $month;
            if (strlen($monthToWrite) < 2) {
                $monthToWrite="0$monthToWrite";
            }
            
            $dayToWrite = $day;
            if (strlen($dayToWrite) < 2) {
                $dayToWrite="0$dayToWrite";
            }
            
            // Date to search in event
            $dateToSearch = date("Y") . "-" . $monthToWrite . "-" . $dayToWrite;
            
            $plugvToUse = "00";
            if (array_key_exists($dateToSearch, $plgidx)) {
                $plugvToUse = $plgidx[$dateToSearch];
                    
                if (strlen($plugvToUse) < 2)
                    $plugvToUse = "0$plugvToUse";
            }

            // Write the day
            $return[]=$monthToWrite . $dayToWrite . $plugvToUse;
        }
    }
    return $return;
}
//}}}


// {{{ compare_plgidx()
// ROLE compare plgidx and data from databases to check if the file is up to date
// IN   $sd_card      sd card path to save data
//      $ data        data to be compared to
// RET false is there is something to write, true else
function compare_plgidx($data,$sd_card) {
    if(!is_file("${sd_card}/cnf/prg/plgidx")) return false;
    $file="${sd_card}/cnf/prg/plgidx";

    $plgidx=@file("$file");
    if((count($data))!=(count($plgidx))) return false;

    for($i=0;$i<count($data);$i++) {
        if(strcmp(trim(html_entity_decode($data[$i])),trim(html_entity_decode($plgidx[$i])))!=0) return false;
    }
    return true;
}
// }}}


// {{{ write_pluga()
// ROLE write plug_a into the sd card
// IN   $sd_card        the sd card to be written
//      $out            error or warning messages
// RET false is an error occured, true else
function write_pluga($sd_card,&$out) {
    $file="$sd_card/cnf/plg/pluga";

    if($f=@fopen($file,"w+")) {
        $pluga="";
        $nb_plug=get_configuration("NB_PLUGS",$out);
        while(strlen("$nb_plug")<2) {
            $nb_plug="0$nb_plug";
        }
        $pluga = $nb_plug . "\r\n";
      
        for($i=0;$i<$nb_plug;$i++) {
        
         
            // Get power of the plug
            $tmp_power_max = get_plug_conf("PLUG_POWER_MAX",$i+1,$out);
            
            // Get module of the plug
            $tmp_MODULE = get_plug_conf("PLUG_MODULE",$i+1,$out);
            if ($tmp_MODULE == "") 
                $tmp_MODULE = "wireless";
            
            // Get module number of the plug
            $tmp_NUM_MODULE = get_plug_conf("PLUG_NUM_MODULE",$i+1,$out);
            if ($tmp_NUM_MODULE == "")
                $tmp_NUM_MODULE = 1;

            // Get module options of the plug
            $tmp_MODULE_OPTIONS = get_plug_conf("PLUG_MODULE_OPTIONS",$i+1,$out);

            // Get module output used
            $tmp_MODULE_OUPUT = get_plug_conf("PLUG_MODULE_OUPUT",$i+1,$out);
            if ($tmp_MODULE_OUPUT == "") 
                $tmp_MODULE_OUPUT = 1;

            // Create adress for this plug
            $tmp_pluga = 0;
            switch ($tmp_MODULE) {
                case "wireless":
                    if ($tmp_power_max == "3500") {
                        $tmp_pluga = $GLOBALS['PLUGA_DEFAULT_3500W'][$i];
                    } else {
                        $tmp_pluga = $GLOBALS['PLUGA_DEFAULT'][$i];
                    }
                    break;
                case "direct":
                    // Direct plug case (Adresse 50 --> 58)
                    $tmp_pluga = $tmp_MODULE_OUPUT + 49;
                    break;
                case "mcp230xx":
                    // MCP plug case 
                    // Module 1 : (Adresse 60 --> 67)
                    // Module 2 : (Adresse 70 --> 77)
                    // Module 3 : (Adresse 80 --> 87)
                    $tmp_pluga = 60 + 10 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUPUT - 1;
                    break;
                case "dimmer":
                    // Dimmer plug case 
                    // Module 1 : (Adresse 90 --> 93)
                    // Module 2 : (Adresse 95 --> 98)
                    // Module 3 : (Adresse 100 --> 103)
                    $tmp_pluga = 90 + 5 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUPUT - 1;
                    break;
                case "network":
                    $tmp_pluga = 1000 + 16 * ($tmp_NUM_MODULE - 1) + $tmp_MODULE_OUPUT - 1;
                    break;
                case "xmax":
                    // xmax plug case 
                    // Module 1 : (Adresse 105 --> 108)
                    $tmp_pluga = 105 + $tmp_MODULE_OUPUT - 1;
                    break;                    
            }

            while(strlen($tmp_pluga)<3) {
                $tmp_pluga = "0$tmp_pluga";
            }


            $pluga = $pluga . $tmp_pluga . "\r\n";
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


// {{{ write_plgidx()
// ROLE write plgidx into the sd card
// IN   $data           array containing datas to write
//      $sd_card        the sd card to be written
// RET false is an error occured, true else
function write_plgidx($data,$sd_card) {
   $file="$sd_card/cnf/prg/plgidx";
   if($f=@fopen("$file","w+")) {
      if(!@fputs($f,implode("\r\n", $data))) {
            return false;
      }
      fclose($f);
      return true;
    }
    return false;
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
//      $enable_led         Allow backlight
// RET false if there is a difference, true else
function compare_sd_conf_file($sd_card="",
                              $record_frequency,
                              $update_frequency,
                              $power_frequency,
                              $alarm_enable,
                              $alarm_value,
                              $reset_value,
                              $rtc,
                              $enable_led) {

    if(!is_file($sd_card."/cnf/conf")) 
        return false;

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
   
    while(strlen($enable_led)<4) {
        $enable_led="0$enable_led";
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
    $conf[]="ENABLE_LEDs:$enable_led";

    $buffer=@file("$file");

    if(count($conf)!=count($buffer)) 
        return false;
        
    for($nb=0;$nb<count($conf);$nb++) {
        if(strcmp(trim($conf[$nb]),trim($buffer[$nb]))!=0) {
            return false;
        }
    }
    return true;
}
// }}}

// {{{ read_sd_conf_file()
// ROLE   Read one variable of conffile
// IN   $sd_card      location of the sd card to save data
//      $variable      Variable to read    
//      $out                error or warning messages
// RET Value read
function read_sd_conf_file($sd_card,$variable,$out="") {
   // Check if sd card is defined
    if (empty($sd_card))
        return false;

    $file="$sd_card/cnf/conf";

    if(!is_file("$sd_card/cnf/conf")) return false;
    
    // Open file
    $fid = @fopen($file,"r+");
    $offset = "";
    
    switch ($variable) {
        case "update_plugs_frequency":
            $offset = 18 * 0 + 12;
            
            if ($value == -1)
                $value = 0;
            
            break;
        case "record_frequency":
            $offset = 18 * 1 + 12;
            
            $value = $value * 60;
            
            break;
        case "power_frequency":
            $offset = 18 * 2 + 12;
            
            $value = $value * 60;
            
            break;
        case "alarm_activ":
            $offset = 18 * 3 + 12;
            break; 
        case "alarm_value":
            $offset = 18 * 4 + 12;
            break; 
        case "rtc_offset":
            $offset = 18 * 7 + 12;
            break; 
        case "minmax":
            $offset = 18 * 8 + 12;
            break;             
    }
    
    $val = "";
    
    if(($offset != "") && ($fid)) {
        fseek($fid, $offset);
        $val = fread($fid,4);
    }
    
    // Close
    if($fid) fclose($fid);
    return $val;
    
}
//}}}


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
//   $enable_led         Allow backlight of LCD screen
//   $out                error or warning messages
// RET false if an error occured, true else  
function write_sd_conf_file($sd_card,
                            $record_frequency=1,
                            $update_frequency=1,
                            $power_frequency=1,
                            $alarm_enable="0000",
                            $alarm_value="50.00",
                            $reset_value,
                            $rtc="0000",
                            $enable_led="0001",
                            $out="") {
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
   
    while(strlen($enable_led)<4) {
        $enable_led="0$enable_led";
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
      if(!@fputs($f,"ENABLE_LEDs:$enable_led\r\n")) $check=false;
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
//    $data            data to write into the sd card (come from calendar\read_event_from_db )
//    $out             error or warning messages
//    $start           write calendar between two dates (ms format)
//    $end             if start and end are not set: write full calendar (ms format)
// RET false if an error occured, true else
function write_calendar($sd_card,$data,&$out,$start="",$end="") {

    // If sd card is not defined, return
    if(!isset($sd_card) || empty($sd_card)) {
        return false;
    }

    $status=true;

    // If there are some events
    if(count($data)>0) {
        // If not defined Use today
        if ($start == "")
            $date =  strtotime(date("Y-m-d"));
        else
            $date =  $start;

        // Use today + 3 month
        if ($end == "")
            $endSearch  = strtotime("+3 months", $date);
        else
            $endSearch =  $end;

        while($date <= $endSearch)
        {
        
            $val = calendar\concat_entries($data,date("Y-m-d",$date));

            // Create filename
            $month = date("m",$date);
            $day = date("d",$date);
            $file = "$sd_card/logs/$month/cal_$day";

            // If there is something to write
            if($val) {
                // If file can be opened
                if($fid = fopen($file,"w+")) {
                
                    // If there is an Lune event, write symbols at top
                    foreach($val as $value) {
                        // Search if symbols exists
                        if (array_key_exists("cbx_symbol",$value))
                        {
                            $outSymbol = "";
                            
                            // Foreach symbol, add it
                            foreach (explode(" ",$value['cbx_symbol']) as $symbol)
                            {
                                // COnvert into binary string
                                $outSymbol = $outSymbol . hex2bin(substr($symbol,-2));
                            }
                            
                            // rite t
                            fputs($fid,$outSymbol . "\r\n");
                        }
                    }
                
                    // Foreach event to write
                    foreach($val as $value) {
                    
                        $sub  =  clean_calendar_message($value["subject"]);
                        $desc =  clean_calendar_message($value["description"]);

                        if(!fputs($fid, $sub . "\r\n")) 
                            $status=false;
                            
                        if(!fputs($fid, $desc . "\r\n")) 
                            $status=false;
                            
                    }
                    
                    // Close file
                    fclose($fid);
                } else {  
                    $status=false;
                }
            } else {
                // Delete file if present
                
                if (file_exists($file))               
                    unlink($file);
                    
            }
        
            // Incr date
            $date = strtotime("+1 day", $date);
            
            // Clear val
            unset($val);
            
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
        if(is_file($new_file)) {
            $handle = @fopen($new_file, 'r');
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
// ROLE check if cnf/prg/plgidx exists
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
        $path="../../../tmp/logs/index";
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

    // If file does not exists, return false
    if(!file_exists($log_file)) 
        return false;
        
    // Init return array
    $ret["cbx_id"]      = "";
    $ret["firm_version"]= "";
    $ret["log"]         = "";

    // Read the file
    $buffer_array = file($log_file);
    
    // Foreach line
    foreach($buffer_array as $buffer) {
    
        // Remove space before and after
        $buffer=trim($buffer);

        // If th line is empty, reurn
        if($buffer == "") 
            break;

        // Init log with buffer
        if(strcmp($ret["log"],"")==0) {
            $ret["log"] = $buffer;
        } else {
            $ret["log"] = $ret["log"] . "#" . $buffer;
        }

        switch (substr($buffer,14,1)) {
            case 'I':
                $ret["cbx_id"] = substr($buffer,16,5);
                break;
            case 'V':
                $ret["firm_version"] = substr($buffer,16,7); 
                break;
        }
    }
    
    return true;
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

// {{{ create_conf_XML()
// ROLE Used to creat a conf file
// IN      $file        Path for the conf file
// IN      $paramList       List of params
// RET true if we can, false else
function create_conf_XML($file, $paramList) {

    // Open in write mode
    $fid = fopen($file,"w+");
    
    // Add header
    fwrite($fid,'<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>' . "\r\n");
    fwrite($fid,'<conf>'. "\r\n");
    
    // Foreach param to write, add it to the file
    foreach ($paramList as $key => $value) {
        fwrite($fid,'    <item name="' . $key . '" value="' . $value . '" />'. "\r\n");
    }

    // Add Footer
    fwrite($fid,'</conf>'. "\r\n");
    
    // Close file
    fclose($fid);
    
    return true;
}
// }}}

?>

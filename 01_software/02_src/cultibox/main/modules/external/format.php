<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');
require_once('../../libs/utilfunc_sd_card.php');


if((isset($_GET['hdd']))&&(!empty($_GET['hdd']))) {
    $path=$_GET['hdd'];
}


if((isset($_GET['progress']))&&(!empty($_GET['progress']))) {
    $progress=$_GET['progress'];
}

if((!isset($path))||(empty($path))) {
    echo -1;
} else {
   if(check_sd_card($path)) {
        if((!isset($progress))||(empty($progress))) {
            $progress=0;
        }

        $logs = "$path/logs";
        $cnf  = "$path/cnf";
        $plg  = "$cnf/plg";
        $prg  = "$cnf/prg";
        $bin  = "$path/bin";

        if(strcmp("$progress","0")==0) {
            if(!is_dir($logs)) mkdir($logs);
            if(!is_dir($cnf)) mkdir($cnf);
            if(!is_dir($plg)) mkdir($plg);
            if(!is_dir($prg)) mkdir($prg);
            if(!is_dir($bin)) mkdir($bin);

            if(!is_file("$cnf/cnt")) {
                if(!copy("../../../tmp/cnf/cnt","$cnf/cnt")) {
                    echo -1;
                    return 0;
                }
            }

             //Copiyng firmware:
            check_and_copy_firm($path);

            
            if(!check_and_copy_index($path)) {
                echo "-1";
                return 0;
            }

            if(!check_and_copy_plgidx($path)) {
                echo "-1";
                return 0;
            }

            //Copiyng id file: 
            check_and_copy_id($path,get_informations("cbx_id"));
            
            //Creating pluga file:
            if(!write_pluga($path,$out)) {
                echo -1;
                return 0;
            }

            //Creating conf file:
            $update_frequency = get_configuration("UPDATE_PLUGS_FREQUENCY",$out);
            if($update_frequency == "-1") $update_frequency="0";
            if(!write_sd_conf_file($path,
                                    get_configuration("RECORD_FREQUENCY",$out),
                                    $update_frequency,
                                    get_configuration("POWER_FREQUENCY",$out),
                                    get_configuration("ALARM_ACTIV",$out),
                                    get_configuration("ALARM_VALUE",$out),
                                    get_configuration("RESET_MINMAX",$out),
                                    get_configuration("RTC_OFFSET",$out)))
            {
                echo -1;
                return 0;
            }



            // Creating log.txt file:
            if(!is_file("$path/log.txt")) {
                if(!copy_template_file("empty_file_big.tpl","$path/log.txt")) {
                    echo -1;
                    return 0;
                }
            } 


            // For pluXX :
            $program_index=array();
            program\get_program_index_info($program_index);

            foreach ($program_index as $key => $value) {
                // Read from database program
                $program = create_program_from_database($out,$value['program_idx']);

                $fileName = "${path}/cnf/prg/" . "plu" . $value['plugv_filename'];
                if(!save_program_on_sd($fileName,$program)) {      
                    echo "-1";
                    return 0;
                }
            }

            //For plugv
            $program = create_program_from_database($out);
            $fileName = "${path}/cnf/prg/" . "plugv";
            if(!save_program_on_sd($fileName,$program)) {
                echo "-1";
                return 0;
            }


            $data=array();
            calendar\read_event_from_db($data);
            $plgidx=create_plgidx($data);
            if(count($plgidx)>0) {
               if(!write_plgidx($plgidx,$path)) {
                    echo "-1";
                    return 0;
               }
            } else {
                if(!check_and_copy_plgidx($path)) {
                    echo "-1";
                    return 0;
                }
            }


            //Create plugXX files:
            $plugconf=create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$out);
            if(count($plugconf)>0) {
                if(!write_plugconf($plugconf,$path)) {
                    echo -1;
                    return 0;
                }
            }   

            //Copying cultibox icon:
            if(!copy("../../../tmp/cultibox.ico","$path/cultibox.ico")) {
                echo -1;
                return 0;
            }

            //Copying cultibox homepage:
            if(!copy("../../../tmp/cultibox.html","$path/cultibox.html")) {
                echo -1;
                return 0;
            }
            echo 1;
    } else {
            for($j=1;$j<=31;$j++) {
                    if(strlen($progress)<2) {
                        $month="0".$progress;
                    } else {
                        $month="$progress";
                    }

                    if(strlen($j)<2) {
                        $day="0".$j;
                    } else {
                        $day="$j";
                    }

                    if(!is_dir("$logs/$month")) {
                        mkdir("$logs/$month");
                    }

                    //Restore log and power files:
                    if(is_file("$logs/$month/$day")) {
                        if(filesize("$logs/$month/$day")!=filesize("../../templates/data/empty_file_big.tpl")) {
                            if(!copy_template_file("empty_file_big.tpl","$logs/$month/$day")) {
                                echo -1;
                                return 0;
                            }
                        }
                    } else {
                        if(!copy_template_file("empty_file_big.tpl", "$logs/$month/$day")) {
                            echo -1;
                            return 0;
                        }
                    }


                    if(is_file("$logs/$month/pwr_$day")) {
                        if(filesize("$logs/$month/pwr_$day")!=filesize("../../templates/data/empty_file_big.tpl")) {
                            if(!copy_template_file("empty_file_big.tpl","$logs/$month/pwr_$day")) {
                                echo -1;
                                return 0;
                            }
                        }
                    } else {
                        if(!copy_template_file("empty_file_big.tpl","$logs/$month/pwr_$day")) {
                            echo -1;
                            return 0;
                        }
                    }
            }
            if($progress==12) {
                $data = array();
                calendar\read_event_from_db($data);

                // Read event from XML
                foreach (calendar\get_external_calendar_file() as $fileArray) {
                    if ($fileArray['activ'] == 1) {
                        calendar\read_event_from_XML($fileArray['filename'],$data);
                    }
                }

                if(!write_calendar($path,$data,$out)) {
                    echo -1;
                    return 0;
                }
                echo 100;
            } else {
                echo $progress+1;
            }
    }
  }
}

?>

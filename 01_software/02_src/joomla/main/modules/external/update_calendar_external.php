<?php 

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_common.php');
require_once('../../libs/config.php');

if (!isset($_SESSION)) {
    session_start();
}


if((isset($_GET['substrat']))&&(!empty($_GET['substrat']))) {
    $program_substrat=$_GET['substrat'];
} 

if((isset($_GET['product']))&&(!empty($_GET['product']))) {
    $program_product=$_GET['product'];
}

if((isset($_GET['calendar_start']))&&(!empty($_GET['calendar_start']))) {
    $calendar_start=$_GET['calendar_start'];
}

$calendar_end=$calendar_start;

if((isset($_GET['sd_card']))&&(!empty($_GET['sd_card']))) {
    $sd_card=$_GET['sd_card'];
}

if((isset($program_substrat))&&(!empty($program_substrat))&&(isset($program_product))&&(!empty($program_product))&&(isset($calendar_start))&&(!empty($calendar_start))) {
    $file="";
    $main_error=array();

    if((isset($program_substrat))&&(!empty($program_substrat))&&(isset($program_product))&&(!empty($program_product))) {
        if($handle = @opendir('../../xml')) {
            while (false !== ($entry = readdir($handle))) {
                if(($entry!=".")&&($entry!="..")) {
                    $rss_file = file_get_contents("../../xml/".$entry);
                    $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);

                    foreach ($xml as $tab) {
                        if(is_array($tab)) {
                            if((array_key_exists('substrat', $tab))&&(array_key_exists('marque', $tab))&&(array_key_exists('periode', $tab))) {
                                if((strcmp(strtolower($tab['marque']." - ".$tab['periode']),strtolower($program_product))==0)&&((strcmp(strtolower($tab['substrat']),strtolower($program_substrat))==0))) {
                                    $file=$entry;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        if(!empty($file)) {
            $event=array();
            if($handle = @opendir('../../xml')) {
                $rss_file = file_get_contents("../../xml/".$file);
                $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);
                
                foreach ($xml as $tab) {
                    if(is_array($tab)) {
                        foreach($tab as $val) {
                            if(is_array($val)) {
                                if((array_key_exists('title', $val))&&(array_key_exists('duration', $val))&&(array_key_exists('start', $val))&&(array_key_exists('content', $val))) {
                                        if((!empty($val['title']))&&(!empty($val['content']))) {
                                            $datestartTimestamp = strtotime($calendar_start);
                                            $timestart = date('Ymd', strtotime('+'.$val['start'].' days', $datestartTimestamp));

                                            if(empty($val['start'])) {
                                                $val['start']=0;
                                            }

                                            if(empty($val['duration'])) {
                                                $val['duration']=0;
                                            }

                                            $val['duration']=$val['duration']-1;
                                            if($val['duration']<=0) {
                                                $timeend=$timestart;
                                            } else {
                                                $dateendTimestamp = strtotime($timestart);
                                                $timeend = date('Ymd', strtotime('+'.$val['duration'].' days', $dateendTimestamp));
                                            }

                                            if(array_key_exists('color', $val))  {
                                                $color=$val['color'];
                                            } else {
                                                $color="#821D78";
                                            }

                                            if(array_key_exists('icon', $val))  {
                                                $icon=$val['icon'];
                                            } else {
                                                $icon=null;
                                            }

                                            // Create description using nutriment section
                                            $desc = "";
                                            if(array_key_exists('nutriment', $val))  {
                                                $desc=$desc."Engrais:\n";
                                                if(is_array($val['nutriment'])) {
                                                    foreach($val['nutriment'] as $nut) {
                                                        if(is_array($nut)) {
                                                            if((array_key_exists('name', $nut))&&(array_key_exists('dosage', $nut)))  {
                                                                $desc=$desc.$nut['name']." ".$nut['dosage']."\n";
                                                            }
                                                        } else {
                                                            $desc=$desc.$nut." ";
                                                        }
                                                    }
                                                } 
                                            } 

                                            // Add in description part content section
                                            if(array_key_exists('content', $val))  {
                                                $desc=$desc.$val['content'];
                                            }

                                            $event[]=array(
                                                    "title" => $val['title'],
                                                    "start" => $timestart,
                                                    "end" => $timeend,
                                                    "description" => $desc,
                                                    "color" => $color,
                                                    "icon" => $icon,
                                                    "external" => "0"
                                                    //"allDay" => false
                                            );
    
                                            $calendar_end=$timeend;
                                        }   
                                }
                            }
                       }
                   } 
                } 
            }
            if(count($event)>0) {
                 if(insert_calendar($event,$main_error)) {
                    if((isset($sd_card))&&(!empty($sd_card))) {
                        if(strlen($calendar_end)==8) {
                            $year=substr($calendar_end,0,4);
                            $month=substr($calendar_end,4,2);
                            $day=substr($calendar_end,6,2);
                            $calendar_end="$year-$month-$day";
                      
                            $calendar=create_calendar_from_database($main_error,$calendar_start,$calendar_end);
                            if(count($calendar)>0) {
                                clean_calendar($sd_card,$calendar_start,$calendar_end);
                                write_calendar($sd_card,$calendar,$main_error,$calendar_start,$calendar_end);
                            }
                        }
                    }
                    echo "1";
                 } else {
                    echo "-1";
                }
            }
        }
    }
}


?>
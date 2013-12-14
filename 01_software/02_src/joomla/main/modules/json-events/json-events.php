<?php

usleep(500000);
require_once('../../../main/libs/config.php');
require_once('../../../main/libs/db_common.php');
require_once('../../../main/libs/utilfunc.php');

if(!$db = db_priv_pdo_start()) return false;

// Initializes a container array for all of the calendar events
$jsonArray = array();

$event=array();

$sql = <<<EOF
SELECT * FROM `calendar`;
EOF;

foreach($db->query("$sql") as $row) {
        if($row['Important']==1) {
            $color_event="red";
        } else {
            $color_event="white";
        }
        
        $event[]=array(
                        "id" => $row['Id'],
                        "title" => $row['Title'],
                        "start" => $row['StartTime'],
                        "end" => $row['EndTime'],
                        "description" => $row['Description'],
                        "color" => $row['Color'],
                        "icon" => $row['Icon'],
                        "important" => $row['Important'],
                        "textColor" => $color_event,
                        "external" => 0
            );
}
$db=null;


if ($handle = opendir('../../xml')) {
    while (false !== ($entry = readdir($handle))) {
     if(($entry!=".")&&($entry!="..")&&(check_xml_calendar_file($entry))) {
        $rss_file = file_get_contents("../../xml/".$entry);
        $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);
        $id=10000;
        $value=array();


        foreach ($xml as $tab) {
            if(is_array($tab)) {
                if((array_key_exists('title', $tab))&&(array_key_exists('content', $tab))&&(array_key_exists('start', $tab))) {
                    if(check_config_xml_file($entry)) {
                        $value[]=$tab;
                    }
                }
            }

        }

        if(count($value)==0) {
            foreach($xml as $tab) {
                if(is_array($tab)) {
                    foreach($tab as $val) {
                        if(is_array($val)) {
                            if((array_key_exists('title', $val))&&(array_key_exists('content', $val))&&(array_key_exists('start', $val))) {
                                if((!empty($val['title']))&&(!empty($val['content']))&&(!empty($val['start']))) {
                                    if(check_config_xml_file($entry)) {
                                        $value[]=$val;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }  


        if(count($value)>0) {
             foreach($value as $val) {
                $timestart=date("U",strtotime($val['start']));

                if(array_key_exists('duration', $val))  {
                    $timeend=date("U", strtotime("+".$val['duration']." days", $timestart));
                }

               
               if(array_key_exists('period', $val))  {
                   if(!empty($val['period'])) {
                        $py=substr($val['period'], 0, 4);
                        $pm=substr($val['period'], 5, 2);
                        $pd=substr($val['period'], 8, 2);
                        $ph=substr($val['period'], 11, 2);
                        $pmi=substr($val['period'], 14, 2);
                        $ps=substr($val['period'], 17, 2);
                        $timeperiod=$ps+(60*$pmi)+(3600*$ph)+(86400*$pd);
                    }
                } 
                $actual_year=substr($val['start'], 0, 4);

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


                do {
                    $start=date('Y-m-d H:i:s', $timestart);
                    $end=date('Y-m-d H:i:s', $timeend);
                    $event[]=array(
                            "id" => $id,
                            "title" => $val['title'],
                            "start" => $start,
                            "end" => $end,
                            "description" => $val['content'],
                            "color" => $color,
                            "icon" => $icon,
                            "external" => 1
                            //"allDay" => false
                        );
                        $id=$id+1;

                    if(!array_key_exists('period', $val)) {
                        break;
                    } else {
                        $timestart=$timestart+$timeperiod;
                        $timeend=$timeend+$timeperiod;
                    }                    

                    $test_year=date('Y',$timeend);
                } while($actual_year==$test_year);
                if(isset($timeperiod)) unset($timeperiod);
            }
        }
    }
  }
}


echo json_encode($event);


?>
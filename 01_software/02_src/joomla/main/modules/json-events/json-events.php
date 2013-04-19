<?php

$link = mysql_connect('localhost','cultibox','cultibox');
if (!$link) { die('Could not connect: ' . mysql_error()); }
mysql_select_db('cultibox');


// Initializes a container array for all of the calendar events
$jsonArray = array();

$event=array();

$sql = <<<EOF
SELECT * FROM `calendar`;
EOF;


$res = mysql_query($sql);
while($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
        $event[]=array(
                        "id" => $row['Id'],
                        "title" => utf8_encode($row['Title']),
                        "start" => $row['StartTime'],
                        "end" => $row['EndTime'],
                        "description" => utf8_encode($row['Description']),
                        "color" => $row['Color'],
                        "external" => 0
            );
}


if ($handle = opendir('../../xml')) {
    while (false !== ($entry = readdir($handle))) {
        $rss_file = file_get_contents("../../xml/".$entry);
        $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);
        $id=10000;

        foreach ($xml as $tab) {
            if(is_array($tab)) {
                foreach($tab as $val) {
                    if(is_array($val)) {
                        if((array_key_exists('title', $val))&&(array_key_exists('content', $val))&&(array_key_exists('start', $val))) {
                            if((!empty($val['title']))&&(!empty($val['content']))&&(!empty($val['start']))) {
                                $start=substr($val['start'], 0, 10);  
                                $hour="00:00:00";
                                //$hour=substr($val['start'], 11, 8);
                                if(array_key_exists('duration', $val))  {
                                        if($val['duration']>1) {
                                            $duration=$val['duration']-1;
                                            $tmpEnd=strtotime($start);
                                            $end=date("Y-m-d", strtotime("+".$duration." days", $tmpEnd ));
                                        } else {
                                            $end=$start;
                                        }
                                } else {
                                    $end=$start;
                                    //$hour="00:00:00";
                                }

                
                               if(array_key_exists('period', $val))  {
                                    if(!empty($val['period'])) {
                                        $period=substr($val['period'], 0, 10);
                                        $py=substr($period, 0, 4);    
                                        $pm=substr($period, 5, 2);
                                        $pd=substr($period, 8, 2);
                                        $actual_year=substr($val['start'], 0, 4);
                                    }
                                } 
                    
                                do {

                                    if((strlen($start)==10)&&(strlen($end)==10)) {
                                        $event[]=array(
                                            "id" => $id,
                                            "title" => utf8_encode($val['title']),
                                            "start" => $start." ".$hour,
                                            "end" => $end." ".$hour,
                                            "description" => utf8_encode($val['content']),
                                            "color" => "#A4408D",
                                            "external" => 1
                                        );
                                        $id=$id+1;
                                    }

                                    if(!isset($period)) {
                                        break;
                                    } else {
                                        $tmpStart=strtotime($start);
                                        if($py!=0) {
                                            $start=date("Y-m-d", strtotime("+".$py." years", $tmpStart ));
                                            $tmpStart=strtotime($start);
                                        }
                                        if($pm!=0) {
                                            $start=date("Y-m-d", strtotime("+".$pm." months", $tmpStart ));
                                            $tmpStart=strtotime($start);
                                        }

                                        if($pd!=0) {
                                            $start=date("Y-m-d", strtotime("+".$pd." days", $tmpStart ));
                                            $tmpStart=strtotime($start);
                                        }

                                        if(array_key_exists('duration', $val))  {
                                            if($val['duration']>1) {
                                                $tmpEnd=strtotime($start);
                                                $end=date("Y-m-d", strtotime("+".$duration." days", $tmpEnd ));
                                            } else {
                                                $end=$start;
                                            }
                                        } else {
                                            $end=$start;
                                            //$hour="00:00:00";
                                        }                    

                                        $test_year=substr($start, 0, 4);
                                    }
                                } while($actual_year==$test_year);
                            }
                        }
                    }
                }
            }
        }
    }
}



echo json_encode($event);


?>

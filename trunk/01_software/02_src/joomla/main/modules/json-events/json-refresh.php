<?php


require_once('../../../main/libs/utilfunc.php');

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
$sd_card=$_POST['sd_card'];
$main_error=array();

$link = mysql_connect('localhost','cultibox','cultibox');
if (!$link) { die('Could not connect: ' . mysql_error()); }
mysql_select_db('cultibox');

// Part for the calendar: if a cultibox SD card is present, the 'calendar' is updated into this SD card
if((isset($sd_card))&&(!empty($sd_card))) {
        $year=date('Y');
        $sql = <<<EOF
SELECT `Title`,`StartTime`,`EndTime`, `Description` FROM `calendar` WHERE `StartTime` LIKE "{$year}-%"
EOF;
        $res = mysql_query($sql);
        $data=array();
        while($val = mysql_fetch_array($res, MYSQL_ASSOC)) {
         $s=array();
         $desc=array();
         $line="";
         $start_month=substr($val['StartTime'],5,2);
         $start_day=substr($val['StartTime'],8,2);
         $start_year=substr($val['StartTime'],0,4);

         $end_month=substr($val['EndTime'],5,2);
         $end_day=substr($val['EndTime'],8,2);
         $end_year=substr($val['EndTime'],0,4);

         $count=0;
         $number=0;

         for($i=0;$i<strlen($val['Title']);$i++) {
            $count=$count+1;
            if($count==1) {
               if(strcmp($val['Title'][$i]," ")==0) {
                  $count=0;
               } else {
                  $line=$line.$val['Title'][$i];
               }
            } else {
                 $line=$line.$val['Title'][$i];
            }

            if($count==12) {
               if((strcmp($val['Title'][$i]," ")!=0)&&(isset($val['Title'][$i+1]))&&(strcmp($val['Title'][$i+1]," ")!=0)) {
                  if(isset($val['Title'][$i+2])) {
                     $line=$line."-";
                     $count=$count+1;
                  }
               } elseif(strcmp($val['Title'][$i]," ")==0) {
                     $line=$line." ";
                     $count=$count+1;
              }
            }

            if($count==13) {
               $s[]=strtoupper($line);
               $line="";
               $count=0;
               $number=$number+1;
            }

            if("$number"=="18") {
               break;
            }
         }

         if(("$count"!="13")&&("$number"!="18")) {
            $s[]=strtoupper($line);
            $number=$number+1;
         }

         while(strlen($s[$number-1])<13) {
            $s[$number-1]=$s[$number-1]." ";
         }


         if((isset($val['Description']))&&(!empty($val['Description']))) {
            $count=0;
            $line="";
            for($i=0;$i<strlen($val['Description']);$i++) {
               if(strcmp($val['Description'][$i],"\n")==0) {
                   while(strlen($line)<=12) {
                        $line=$line." ";
                   }
                   $desc[]=$line;$line="";
                   $count=0;
                   $number=$number+1;
               } else {
                   $count=$count+1;
                   if($count==1) {
                        if(strcmp($val['Description'][$i]," ")==0) {
                            $count=0;
                        } else {
                            $line=$line.$val['Description'][$i];
                        }
                    } else {
                            $line=$line.$val['Description'][$i];
                    }

                    if($count==12) {
                        if((strcmp($val['Description'][$i]," ")!=0)&&(isset($val['Description'][$i+1]))&&(strcmp($val['Description'][$i+1]," ")!=0)) {
                            if(isset($val['Description'][$i+2])) {
                                $line=$line."-";
                                $count=$count+1;
                            }
                        } elseif(strcmp($val['Description'][$i]," ")==0) {
                                $line=$line." ";
                                $count=$count+1;
                        }
                    }

                    if($count==13) {
                        $desc[]=$line;
                        $line="";
                        $count=0;
                        $number=$number+1;
                    }
               }

               if("$number"=="18") {
                  break;
               }
            }

            if(("$count"!="13")&&("$number"!="18")) {
               $desc[]=$line;
               $number=$number+1;
            }


            if(count($desc)>0) {
               while(strlen($desc[count($desc)-1])<13) {
                  $desc[count($desc)-1]=$desc[count($desc)-1]." ";
               }
            }
         }

         $data[]=array(
            "start_month" => $start_month,
            "start_day" => $start_day,
            "end_month" => $end_month,
            "end_day" => $end_day,
            "number" => $number,
            "subject" => $s,
            "description" => $desc
         );
         unset($s);
      }


    if(isset($sd_card)&&(!empty($sd_card))) {
      if(count($data)>0) {
         write_calendar($sd_card,$data,$main_error);
      }
    }
}

?>

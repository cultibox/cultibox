<?php

// {{{ db_priv_pdo_start()
// ROLE create a connection to the DB using PDO
// IN none 
// RET database connection object
function db_priv_pdo_start($user="cultibox") {
    try {
        $db = new PDO('mysql:host=127.0.0.1;port=3891;dbname=cultibox;charset=utf8', $user, 'cultibox');
        //$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $db->exec("SET CHARACTER SET utf8");
        return $db;
    } catch (PDOException $e) {
        return 0;
    }
}

// {{{ get_configuration()
// ROLE get configuration value for specific entries
// IN $key   the key selectable from the database 
//    $out   errors or warnings messages
// RET $res   value of the key   
//Note: if to select a value is limited to 1. Only one configuration is available,
//there isn't a user configuration management yet.
function get_configuration($key,&$out="") {
        $sql = <<<EOF
SELECT {$key} FROM `configuration` WHERE id = 1
EOF;
    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth-> execute();
        $res=$sth->fetch();
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
    }
    return $res[0];
}
// }}}


// {{{ get_webcam()
// ROLE get webcam value for specific entries
// IN $key   the key selectable from the database 
//    $out   errors or warnings messages
// RET $res   value of the key   
function get_webcam($key,&$out="") {
        $sql = <<<EOF
SELECT {$key} FROM `webcam` WHERE id = 1
EOF;
    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth-> execute();
        $res=$sth->fetch();
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
    }
    return $res[0];
}
// }}}



// {{{ get_informations()
// ROLE get informations value for specific entries
// IN $key   the key selectable from the database 
//    $out   errors or warnings messages
// RET $res   value of the key   
function get_informations($key,&$out="") {
   $sql = <<<EOF
SELECT {$key} FROM `informations` WHERE id = 1
EOF;
    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth-> execute();
        $res=$sth->fetch();
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
    }

    return $res[0];
}
// }}}


// {{{ get_plug_conf()
// ROLE get plug configuration value for specific entries
// IN $key      the key selectable from the database 
//    $id   id of the plug
//    $out      errors or warnings messages
// RET $res   value result for the plug configuration entrie
function get_plug_conf($key,$id,&$out) {

    // Init var 
    $res = array();

    $sql = "SELECT {$key} FROM plugs WHERE id = {$id};";
   
    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare($sql);
        $sth-> execute();
        $res=$sth->fetch();
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if(isset($ret) && !empty($ret)) {
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
    }
  
    if (count($res)>=1) {
        return $res[0];
    } else {
        return "";
    }
}
// }}}


// {{{ get_plugs_infos()
// ROLE get plugs informations (name,id,type)
// IN $id      id of the plug
//    $out      errors or warnings messages
// RET return an array containing plugid and its name
function get_plugs_infos($nb=0,&$out) {

    $sql = "SELECT id, PLUG_NAME, PLUG_TYPE, PLUG_REGUL, PLUG_POWER_MAX, PLUG_TOLERANCE"
            . " FROM plugs"
            . " WHERE id <= '{$nb}'"
            . " ORDER by id ASC";

    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
    }
    return $res;
}
// }}}


// {{{ get_data_plug()
// ROLE get a specific plug program
// IN $selected_plug   plug id to select
//    $out      errors or warnings messages
//    $number   number of the program in program_index table
// RET plug data formated for highchart
function get_data_plug($selected_plug="",&$out,$number=1) {
   $res="";
   if((isset($selected_plug))&&(!empty($selected_plug))) {
      $sql = <<<EOF
SELECT time_start, time_stop, value, type, number FROM programs WHERE plug_id = {$selected_plug} AND number = {$number} ORDER by time_start ASC
EOF;
        $db=db_priv_pdo_start();
        try {
            $sth=$db->prepare("$sql");
            $sth->execute();
            $res=$sth->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $ret=$e->getMessage();
        }
        $db=null;

        if((isset($ret))&&(!empty($ret))) {
            if($GLOBALS['DEBUG_TRACE']) {
                    $out[]=__('ERROR_SELECT_SQL').$ret;
                } else {
                    $out[]=__('ERROR_SELECT_SQL');
                }
                return 0;
          }
    }
    return $res;
}
// }}}


// {{{ get_data_power()
// ROLE get a data power from the database
// IN $date   date to select reccords
//    $datend end of the interval of date
//    $id     id of the plug to be used ('all' for all the plugs)
//    $out    errors or warnings messages
//    $short  select 0 if $short is not set 
// RET data power formated for highchart
function get_data_power($date="",$dateend="",$id=0,&$out,$short="") {
   $res="";
   $nb_plugs=get_configuration("NB_PLUGS",$out);
   $date=str_replace("-","",$date);
   $date=substr($date,2,8);

   if((isset($date))&&(!empty($date))) {
      if(is_array($id)) {
          $list="";
          foreach($id as $nid) {
            if(strcmp("$list","")==0) {
                $list=$nid;    
            } else {
                $list=$list.",".$nid;
            } 
          }
          if((!isset($dateend))||(empty($dateend))) {
              if(empty($short)) {
                  $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp LIKE "{$date}%" AND `plug_number` IN ({$list}) ORDER by time_catch,plug_number ASC, plug_number ASC
EOF;
               } else {
                   $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp LIKE "{$date}%" AND `plug_number` IN ({$list}) AND `record` != 0 ORDER by time_catch,plug_number ASC, plug_number ASC
EOF;
               }
          } else {
              $date=$date."00000000";
              $dateend=str_replace("-","",$dateend);
              $dateend=substr($dateend,2,8);
              $dateend=$dateend."99999999";

              if(empty($short)) {
                  $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp BETWEEN  "{$date}" AND "{$dateend}" AND `plug_number` IN ({$list})
EOF;
              } else {
                  $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp BETWEEN  "{$date}" AND "{$dateend}" AND `plug_number` IN ({$list}) AND `record` != 0
EOF;
              }
          }
      } else if(strcmp("$id","all")==0) {
         if((!isset($dateend))||(empty($dateend))) {
            if(empty($short)) {
            $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp LIKE "{$date}%" AND `plug_number` IN (SELECT `id` FROM `plugs`) GROUP BY time_catch,plug_number ORDER by timestamp ASC, plug_number ASC
EOF;
            } else {
            $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp LIKE "{$date}%" AND `plug_number` IN (SELECT `id` FROM `plugs`) AND `record` != 0 GROUP BY time_catch,plug_number ORDER by timestamp ASC, plug_number ASC
EOF;
            }
         } else {
         $date=$date."00000000";
         $dateend=str_replace("-","",$dateend);
         $dateend=substr($dateend,2,8);
         $dateend=$dateend."99999999";
        
         if(empty($short)) {  
      $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp BETWEEN  "{$date}" AND "{$dateend}" AND `plug_number` IN (SELECT `id` FROM `plugs`)
EOF;
         } else {
$sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp BETWEEN  "{$date}" AND "{$dateend}" AND `plug_number` IN (SELECT `id` FROM `plugs`) AND `record` != 0
EOF;
         }
        }
      } else {
         if((!isset($dateend))||(empty($dateend))) {
            if(empty($short)) {
            $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp LIKE "{$date}%" AND `plug_number` = "{$id}" ORDER by time_catch,plug_number ASC, plug_number ASC
EOF;
            } else {
            $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp LIKE "{$date}%" AND `plug_number` = "{$id}" AND `record` != 0 ORDER by time_catch,plug_number ASC, plug_number ASC
EOF;
            }
        } else {
            $date=$date."00000000";
            $dateend=str_replace("-","",$dateend);
            $dateend=substr($dateend,2,8);
            $dateend=$dateend."99999999";
    
            if(empty($short)) {
            $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp BETWEEN  "{$date}" AND "{$dateend}" AND `plug_number` = "{$id}" 
EOF;
            } else {
            $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp BETWEEN  "{$date}" AND "{$dateend}" AND `plug_number` = "{$id}" AND `record` != 0
EOF;
            }
      }
    }


        $db=db_priv_pdo_start();
        try {
            $sth=$db->prepare("$sql");
            $sth->execute();
            $res=$sth->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $ret=$e->getMessage();
        }
        $db=null;

        if((isset($ret))&&(!empty($ret))) {
         if($GLOBALS['DEBUG_TRACE']) {
                      $out[]=__('ERROR_SELECT_SQL').$ret;
                } else {
                      $out[]=__('ERROR_SELECT_SQL');
                }
                return 0;
        }
   }

   
        $sql = <<<EOF
SELECT `PLUG_POWER` FROM `plugs` WHERE `id` <= {$nb_plugs};  
EOF;

        $db=db_priv_pdo_start();
        try {
            $sth=$db->prepare("$sql");
            $sth->execute();
            $res_power=$sth->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $ret=$e->getMessage();
        }
        $db=null;

        

        if((isset($ret))&&(!empty($ret))) {
         if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
         } else {
            $out[]=__('ERROR_SELECT_SQL');
         }
         return 0;
        }

    
        if(is_array($id)) {
            //For all plugs
            for($i=0;$i<count($res_power);$i++) {
                if(strcmp($res_power[$i]['PLUG_POWER'],"")==0) {
                    $res_power[$i]['PLUG_POWER']=0;
                }
            }

            while(count($res_power)!=$GLOBALS['NB_MAX_PLUG']) {
                $res_power[]=0;
            }
        } else if(strcmp("$id","all")!=0) {
            if(strcmp($res_power[$id-1]['PLUG_POWER'],"")==0) {
                $res_power[$id-1]['PLUG_POWER']=0;
            }

            $tmp=array();
            foreach($res as $val) {
                $value=round($val["record"]/10);
                $tmp[]=array(
                     "timestamp" => $val["timestamp"],
                     "record" => round(($res_power[$id-1]['PLUG_POWER']*$value)/999),
                     "plug_number" => $val["plug_number"],
                     "date_catch" => $val["date_catch"],
                     "time_catch" => $val["time_catch"]
               );
            }
            return $tmp;
        } else {
            //For all plugs
            for($i=0;$i<count($res_power);$i++) {
                if(strcmp($res_power[$i]['PLUG_POWER'],"")==0) {
                    $res_power[$i]['PLUG_POWER']=0; 
                }
            }

            while(count($res_power)!=$GLOBALS['NB_MAX_PLUG']) {
                $res_power[]=0;
            }
        }


        if(count($res)>0) {
            $timestamp=$res[0]['timestamp'];
            $save=$res[0];
            $val=0;
            $tmp=array();
            for($i=0;$i<count($res);$i++) {
                if(strcmp($res[$i]['timestamp'],$timestamp)==0) { 
                    $count=$res[$i]['plug_number']-1;
                    $pcent=round((int)$res[$i]['record']/10);
                    $val=$val+($pcent * (int)$res_power[$count]['PLUG_POWER'])/999;
                } else {
                    $tmp[] = array (
                        "timestamp" => "$timestamp",
                        "record" => round($val),
                        "plug_number" => $res[$i-1]["plug_number"],
                        "date_catch" => $save["date_catch"],
                        "time_catch" => $save["time_catch"]
                    ); 

                    $save=$res[$i];
                    $val=0;
                    $pcent=0;
                    $timestamp=$res[$i]['timestamp'];
                    $i=$i-1;
                } 
            }
        } else { 
            return 0;
        }
        return $tmp;
}
// }}}


// {{{ get_theorical_power()
// ROLE get a theorical data power from the database
// IN $id     id of the plug to be used ('all' for all the plugs)
//    $out      errors or warnings messages
//    $type   type of the electric installation: hpc or standard
// RET data power formated for highchart
function get_theorical_power($id=0,$type="",&$out,&$error=0) {
   $nb_plugs = get_configuration("NB_PLUGS",$out);

   if(strcmp($type,"standard")==0) {
        $price=get_configuration("COST_PRICE",$out);
        if($price==0) {
            $out[]=__('ERROR_COST_PRICE_NULL');
        }
        $price=($price/3600)/1000;
   } else {
        $price_hp=get_configuration("COST_PRICE_HP",$out);
        $price_hc=get_configuration("COST_PRICE_HC",$out);
        $start_hc=get_configuration("START_TIME_HC",$out);
        $stop_hc=get_configuration("STOP_TIME_HC",$out);
        $starthc=0;
        $stophc=0;

        if(($price_hc==0)||($price_hp==0)) {
            $out[]=__('ERROR_COST_PRICE_NULL');
        }

        if((strcmp($start_hc,"")==0)||(strcmp($stop_hc,"")==0)) {
            $out[]=__('ERROR_HPC_TIME_NULL')."<a href='/cultibox/index.php?menu=plugs'>".__('HERE')."</a>";  
        } else {
            $stahch=substr($start_hc,0,2);
            $stahcm=substr($start_hc,3,2);

            $stohch=substr($stop_hc,0,2);
            $stohcm=substr($stop_hc,3,2);

            date_default_timezone_set('UTC');

            $starthc=mktime($stahch,$stahcm,0,0,0,1971);
            $stophc=mktime($stohch,$stohcm,0,0,0,1971);

        }

        $price_hc=($price_hc/3600)/1000;
        $price_hp=($price_hp/3600)/1000;
   }

   $res="";
   if(strcmp("$id","all")==0) {
          $sql = <<<EOF
SELECT * FROM `programs` WHERE `plug_id` > 0 AND `plug_id` <= ${nb_plugs} AND `plug_id` IN (SELECT `id` FROM `plugs`) 
EOF;
      } else {
      $sql = <<<EOF
SELECT * FROM `programs` WHERE `plug_id` = "{$id}" AND `plug_id` IN (SELECT `id` FROM `plugs`)
EOF;
   }

   $db=db_priv_pdo_start();
   try {
       $sth=$db->prepare("$sql");
       $sth->execute();
       $res=$sth->fetchAll(PDO::FETCH_ASSOC);
   } catch(PDOException $e) {
       $ret=$e->getMessage();
   }
   $db=null;

   if((isset($ret))&&(!empty($ret))) {
         if($GLOBALS['DEBUG_TRACE']) {
                      $out[]=__('ERROR_SELECT_SQL').$ret;
         } else {
                      $out[]=__('ERROR_SELECT_SQL');
         }
         return 0;
   }


   if(count($res)>0) {
      $sql = <<<EOF
SELECT `PLUG_POWER` FROM `plugs` WHERE `id` <= ${nb_plugs}
EOF;

     $db=db_priv_pdo_start();
     unset($ret);
     try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res_power=$sth->fetchAll(PDO::FETCH_ASSOC);
     } catch(PDOException $e) {
        $ret=$e->getMessage();
     }
     $db=null;

     if((isset($ret))&&(!empty($ret))) {
         if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
         } else {
            $out[]=__('ERROR_SELECT_SQL');
         }
         return 0;
      }

   } else {
      return 0;
   }

   if(count($res_power)==$nb_plugs) {
         date_default_timezone_set('UTC');
         $theorical=0;
         foreach($res as $val) {
               $id=$val['plug_id']-1;

               if($res_power[$id]['PLUG_POWER']==0) {
                     $error=1;
                     $enable=0;
               } else {
                     $enable=1;
               }

               $shh=substr($val['time_start'],0,2);
               $smm=substr($val['time_start'],2,2);
               $sss=substr($val['time_start'],4,2);
               $ehh=substr($val['time_stop'],0,2);
               $emm=substr($val['time_stop'],2,2);
               $ess=substr($val['time_stop'],4,2);

               $time_end=mktime($ehh,$emm,$ess,0,0,1971);
               $time_start=mktime($shh,$smm,$sss,0,0,1971);
               $time_final=$time_end-$time_start;

               if(strcmp($type,"hpc")==0) {
                    while($time_start<=$time_end) {
                        if($starthc<=$stophc) {
                            if(($time_start>=$starthc)&&($time_start<=$stophc)) {
                                $price=$price_hc;

                            } else {
                                $price=$price_hp;
                            }
                        } else {
                            if(($time_start>=$starthc)||($time_start<=$stophc)) {
                                $price=$price_hc; 

                            } else {
                                $price=$price_hp;
                            }
                        }
                        $theorical=$theorical+($price*$res_power[$id]['PLUG_POWER']*$enable);
                        $time_start=$time_start+1;
                    }          
                } else {
                    $theorical=$theorical+($time_final*$price*$res_power[$id]['PLUG_POWER']*$enable);
                }

         }
      return number_format($theorical,2);
   }
   return 0;
}
// }}}


// {{{ get_real_power()
// ROLE get a real price for power used
// IN $data     array containing data to compute
//    $out      errors or warnings messages
//    $type     type of the electric installation: hpc or standard
// RET data power formated for highchart
function get_real_power($data="",$type="",&$out)  {
    if((empty($data))||(!isset($data))||(empty($type))||(!isset($type))) {
        return 0;
    }

    if(strcmp($type,"standard")==0) {
        $price=get_configuration("COST_PRICE",$out);
        if($price==0) {
            return 0;
        }
        $price=($price/60)/1000;
   } else {

        $price_hp=get_configuration("COST_PRICE_HP",$out);
        $price_hc=get_configuration("COST_PRICE_HC",$out);
        $start_hc=get_configuration("START_TIME_HC",$out);
        $stop_hc=get_configuration("STOP_TIME_HC",$out);
        $starthc=0;
        $stophc=0;
    
        if(($price_hp==0)||($price_hc==0)) {
            $out[]=__('ERROR_COST_PRICE_NULL');
            return 0;
        }

        if((strcmp($start_hc,"")==0)||(strcmp($stop_hc,"")==0)) {
            $out[]=__('ERROR_HPC_TIME_NULL');
            return 0;
        } else {
            $stahch=substr($start_hc,0,2);
            $stahcm=substr($start_hc,3,2);

            $stohch=substr($stop_hc,0,2);
            $stohcm=substr($stop_hc,3,2);

            date_default_timezone_set('UTC');

        }


        $price_hc=($price_hc/60)/1000;
        $price_hp=($price_hp/60)/1000;
  }

  if(strcmp($type,"standard")==0) {
      $compute=0;
      foreach($data as $val) {
         $compute=$compute+($val['record']*$price);
      }
  } else {

    date_default_timezone_set('UTC');
    $compute=0;

    foreach($data as $val) {
               $hh=substr($val['time_catch'],0,2);
               $mm=substr($val['time_catch'],2,2);
               $ss=substr($val['time_catch'],4,2);
               $MM=substr($val['date_catch'],5,2);
               $DD=substr($val['date_catch'],8,2);
               $YYYY=substr($val['date_catch'],0,4);
        
               $time=mktime($hh,$mm,$ss,$DD,$MM,$YYYY);
               $starthc=mktime($stahch,$stahcm,0,$DD,$MM,$YYYY);
               $stophc=mktime($stohch,$stohcm,0,$DD,$MM,$YYYY);

               if($starthc<=$stophc) {
                   if(($time>=$starthc)&&($time<=$stophc)) {
                          $price=$price_hc;
                   } else {
                          $price=$price_hp;
                          return "test: ".$val['time_catch'];
                   }
               } else {
                   if(($time>=$starthc)||($time<=$stophc)) {
                          $price=$price_hc;
                    } else {
                          $price=$price_hp;
                    }
               }
               $compute=$compute+($price*$val['record']);
    } 
             
  }
  return number_format($compute,2);

}
// }}}


// {{{ compare_data_program()
// ROLE compare and format 3 values of the program graph
// IN $first      first value to compare
//    $last      last value to compare
//    $current      current value submitted by user to be added
//    $tmp      array to save datas
// RET 0: nothing to be done, value are saved in tmp. 1: save the first value and continue with last value as first value. 2: sagin the current value as first value and passing to the next value for the last value (for special case).
function compare_data_program(&$first,&$last,&$current,&$tmp) {
       if(($current['time_start']>=$first['time_start'])&&($current['time_stop']<=$first['time_stop'])) {
       //case 1: current value is in the first value
           if($GLOBALS['DEBUG_TRACE']) {
               echo "case 1-";
           }

           if($current['value']==$first['value']) {
           //first=current: nothing to do
               if($GLOBALS['DEBUG_TRACE']) {
                   echo "1<br />";
                   echo "----------------<br />";
                   print_r($first);
                   echo "<br />";
                   print_r($last);
                   echo "<br />";
                   print_r($current);
                   echo "<br />";
               }
               $tmp[]=$first;
               $tmp[]=$last;
               return "0";      
            } else if(($current['time_start']==$first['time_start'])&&($current['time_stop']==$first['time_stop'])) {
            //first==current: replacement of the value
                if($current['value']==0) {
                //delete the first
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "2<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    $tmp[]=$last;
                    return "0";
                } else if($current['value']!=$first['value']) {
                    //replacement of the first
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "3<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    $tmp[]=$current;
                    $tmp[]=$last;
                    return "0";
                } else {
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "un-1<br />";
                    }
                }
            } else if($current['time_start']==$first['time_start']) {
                //current begin with the first value but doesn't ended with the first
                if($current['value']==0) {
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "4<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    $first['time_start']=$current['time_stop'];
                    $tmp[]=$first;
                    $tmp[]=$last;
                    return "0";   
                } else if($current['value']!=$first['value']) {
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "5<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    $new_value = array(
                                "time_start" => $first['time_start'],
                                "time_stop" => $current['time_stop'],
                                "value" => $current['value'],
                                "type" => $current['type'],
                                "number" => $current['number']
                    );

                    $first['time_start']=$current['time_stop'];
                    $tmp[]=$new_value;
                    $tmp[]=$first;
                    $tmp[]=$last;
                    return "0";
                } else {
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "un-2<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                } 
            } else if($current['time_stop']==$first['time_stop']) {
            //current doesn't start with the start value of first but ended with the stop value of first
                if($current['value']==0) {
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "6<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    $first['time_stop']=$current['time_start'];
                    $tmp[]=$first;
                    $tmp[]=$last;
                    return "0";   
                } else if($current['value']!=$first['value']) {
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "7<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    $first['time_stop']=$current['time_start'];
                    $tmp[]=$first;
                    $tmp[]=$current;
                    $tmp[]=$last;
                    return "0";
                } else {
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "un-3<br />";
                    }
                }
            } else {
            //current is in the first value: cut in three
                if($current['value']==0) {
                    $save_time=$first['time_stop'];
                    $first['time_stop']=$current['time_start'];
                    $new_value= array(
                        "time_start" => $current['time_stop'],
                        "time_stop" => $save_time,
                        "value" => $first['value'],
                        "type" => $first['type'],
                        "number" => $current['number']
                    );

                    $tmp[]=$first;
                    $tmp[]=$new_value;   
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "8<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else {
                    $save_time=$first['time_stop'];
                    $first['time_stop']=$current['time_start'];
                    $new_value=array(
                            "time_start" => $current['time_stop'],
                            "time_stop" => $save_time,
                            "value" => $first['value'],
                            "type" => $first['type'],
                            "number" => $current['number']
                    );

                    $tmp[]=$first;
                    $tmp[]=$current;   
                    $tmp[]=$new_value;
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "9<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                }
            }
       } else if(($current['time_start']>=$first['time_stop'])&&($current['time_stop']<=$last['time_start'])) {
            if($GLOBALS['DEBUG_TRACE']) {
                echo "case 2-";
            }
            //case 2: current value is between first and last value
            if($current['value']==0) {
            //nothing to do
                if($GLOBALS['DEBUG_TRACE']) {
                        echo "1<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                }
                $tmp[]=$first;
                $tmp[]=$last;
                return "0";
            } else if(($current['time_start']==$first['time_stop'])&&($current['time_stop']==$last['time_start'])) {
            //first->current->last: replacement of the value
                if(($current['value']==$first['value'])&&($current['value']==$last['value'])) {
                    // same value: joins 3 spacetimes
                    $first['time_stop']=$last['time_stop'];
                    $tmp[]=$first;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "2<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else if($current['value']==$first['value']) {
                    //Same value for current and the fist spacetime: join first and current:
                    $first['time_stop']=$current['time_stop'];
                    $tmp[]=$first;
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "3<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else if($current['value']==$last['value'])  {
                    //same value between current and last: join current and last:
                    $tmp[]=$first;
                    $last['time_start']=$current['time_start'];
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "4<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";   
                } else {
                    //Different value: add 3 spacetimes
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "5<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    $tmp[]=$first;
                    $tmp[]=$current;
                    $tmp[]=$last;
                    return "0";
                }
            } else if($current['time_start']==$first['time_stop']) {
                if(($current['value']==$first['value'])) {
                    //Join current dans first:
                    $first['time_stop']=$current['time_stop'];
                    $tmp[]=$first;
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "6<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else {
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "7<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    $tmp[]=$first;
                    $tmp[]=$current;
                    $tmp[]=$last;
                    return "0";
                }
            } else if($current['time_stop']==$last['time_start']) {
                //If end time of current value is as the start ime of last value
                if(($current['value']==$last['value'])) {
                    $last['time_start']=$current['time_start'];
                    $tmp[]=$first;
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "8<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else {
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "9<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    $tmp[]=$first;
                    $tmp[]=$current;
                    $tmp[]=$last;
                    return "0";
                }
            } else {
                if($GLOBALS['DEBUG_TRACE']) {
                        echo "10<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                }
                $tmp[]=$first;
                $tmp[]=$current;
                $tmp[]=$last;
                return "0";
            }
       } else if(($current['time_start']>=$last['time_start'])&&($current['time_stop']<=$last['time_stop'])) {
            // case 3: current value is in the last value
            // Saving the first value and return 1 to continue with the last value as first value
            $tmp[]=$first;
            return "1";   
       } else if(($current['time_start']>=$first['time_start'])&&($current['time_start']<=$first['time_stop'])&&($current['time_stop']<$last['time_start'])&&($current['time_stop']>$first['time_stop'])) {
            //case 4: current value is in the first value and stop between the first and before the last value
            if($GLOBALS['DEBUG_TRACE']) {
                echo "case 4-";
            }

            if($current['value']==$first['value']) {
                $first['time_stop']=$current['time_stop'];
                $tmp[]=$first;
                $tmp[]=$last;   
                if($GLOBALS['DEBUG_TRACE']) {
                    echo "1<br />";
                    echo "----------------<br />";
                    print_r($first);
                    echo "<br />";
                    print_r($last);
                    echo "<br />";
                    print_r($current);
                    echo "<br />";
                }
                return "0";
            } else if($current['value']==0) {
                if($current['time_start']==$first['time_start']) {
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "2<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else {
                    $first['time_stop']=$current['time_start'];
                    $tmp[]=$first;
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "3<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0"; 
                }
            } else {
                if($current['time_start']==$first['time_start']) {
                    $tmp[]=$current;
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "4<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else {
                    $first['time_stop']=$current['time_start'];
                    $tmp[]=$first;
                    $tmp[]=$current;
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "5<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                }        
            }
        } else if(($current['time_start']>=$first['time_stop'])&&($current['time_start']<$last['time_start'])&&($current['time_stop']>$last['time_start'])&&($current['time_stop']<=$last['time_stop'])) {
                //case 5: current value is betwwen the first and last value and stop in the last value
                if($GLOBALS['DEBUG_TRACE']) {
                    echo "case 5-";
                }

                if(($current['time_start']==$first['time_stop'])&&($current['time_stop']==$last['time_stop'])) {
                //if current touch the end of the first value and erase completely the last value
                    if($current['value']==0) {
                            $tmp[]=$first;
                            if($GLOBALS['DEBUG_TRACE']) {
                                echo "1<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                            }
                            return "0";
                    } else if(($current['value']==$last['value'])&&($current['value']==$first['value'])) {
                            $first['time_stop']=$last['time_stop'];
                            $tmp[]=$first;
                            if($GLOBALS['DEBUG_TRACE']) {
                                echo "2<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                            }
                            return "0";
                    } else if($current['value']==$first['value']) {
                            $first['time_stop']=$current['time_stop'];
                            $tmp[]=$first;
                            if($GLOBALS['DEBUG_TRACE']) {
                                echo "3<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                            }
                            return "0";
                    } else {
                            $tmp[]=$first;
                            $tmp[]=$current;
                            if($GLOBALS['DEBUG_TRACE']) {
                                echo "4<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                            }
                            return "0";
                    } 
                } else if(($current['time_start']>$first['time_stop'])&&($current['time_stop']==$last['time_stop'])) {
                    $tmp[]=$first;
                    if($current['value']==0) {
                        if($GLOBALS['DEBUG_TRACE']) {
                                echo "5<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                        }
                        return "0";
                    } else {
                        $tmp[]=$current;
                        if($GLOBALS['DEBUG_TRACE']) {
                                echo "6<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                        }
                        return "0";
                    } 
                } else if(($current['time_start']==$first['time_stop'])&&($current['time_stop']<$last['time_stop'])) {
                    if($current['value']==0) {
                        $last['time_start']=$current['time_stop'];
                        $tmp[]=$first;
                        $tmp[]=$last;
                        if($GLOBALS['DEBUG_TRACE']) {
                                echo "7<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                        }
                        return "0";
                    } else if(($current['value']==$last['value'])&&($current['value']==$first['value'])) {
                        $first['time_stop']=$last['time_stop'];
                        $tmp[]=$first;
                        if($GLOBALS['DEBUG_TRACE']) {
                                echo "8<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                        }
                        return "0";
                    } else if($current['value']==$first['value']) {
                        $first['time_stop']=$current['time_stop'];
                        $last['time_start']=$current['time_stop'];
                        $tmp[]=$first;
                        $tmp[]=$last;
                        if($GLOBALS['DEBUG_TRACE']) {
                                echo "9<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                        }
                        return "0";
                    } else if($current['value']==$last['value']) {
                        $last['time_start']=$current['time_start'];
                        $tmp[]=$first;
                        $tmp[]=$last;
                        if($GLOBALS['DEBUG_TRACE']) {
                                echo "10<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                        }
                        return "0";
                    } else {
                        $last['time_start']=$current['time_stop'];
                        $tmp[]=$first;
                        $tmp[]=$current;
                        $tmp[]=$last;
                        if($GLOBALS['DEBUG_TRACE']) {
                                echo "11<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                        }
                        return "0";
                    }
                } else {
                    $tmp[]=$first;
                    if($current['value']==0) {
                        $last['time_start']=$current['time_stop'];
                        $tmp[]=$last;
                        if($GLOBALS['DEBUG_TRACE']) {
                                echo "12<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                        }
                        return "0";
                    } else if($current['value']==$last['value']) {
                        $last['time_start']=$current['time_start'];
                        $tmp[]=$last;
                        if($GLOBALS['DEBUG_TRACE']) {
                                echo "13<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                        }
                        return "0";
                    } else {
                        $last['time_start']=$current['time_stop'];
                        $tmp[]=$current;
                        $tmp[]=$last;
                        if($GLOBALS['DEBUG_TRACE']) {
                                echo "14<br />";
                                echo "----------------<br />";
                                print_r($first);
                                echo "<br />";
                                print_r($last);
                                echo "<br />";
                                print_r($current);
                                echo "<br />";
                        }
                        return "0";
                    }
                }
        } else if(($current['time_start']>=$first['time_start'])&&($current['time_start']<$first['time_stop'])&&($current['time_stop']>$last['time_start'])&&($current['time_stop']<=$last['time_stop'])) {
            if($GLOBALS['DEBUG_TRACE']) {
                echo "case 6-";
            }
            //case 6: current value is in the first, between first and last and stop in the last value
            if(($current['time_start']==$first['time_start'])&&($current['time_stop']==$last['time_stop'])) {
                if($current['value']==0) {
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "1<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else {
                    $tmp[]=$current;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "2<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                }
            } else if($current['time_start']==$first['time_start']) {
                if($current['value']==0) {
                    $last['time_start']=$current['time_stop'];
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "3<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else if($current['value']==$last['value']) {
                    $current['time_stop']=$last['time_stop'];
                    $tmp[]=$current;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "4<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else {
                    $last['time_start']=$current['time_stop'];
                    $tmp[]=$current;
                    $tmp[]=$last;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "5<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                }
            } else if($current['time_stop']==$last['time_stop']) {
                if($current['value']==0) {
                    $first['time_stop']=$current['time_start'];
                    $tmp[]=$first;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "6<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else if($current['value']==$last['value']) {
                    $first['time_stop']=$current['time_start'];
                    $tmp[]=$first;
                    $tmp[]=$current;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "7<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else if($current['value']==$first['value']) {
                    $first['time_stop']=$current['time_stop'];
                    $tmp[]=$first;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "8<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";
                } else {
                    $first['time_stop']=$current['time_start'];
                    $tmp[]=$first;
                    $tmp[]=$current;
                    if($GLOBALS['DEBUG_TRACE']) {
                        echo "9<br />";
                        echo "----------------<br />";
                        print_r($first);
                        echo "<br />";
                        print_r($last);
                        echo "<br />";
                        print_r($current);
                        echo "<br />";
                    }
                    return "0";   
            }
        } else {
            if($current['value']==0) {
                $first['time_stop']=$current['time_start'];
                $last['time_start']=$current['time_stop'];
                $tmp[]=$first;
                $tmp[]=$last;
                if($GLOBALS['DEBUG_TRACE']) {
                    echo "10<br />";
                    echo "----------------<br />";
                    print_r($first);
                    echo "<br />";
                    print_r($last);
                    echo "<br />";
                    print_r($current);
                    echo "<br />";
                }
                return "0";
            } else if($current['value']==$last['value']) {
                $first['time_stop']=$current['time_start'];
                $last['time_start']=$current['time_start'];
                $tmp[]=$first;
                $tmp[]=$last;
                if($GLOBALS['DEBUG_TRACE']) {
                    echo "11<br />";
                    echo "----------------<br />";
                    print_r($first);
                    echo "<br />";
                    print_r($last);
                    echo "<br />";
                    print_r($current);
                    echo "<br />";
                }
                return "0";
            } else if($current['value']==$first['value']) {
                $first['time_stop']=$current['time_stop'];
                $last['time_start']=$current['time_stop'];
                $tmp[]=$first;
                $tmp[]=$last;
                if($GLOBALS['DEBUG_TRACE']) {
                    echo "12<br />";
                    echo "----------------<br />";
                    print_r($first);
                    echo "<br />";
                    print_r($last);
                    echo "<br />";
                    print_r($current);
                    echo "<br />";
                }
                return "0";
            } else {
                $first['time_stop']=$current['time_start']; 
                $last['time_start']=$current['time_stop'];
                $tmp[]=$first;
                $tmp[]=$current;
                $tmp[]=$last;
                if($GLOBALS['DEBUG_TRACE']) {
                    echo "13<br />";
                    echo "----------------<br />";
                    print_r($first);
                    echo "<br />";
                    print_r($last);
                    echo "<br />";
                    print_r($current);
                    echo "<br />";
                }
                return "0";
            }
        } 
    } else if(($current['time_start']>=$first['time_start'])&&($current['time_stop']>$last['time_stop'])&&($current['time_start']<$last['time_stop'])&&($current['time_start']<$last['time_start'])) {
        if($GLOBALS['DEBUG_TRACE']) {
            echo "special case: <br />";
            echo "--------------------<br />";
        }
        $tmp_current=$current;
        $tmp_current['time_stop']=$last['time_stop'];
        $continue=compare_data_program($first,$last,$tmp_current,$tmp);
        $current['time_start']=$last['time_stop'];
        return "2";      
    } else {
      $tmp[]=$first;
      return "1";
  }
}
//}}}


// {{{ optimize_program()
// ROLE optimize a program by deleting useless value
// IN $arr      array containing value of the program
// RET the array opzimized 
function optimize_program($arr) {
   $tmp=array();
   asort($arr);
   if(count($arr)>1) {
      $jump=false;
      while(!$jump) {
         $jump=true;
         $i=0;
         while(array_key_exists($i+1,$arr)) {
            if(($arr[$i+1]['time_start']<=$arr[$i]['time_stop'])&&($arr[$i+1]['value']==$arr[$i]['value'])) {
               $val=$arr[$i];
               $val['time_stop']=$arr[$i+1]['time_stop'];
               $tmp[]=$val;
               $jump=false;
               $i=$i+2;   
            } else {
               if(array_key_exists($i,$arr)) {
                    $tmp[]=$arr[$i];
               }
               $i=$i+1;
            }
         }
        
         if(array_key_exists($i,$arr)) {
            $tmp[]=$arr[$i];
         }
         if(!$jump) {
            $arr=$tmp;
            unset($tmp);
         }
      }
      return $tmp;
   }
        return $arr;
}
// }}}


/// {{{ create_plugconf_from_database()
// 
// 1 fichier par prise : plugnn ou n est le numéro de la prise. Exemple plug01 ou encore plug14
// 
// Dans ce fichier:
// Première ligne: REG:{T|H|N}{+|-}{PRECISION}0x0D0A
// {T|H|N} : doit être T pour température ou H pour humidité ou N pour null. En cas de null, les valeur suivante sont rempli au pif.
// {+|-} : doit être + Si l'effecteur doit se mettre en route en dessus de la consigne ou - si le contraire
// {PRECISION} : Valeur de précision X 10 sur 3 digits
// 0x0D0A : caractère de fin de ligne (CR LF, \r\n)
// Exemple "REG:T+020" : L'effecteur se met en route si la température dépasse la consigne + 2,0°C. (2,0°C --> 020)
// 
// Deuxième ligne SEC:{T|H|N}{+|-}{1|0}{VALEUR}0x0D0A
// Cette ligne permet de définir une deuxième capteur permettant la marche forcé ou larrêt forcé de l'effecteur.
// {T|H|N} : doit être T pour température ou H pour humidité ou N pour null. En cas de null, les valeur suivante sont rempli au pif.
// {+|-} : doit être + Si l'effecteur doit se mettre en {ON|OFF} en dessus de la consigne ou - si le contraire
// {1|0} : Est_ce que l'effecteur doit être ON (1) ou off (0) lorsque les conditions sont remplises.
// {VALEUR} : Valeur X 10 sur 3 digits
// 0x0D0A : caractère de fin de ligne (CR LF, \r\n)
// Exmple "SEC:H+1800" : L'effecteur doit être On (1) si l'humidité (H) devient supérieur (+) à 80,0% RH (800).
// 
// Troisième ligne SEN:{CALCUL}{CAPTEUR_1}{CAPTEUR_2}{CAPTEUR_3}{CAPTEUR_4}0x0D0A
// Cette ligne indique le(s) capteur(s) à utiliser pour effectuer la régulation
// {CALCUL} : Valeur sur 1 digit Indique le mode de calcul entre les différents capteurs:
// M : Moyenne des capteurs (par défaut)
// I : Minimum des capteurs
// A : Maximum des capteurs
// {CAPTEUR_X} Indique si le capteur doit être utilisé (1) pour le calcul ou non (0)
// Exemple SEN:M0101 : La régulation est effectué sur la moyenne du capteur 2 et 4
// 
// Quatrième ligne STOL:{VALEUR}0x0D0A
// Cette ligne indique la tolérance à utiliser pour la régulation secondaire
// {VALEUR} : Valeur sur 3 digits la tolérance X 10
// Exemple STOL:025 : La régulation secondaire aura une tolérance de 2.5°C ou 2.5%RH
//
// ROLE read plugs configuration from the database and format its to be write into a sd card
// IN $nb   the number of plug to read
//    $out   error or warning message
// RET an array containing datas
function create_plugconf_from_database($nb=0,&$out) {

   $second_regul=get_configuration("ADVANCED_REGUL_OPTIONS",$out);
   
   if($nb>0) {
   
        $sql = "SELECT * FROM plugs WHERE id <= '{$nb}';" ;
      
       $db=db_priv_pdo_start();
       try {
           $sth=$db->prepare("$sql");
           $sth->execute();
           $res=$sth->fetchAll(PDO::FETCH_ASSOC);
       } catch(PDOException $e) {
           $ret=$e->getMessage();
       }
       $db=null;
      if((isset($ret))&&(!empty($ret))) {
          if(($GLOBALS['DEBUG_TRACE'])) {
                  $out[]=__('ERROR_SELECT_SQL').$ret;
            } else {
                  $out[]=__('ERROR_SELECT_SQL');
            }
      }
      
      if(count($res)>0) {
         $arr=array();
         foreach($res as $data) {
            $sens="";
            if($data['PLUG_TOLERANCE']) {
               $tol=$data['PLUG_TOLERANCE']*10;
               while(strlen($tol)<3) {
                  $tol="0$tol";
               }
            } else {
               $tol="000";
            }

            //Main regulation:
            switch ($data['PLUG_TYPE']) {
                case "extractor":
                case "intractor":
                case "ventilator":
                    $reg="REG:T+${tol}";
                    break;
                case "heating":
                    $reg="REG:T-${tol}";
                    break;
                case "pumpfiling":
                    $reg="REG:L-${tol}";
                    break;
                case "pumpempting":
                    $reg="REG:L+${tol}";
                    break;
                case "pump":
                    $reg="REG:T-${tol}";
                    break;
                case "humidifier":
                    "REG:H-${tol}";
                    break;
                case "dehumidifier":
                    $reg="REG:H+${tol}";
                    break;
                default:
                    $reg="REG:N+000";
                    break;
            }

            // Second regulation:
            if($second_regul == "True") {
                //Default second regulation value:
                switch ($data['PLUG_TYPE']) {
                    case "extractor":
                    case "intractor":
                    case "ventilator":
                        $sec="SEC:T+1000";
                        break;
                    case "heating":
                        $sec="SEC:N-1000";
                        break;
                    case "dehumidifier":
                        $sec="SEC:N+1000";
                        break;
                    case "humidifier":
                    case "pumpfiling":
                    case "pumpempting":
                    case "pump":
                    default:
                        $sec="SEC:N-1000";
                        break;
                }

                //User second regulation value:
                if(strcmp($data['PLUG_REGUL'],"False")==0) {
                    $sec="SEC:N+0000";
                } else {
                    $sec="SEC:".$data['PLUG_SENSO'].$data['PLUG_SENSS'];
                    $val=$data['PLUG_REGUL_VALUE']*10;
                    while(strlen($val)<3) {
                        $val="0$val";
                    }
                    $sec=$sec."1$val";
                }
            } else {
                $sec="SEC:N+0000";
            }

            if(strcmp($data['PLUG_REGUL_SENSOR'],"")!=0) {  
                $vsen="";
                $tmp_sensor=explode("-",$data['PLUG_REGUL_SENSOR']);
                $find=false;
                for($i=1;$i<=$GLOBALS['NB_MAX_SENSOR_PLUG'];$i++) {
                    foreach($tmp_sensor as $sensor) {
                        if($sensor==$i) {
                            $vsen=$vsen."1";
                            $find=true;
                            break;
                        }
                    }

                    if(!$find) {
                        $vsen=$vsen."0";
                    } else {
                        $find=false;
                    }
                } 
             } else {
                $vsen="1000";
             }

             if(strcmp($data['PLUG_COMPUTE_METHOD'],"")!=0) {
                $sens="SEN:".$data['PLUG_COMPUTE_METHOD'].$vsen;
             } else {
                $sens="SEN:M".$vsen;
            }

            $sereg=$data['PLUG_SECOND_TOLERANCE']*10;
            while(strlen($sereg)<3) {
                $sereg="0".$sereg;
            }
            $sec_regul="STOL:$sereg";

            $arr[]="$reg"."\r\n"."$sec"."\r\n"."$sens"."\r\n"."$sec_regul";
         }
         return $arr;
      }
    }
}

// }}}



/// {{{ create_program_from_database()
// ROLE read programs from the database and format its to be writen into a sd card
// IN $out        error or warning message
// RET an array containing datas
function create_program_from_database(&$out,$fieldNumber = 1) {

    date_default_timezone_set('UTC');

    // Read the number of plugs
    $nb_plugs=get_configuration("NB_PLUGS",$out);
    
    // Get programs for plugs 
   $sql = "SELECT * FROM programs WHERE plug_id IN (SELECT id FROM plugs WHERE id <= " . $nb_plugs . ") AND number = '" . $fieldNumber . "' ORDER BY time_start ASC;";
  
   $db=db_priv_pdo_start();
   try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);
   } catch(PDOException $e) {
        $ret=$e->getMessage();
       
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
        
        unset($ret);
   }

   
   // Select first element of program
   $sql = "SELECT * FROM programs WHERE time_start = '000000' AND plug_id IN (SELECT id FROM plugs WHERE id <= " . $nb_plugs . ") AND number = '" . $fieldNumber . "' ORDER BY time_start ASC;";

    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $first=$sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
       
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
        
        unset($ret);
    }

    // Select last element of program
   $sql = "SELECT * FROM programs WHERE time_stop = '235959' AND plug_id IN (SELECT id FROM plugs WHERE id <= " . $nb_plugs . ") AND number = '" . $fieldNumber . "' ORDER by time_start ASC;";

    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $last=$sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
       
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
        
        unset($ret);
    }
   
   $j=1;
   $data=array();
   $data[0] = "";
   date_default_timezone_set('UTC');

   if(count($first)>0) {
      while( $j <= $GLOBALS['NB_MAX_PLUG'] ) {
         if($j>$nb_plugs) {
            $result="000";
         } else {
            $result=find_value_for_plug($first,"000000",$j);
         }
         $data[0]=$data[0]."$result";
         $j=$j+1;
      }
      $data[0]="00000".$data[0];
   } else {
      $data[0]="00000000000000000000000000000000000000000000000000000";
   }

    $event=array();
    foreach($res as $result) {
        if($result['time_start']!="000000") {
            $event[]=$result['time_start'];
        }
        if($result['time_stop']!="235959") {
            $event[]=$result['time_stop'];
        }
    }
    if(count($event)>0) {
        $event = array_unique ($event);
        sort($event);
    }

    $evt=array();
    $i=0;
    $count=0;

    if(count($event)>0) {
        while($count<count($event)) {
            if((isset($event[$i]))) {
                $evt[]=$event[$i];   
                $count=$count+1;
            }
            $i=$i+1;
        }
        $event=$evt;
    }
	
	$plg=array();
	for($i=1;$i<= $nb_plugs;$i++) {
        $sql = "SELECT * FROM programs WHERE plug_id = " . $i . " AND number = '" . $fieldNumber . "' ORDER BY time_start ASC;";

		try {
			$sth=$db->prepare($sql);
			$sth->execute();
			$plg[$i]=$sth->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			$plg[$i]=$e->getMessage();
       
			if($GLOBALS['DEBUG_TRACE']) {
				$out[]=__('ERROR_SELECT_SQL').$ret;
			} else {
				$out[]=__('ERROR_SELECT_SQL');
			}
        
			unset($ret);
		}
   }
      
   if(count($event)>0) {
        for($i=0;$i<count($event);$i++) {
            $data[$i+1] = "";
            $j=1;
            while($j<= $GLOBALS['NB_MAX_PLUG']) {
                if($j>$nb_plugs) {
                    $result="000";
                } else {
                    $result=find_value_for_plug($plg[$j],$event[$i],$j);
				}

                $data[$i+1]=$data[$i+1]."$result";
                $j=$j+1;
            }
         
            $ehh=substr($event[$i],0,2);
            $emm=substr($event[$i],2,2);
            $ess=substr($event[$i],4,2);
            $time_event=mktime($ehh,$emm,$ess,1,1,1970);
            while(strlen($time_event)!=5) {
                $time_event="0$time_event";
            }
            $data[$i+1]=$time_event.$data[$i+1];
        }
    }
	
    $count=count($data);
    $j=1;
	
    if(count($last)>0) {
        while($j<= $GLOBALS['NB_MAX_PLUG']) {
            if($j>$nb_plugs) {
                $result="000";
            } else {
                $result=find_value_for_plug($last,"235959",$j);
            }

            if(isset($data[$count])) {
                $data[$count]=$data[$count]."$result";
            } else {
                $data[$count]="$result";
            }
            $j=$j+1;
        }
        $data[$count]="86399".$data[$count];
    } else {
        $data[$count]="86399000000000000000000000000000000000000000000000000";
    }
	
    return $data;
	
}
// }}}

// {{{ find_value_for_plug()
//ROLE find if a plug is concerned by a time spaces and return its value
// IN $data       array to look for time space
//    $time       the time to find
//    $plug        the specific plug concerned
// RET   000 if the plug is not concerned or if its value is 0, 0001 else
function find_value_for_plug($data,$time,$plug) {
    for($i=0;$i<count($data);$i++) {
        //data must be ordered by time_start ASC
        if($data[$i]['time_start']>$time) {
            $ret="000";
            return $ret;
        } 

        if(($data[$i]['time_start']<=$time)&&($data[$i]['time_stop']>=$time)&&($data[$i]['plug_id']==$plug)) {
            if($data[$i]['time_stop']==$time) {
                if($data[$i]['time_stop']=="235959") {
                    $ret=$data[$i]['value']*10;
                } else {
                    for($j=0;$j<count($data);$j++) {
                        if(($data[$j]["time_start"]=="$time")&&($data[$j]['plug_id']==$plug)) {
                            $ret=$data[$j]['value']*10;
                        } 
                    }
                    if(empty($ret)) {
                        $ret="000";
                    }   
                }
            } else {
                $ret=$data[$i]['value']*10;
            }
            while(strlen($ret)<3) {
                $ret="0$ret";
            }
            return "$ret";
        }
    }
    return "000";
}
// }}}


// {{{ check_programs()
//ROLE check if a program have been defined yet
// IN  $nb_plugs        number of plugs used
//     $id_plug         id of a plug to check: -1: all
// RET true if there is a program defined, false else
function check_programs($nb_plugs=3,$id=-1) {

    if($id==-1) {
        $sql = "SELECT * FROM programs WHERE plug_id <= {$nb_plugs} LIMIT 1;";
    } else {
        $sql = "SELECT * FROM programs WHERE plug_id = {$id} LIMIT 1;";
    }

    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if(!isset($res) || empty($res)) {
        return false;
    } else {
        if(count($res)>0) {
            return true;
        } else {
            return false;
        }
    }
}
// }}}


// {{{ format_regul_sumary()
// ROLE format regulation of a plug to be displayed in a summary
// IN    $number     id of the plug
//       $out        error or warning messages
// RET   summary formated 
function format_regul_sumary($number=0, &$out) {

    $sql = "SELECT id, PLUG_SENSO, PLUG_SENSS, PLUG_REGUL_VALUE"
                . " FROM plugs WHERE PLUG_REGUL LIKE 'True'"
                . " AND id = '{$number}' ;";

    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $result=$sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;
    $resume="";

    if((isset($ret))&&(!empty($ret))) {
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
    }

    $unity="";
    if(count($result)==1) {
        $resume=__('SUMARY_REGUL_SUBTITLE').": <b>".__('SECOND_REGUL_ON')."</b>.<br />".__('SUMARY_REGUL_SENSO')." ";
        if($result[0]['PLUG_SENSO'] == "H") {
            $resume=$resume."<b>".__('HUMI_REGUL');
            $unity="%";
        } else {
            $resume=$resume."<b>".__('TEMP_REGUL');
            $unity="°C";
        }

        if($result[0]['PLUG_SENSS'] == "+") {
            $resume=$resume." > ".$result[0]['PLUG_REGUL_VALUE'].$unity."</b>";
        } else {
            $resume=$resume." < ".$result[0]['PLUG_REGUL_VALUE'].$unity."</b>";
        }
    } else {
        $resume=__('SUMARY_REGUL_SUBTITLE').": <b>".__('SECOND_REGUL_OFF')."</b>";
    }

    return $resume;
}
// }}}

// {{{ get_cost_summary()
// ROLE format cost configuration informations be displayed in a sumary
// IN   out     error or warning messages
// RET  sumary formated 
function get_cost_summary(&$out) {
    $resume="";

    $sql = "SELECT COST_PRICE, COST_PRICE_HP, COST_PRICE_HC, START_TIME_HC, STOP_TIME_HC, COST_TYPE FROM configuration;";

    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
    }

   foreach($res as $result) {
        $resume="<p align='center'><b><i>".__('SUMARY_COST_TITLE').":<br /></i></b></p>";
        if(isset($_COOKIE['LANG'])) {
            if((strcmp($_COOKIE['LANG'],"fr_FR")==0)||(strcmp($_COOKIE['LANG'],"de_DE"))||(strcmp($_COOKIE['LANG'],"es_ES"))||(strcmp($_COOKIE['LANG'],"it_IT"))) {
                $unity="&euro;";
            } else {
                $unity="&#163;";
            }
        } else {
            $unity="&#163;";
        }
        if(strcmp($result['COST_TYPE'],"standard")==0) {
            $resume=$resume."<br /><b>".__('SUMARY_COST_TYPE').":</b> ".$result['COST_TYPE'];
            $resume=$resume."<br /><b>".__('SUMARY_COST_PRICE').":</b> ".$result['COST_PRICE'].$unity;
        } else {
            $resume=$resume."<br /><b>".__('SUMARY_COST_TYPE').":</b> ".__('SUMARY_HP_HC');
            $resume=$resume."<br /><b>".__('SUMARY_COST_PRICE_HC').":</b> ".$result['COST_PRICE_HC'].$unity;
            $resume=$resume."<br /><b>".__('SUMARY_COST_PRICE_HP').":</b> ".$result['COST_PRICE_HP'].$unity;
            $resume=$resume."<br /><b>".__('SUMARY_START_HC').":</b> ".$result['START_TIME_HC'];
            $resume=$resume."<br /><b>".__('SUMARY_STOP_HC').":</b> ".$result['STOP_TIME_HC'];
        }
    }

    if(strlen($resume)>0) return $resume;
    return "<p align='center'><b><i>".__('SUMARY_COST_TITLE').":<br /></i></b></p><p align='center'>".__('EMPTY_COST_INFOS')."</p>"; 
}
// }}}


// {{{ check_configuration_power()
// ROLE check that power of a plug is configured
// IN  $id     id of the plug to be checked
// OUT false if a plug is not configured, true else
function check_configuration_power($id=0) {
    
    $sql = "SELECT PLUG_POWER FROM plugs WHERE id = {$id};";
      
    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res_power=$sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_SELECT_SQL').$ret;
        } else {
            $out[]=__('ERROR_SELECT_SQL');
        }
        return 0;
    }

    if(count($res_power)>0) {
        if(strcmp($res_power[0]['PLUG_POWER'],"")==0) {
            return false;
        }
    }
    return true;
}
// }}}


// {{{ get_notes()
// ROLE get welcome's notes depending the lang
// IN $out      error or warning message
//    $res      return array containing data
//    $lang     lang of the current note to search
// RET none 
function get_notes(&$res,$lang="fr_FR",&$out) {

    $sql = "SELECT * from notes WHERE lang LIKE '{$lang}' ORDER by id;";

    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_DELETE_SQL').$ret;
        } else {
            $out[]=__('ERROR_DELETE_SQL');
        }
    }

    for($i=0;$i<count($res);$i++) {
        $res[$i]['title']=htmlentities($res[$i]['title'], ENT_NOQUOTES, "UTF-8");
        $res[$i]['title']=htmlspecialchars_decode($res[$i]['title']);
        $res[$i]['desc']=htmlentities($res[$i]['desc'], ENT_NOQUOTES, "UTF-8");
        $res[$i]['desc']=htmlspecialchars_decode($res[$i]['desc']);
    }
}
// }}}


// {{{ get_plug_regul_sensor()
// ROLE get plug sensors
// IN $out      error or warning message
//    $id       id of the plug
// RET return array containing data
function get_plug_regul_sensor($id,&$out) {

    $sql = "SELECT PLUG_REGUL_SENSOR FROM plugs WHERE id = {$id};";
    
    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
        if($GLOBALS['DEBUG_TRACE']) {
            $out[]=__('ERROR_DELETE_SQL').$ret;
        } else {
            $out[]=__('ERROR_DELETE_SQL');
        }
    }

    if(!empty($res)) {
        $tmp=explode("-",$res[0]['PLUG_REGUL_SENSOR']);
        $result=array();                                           
        for($i=1;$i<=$GLOBALS['NB_MAX_SENSOR_PLUG'];$i++) {
            foreach($tmp as $sensor) {
                if($sensor==$i) {
                    $result[$i]="True";
                }
            }
            if(!isset($result[$i])) {
                $result[$i]="False";
            }
        }
        return $result; 
    } else {
        return false;
    }
}
// }}}


// {{{ get_canal_status()
// ROLE get status of dimmer canal to get available canal to be used by new dimmer's configuration
// IN $out   error or warning message
// RET array containing number of the dimmer canal and its status (USED: 0, AVAILABLE: 1)
function get_canal_status(&$out) {

    $sql = "SELECT PLUG_POWER_MAX FROM plugs WHERE PLUG_POWER_MAX<10 ORDER BY PLUG_POWER_MAX ASC;";

    $db=db_priv_pdo_start();
    $res="";
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;
    $status=array();
    $value=array();

    foreach($res as $result) {    
        $value[]=$result['PLUG_POWER_MAX']; 
    }

    for($i=1;$i<=$GLOBALS['NB_MAX_CANAL_DIMMER'];$i++) {
        if(in_array($i, $value)) {
            $status[]=0;
        } else {
            $status[]=1;
        }
    }

    return $status;
}
/// }}}


// {{{ get_nb_daily_program()
// ROLE get number of daily programs recorded
// IN $out   error or warning message
// RET number of daily programs recorded
function get_nb_daily_program(&$out) {
    $sql = "SELECT count(id) as nb_daily FROM program_index WHERE id > 1;";

    $db=db_priv_pdo_start();
    $res="";
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res=$sth->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;
    return $res['nb_daily'];
}
/// }}}
?>



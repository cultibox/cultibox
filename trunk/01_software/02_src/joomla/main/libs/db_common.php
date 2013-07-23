<?php

// {{{ db_priv_pdo_start()
// ROLE create a connection to the DB using PDO
// IN none 
// RET database connection object
function db_priv_pdo_start() {
    try {
        $db = new PDO('mysql:host=127.0.0.1;port=3891;dbname=cultibox;charset=utf8', 'cultibox', 'cultibox');
        $db->exec("SET CHARACTER SET utf8");
        return $db;
    } catch (PDOException $e) {
        return 0;
    }
}

function db_priv_pdo_start_joomla() {
    try {
        $db = new PDO('mysql:host=127.0.0.1;port=3891;dbname=cultibox_joomla;charset=utf8', 'root', 'cultibox');
        $db->exec("SET CHARACTER SET utf8");
        return $db;
    } catch (PDOException $e) {
        return 0;
    }
}



// {{{ db_update_logs()
// ROLE update logs table in the Database with the array $arr
// IN $arr   array containing values to update database
//    $out   error or warning message 
// RET none
function db_update_logs($arr,&$out) {
   $index=0;
   $return=1;
   $sql = <<<EOF
INSERT INTO `logs`(`timestamp`,`temperature`, `humidity`,`date_catch`,`time_catch`,`sensor_nb`) VALUES
EOF;
   foreach($arr as $value) {
      if(empty($value['temperature'])) {
        $value['temperature']="NULL";
      }
    
      if(empty($value['humidity'])) {
        $value['humidity']="NULL";
      }

      if((array_key_exists("timestamp", $value))&&(array_key_exists("temperature", $value))&&(array_key_exists("humidity", $value))&&(array_key_exists("date_catch", $value))&&(array_key_exists("time_catch", $value))&&(array_key_exists("sensor_nb", $value))) {
         if("$index" == "0") {
            $sql = $sql . "(${value['timestamp']}, ${value['temperature']},${value['humidity']},\"${value['date_catch']}\",\"${value['time_catch']}\",\"${value['sensor_nb']}\")";
         } else {
            $sql = $sql . ",(${value['timestamp']}, ${value['temperature']},${value['humidity']},\"${value['date_catch']}\",\"${value['time_catch']}\",\"${value['sensor_nb']}\")";
         }
         $index = $index +1;
      }
   }

   $db=db_priv_pdo_start();
   try {
        $db->exec("$sql");
   } catch(PDOException $e) {
        $ret=$e->getMessage();     
   }
   $db=null;

   if((isset($ret))&&(!empty($ret))) {
      if($GLOBALS['DEBUG_TRACE']) {
         $out[]=__('ERROR_UPDATE_SQL').$ret;
      } else {
         $out[]=__('ERROR_UPDATE_SQL');
      }
      $return=0; 
   }
   return $return;
}
// }}}


// {{{ db_update_power()
// ROLE update power table in the Database with the array $arr
// IN $arr   array containing values to update database
//    $out   error or warning message 
// RET none
function db_update_power($arr,&$out) {
   $index=0;
   $return=1;
   $sql = <<<EOF
INSERT INTO `power`(`timestamp`,`plug_number`,`record`, `date_catch`,`time_catch`) VALUES
EOF;
   foreach($arr as $value) {
      if((array_key_exists("timestamp", $value))&&(array_key_exists("plug_number", $value))&&(array_key_exists("power", $value))&&(array_key_exists("date_catch", $value))&&(array_key_exists("time_catch", $value))) {
         if("$index" == "0") {
            $sql = $sql . "(${value['timestamp']}, ${value['plug_number']},${value['power']},\"${value['date_catch']}\",\"${value['time_catch']}\")";
         } else {
            $sql = $sql . ",(${value['timestamp']}, ${value['plug_number']},${value['power']},\"${value['date_catch']}\",\"${value['time_catch']}\")";
         }
         $index = $index +1;
      }
   }

   $db=db_priv_pdo_start();
   try {
        $db->exec("$sql");
   } catch(PDOException $e) {
        $ret=$e->getMessage();  
   }
   $db=null;

   if((isset($ret))&&(!empty($ret))) {
      if($GLOBALS['DEBUG_TRACE']) {
         $out[]=__('ERROR_UPDATE_SQL').$ret;
      } else {
         $out[]=__('ERROR_UPDATE_SQL');
      }
      $return=0;
   }
   return $return;
}
// }}}


// {{{ get_graph_array()
// ROLE get array needed to build graphics
// IN $res         the array containing datas needed for the graphics
//    $key      the key selectable from the database (temperature,humidity...)
//    $startdate   date (format YYYY-MM-DD) to check what datas to select
//    $sensor       the number of the sensor to be displayed
//    $fake   to select fake or real logs
//    $limit    limit number of row return from sql request
//    $out      errors or warnings messages
// RET none
function get_graph_array(&$res,$key,$startdate,$sensor=1,$fake="False",$limit=0,&$out) {
   $startdate=str_replace("-","",$startdate);
   $startdate=substr($startdate,2,8);

   if($limit!=0) {
        $sql_limit="LIMIT ".$limit;
   } else {
        $sql_limit="";
   }
    
   if(strcmp("$sensor","all")==0) {
        $sql = <<<EOF
SELECT ${key} as record,time_catch FROM `logs` WHERE timestamp LIKE "{$startdate}%" AND fake_log LIKE "{$fake}" GROUP BY timestamp ORDER BY time_catch ASC {$sql_limit}
EOF;
} else {
        $sql = <<<EOF
SELECT ${key} as record,time_catch FROM `logs` WHERE timestamp LIKE "{$startdate}%" AND fake_log LIKE "{$fake}" AND sensor_nb LIKE "{$sensor}" GROUP BY timestamp ORDER BY time_catch ASC {$sql_limit}
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
   }
}
// }}}


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

// {{{ get_informations()
// ROLE get informations value for specific entries
// IN $key   the key selectable from the database 
// RET $res   value of the key   
function get_informations($key) {
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


// {{{ insert_configuration()
// ROLE set configuration value for specific entries
// IN $key      the key selectable from the database 
//    $value   value of the key to insert
//    $out      errors or warnings messages
// RET none
//Note: if to select a value is limited to 1. Only one configuration is available,
//there isn't a user configuration management yet.
function insert_configuration($key,$value,&$out) {
   $sql = <<<EOF
UPDATE `configuration` SET  {$key} = "{$value}" WHERE id = 1
EOF;
   $db=db_priv_pdo_start();
   try {
        $db->exec("$sql");
   } catch(PDOException $e) {
        $ret=$e->getMessage();
   }
   $db=null;

   if((isset($ret))&&(!empty($ret))) {
      if($GLOBALS['DEBUG_TRACE']) {
         $out[]=__('ERROR_UPDATE_SQL').$ret;
      } else {
         $out[]=__('ERROR_UPDATE_SQL');
      }
   }
}
// }}}


// {{{ insert_informations()
// ROLE set informations value for specific entries
// IN $key      the key selectable from the database 
//    $value   value of the key to insert
// RET none
//Note: if to select a value is limited to 1. Only one configuration is available,
//there isn't a user configuration management yet.
function insert_informations($key,$value) {
   $sql = <<<EOF
UPDATE `informations` SET  {$key} = "{$value}" WHERE id = 1
EOF;
   $db=db_priv_pdo_start();
   try {
        $db->exec("$sql");
   } catch(PDOException $e) {
        $ret=$e->getMessage();
   }
   $db=null;
}
// }}}


// {{{ get_plug_conf()
// ROLE get plug configuration value for specific entries
// IN $key      the key selectable from the database 
//    $id   id of the plug
//    $out      errors or warnings messages
// RET $res   value result for the plug configuration entrie
function get_plug_conf($key,$id,&$out) {
   $sql = <<<EOF
SELECT {$key} FROM `plugs` WHERE id = {$id}
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


// {{{ insert_plug_conf()
// ROLE set plug configuration value for specific entries
// IN $key      the key selectable from the database 
//    $id       id of the plug
//    $value   value of the configuration field to update
//    $out      errors or warnings messages
// RET none
function insert_plug_conf($key,$id,$value,&$out) {
    if(strcmp("$value","")!=0) {
   $sql = <<<EOF
UPDATE `plugs` SET  {$key} = "{$value}" WHERE id = {$id}
EOF;
    } else {
$sql = <<<EOF
UPDATE `plugs` SET  {$key} = NULL WHERE id = {$id}
EOF;
    }
   $db=db_priv_pdo_start();
   try {
        $db->exec("$sql");
   } catch(PDOException $e) {
        $ret=$e->getMessage();
   }
   $db=null;

   if((isset($ret))&&(!empty($ret))) {
      if($GLOBALS['DEBUG_TRACE']) {
         $out[]=__('ERROR_UPDATE_SQL').$ret;
      } else {
         $out[]=__('ERROR_UPDATE_SQL');
      }
   }
}
// }}}


// {{{ get_plugs_infos()
// ROLE get plugs informations (name,id,type)
// IN $id      id of the plug
//    $out      errors or warnings messages
// RET return an array containing plugid and its name
function get_plugs_infos($nb=0,&$out) {
        $sql = <<<EOF
SELECT `id` , `PLUG_NAME`,`PLUG_TYPE`,`PLUG_REGUL`, `PLUG_ENABLED`
FROM `plugs`
WHERE id <= {$nb}
ORDER by id ASC
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
       }
       return $res;
}
// }}}


// {{{ get_data_plug()
// ROLE get a specific plug program
// IN $selected_plug   plug id to select
//    $out      errors or warnings messages
// RET plug data formated for highchart
function get_data_plug($selected_plug="",&$out) {
   $res="";
   if((isset($selected_plug))&&(!empty($selected_plug))) {
      $sql = <<<EOF
SELECT  `time_start`,`time_stop`,`value` FROM `programs` WHERE plug_id = {$selected_plug} ORDER by time_start ASC
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
//    $out      errors or warnings messages
// RET data power formated for highchart
function get_data_power($date="",$dateend="",$id=0,&$out) {
   $res="";
   $nb_plugs=get_configuration("NB_PLUGS",$out);
   $date=str_replace("-","",$date);
   $date=substr($date,2,8);
   

   if((isset($date))&&(!empty($date))) {
      if(strcmp("$id","all")==0) {
         if((!isset($dateend))||(empty($dateend))) {
            $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp LIKE "{$date}%" AND `plug_number` IN (SELECT `id` FROM `plugs` WHERE `PLUG_ENABLED` LIKE "True") ORDER by timestamp ASC, plug_number ASC
EOF;
         } else {
         $date=$date."00000000";
         $dateend=str_replace("-","",$dateend);
         $dateend=substr($dateend,2,8);
         $dateend=$dateend."99999999";
         
      $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp BETWEEN  "{$date}" AND "{$dateend}" AND `plug_number` IN (SELECT `id` FROM `plugs` WHERE `PLUG_ENABLED` LIKE "True")
EOF;
}
      } else {
         if((!isset($dateend))||(empty($dateend))) {
            $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp LIKE "{$date}%" AND `plug_number` = "{$id}" ORDER by timestamp ASC, plug_number ASC
EOF;
      } else {
            $date=$date."00000000";
            $dateend=str_replace("-","",$dateend);
            $dateend=substr($dateend,2,8);
            $dateend=$dateend."99999999";
            $sql = <<<EOF
SELECT  * FROM `power` WHERE timestamp BETWEEN  "{$date}" AND "{$dateend}" AND `plug_number` = "{$id}" 
EOF;
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


        if(strcmp("$id","all")!=0) {
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
            $out[]=__('ERROR_HPC_TIME_NULL')." <a href='plugs-".$_SESSION['SHORTLANG']."'>".__('HERE')."</a>";  
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
SELECT * FROM `programs` WHERE `plug_id` > 0 AND `plug_id` <= ${nb_plugs} AND `plug_id` IN (SELECT `id` FROM `plugs` WHERE `PLUG_ENABLED` LIKE "True") 
EOF;
      } else {
      $sql = <<<EOF
SELECT * FROM `programs` WHERE `plug_id` = "{$id}" AND `plug_id` IN (SELECT `id` FROM `plugs` WHERE `PLUG_ENABLED` LIKE "True")
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
SELECT `PLUG_POWER`,`PLUG_ENABLED` FROM `plugs` WHERE `id` <= ${nb_plugs}
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

               if(($res_power[$id]['PLUG_POWER']==0)&&(strcmp($res_power[$id]['PLUG_ENABLED'],"True")==0)) {
                     $error=1;
               }

               if(strcmp($res_power[$id]['PLUG_ENABLED'],"True")==0) {
                    $enable=1;
               } else {
                    $enable=0;
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

            $starthc=mktime($stahch,$stahcm,0,0,0,1971);
            $stophc=mktime($stohch,$stohcm,0,0,0,1971);

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

               if($starthc<=$stophc) {
                   if(($time>=$starthc)&&($time<=$stophc)) {
                          $price=$price_hc;
                   } else {
                          $price=$price_hp;
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


// {{{ insert_program()
// ROLE check and create new plug program
// IN $program     array containing program data 
//    $out      error or warning message
// RET true
function insert_program($program,&$out) {
   $ret=true;
   $data_plug=get_data_plug($program[0]['selected_plug'],$out);
   if(count($program>0)) clean_program($program[0]['selected_plug'],$out);
   $final=array();

   $first=array(
        "time_start" => "000000",
        "time_stop" => "000000",
        "value" => "0"
   );

   $data_plug[] = array(
        "time_start" => "240000",
        "time_stop" => "240000",
        "value" => "0"
   );

   $data_plug[] = array(
        "time_start" => "end",
        "time_stop" => "end",
        "value" => "end"
   );

   $data_plug[] = array(
        "time_start" => "end",
        "time_stop" => "end",
        "value" => "end"
   );


   $tmp=array();
   foreach($program as $prog) {
        asort($data_plug);
        $start_time=str_replace(':','',$prog['start_time']);
        $end_time=str_replace(':','',$prog['end_time']);
        $value=$prog['value_program'];
        $current= array(
            "time_start" => "$start_time",
            "time_stop" => "$end_time",
            "value" => "$value"
        );

        $continue="1";
        if(count($data_plug)>1) {
            foreach($data_plug as $data) {   
                if("$continue"!="3") {
                    if((empty($last))||(!isset($last))) {
                        $last = $data;
                    } 

                    if(("$continue"=="1")) {
                        if($GLOBALS['DEBUG_TRACE']) {
                            echo "<br />";
                                   print_r($first);
                                   echo "<br />";
                                   print_r($last);
                                   echo "<br />";
                                   print_r($current);
                            echo "<br />";
                        }

                        $continue=compare_data_program($first,$last,$current,$tmp);

                        if($GLOBALS['DEBUG_TRACE']) {
                            echo "<br />";
                            print_r($first);
                            echo "<br />";
                            print_r($last);
                            echo "<br />";
                            print_r($current);
                            echo "<br />";
                            print_r($tmp);
                            echo "<br />-------------------<br />";
                        }
                    } else {
                        $continue="1";
                    }

                    if("$continue"!="2") {   
                        $first=$last;
                        unset($last);
                    }
                } else {
                    $continue="1";
                }
            }

            $tmp=purge_program($tmp);
            $tmp=optimize_program($tmp);
            $data_plug=$tmp;
            $first=array(
                "time_start" => "000000",
                "time_stop" => "000000",
                "value" => "0"
            );

            $data_plug[] = array(
                    "time_start" => "240000",
                    "time_stop" => "240000",
                    "value" => "0"
            );

            $data_plug[] = array(
                    "time_start" => "end",
                    "time_stop" => "end",
                    "value" => "end"
            );

            $data_plug[] = array(
                    "time_start" => "end",
                    "time_stop" => "end",
                    "value" => "end"
            );
        }
    }
   
    if(count($tmp)>0) {
            if(!insert_program_value($program[0]['selected_plug'],$tmp,$out)) $ret=false;   
    }
    return $ret;
}
// }}}


// {{{ compare_data_program()
// ROLE compare and format 3 values of the program graph
// IN $first      first value to compare
//    $last      last value to compare
//    $current      current value submitted by user to be added
//    $tmp      array to save datas
// RET false if the function has treated the $last value and we have to skip it in the next call of the function, true else.
function compare_data_program(&$first,&$last,&$current,&$tmp) {
   if(($current['time_start']>=$first['time_start'])&&($current['time_stop']<=$last['time_stop'])) {
   if(($current['time_start']>=$first['time_start'])&&($current['time_stop']<=$first['time_stop'])&&($current['time_start']<$last['time_stop'])) {
           //case 1: current value is in the first value
      if($GLOBALS['DEBUG_TRACE']) {
         echo "case 1-";
      }
      if($current['value']==$first['value']) {
            //first=current: nothing to do
         $tmp[]=$first;
         $tmp[]=$last;
         return "0";      
      } else  if(($current['time_start']==$first['time_start'])&&($current['time_stop']==$first['time_stop'])) {
            //first==current: replacement of the value
            if($current['value']==0) {
               $tmp[]=$last;
               return "0";
            } else if($current['value']!=$first['value']) {
               $tmp[]=$current;
               $tmp[]=$last;
                                        return "0";
            }
      } else if($current['time_start']==$first['time_start']) {
            //current begin with the first value but doesn't ended with the first
            if($current['value']==0) {
               $first['time_start']=$current['time_stop'];
               $tmp[]=$first;
               $tmp[]=$last;
               return "0";   
            } else if($current['value']!=$first['value']) {
               $new_value = array(
                  "time_start" => $first['time_start'],
                                                "time_stop" => $current['time_stop'],
                                                "value" => $current['value']               
               );
               $first['time_start']=$current['time_stop'];
                                        $tmp[]=$new_value;
                                        $tmp[]=$first;
               $tmp[]=$last;
                                        return "0";
                                } 
      } else if($current['time_stop']==$first['time_stop']) {
            //current doesn't start with the start value of first but ended with the stop value of first
            if($current['value']==0) {
               $first['time_stop']=$current['time_start'];
               $tmp[]=$first;
               $tmp[]=$last;
               return "0";   
            } else if($current['value']!=$first['value']) {
                                        $first['time_stop']=$current['time_stop'];
                                        $tmp[]=$first;
               $tmp[]=$current;
                                        $tmp[]=$last;
                                        return "0";
                                }
      } else {
            //current is in the first value: cut in three
            if($current['value']==0) {
               $save_time=$first['time_stop'];
               $first['time_stop']=$current['time_start'];
               $new_value= array(
                  "time_start" => $current['time_stop'],
                  "time_stop" => $save_time,
                  "value" => $first['value']
               );
               $tmp[]=$first;
               $tmp[]=$new_value;   
               $tmp[]=$last;
               return "0";
            } else {
               $save_time=$first['time_stop'];
                                        $first['time_stop']=$current['time_start'];
               $new_value=array(
                  "time_start" => $current['time_stop'],
                                                "time_stop" => $save_time,
                                                "value" => $first['value']
               );
               $tmp[]=$first;
               $tmp[]=$current;   
               $tmp[]=$new_value;
               $tmp[]=$last;
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
         $tmp[]=$first;
         $tmp[]=$last;
         return "0";
      } else if(($current['time_start']==$first['time_stop'])&&($current['time_stop']==$last['time_start'])) {
                                //first->current->last: replacement of the value
                                if(($current['value']==$first['value'])&&($current['value']==$last['value'])) {
               $first['time_stop']=$last['time_stop'];
                                        $tmp[]=$first;
                                        return "0";
                                } else if($current['value']==$first['value']) {
               $first['time_stop']=$current['time_stop'];
               $tmp[]=$first;
               $tmp[]=$last;
               return "0";
            } else if($current['value']==$last['value'])  {
               $tmp[]=$first;
               $last['time_start']=$current['time_start'];
               $tmp[]=$last;
               return "0";   
            } else {
               $tmp[]=$first;
               $tmp[]=$current;
               $tmp[]=$last;
               return "0";
            }
      } else if($current['time_start']==$first['time_stop']) {
            if(($current['value']==$first['value'])) {
                                        $first['time_stop']=$current['time_stop'];
                                        $tmp[]=$first;
               $tmp[]=$last;
                                        return "0";
                                } else {
                                        $tmp[]=$first;
                                        $tmp[]=$current;
                                        $tmp[]=$last;
                                        return "0";
                                }
      } else if($current['time_stop']==$last['time_start']) {
            if(($current['value']==$last['value'])) {
                                        $last['time_start']=$current['time_start'];
                                        $tmp[]=$first;
                                        $tmp[]=$last;
                                        return "0";
                                } else {
                                        $tmp[]=$first;
                                        $tmp[]=$current;
                                        $tmp[]=$last;
                                        return "0";
                                }
                } else {
            $tmp[]=$first;
            $tmp[]=$current;
            $tmp[]=$last;
            return "0";
      }
   } else if(($current['time_start']>=$last['time_start'])&&($current['time_stop']<=$last['time_stop'])) {
      // case 3: current value is in the last value
      if($GLOBALS['DEBUG_TRACE']) {
                        echo "case 3-";
                }
      $tmp[]=$first;
      return "1";   
   } else if(($current['time_start']>=$first['time_start'])&&($current['time_start']<=$first['time_stop'])&&($current['time_stop']<$last['time_start'])&&($current['time_stop']>$first['time_stop'])) {
      if($GLOBALS['DEBUG_TRACE']) {
                        echo "case 4-";
                }
      //case 4: current value is in the first value and stop between the first and before the last value
      if($current['value']==$first['value']) {
         $first['time_stop']=$current['time_stop'];
         $tmp[]=$first;
         $tmp[]=$last;   
      } else if($current['time_start']==$first['time_start']) {
            if($current['value']==0) {
               $tmp[]=$last;
               return "0";
            } else {
               $tmp[]=$current;
               $tmp[]=$last;
               return "0";
            }
      } else {
            if($current['value']==0) {
               $first['time_stop']=$current['time_start'];
                                        $tmp[]=$first;
               $tmp=$last;
                                        return "0";
                                } else {
               $first['time_stop']=$current['time_start'];
               $tmp[]=$first;
               $tmp[]=$current;
               $tmp[]=$last;
               return "0";
            }
      }
   } else if(($current['time_start']>=$first['time_stop'])&&($current['time_start']<$last['time_start'])&&($current['time_stop']>$last['time_start'])) {
      if($GLOBALS['DEBUG_TRACE']) {
                        echo "case 5-";
                }
      //case 5: current value is betwwen the first and last value and stop in the last value
      if(($current['time_start']==$first['time_stop'])&&($current['time_stop']==$last['time_stop'])) {
         if($current['value']==0) {
                                $tmp[]=$first;
                                return "0";
                        } else if(($current['value']==$last['value'])&&($current['value']==$first['value'])) {
                                $first['time_stop']=$last['time_stop'];
                                $tmp[]=$first;
            return "0";
                        } else if($current['value']==$first['value']) {
                                $first['time_stop']=$current['time_stop'];
            $last['time_start']=$current['time_stop'];
                                $tmp[]=$first;
            $tmp[]=$last;
                                return "0";
         } else {
            $tmp[]=$first;
            $tmp[]=$current;
            return "0";
                        } 
      } else if(($current['time_start']>$first['time_stop'])&&($current['time_stop']==$last['time_stop'])) {
         $tmp[]=$first;
         if($current['value']==0) {
                                return "0";
                        } else {
                                $tmp[]=$current;
                                return "0";
                        } 
      } else if(($current['time_start']==$first['time_stop'])&&($current['time_stop']<$last['time_stop'])) {
         if($current['value']==0) {
            $last['time_start']=$current['time_stop'];
                                $tmp[]=$first;
            $tmp[]=$last;
                                return "0";
                        } else if(($current['value']==$last['value'])&&($current['value']==$first['value'])) {
                                $first['time_stop']=$last['time_stop'];
                                $tmp[]=$first;
                                return "0";
                        } else if($current['value']==$first['value']) {
                                $first['time_stop']=$current['time_stop'];
                                $last['time_start']=$current['time_stop'];
                                $tmp[]=$first;
                                $tmp[]=$last;
                                return "0";
                        } else if($current['value']==$last['value']) {
            $last['time_start']=$current['time_start'];
                                $tmp[]=$first;
                                $tmp[]=$last;
                                return "0";
                        } else {
            $last['time_start']=$current['time_stop'];
            $tmp[]=$first;
            $tmp[]=$current;
            $tmp[]=$last;
            return "0";
         }
      } else {
         if($current['value']==0) {
                                $last['time_start']=$current['time_stop'];
                                $tmp[]=$first;
                                $tmp[]=$last;
                                return "0";
                        } else if($current['value']==$last['value']) {
                                $last['time_start']=$current['time_start'];
                                $tmp[]=$first;
                                $tmp[]=$last;
                                return "0";
                        } else {
                                $last['time_start']=$current['time_stop'];
                                $tmp[]=$first;
                                $tmp[]=$current;
                                $tmp[]=$last;
                                return "0";
                        }
      }
   } else {
      if($GLOBALS['DEBUG_TRACE']) {
                        echo "case 6-";
                }
               //case 6: current value is in the first, between first and last and stop in the last value
      if(($current['time_start']==$first['time_start'])&&($current['time_stop']==$last['time_stop'])) {
         if($current['value']==0) {
            return "0";
         } else {
            $tmp[]=$current;
            return "0";
         }
      } else if($current['time_start']==$first['time_start']) {
         if($current['value']==0) {
            $last['time_start']=$current['time_stop'];
            $tmp[]=$last;
            return "0";
         } else if($current['value']==$last['value']) {
            $current['time_stop']=$last['time_stop'];
            $tmp[]=$current;
            return "0";
         } else {
            $last['time_start']=$current['time_stop'];
            $tmp[]=$current;
            $tmp[]=$last;
            return "0";
         }
      } else if ($current['time_start']==$last['time_stop']) {
         if($current['value']==0) {
                                $first['time_stop']=$current['time_start'];
                                $tmp[]=$first;
                                return "0";
                        } else if($current['value']==$last['value']) {
            $first['time_stop']=$current['time_start'];
                                $tmp[]=$first;
            $tmp[]=$current;
                                return "0";
                        } else if($current['value']==$first['value']) {
                                $first['time_stop']=$current['time_stop'];
                                $tmp[]=$first;
                                return "0";
         } else {
            $first['time_stop']=$current['time_start'];
            $tmp[]=$first;
            $tmp[]=$current;
            return "0";   
         }
      } else {
         if($current['value']==0) {
                                $first['time_stop']=$current['time_start'];
            $last['time_start']=$current['time_stop'];
                                $tmp[]=$first;
            $tmp[]=$last;
                                return "0";
                        } else if($current['value']==$last['value']) {
                                $first['time_stop']=$current['time_start'];
                                $tmp[]=$first;
                                $tmp[]=$current;
                                return "0";
                        } else if($current['value']==$first['value']) {
                                $first['time_stop']=$current['time_stop'];
            $last['time_start']=$current['time_stop'];
                                $tmp[]=$first;
            $tmp[]=$last;
                                return "0";
                        } else {
                                $first['time_stop']=$current['time_start']; 
            $last['time_start']=$current['time_stop'];
                                $tmp[]=$first;
                                $tmp[]=$current;
            $tmp[]=$last;
                                return "0";
                        }
      }
   }   
   } else if(($current['time_start']>=$first['time_start'])&&($current['time_stop']>$last['time_stop'])&&($current['time_start']<$last['time_stop'])&&($current['time_start']<$last['time_start'])) {
      if($GLOBALS['DEBUG_TRACE']) {
                        echo "special case: ";
                }
      $tmp_current=$current;
      $tmp_current['time_stop']=$last['time_stop'];
      $continue=compare_data_program($first,$last,$tmp_current,$tmp);
      if(!$continue) {
         $current['time_start']=$last['time_start'];
         return "2";      
      }
      return $continue;
  } else {
      if($GLOBALS['DEBUG_TRACE']) {
            echo "nothing;";
      }
      $tmp[]=$first;
      return "1";
  }
}
//}}}


// {{{ insert_program_value()
// ROLE insert a program into the database
// IN $plugid           id of the plug
//    $program          array containing programs datas
//    $out              error or warning message
// RET false is there is an error, true else
function insert_program_value($plugid,$program,&$out) {
$sql="";
foreach($program as $prog) {
    $start_time=$prog['time_start'];
    $end_time=$prog['time_stop'];
    $value=$prog['value'];

   $sql = $sql . <<<EOF

INSERT INTO `programs`(`plug_id`,`time_start`,`time_stop`, `value`) VALUES('{$plugid}',"{$start_time}","{$end_time}",'{$value}');
EOF;
}

   $db=db_priv_pdo_start();
   try {
        $db->exec("$sql");
   } catch(PDOException $e) {
        $ret=$e->getMessage();
   }
   $db=null;

   if((isset($ret))&&(!empty($ret))) {
       if($GLOBALS['DEBUG_TRACE']) {
             $out[]=__('ERROR_UPDATE_SQL').$ret;
             return false;
       } else {
             $out[]=__('ERROR_UPDATE_SQL');
             return false;
       }
   }
   return true;
}
// }}}


// {{{ clean_program()
// ROLE clean program table
// IN $plug_id          id of the plug
//    $out              error or warning message
// RET false if an error occured, true else
function clean_program($plug_id,&$out) {
   $sql = <<<EOF
DELETE FROM `programs` WHERE plug_id = {$plug_id}
EOF;
   $db=db_priv_pdo_start();
   try {
        $db->exec("$sql");
   } catch(PDOException $e) {
        $ret=$e->getMessage();
   }
   $db=null;

    if((isset($ret))&&(!empty($ret))) {
     if($GLOBALS['DEBUG_TRACE']) {
             $out[]=__('ERROR_UPDATE_SQL').$ret;
     } else {
            $out[]=__('ERROR_UPDATE_SQL');
     }
     return false;
    }
    return true;
}
// }}}}





// {{{ export_program()
// ROLE export a program into a text file
// IN $id          id of the program
//    $out         error or warning message
// RET none
function export_program($id,&$out) {
       $sql = <<<EOF
SELECT * FROM `programs` WHERE plug_id = {$id}
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
       $file="tmp/program_plug${id}.prg";

      if($f=fopen("$file","w+")) {
            fputs($f,"#Program : time_start time_stop value\r\n");
            if(count($res)>0) {
               foreach($res as $record) {
                  fputs($f,$record['time_start'].",".$record['time_stop'].",".$record['value']."\r\n");
               }
            } else {
                    fputs($f,"000000".",235959,0\r\n");
            }
      } 
      fclose($f);
}
// }}}


// {{{ export_table_csv()
// ROLE export a program into a text file
// IN $name       name of the table to be exported
//    $out         error or warning message
// RET none
function export_table_csv($name="",&$out) {
       if(strcmp("$name","")==0) return 0;

       $file="tmp/$name.csv";
       $sql_name = <<<EOF
SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$name}'
EOF;
        $db=db_priv_pdo_start();
        try {
            $sth=$db->prepare("$sql_name");
            $sth->execute();
            $res_name=$sth->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $ret_name=$e->getMessage();
        }
        $db=null;

        if(is_file($file)) {
            unlink($file);
        }
        $os=php_uname('s');
        switch($os) {
                case 'Linux':
                        exec("../../bin/mysqldump -u cultibox -pcultibox --port=3891 -t -T tmp/ cultibox $name --fields-terminated-by=,");
                        break;
                case 'Mac':
                case 'Darwin':
                        exec("../../bin/mysqldump -u cultibox -pcultibox --port=3891 -t -T /tmp/ cultibox $name --fields-terminated-by=,");
                        copy("/tmp/$name.txt","$file");
                        copy("/tmp/$name.sql","tmp/$name.sql");
                        unlink("/tmp/$name.txt");
                        unlink("/tmp/$name.sql");
                        break;
                case 'Windows NT':
                        exec("..\..\mysql\bin\mysqldump.exe -u cultibox -pcultibox --port=3891 -t -T tmp cultibox $name --fields-terminated-by=,");
                        break;
        }
       if (copy("tmp/$name.txt","$file")) {
            unlink("tmp/$name.txt");
            
        }
        unlink("tmp/$name.sql");


         $line="";
         if($f=fopen("$file","r+")) {
            foreach($res_name as $column) {
                foreach($column as $col) {
                    if(strcmp("$line","")==0) {
                        $line="$col";
                    } else {
                        $line=$line.",".$col;
                    }
                }
            }
        
            $contents = file_get_contents($file);
            fputs($f,$line."\n".$contents);
            fclose($f);
        }
}
// }}}



// {{{ check_export_table_csv()
// ROLE check that a table is empty or not
// IN $name       name of the table to be exported
//    $out         error or warning message
// RET false is table is empty, true else
function check_export_table_csv($name="",&$out) {
       if(strcmp("$name","")==0) return false;

        if(strcmp("$name","logs")==0) {
            $sql = <<<EOF
SELECT `timestamp` FROM `{$name}` WHERE `fake_log` LIKE "False" LIMIT 1;
EOF;
       } else if(strcmp("$name","power")==0) {
            $sql = <<<EOF
SELECT `timestamp` FROM `{$name}` LIMIT 1;
EOF;
       } else {
            $sql = <<<EOF
SELECT * FROM `{$name}` LIMIT 1;
EOF;
}
        $db=db_priv_pdo_start();
        try {
            $sth=$db->prepare("$sql");
            $sth->execute();
            $res=$sth->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $ret=$e->getMessage();
        }
        $db=null;

       if(count($res)>0) {
             if(strcmp($res['timestamp'],"")!=0) {
                return true;
             }
       }
       return false;
}
// }}}


// {{{ purge_program()
// ROLE purge,check and format program 
// IN $arr        array containing value of the program
// RET the array purged
function purge_program($arr) {
   $tmp=array();
   asort($arr);
   if(count($arr)>0) {
      foreach($arr as $val) {
         if(($val['value']!=0)&&($val['time_start']!=$val['time_stop'])&&(strcmp($val['value'],"")!=0)&&(strcmp($val['time_start'],"")!=0)&&(strcmp($val['time_stop'],"")!=0)) {
               $tmp_arr = array(
                  "time_start" => $val['time_start'],
                  "time_stop" => $val['time_stop'],
                  "value" => $val['value']
               );
               $tmp[]=$tmp_arr;
             }
      } 
   return $tmp;
   }
}
// }}}


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
   $second_regul=get_configuration("SECOND_REGUL",$out);
   if($nb>0) {
      $sql = <<<EOF
SELECT * FROM `plugs` WHERE id <= {$nb}
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

            if(strcmp($data['PLUG_ENABLED'],"True")==0) {
                //Main regulation:
                if($data['PLUG_TYPE']=="ventilator") {
                    $reg="REG:T+${tol}";
                } else if($data['PLUG_TYPE']=="heating") {
                    $reg="REG:T-${tol}";
                } else if($data['PLUG_TYPE']=="humidifier") {
                    $reg="REG:H-${tol}";
                } else if($data['PLUG_TYPE']=="dehumidifier") {
                    $reg="REG:H+${tol}";
                } else {
                    $reg="REG:N+000";
                }


                // Second regulation:
                if(strcmp("$second_regul","True")==0) {
                    //Default second regulation value:
                    if($data['PLUG_TYPE']=="ventilator") {
                        $sec="SEC:T+1000";
                    } else if($data['PLUG_TYPE']=="heating") {
                        $sec="SEC:N-1000";
                    } else if($data['PLUG_TYPE']=="humidifier") {
                        $sec="SEC:N-1000";
                    } else if($data['PLUG_TYPE']=="dehumidifier") {
                        $sec="SEC:N+1000";
                    } else {
                        $sec="SEC:N+0000";
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
            } else {
                $reg="REG:N+000";
                $sec="SEC:N+0000";
            }

            if(strcmp($data['PLUG_REGUL_SENSOR'],"")!=0) {  
                $vsen="";
                $tmp_sensor=explode("-",$data['PLUG_REGUL_SENSOR']);
                $find=false;
                for($i=1;$i<=$GLOBALS['NB_MAX_SENSOR'];$i++) {
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
function create_program_from_database(&$out) {
   $nb_plugs=get_configuration("NB_PLUGS",$out);
   $sql = <<<EOF
SELECT * FROM `programs` WHERE `plug_id` IN (SELECT `id` FROM `plugs` WHERE `PLUG_ENABLED` LIKE "True") ORDER by `time_start`
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
   }
   unset($ret);
   $sql = <<<EOF
SELECT * FROM `programs` WHERE time_start = "000000" AND `plug_id` IN (SELECT `id` FROM `plugs` WHERE `PLUG_ENABLED` LIKE "True") ORDER by `time_start`
EOF;
   $db=db_priv_pdo_start();
   try {
       $sth=$db->prepare("$sql");
       $sth->execute();
       $first=$sth->fetchAll(PDO::FETCH_ASSOC);
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
   unset($ret);

   $sql = <<<EOF
SELECT * FROM `programs` WHERE time_stop = "235959" AND `plug_id` IN (SELECT `id` FROM `plugs` WHERE `PLUG_ENABLED` LIKE "True") ORDER by `time_start`
EOF;
   $db=db_priv_pdo_start();
   try {
        $sth=$db->prepare("$sql");
        $sth->execute();
        $last=$sth->fetchAll(PDO::FETCH_ASSOC);
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

   if(count($event)>0) {
      for($i=0;$i<count($event);$i++) {
         $data[$i+1] = "";
         $j=1;
         while($j<= $GLOBALS['NB_MAX_PLUG']) {
            if($j>$nb_plugs) {
                $result="000";
            } else {
                $result=find_value_for_plug($res,$event[$i],$j);
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


// {{{ create_calendar_from_database()
// ROLE read calendar from the database and format its to be writen into a sd card
// IN   $out         error or warning message
// RET an array containing datas
function create_calendar_from_database(&$out,$start="",$end="") {
        $year=date('Y');
        $data=array();
        $db = db_priv_pdo_start();
        if((strcmp("$start","")!=0)&&((strcmp("$end","")!=0))) {
             $sql = <<<EOF
SELECT `Title`,`StartTime`,`EndTime`, `Description` FROM `calendar` WHERE (`StartTime` BETWEEN '{$start}' AND '{$end}') OR (`EndTime` BETWEEN '{$start}' AND '{$end}') OR (`StartTime` <= '{$start}' AND `EndTime` >= '{$end}')
EOF;
        } else if((strcmp("$start","")!=0)&&((strcmp("$end","")==0))) {
             $sql = <<<EOF
SELECT `Title`,`StartTime`,`EndTime`, `Description` FROM `calendar` WHERE "{$start}" BETWEEN `StartTime`AND `EndTime` 
EOF;
        } else {
            $sql = <<<EOF
SELECT `Title`,`StartTime`,`EndTime`, `Description` FROM `calendar` WHERE `StartTime` LIKE "{$year}-%"
EOF;
        }

        foreach($db->query("$sql") as $val) {
         $val['Title']=clean_calendar_message($val['Title']);
         $val['Description']=clean_calendar_message($val['Description']);

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

         $value=array();
         $len = mb_strlen($val['Title'], "UTF-8");
         for ($i = 0; $i < $len; $i++) {
             $value[] = mb_substr($val['Title'], $i, 1, "UTF-8");
         }

         for($i=0;$i<count($value);$i++) {
            $count=$count+1;
            if($count==1) {
               if(strcmp($value[$i]," ")==0) {
                  $count=0;
               } else {
                  $line=$line.$value[$i];
               }
            } else {
                 $line=$line.$value[$i];
            }

            if($count==12) {
               if((strcmp($value[$i]," ")!=0)&&(isset($value[$i+1]))&&(strcmp($value[$i+1]," ")!=0)) {
                  if(isset($value[$i+2])) {
                     $line=$line."-";
                     $count=$count+1;
                  }
               } elseif(strcmp($value[$i]," ")==0) {
                     $line=$line." ";
                     $count=$count+1;
              }
            }

            if($count==13) {
               $s[]=mb_strtoupper($line, 'UTF-8');
               $line="";
               $count=0;
               $number=$number+1;
            }

            if("$number"=="18") {
               break;
            }
         }

         if(("$count"!="13")&&("$number"!="18")) {
            $s[]=mb_strtoupper($line, 'UTF-8');
            $number=$number+1;
         }


         while(mb_strlen($s[$number-1])<13) {
            $s[$number-1]=$s[$number-1]." ";
         }


         if((isset($val['Description']))&&(!empty($val['Description']))) {
            $count=0;
            $line="";
            $value=array();
            $len = mb_strlen($val['Description'], "UTF-8");
            for ($i = 0; $i < $len; $i++) {
                $value[] = mb_substr($val['Description'], $i, 1, "UTF-8");
            }
            for($i=0;$i<count($value);$i++) {
               if(strcmp($value[$i],"\n")==0) {
                   while(mb_strlen($line)<=12) {
                        $line=$line." ";
                   }
                   $desc[]=$line;$line="";
                   $count=0;
                   $number=$number+1;
               } else {
                   $count=$count+1;
                   if($count==1) {
                        if(strcmp($value[$i]," ")==0) {
                            $count=0;
                        } else {
                            $line=$line.$value[$i];
                        }
                    } else {
                            $line=$line.$value[$i];
                    }

                    if($count==12) {
                        if((strcmp($value[$i]," ")!=0)&&(isset($value[$i+1]))&&(strcmp($value[$i+1]," ")!=0)) {
                            if(isset($value[$i+2])) {
                                $line=$line."-";
                                $count=$count+1;
                            }
                        } elseif(strcmp($value[$i]," ")==0) {
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
               while(mb_strlen($desc[count($desc)-1])<13) {
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

      $db=null;
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


// {{{ check_empty_programs()
//ROLE check if no programs have been defined yet
// IN  $nb_plugs        number of plugs used
// RET true if there is a program defined, false else
function check_programs($nb_plugs=0) {
   if($nb_plugs>0) {
           $sql = <<<EOF
SELECT * FROM `programs` WHERE `plug_id` <= {$nb_plugs} LIMIT 1
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

      if(!isset($res)||(empty($res))) {
         return false;
      } else {
         if(count($res)>0) {
            return true;
         } else {
            return false;
         }
      }
   } else {
      return false;
   }
}
// }}}


// {{{ reset_plug_identificator()
//ROLE check if no programs have been defined yet
// IN  $out       warnings or errors messages 
// RET none
function reset_plug_identificator(&$out) {
           $sql = <<<EOF
UPDATE `plugs` SET  `PLUG_ID` = ""
EOF;
           $db=db_priv_pdo_start();
           try {
                $db->exec("$sql");
           } catch(PDOException $e) {
                $ret=$e->getMessage();
           }
           $db=null;

           if((isset($ret))&&(!empty($ret))) {
               if($GLOBALS['DEBUG_TRACE']) {
                  $out[]=__('ERROR_UPDATE_SQL').$ret;
               } else {
                  $out[]=__('ERROR_UPDATE_SQL');
               }
           }
}
// }}}


// {{{ generate_program_from_file()
//ROLE generate array containing data for a prgram from a file
// IN  $file         file to be read
//     $plug         plug id for the program
//     $out          error or warning message
// RET array containing program's data
function generate_program_from_file($file="",$plug,&$out) {
         $res=array();
         $handle=fopen("$file", 'r');
            if($handle) {
               while (!feof($handle)) {
                  $buffer = fgets($handle);
                  $temp = explode(",", $buffer);
                  if(count($temp)==3) {
                     if(is_numeric($plug)) {
                        $res[]=array(
                           "selected_plug" => $plug,
                           "start_time" => $temp[0],
                           "end_time" => $temp[1],
                           "value_program" => $temp[2]
                        );
                     }
                  }
               }
            }
         return $res;
}
// }}}


// {{{ reset_log()
// IN $table    table to be deleted: logs, power...
//    $start    delete logs between two specific dates, between $start and $end
//    $end      
// RET  0 is an error occured, 1 else
function reset_log($table="",$start="",$end="") {
    if(strcmp("$table","")==0) return 0;
    $error=1;

if((strcmp($start,"")==0)||(strcmp($end,"")==0)) {
    $sql = <<<EOF
TRUNCATE `{$table}`
EOF;
} else {
    $sql = <<<EOF
DELETE FROM `{$table}` WHERE `date_catch` BETWEEN "{$start}" AND "{$end}"
EOF;
}
           $db=db_priv_pdo_start();
           try {
                $db->exec("$sql");
           } catch(PDOException $e) {
                $ret=$e->getMessage();
           }
           $db=null;

           if((isset($ret))&&(!empty($ret))) {
                  $error=0;
           }
           return $error;
}
// }}}


// {{{ get_historic_value()
// IN $out      error or warning message
//    $res      return array containing data
// RET none 
function get_historic_value(&$res,&$out) {
    $sql = <<<EOF
SELECT * from `historic` ORDER by `timestamp` DESC LIMIT 0,100 
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
          $out[]=__('ERROR_DELETE_SQL').$ret;
       } else {
          $out[]=__('ERROR_DELETE_SQL');
       }
   }
}
// }}}



// {{{ set_historic_value()
// IN $out      error or warning message
//    $message  message to be written into the table
//    $type     type of message: ERROR or INFO
// RET none
function set_historic_value($message="",$type="",&$out) {
    if((strcmp("$message","")!=0)&&(strcmp("$type","")!=0)) {
        if(!isset($_SESSION['TIMEZONE'])) {
                $_SESSION['TIMEZONE']="Europe/Paris";
        }

        date_default_timezone_set($_SESSION['TIMEZONE']);
        $timestamp=date('Y-m-d H:i:s');

        $sql = <<<EOF
INSERT INTO `historic`(`timestamp`,`action`, `type`) VALUES ("${timestamp}","${message}","${type}");
EOF;
        $db=db_priv_pdo_start();
        try {
            $db->exec("$sql");
        } catch(PDOException $e) {
            $ret=$e->getMessage();
        }
        $db=null;

        if((isset($ret))&&(!empty($ret))) {
            if($GLOBALS['DEBUG_TRACE']) {
                $out[]=__('ERROR_UPDATE_SQL').$ret;
            } else {
                $out[]=__('ERROR_UPDATE_SQL');
            }
        }
   }
}
// }}}


// {{{ get_active_plugs()
// ROLE get list of active plugs
// IN $nb   number of maximal plug confiured
//    $out   errors or warnings messages
// RET array containing the list of active plug
function get_active_plugs($nb,&$out="") {
        $sql = <<<EOF
SELECT id FROM `plugs` WHERE id <={$nb} AND `PLUG_ENABLED` LIKE "True" 
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
   }
   return $res;
}
// }}}


// {{{ format_regul_sumary()
// ROLE format regulation of a plug to be displayed in a sumary
// IN    $number     maximale plug's number
//       $out        error or warning messages
//       $resume     string containing sumary formated
//       $max        the maximal number of plug tu be seeked
// RET   sumary formated 
function format_regul_sumary($number=0, &$out,&$resume="",$max=0) {
    if(strcmp("$number","all")==0) {
    $sql = <<<EOF
SELECT id, PLUG_REGUL, PLUG_SENSO, PLUG_SENSS, PLUG_REGUL_VALUE FROM `plugs` WHERE `PLUG_REGUL` LIKE "True" AND `PLUG_ENABLED` LIKE "True" AND id<{$max} 
EOF;
    } else {
        $sql = <<<EOF
SELECT id, PLUG_SENSO, PLUG_SENSS, PLUG_REGUL_VALUE FROM `plugs` WHERE `PLUG_REGUL` LIKE "True" AND `PLUG_ENABLED` LIKE "True" AND `id` = {$number} AND id<={$max}
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
    }

    if(count($res)>0) {
        foreach($res as $result) {
            $resume=$resume."<p align='center'><i>".__('SUMARY_REGUL_SUBTITLE')." ".$result['id'].":</i></p>";
            if(strcmp($result['PLUG_SENSO'],"H")==0) {
                $resume=$resume."<b>".__('SUMARY_REGUL_SENSO').":</b> ".__('SUMARY_REGUL_HYGRO');
            } else {
                $resume=$resume."<b>".__('SUMARY_REGUL_SENSO').":</b> ".__('SUMARY_REGUL_TEMP');
            }

            if(strcmp($result['PLUG_SENSS'],"+")==0) {
                $resume=$resume."<br /><b>".__('SUMARY_REGUL_SENSS').":</b> ".__('SUMARY_ABOVE');
            } else {
                $resume=$resume."<br /><b>".__('SUMARY_REGUL_SENSS').":</b> ".__('SUMARY_UNDER');    
            }
            $resume=$resume."<br /><b>".__('SUMARY_REGUL_VALUE').":</b> ".$result['PLUG_REGUL_VALUE']."<br /><br />";
        }
    }
}
// }}}


// {{{ get_cost_summary()
// ROLE format cost configuration informations be displayed in a sumary
// IN   out     error or warning messages
// RET  sumary formated 
function get_cost_summary(&$out) {
    $resume="";
    $sql = <<<EOF
SELECT COST_PRICE, COST_PRICE_HP, COST_PRICE_HC, START_TIME_HC, STOP_TIME_HC, COST_TYPE FROM `configuration` 
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
   }

   foreach($res as $result) {
            $resume="<p align='center'><b><i>".__('SUMARY_COST_TITLE').":<br /></i></b></p>";
            if(isset($_SESSION['LANG'])) {
                if((strcmp($_SESSION['LANG'],"fr_FR")==0)||(strcmp($_SESSION['LANG'],"de_DE"))||(strcmp($_SESSION['LANG'],"es_ES"))||(strcmp($_SESSION['LANG'],"it_IT"))) {
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


// {{{ configure_menu()
// ROLE hide or display joomla's menu
// IN   cost        value for displaying or not the cost menu
//      wizard      value for displaying or not the wizard menu
//      historic    value for displaying or not the historic menu
// RET  none
function configure_menu($cost="False",$wizard="False",$historic="False") {
   if(strcmp("$cost","True")==0) {
            $cost=1;
    } else {
            $cost=0;
    }

    if(strcmp("$wizard","True")==0) {
            $wizard=1;
    } else {
            $wizard=0;
    }

    if(strcmp("$historic","True")==0) {
            $historic=1;
    } else {
            $historic=0;
    }


  $sql = <<<EOF
UPDATE `dkg45_menu` SET  published = "{$cost}" WHERE alias LIKE "cost-%";
UPDATE `dkg45_menu` SET  published = "{$historic}" WHERE alias LIKE "historic-%";
EOF;
   $db=db_priv_pdo_start_joomla();
   if($db) {
        try {
            $db->exec("$sql");
        } catch(PDOException $e) {
            $ret=$e->getMessage();
        }
        $db=null;
    }
}
// }}}


// {{{ check_configuration_power()
// ROLE check of power of plugs is configured
// IN  $id     id of the plug to be checked
// OUT false if a plug is not configured, true else
function check_configuration_power($id=0) {
      $sql = <<<EOF
SELECT `PLUG_POWER` FROM `plugs` WHERE `id` = {$id} AND `PLUG_ENABLED` LIKE "True";
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
    $sql = <<<EOF
SELECT * from `notes` WHERE `lang` LIKE "{$lang}" ORDER by `id`
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
          $out[]=__('ERROR_DELETE_SQL').$ret;
       } else {
          $out[]=__('ERROR_DELETE_SQL');
       }
   }
}
// }}}


// {{{ get_plug_regul_sensor()
// ROLE get plug sensors
// IN $out      error or warning message
//    $id       id of the plug
// RET return array containing data
function get_plug_regul_sensor($id,&$out) {
    $sql = <<<EOF
SELECT `PLUG_REGUL_SENSOR` from `plugs` WHERE `id` = {$id}
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
          $out[]=__('ERROR_DELETE_SQL').$ret;
       } else {
          $out[]=__('ERROR_DELETE_SQL');
       }
   }

   if(!empty($res)) {
           $tmp=explode("-",$res[0]['PLUG_REGUL_SENSOR']);
           $result=array();                                           
           for($i=1;$i<=$GLOBALS['NB_MAX_SENSOR'];$i++) {
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



?>

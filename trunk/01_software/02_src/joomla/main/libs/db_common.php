<?php

// {{{ db_priv_start()
// ROLE create a connection to the DB using joomla connector: JDatabase
// IN none 
// RET database connection object
function db_priv_start() {
   if (isset($GLOBALS['dbconn'])) {
      $dbconn = &$GLOBALS['dbconn'];
   } else {
      $option = array(); //Prevent problems
      $option['driver']   = 'mysql';            // Database driver name
      $option['host']     = 'localhost';    // Database host name
      $option['user']     = 'cultibox';       // User for database authentication
      $option['password'] = 'cultibox';   // Password for database authentication
      $option['database'] = 'cultibox';      // Database name
      $option['prefix']   = '';             // Database prefix (may be empty)
      $dbconn = & JDatabase::getInstance( $option );

      if ($dbconn) {
         $GLOBALS['dbconn'] = &$dbconn;
      }
   }
   return $dbconn;
}
// }}}


// {{{ db_priv_end()
// ROLE closes the connection to the DB
// IN $dbconn as a JDatabase object
// RET none
function db_priv_end($dbconn) {
   if($dbconn) {
      $dbconn->disconnect();
      return 1;
   }
   return 0;
}
// }}}


// {{{ db_update_logs($arr,&$out)
// ROLE update logs table in the Database with the array $arr
// IN $arr   array containing values to update database
//    $out   error or warning message 
// RET none
function db_update_logs($arr,&$out="") {
   $db = db_priv_start();
   $index=0;
   $return=1;
   $sql = <<<EOF
INSERT INTO `logs`(`timestamp`,`temperature`, `humidity`,`date_catch`,`time_catch`) VALUES
EOF;
   foreach($arr as $value) {
      if((array_key_exists("timestamp", $value))&&(array_key_exists("temperature", $value))&&(array_key_exists("humidity", $value))&&(array_key_exists("date_catch", $value))&&(array_key_exists("time_catch", $value))) {
         if("$index" == "0") {
            $sql = $sql . "(${value['timestamp']}, ${value['temperature']},${value['humidity']},\"${value['date_catch']}\",\"${value['time_catch']}\")";
         } else {
            $sql = $sql . ",(${value['timestamp']}, ${value['temperature']},${value['humidity']},\"${value['date_catch']}\",\"${value['time_catch']}\")";
         }
         $index = $index +1;
      }
   }
   $db->setQuery($sql);
   $db->query();
   $ret=$db->getErrorMsg();
   if((isset($ret))&&(!empty($ret))) {
      $out=$out.__('ERROR_UPDATE_SQL').$ret;
      $return=0; 
   }
   if(!db_priv_end($db)) {
      $out=$out.__('PROBLEM_CLOSING_CONNECTION');   
   }

   return $return;
}
// }}}


// {{{ get_graph_array(&$res,$key,$startdate,$out)
// ROLE get array needed to build graphics
// IN $res         the array containing datas needed for the graphics
//    $key      the key selectable from the database (temperature,humidity...)
//    $startdate   date (format YYYY-MM-DD) to check what datas to select
//    $out      errors or warnings messages
// RET none
function get_graph_array(&$res,$key,$startdate,&$out="") {
   $db = db_priv_start();
        $sql = <<<EOF
SELECT ${key} as record,time_catch FROM `logs` WHERE date_catch LIKE "{$startdate}"
EOF;
   $db->setQuery($sql);
   $res = $db->loadAssocList();
   $ret=$db->getErrorMsg();
   if((isset($ret))&&(!empty($ret))) {
      $out=$out.__('ERROR_SELECT_SQL').$ret;
   }

   if(!db_priv_end($db)) {
      $out=$out.__('PROBLEM_CLOSING_CONNECTION'); 
   }
}
// }}}


// {{{ get_configuration($key,&$out)
// ROLE get configuration value for specific entries
// IN $key   the key selectable from the database 
//    $out   errors or warnings messages
// RET $res   value of the key   
//Note: if to select a value is limited to 1. Only one configuration is available,
//there isn't a user configuration management yet.
function get_configuration($key,&$out="") {
        $db = db_priv_start();
        $sql = <<<EOF
SELECT {$key} FROM `configuration` WHERE id = 1
EOF;
   $db->setQuery($sql);
        $res = $db->loadResult();
   $ret=$db->getErrorMsg();
   if((isset($ret))&&(!empty($ret))) {
      $out=$out.__('ERROR_SELECT_SQL').$ret;
   }
   
   if(!db_priv_end($db)) {
      $out=$out.__('PROBLEM_CLOSING_CONNECTION');
   }
   return $res;
}
// }}}


// {{{ insert_configuration($key,$value,&$out)
// ROLE set configuration value for specific entries
// IN $key      the key selectable from the database 
//    $value   value of the key to insert
//    $out      errors or warnings messages
// RET none
//Note: if to select a value is limited to 1. Only one configuration is available,
//there isn't a user configuration management yet.
function insert_configuration($key,$value,&$out="") {
        $db = db_priv_start();
        $sql = <<<EOF
UPDATE `configuration` SET  {$key} = "{$value}" WHERE id = 1
EOF;
   $db->setQuery($sql);
   $db->query();
   $ret=$db->getErrorMsg();
   if((isset($ret))&&(!empty($ret))) {
      $out=$out.__('ERROR_UPDATE_SQL').$ret;
   }
        
   if(!db_priv_end($db)) {
      $out=$out.__('PROBLEM_CLOSING_CONNECTION');
   }
}
// }}}


// {{{ get_plug_conf($key,$id,&$out)
// ROLE get plug configuration value for specific entries
// IN $key      the key selectable from the database 
//    $id   id of the plug
//    $out      errors or warnings messages
// RET $res   value result for the plug configuration entrie
function get_plug_conf($key,$id,&$out="") {
        $db = db_priv_start();
        $sql = <<<EOF
SELECT {$key} FROM `plugs` WHERE id = {$id}
EOF;
        $db->setQuery($sql);
        $res = $db->loadResult();
   $ret=$db->getErrorMsg();
   if((isset($ret))&&(!empty($ret))) {
      $out=$out.__('ERROR_SELECT_SQL').$ret;
   }

        if(!db_priv_end($db)) {
      $out=$out.__('PROBLEM_CLOSING_CONNECTION');
   }
   return $res;
}
// }}}


// {{{ insert_plug_conf($key,$id,$value,&$out)
// ROLE set plug configuration value for specific entries
// IN $key      the key selectable from the database 
//    $id       id of the plug
//    $value   value of the configuration field to update
//    $out      errors or warnings messages
// RET none
function insert_plug_conf($key,$id,$value,&$out="") {
        $db = db_priv_start();
        $sql = <<<EOF
UPDATE `plugs` SET  {$key} = "{$value}" WHERE id = {$id}
EOF;
        $db->setQuery($sql);
        $db->query();
   $ret=$db->getErrorMsg();
   if((isset($ret))&&(!empty($ret))) {
      $out=$out.__('ERROR_UPDATE_SQL').$ret;
   }

   if(!db_priv_end($db)) {
      $out=$out.__('PROBLEM_CLOSING_CONNECTION');
   }
}
// }}}


// {{{ get_plugs_infos($nb,$out)
// ROLE get plugs informations (name,id,type)
// IN $id      id of the plug
//    $out      errors or warnings messages
// RET return an array containing plugid and its name
function get_plugs_infos($nb=0,$out="") {
        $db = db_priv_start();
        $sql = <<<EOF
SELECT `id` , `PLUG_NAME`,`PLUG_TYPE`
FROM `plugs`
WHERE id <= {$nb}
ORDER by id ASC
EOF;
        $db->setQuery($sql);
        $db->query();
   $res = $db->loadAssocList();
        $ret=$db->getErrorMsg();
        if((isset($ret))&&(!empty($ret))) {
                $out=$out.__('ERROR_SELECT_SQL').$ret;
        }

        if(!db_priv_end($db)) {
                $out=$out.__('PROBLEM_CLOSING_CONNECTION');
        }
        return $res;
}
// }}}


// {{{ get_data_plug($id,$out)
// ROLE get a specific plug program
// IN $selected_plug   plug id to select
//    $out      errors or warnings messages
// RET plug data formated for highchart
function get_data_plug($selected_plug="",$out="") {
   $res="";
   if((isset($selected_plug))&&(!empty($selected_plug))) {
      $db = db_priv_start();
      $sql = <<<EOF
SELECT  `time_start`,`time_stop`,`value` FROM `programs` WHERE plug_id = {$selected_plug} ORDER by time_start ASC
EOF;
           $db->setQuery($sql);
           $db->query();
           $res=$db->loadAssocList();
      $ret=$db->getErrorMsg();
           if((isset($ret))&&(!empty($ret))) {
                   $out=$out.__('ERROR_SELECT_SQL').$ret;
         return 0;
           }

           if(!db_priv_end($db)) {
                   $out=$out.__('PROBLEM_CLOSING_CONNECTION');
         return 0;   
           }
   }
   return $res;
}
// }}}


// {{{ insert_program($plug_id,$start_time,$end_time,$value,&$out)
// ROLE check and create new plug program
// IN $plug_id      id of the plug
//    $start_time   start time for the program
//    $end_time      end time for the program
//    $value      value of the program
//    $out      error or warning message
// RET true
function insert_program($plug_id,$start_time,$end_time,$value,&$out) {
   $data_plug=get_data_plug($plug_id);
   asort($data_plug);
   $start_time=str_replace(':','',"$start_time");
   $end_time=str_replace(':','',"$end_time");
   $tmp=array();
   $current= array(
      "time_start" => "$start_time",
                "time_stop" => "$end_time",
                "value" => "$value"
   );
   //if(count($data_plug)>0) {
      //if($data_plug[0]['time_start']=="000000") {
       //  $first= array(
        //              "time_start" => $data_plug[0]['time_start'],
         //             "time_stop" => $data_plug[0]['time_stop'],
          //            "value" => $data_plug[0]['value']
           //   );   
      //}
   //}
   if((empty($first))||(!isset($first))) {
      $first=array(
         "time_start" => "000000",
         "time_stop" => "000000",
         "value" => "0"
      );
   }

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


   $continue="1";
   clean_program($plug_id,$out);

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
      if(count($tmp)>0) {
         foreach($tmp as $new_val) {
            insert_program_value($plug_id,$new_val['time_start'],$new_val['time_stop'],$new_val['value'],$out);   
         }
      }
   } else {
      if($value!=0) {
         insert_program_value($plug_id,$start_time,$end_time,$value,$out);
      }
   }
   return true;
}
// }}}


// {{{ compare_data_program($first,$last,$current,&$tmp)
// ROLE compare and format 3 values of the pgroram graph
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
                                                "value" => $current['time_value']               
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


// {{{ insert_program_value($plug_id,$start_time,$end_time,$value,&$out)
// ROLE insert a program into the database
// IN $plug_id          id of the plug
//    $start_time       start time for the program
//    $end_time         end time for the program
//    $value            value of the program
//    $out              error or warning message
// RET none
function insert_program_value($plug_id,$start_time,$end_time,$value,&$out) {
   $db = db_priv_start();
   $sql = <<<EOF
INSERT INTO `programs`(`plug_id`,`time_start`,`time_stop`, `value`) VALUES('{$plug_id}',"{$start_time}","{$end_time}",'{$value}')
EOF;
        $db->setQuery($sql);
        $db->query();
        $ret=$db->getErrorMsg();
        if((isset($ret))&&(!empty($ret))) {
                $out=$out.__('ERROR_UPDATE_SQL').$ret;
        }
        if(!db_priv_end($db)) {
                $out=$out.__('PROBLEM_CLOSING_CONNECTION');
        }
}
// }}}


// {{{ clean_program($plug_id,&$out)
// ROLE clean program table
// IN $plug_id          id of the plug
//    $out      error or warning message
// RET none
function clean_program($plug_id,&$out) {
   $db = db_priv_start();
        $sql = <<<EOF
DELETE FROM `programs` WHERE plug_id = {$plug_id}
EOF;
   $db->setQuery($sql);
        $db->query();
        $ret=$db->getErrorMsg();
        if((isset($ret))&&(!empty($ret))) {
                $out=$out.__('ERROR_UPDATE_SQL').$ret;
        }
        if(!db_priv_end($db)) {
                $out=$out.__('PROBLEM_CLOSING_CONNECTION');
        }
}
// }}}}


// {{{ export_program($id,&$out)
// ROLE export a program into a text file
// IN $id          id of the program
//    $out      error or warning message
// RET none
function export_program($id,&$out) {
       $db = db_priv_start();
       $sql = <<<EOF
SELECT * FROM `programs` WHERE id = `{$id}`
EOF;
       $db->setQuery($sql);
       $res = $db->loadAssocList();
       $ret=$db->getErrorMsg();

       $file="tmp/program${id}.txt";

       if($f=fopen("$file","w+")) {
      		fputs($f,"#Program : plug_id,time_start,time_stop,value\r\n");
		if(count($res)>0) {
			foreach($res as $record) {
         			fputs($f,$record['plug_id'].",".$record['time_start'].",".$record['time_stop'].",".$record['value']."\r\n");
			}
		}
      }
      fclose($f);
}
// }}}


// {{{ purge_program($arr)
// ROLE purge and check program 
// IN $arr   array containing value of the program
// RET the array purged
function purge_program($arr) {
   $tmp=array();
   asort($arr);
   if(count($arr)>0) {
      foreach($arr as $val) {
         if(($val['value']!=0)&&($val['time_start']!=$val['time_stop'])) {
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


// {{{ optimize_program($arr)
// ROLE optimize a program by deleting uselles value
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
               $tmp[]=$arr[$i];
               $i=$i+1;
            }
         }
         $tmp[]=$arr[$i];
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


/// {{{ create_plugconf_from_database($nb)
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
// Cette ligne permet de définir une deuxième capteur permettant la marche forcé ou l’arrêt forcé de l'effecteur.
// {T|H|N} : doit être T pour température ou H pour humidité ou N pour null. En cas de null, les valeur suivante sont rempli au pif.
// {+|-} : doit être + Si l'effecteur doit se mettre en {ON|OFF} en dessus de la consigne ou - si le contraire
// {1|0} : Est_ce que l'effecteur doit être ON (1) ou off (0) lorsque les conditions sont remplises.
// {VALEUR} : Valeur X 10 sur 3 digits
// 0x0D0A : caractère de fin de ligne (CR LF, \r\n)
// Exmple "SEC:H+1800" : L'effecteur doit être On (1) si l'humidité (H) devient supérieur (+) à 80,0% RH (800).
// 
// ROLE read plugs configuration from the database and format its to be write into a sd card
// IN $nb   the number of plug to read
//    $out   error or warning message
// RET an array containing datas
function create_plugconf_from_database($nb=0,&$out="") {
   if($nb>0) {
      $db = db_priv_start();
      $sql = <<<EOF
SELECT * FROM `plugs` WHERE id <= {$nb}
EOF;
      $db->setQuery($sql);
      $res = $db->loadAssocList();
      $ret=$db->getErrorMsg();
      if((isset($ret))&&(!empty($ret))) {
         $out=$out.__('ERROR_SELECT_SQL').$ret;
      }
      
      if(!db_priv_end($db)) {
         $out=$out.__('PROBLEM_CLOSING_CONNECTION');
      }

      if(count($res)>0) {
         $arr=array();
         foreach($res as $data) {
            if($data['PLUG_TOLERANCE']) {
               $tol=$data['PLUG_TOLERANCE']*10;
               while(strlen($tol)<3) {
                  $tol="0$tol";
               }
            } else {
               $tol="000";
            }

            if($data['PLUG_TYPE']=="ventilator") {
               $reg="REG:T+${tol}";
               $sec="SEC:T+1000";
            } else if($data['PLUG_TYPE']=="heating") {
               $reg="REG:T-${tol}";
               $sec="SEC:N-1000";
            } else if($data['PLUG_TYPE']=="humidifier") {
               $reg="REG:H-${tol}";
               $sec="SEC:N-1000";
            } else if($data['PLUG_TYPE']=="dehumidifier") {
               $reg="REG:H+${tol}";
               $sec="SEC:N+1000";
            } else {
               $reg="REG:N+000";
               $sec="SEC:N+0000";
            }
            
            $arr[]="$reg"."\r\n"."$sec";
         }
         return $arr;
      }
   } 
}

// }}}


/// {{{ create_program_from_database()
// ROLE read programs from the database and format its to be write into a sd card
// IN $out   error or warning message
// RET an array containing datas
function create_program_from_database(&$out="") {
   $db = db_priv_start();
   $sql = <<<EOF
SELECT * FROM `programs` ORDER by `time_start`
EOF;
   $db->setQuery($sql);
   $res = $db->loadAssocList();
   $ret=$db->getErrorMsg();
   if((isset($ret))&&(!empty($ret))) {
          $out=$out.__('ERROR_SELECT_SQL').$ret;
   }

   $sql = <<<EOF
SELECT * FROM `programs` WHERE time_start = "000000" ORDER by `time_start`
EOF;
   $db->setQuery($sql);
   $first = $db->loadAssocList();
   $ret=$db->getErrorMsg();
   if((isset($ret))&&(!empty($ret))) {
          $out=$out.__('ERROR_SELECT_SQL').$ret;
   }

   $sql = <<<EOF
SELECT * FROM `programs` WHERE time_stop = "235959" ORDER by `time_start`
EOF;
   $db->setQuery($sql);
   $last = $db->loadAssocList();
   $ret=$db->getErrorMsg();
   if((isset($ret))&&(!empty($ret))) {
          $out=$out.__('ERROR_SELECT_SQL').$ret;
   }
   
   if(!db_priv_end($db)) {
          $out=$out.__('PROBLEM_CLOSING_CONNECTION');
   }

   $j=1;
   $data=array();
   $data[0] = "";
   date_default_timezone_set('UTC');

   if(count($first)>0) {
      while( $j <= 16 ) {
         $result=find_value_for_plug($first,"000000",$j);
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
   while($count<count($event)) {
      if((isset($event[$i]))) {
         $evt[]=$event[$i];   
         $count=$count+1;
      }
      
      $i=$i+1;
   }
   $event=$evt;

   if(count($event)>0) {
      for($i=0;$i<count($event);$i++) {
         $data[$i+1] = "";
         $j=1;
         while($j<=16) {
            $result=find_value_for_plug($res,$event[$i],$j);
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
       while($j<=16) {
            $result=find_value_for_plug($last,"235959",$j);
            $data[$count]=$data[$count]."$result";
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
// ROLE read calendar from the database and format its to be write into a sd card
// IN   $out   error or warning message
// RET an array containing datas
function create_calendar_from_database(&$out="") {
   $year=date('Y');
        $db = db_priv_start();
        $sql = <<<EOF
SELECT `Subject`,`StartTime`,`EndTime` FROM `jqcalendar` WHERE `StartTime` LIKE "{$year}-%"
EOF;
        $db->setQuery($sql);
        $res = $db->loadAssocList();
        $ret=$db->getErrorMsg();
        if((isset($ret))&&(!empty($ret))) {
                $out=$out.__('ERROR_SELECT_SQL').$ret;
        } else {
      $data=array();   
      foreach($res as $val) {
        
         $s=array();
         $line="";
         $month=substr($val['StartTime'],5,2);
         $day=substr($val['StartTime'],8,2);
         $year=substr($val['StartTime'],0,4);

         $end_month=substr($val['EndTime'],5,2);
         $end_day=substr($val['EndTime'],8,2);
         $end_year=substr($val['EndTime'],0,4);
      
         $count=0;
         $number=0;
         for($i=0;$i<strlen($val['Subject']);$i++) {
               $count=$count+1;
               $line=$line.$val['Subject'][$i];
               if($count==13) {
                  $s[]=$line;
                  $line="";
                  $count=0;
                  $number=$number+1;
               }

               if("$number"=="255") {
                  break;
               }
         }

         if(("$count"!="13")&&("$number"!="255")) {
            $s[]=$line;
            $number=$number+1;
         }

         while(strlen($number)<3) {
            $number="0$number";
         }

         $data[]=array(
               "month" => $month,
               "day" => $day,
               "number" => $number,
               "subject" => $s
         );
         unset($s);
      }
      return $data;
   }
}
// }}}


// {{{ find_value_for_plug($data,$time,$plug)
//ROLE find if a plug is concerned by a time spaces and return its value
// IN   $data   array to look for time space
//   $time   the time to find
//   $plug   the specific plug concerned
// RET   000 if the plug is not concerned or if its value is 0, 0001 else
function find_value_for_plug($data,$time,$plug) {
   for($i=0;$i<count($data);$i++) {
      if(($data[$i]['time_start']<=$time)&&($data[$i]['time_stop']>=$time)&&($data[$i]['plug_id']==$plug)) {
		if($data[$i]['time_stop']==$time) {
            		if($data[$i]['time_stop']=="235959") {
               			$ret=$data[$i]['value']*10;
            		} else {
               			$ret="000";
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


// {{{ check_empty_programs($nb_plugs)
//ROLE check if no programs have been defined yet
// IN  $nb_plugs   number of plugs used
// RET true if there is a program defined, false else
function check_programs($nb_plugs=0) {
   if($nb_plugs>0) {
      $db = db_priv_start();
           $sql = <<<EOF
SELECT * FROM `programs`
EOF;
           $db->setQuery($sql);
           $res = $db->loadAssocList();
           $ret=$db->getErrorMsg();
      if(!isset($res)||(empty($res))) {
         return false;
      } else {
         return true;
      }
   } else {
      return false;
   }
}
// }}}

?>

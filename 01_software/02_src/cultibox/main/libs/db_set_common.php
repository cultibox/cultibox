<?php

// {{{ db_update_logs()
// ROLE update logs table in the Database with the array $arr
// IN $arr   array containing values to update database
//    $out   error or warning message 
// RET none
function db_update_logs($arr,&$out) {
   $index=0;
   $return=1;
   $sql = <<<EOF
INSERT INTO `logs`(`timestamp`,`record1`, `record2`,`date_catch`,`time_catch`,`sensor_nb`) VALUES
EOF;
   foreach($arr as $value) {
      if(empty($value['record1'])) {
        $value['record1']="NULL";
      }
    
      if(empty($value['record2'])) {
        $value['record2']="NULL";
      }

      if((array_key_exists("timestamp", $value))&&(array_key_exists("record1", $value))&&(array_key_exists("record2", $value))&&(array_key_exists("date_catch", $value))&&(array_key_exists("time_catch", $value))&&(array_key_exists("sensor_nb", $value))) {
         if("$index" == "0") {
            $sql = $sql . "(${value['timestamp']}, ${value['record1']},${value['record2']},\"${value['date_catch']}\",\"${value['time_catch']}\",\"${value['sensor_nb']}\")";
         } else {
            $sql = $sql . ",(${value['timestamp']}, ${value['record1']},${value['record2']},\"${value['date_catch']}\",\"${value['time_catch']}\",\"${value['sensor_nb']}\")";
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


// {{{ insert_configuration()
// ROLE set configuration value for specific entries
// IN $key      the key selectable from the database 
//    $value   value of the key to insert
//    $out      errors or warnings messages
// RET none
//Note: if to select a value is limited to 1. Only one configuration is available,
//there isn't a user configuration management yet.
function insert_configuration($key,$value,&$out) {

   $sql = "UPDATE configuration SET " . $key . " = \"" . $value . "\" WHERE id = 1;";

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


// {{{ insert_webcam()
// ROLE set webcam value for specific entries
// IN $key      the key selectable from the database 
//    $value   value of the key to insert
//    $out      errors or warnings messages
// RET none
function insert_webcam($key,$value,&$out) {

   $sql = "UPDATE webcam SET " . $key . " = \"" . $value . "\" WHERE id = 1;";

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


// {{{ insert_program()
// ROLE check and create new plug program
// IN $program     array containing program data 
//    $out      error or warning message
// RET true
// Fonctionnement:
//      Un programme à insérer est passé à la fonction. Celle-ci doit déterminer à quel emplacement placer ce nouveau programme et l'impact qu'il aura sur les
//      programmes déjà enregistrés.
//      Pour cela, on parcours les programmes existant pour déterminer quel intervalle de temps il va impacter en fonction des valeur de début et de fin du programme.
//      Puis on opère les modifications en fonction. Le cas spécial correspond à un cas ou le programme impacte plusieurs intervalle (durée plus grande que la durée de deux espaces de temps);
//      Dans ce cas la, on enregistre les modifications opérée sur l'intervalle de temps que l'on est en train de regarder, on sauvegarde les autres actions, on diminue l'intervalle de temps du
//      programme à enregistrer puis on relance la fonction de comparaison avec ce nouvel intervalle raccourcis. Lorsque l'intervalle de l'action à insérer est assez petit pour tenir entre deux espaces
//      de temps on reprend l'insertion classique.
//      Une fois le nouveau programme calculé, on le passe dans deux fonctions permettant: de supprimer des valeurs résiduelles (qui ne devraient pas être la) et d'optimiser le programme
//      c'est à dire de joindre des espaces de temps contiguë ayant la même valeur
function insert_program($program,&$out,$indexNumber) {
    $ret=true;
    $data_plug=get_data_plug($program[0]['selected_plug'],$out,$indexNumber);
    $tmp=array();
    if(count($program>0)) clean_program($program[0]['selected_plug'],$program[0]['number'],$out);

    if(count($data_plug)==0) {
        foreach($program as $progr) {
            $prg[]=array(
                "time_start" => str_replace(':','',$progr['start_time']),
                "time_stop" => str_replace(':','',$progr['end_time']),
                "value" => $progr['value_program'],
                "type" => $progr['type'],
                "number" => $progr['number']
            );
        }
        $tmp=purge_program($prg);
   } else {
        foreach($program as $progr) {
            $type=$progr['type'];
            $number = $progr['number'];
            if(count($tmp)>0) {
                $data_plug=$tmp;
                unset($tmp);
                $tmp=array();
            }

            $data_plug[] = array(
                "time_start" => "240000",
                "time_stop" => "240000",
                "value" => "0",
                "type" =>  "$type",
                "number" => $number
            );

            $start_time=str_replace(':','',$progr['start_time']);
            $end_time=str_replace(':','',$progr['end_time']);
            $value=$progr['value_program'];
            $current= array(
                "time_start" => "$start_time",
                "time_stop" => "$end_time",
                "value" => "$value",
                "type" =>  "$type",
                "number" => $number
            );

            $first=array(
                    "time_start" => "000000",
                    "time_stop" => "000000",
                    "value" => "0", 
                    "type" =>  "$type",
                    "number" => $number
            );
            asort($data_plug);
            $continue="1";

            $chk_stop=false;
            $chk_test=true;

            while(!$chk_stop) {
                foreach($data_plug as $data) { 
                    if(!$chk_test) {
                        $tmp[]=$data;
                    } else {
                        $chk_stop=true;
                        if((!isset($last))||(empty($last))) {
                            $last=$data;    
                        }

                        if(("$continue"=="1")) {
                            $continue=compare_data_program($first,$last,$current,$tmp);
                        } else {
                            $continue="1";
                        }

                        if("$continue"!="2") {   
                            $first=$last;
                            unset($last);
                        } else  {
                            unset($last);
                            unset($first);
                            $chk_test=false;
                        }
                    }
                }

                if(!$chk_test) {
                    $chk_stop=false;
                    $first=array(
                        "time_start" => "000000",
                        "time_stop" => "000000",
                        "value" => "0",
                        "type" =>  "$type",
                        "number" => $number
                    );
                    $continue="1";
                    unset($data_plug);
                    $data_plug=$tmp;
                    asort($data_plug);
                    unset($tmp);
                    $tmp=array();
                    $chk_test=true;
                }
            }

            if($GLOBALS['DEBUG_TRACE']) {
                echo "Before purge:<br />";
                print_r($tmp);
                echo "<br />";
            }

            $tmp=purge_program($tmp);

            if($GLOBALS['DEBUG_TRACE']) {
                echo "<br />Before optimize:<br />";
                print_r($tmp);
                echo "<br />";
            }
            $tmp=optimize_program($tmp);

            if($GLOBALS['DEBUG_TRACE']) {
                echo "<br />Program to be recorded:<br />";
                print_r($tmp);
                echo "<br />";
            }
        }
    }

    if(count($tmp)>0) {
        if(!insert_program_value($program[0]['selected_plug'],$tmp,$out)) 
            $ret=false;
    }

    return $ret;
}
// }}}

// {{{ insert_program_value()
// ROLE insert a program into the database
// IN $plugid           id of the plug
//    $program          array containing programs datas
//    $out              error or warning message
// RET false is there is an error, true else
function insert_program_value($plugid,$program,&$out) {
    $sql="INSERT INTO `programs`(plug_id, time_start, time_stop, value, type, number) VALUES";
    $count=0;

    foreach($program as $prog) {
        $start_time=$prog['time_start'];
        $end_time=$prog['time_stop'];
        $value=$prog['value'];
        $type=$prog['type'];
        $number = $prog['number'];

        if($count==0) {
            $sql = $sql." ('{$plugid}',\"{$start_time}\",\"{$end_time}\",'{$value}','{$type}','{$number}')";
            $count=1;
        } else {
            $sql = $sql.", ('{$plugid}',\"{$start_time}\",\"{$end_time}\",'{$value}','{$type}','{$number}')";
        }
    }

    $sql=$sql.";";
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
function clean_program($plug_id,$programm_index,&$out) {

   $sql = "DELETE FROM programs WHERE plug_id = \"$plug_id\" AND number = \"$programm_index\" ";
   
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


// {{{ purge_program()
// ROLE purge,check and format program 
// IN $arr        array containing value of the program
// RET the array purged
function purge_program($arr) {
   $tmp=array();
   asort($arr);
   if(count($arr)>0) {
      foreach($arr as $val) {
         if($val['value']==0 || 
            $val['time_start']==$val['time_stop'] ||
            strcmp($val['value'],"")==0 ||
            strcmp($val['time_start'],"")==0 && strcmp($val['time_stop'],"")==0) {
            //nothing to do
         } else {
               $tmp_arr = array(
                    "time_start" => $val['time_start'],
                    "time_stop" => $val['time_stop'],
                    "value" => $val['value'],
                    "type" => $val['type'],
                    "number" => $val['number']
               );
               $tmp[]=$tmp_arr;
             }
        }
      return $tmp;
   }
}
// }}}


// {{{ update_sensor_db_type()
// ROLE update sensor's type list 
// IN     index    array containing sensor's type to be updated
// RET false if an error occured, true else
function update_sensor_type($index) {
    // If there is no sensors, return
    if(count($index)==0) 
        return false;

    $sql="";

    foreach($index as $key => $value) {
        if(($value != "0")&&($value!="")) {
            $sql = $sql . "UPDATE sensors SET  type = \"{$value}\" WHERE id = ${key};";
        }
    }

    // If there is no update, return
    if ($sql == "")
        return false;

    $db=db_priv_pdo_start();
    try {
        $db->exec($sql);
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
/// }}}


// {{{ check_and_update_column_db()
// ROLE update a table
// IN     index    array containing sensor's type to be updated
// RET false if an error occured, true else
function check_and_update_column_db ($tableName, $officialColumn) {

    $db = db_priv_pdo_start("root");

   // Check if columns are present
    $sql = "SHOW COLUMNS FROM " . $tableName . " ;";
    
    //
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $ret = $e->getMessage();
        print_r($ret);
    }
    
    // Check if all columns are usefull
    $colToDelete = array();
    $colToChange = array();
    foreach ($res as $col)
    {
        $usefull = 0;
        foreach ($officialColumn as $needColumn)
        {
            if ($col['Field'] == $needColumn['Field']) {
                $default="NULL";
                if(isset($needColumn['default_value'])) {
                    $default=$needColumn['default_value'];
                }

                if(strcmp(strtoupper($default),"")==0) $default="NULL";
                if(strcmp(strtoupper($col['Default']),"")==0) $col['Default']="NULL";

                if((strtolower($col['Type']) == strtolower($needColumn['Type']))&&(strtolower($col['Default']) == strtolower($default))) {
                    $usefull = 1;   
                    break;
                } else {
                    $usefull = 2;
                    break;
                }
            }
        }
        
        if ($usefull == 0) $colToDelete[] = $col['Field'];
        if ($usefull == 2) $colToChange[] = $col['Field'];
        
    }
    
    // Check if all columns are present
    $colToAdd = array();
    
    foreach ($officialColumn as $needColumn)
    {
        $present = 0;
        foreach ($res as $col)
        {
            if ($col['Field'] == $needColumn['Field'] &&
                strtolower($col['Type']) == strtolower($needColumn['Type']) )
            {
                $present = 1;
                break;
            }
        }
        
        if ($present == 0)
            $colToAdd[] = $needColumn['Field'];
        
    }


    // Delete not used column
    foreach ($colToDelete as $col)
    {
    
        $sql = "ALTER TABLE " . $tableName . " DROP " . $col . ";";
        
        // Delete column
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
        
    }
    
    // Add column not present
    foreach ($colToAdd as $col)
    {
    
        $sql = "ALTER TABLE " . $tableName . " ADD " . $col . " " . $officialColumn[$col]['Type'];
        
        if (array_key_exists('carac', $officialColumn[$col]))
        {
            $sql = $sql . " ".$officialColumn[$col]['carac'] . " ";
            
        } 

        if (array_key_exists('default_value', $officialColumn[$col]))
        {
            $sql = $sql . " DEFAULT '" . $officialColumn[$col]['default_value'] . "' ";
        }
        
        $sql = $sql . ";" ;
        
        // Add column
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
        
    }

   
    // Change column :
    foreach ($colToChange as $col)
    {

        $sql = "ALTER TABLE " . $tableName . " CHANGE " . $col . " " . $col . " " .$officialColumn[$col]['Type'];

        if (array_key_exists('carac', $officialColumn[$col]))
        {
            $sql = $sql . " ".$officialColumn[$col]['carac'] . " ";

        }

        if (array_key_exists('default_value', $officialColumn[$col]))
        {
            $sql = $sql . " DEFAULT '" . $officialColumn[$col]['default_value'] . "' ";
        }
        
        $sql = $sql . ";" ;

        // Change column
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
        
    }

    $db = null;

}
/// }}}

//{{{ check_database()
// ROLE check and update database
function check_database() {
    // Check program_index database consitency
    program\check_db();

    // Check calendar database consitency
    calendar\check_db();

    // Check configuration DB
    configuration\check_db();

    //Check information table
    informations\check_db();

    //Check sensors table:
    sensors\check_db();

    //Check plugs table:
    plugs\check_db();

    //Check logs table:
    logs\check_db();

    //Check webcam table:
    webcam\check_db();

    //Check power table:
    power\check_db();

    //Check wifi table:
    cultipi\check_db();

}
//}}}


//{{{ check_sensors_def()
// ROLE check sensors definition
function check_sensors_def() {
    $sql = <<<EOF
SELECT * FROM `sensors`
EOF;
    $db=db_priv_pdo_start();
    try {
        $sth=$db->prepare("$sql");
        $sth-> execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
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

    $tab=array();
    if(count($res)>0) {
        foreach($res as $sensor) {
            if((!array_key_exists($sensor['type'],$GLOBALS['SENSOR_DEFINITION']))&&($sensor['type']!=0)) {
               $tab[]=$sensor['id'];
            }
        }
    }

    if(count($tab)>0) {
         $sql=" ";
         foreach($tab as $index) {
            $sql = $sql." UPDATE `sensors` SET type=0 WHERE id=".$index."; ";
         }

        echo "$sql";

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
//}}}

?>

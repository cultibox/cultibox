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
// IN $arr	array containing values to update database
//    $out	error or warning message 
// RET none
function db_update_logs($arr,&$out="") {
	$db = db_priv_start();
	$index=0;
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
	}
	if(!db_priv_end($db)) {
		$out=$out.__('PROBLEM_CLOSING_CONNECTION');	
	}
}
// }}}


// {{{ get_graph_array(&$res,$key,$startdate,$out)
// ROLE get array needed to build graphics
// IN $res      	the array containing datas needed for the graphics
//    $key		the key selectable from the database (temperature,humidity...)
//    $startdate	date (format YYYY-MM-DD) to check what datas to select
//    $out		errors or warnings messages
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
// IN $key	the key selectable from the database 
//    $out	errors or warnings messages
// RET $res	value of the key	
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
//    $value	value of the key to insert
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
//    $id	id of the plug
//    $out      errors or warnings messages
// RET $res	value result for the plug configuration entrie
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
//    $value	value of the configuration field to update
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


// {{{ get_plugs_names($nb,$out)
// ROLE get plugs informations (name and id)
// IN $id      id of the plug
//    $out      errors or warnings messages
// RET return an array containing plugid and its name
function get_plugs_names($nb=0,$out="") {
        $db = db_priv_start();
        $sql = <<<EOF
SELECT `id` , `PLUG_NAME`
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
// IN $selected_plug	plug id to select
//    $out      errors or warnings messages
// RET plug data formated for highchart
function get_data_plug($selected_plug="",$out="") {
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
// IN $plug_id		id of the plug
//    $start_time	start time for the program
//    $end_time		end time for the program
//    $value		value of the program
//    $out		error or warning message
// RET none
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
	if(count($data_plug)>0) {
		if($data_plug[0][time_start]=="000000") {
			$first= array(
         		       "time_start" => "$data_plug[0][time_start]",
                		"time_stop" => "$data_plug[0][time_stop]",
                		"value" => "$data_plug[0][value]"
        		);	
		}
	}
	if((empty($first))||(!isset($first))) {
		$first= array(
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
	$continue=true;

	clean_program($plug_id,$out);
	if(count($data_plug)>1) {
		foreach($data_plug as $data) {	
			if((empty($last))||(!isset($last))) {
				$last = $data;
				
			} 

			
			if($continue) {
				$continue=compare_data_program($first,$last,$current,$tmp);
			} else {
				$continue=true;
			}
			$first=$last;
			unset($last);
		}


		$tmp=purge_program($tmp);
		if(count($tmp)>0) {
			foreach($tmp as $new_val) {
				insert_program_value($plug_id,$new_val[time_start],$new_val[time_stop],$new_val[value],$out);	
			}
		}
	} else {
		if($value!=0) {
			insert_program_value($plug_id,$start_time,$end_time,$value,$out);
		}
	}
}
// }}}


// {{{ compare_data_program($first,$last,$current,&$tmp)
// ROLE compare and format 3 values of the pgroram graph
// IN $first		first value to compare
//    $last		last value to compare
//    $current		current value submitted by user to be added
//    $tmp		array to save datas
// RET false if the function has treated the $last value and we have to skip it in the next call of the function, true else.
function compare_data_program($first,$last,&$current,&$tmp) {
	if(($current[time_start]>=$first[time_start])&&($current[time_stop]>$last[time_stop])&&($current[time_start]<=$last[time_stop])&&($current[time_start]<=$last[time_start])) {
		//si l'echantillon est dans l'interval mais qu'il deborde
		$new_current= array (
			"time_start" => "$current[time_start]",
			"time_stop" => "$last[time_stop]",
			"value" => "$current|value]"
		);
		$ret = compare_data_program($first,$last,$new_current,$tmp);
		$current[time_start] = $last[time_stop];
		return true;
	} else if(($current[time_start]>=$first[time_start])&&($current[time_stop]<=$last[time_stop])) {
		// Si l'éxchantillon est dans l'interval à modifier
		if(($current[time_start]>$first[time_stop])&&($current[time_stop]<$last[time_stop])) {
				//s'il n'y a rien à modifier on ajoute la valeur
				if($first[value]!=0) {
					$tmp[]=$first;
				}
				if($current[value]!=0) {
					$tmp[]=$current;
				}
				return true;
		} else if($current[time_stop]<=$first[time_stop]) {
			//si l'echantillon est dans le premier interval
				if($current[value]==0) {
					if(($current[time_start]!=$first[time_start])&&($current[time_stop]!=$first[time_stop])) {
						//s'il est vraiment dans l'interval et qu'il n'englobe pas tout l'interval
						$save_stop=$first[time_stop];
						$first[time_stop]=$current[time_start];
						$new= array(
							    "time_start" => "$current[time_stop]",
					                    "time_stop" => "$save_stop",
                   					    "value" => "1"
						);
						$tmp[]=$first;
						$tmp[]=$new;
						return true;
					} else if(($current[time_start]==$first[time_start])&&($current[time_stop]==$first[time_stop])) {
						//S'il englobe tout l'interval
						return true;
					} 
				}
		} else if(($current[time_start]==$first[time_stop])&&($current[time_stop]==$last[time_start])) {
			//si l'echantillon est pile entre les deux interval
			if($current[value]==1) {
				$first[time_stop]=$last[time_stop];
				$tmp[]=$first;
				return false;
			}
			return true;
		} else if(($current[time_stop]<$last[time_start])&&($current[time_start]>=$first[time_start])&&($current[time_start]<$first[time_stop])) {
			//si l'echantillon est dans le premier interval et qu'il en sort mais s'arrete avant le second
			if($current[value]==0) {
				$first[time_stop]=$current[time_start];
				$tmp[]=$first;
				return true;
			} else {
				$first[time_stop]=$current[time_stop];
				$tmp[]=$first;
				return true;
			}
                } else if($current[time_stop]>$last[time_start]) {
			//si l'echantillon déborde sur le dernier interval
			if($current[time_stop]<=$last[time_stop]) {
				//mais qu'il ne touche pas d'autre interval
				if($current[value]==0) {
					//s'il est dans le premier, entre les deux et sur le deuxieme interval:
					if(($current[time_start]==$first[time_start])&&($current[time_stop]==$last[time_stop])) {
						return false;
					} else if(($current[time_start]>$first[time_start])&&($current[time_start]<=$first[time_stop])) {
						//suppression du deuxieme interval
						if($current[time_stop]==$last[time_stop]) {
							$first[time_stop] = $current[time_start];
							$tmp[] = $first;
							return false;
						} else {
							$first[time_stop] = $current[time_start];
							$last[time_start] = $current[time_stop];
							$tmp[]=$first;
							$tmp[]=$last;
							return false;
						}
					} else if($current[time_start]>$first[time_stop]) {
						if($current[time_stop]==$last[time_stop]) {
							$tmp[]=$first;
							return false;	
						} else {
							$last[time_start]=$current[time_start];
							$tmp[]=$first;
							$tmp[]=$last;
							return false;	
						}
					
					}
					//return true;
				} else {
					if(($current[time_start]>=$first[time_start])&&($current[time_start]<=$first[time_stop])) {	
						$first[time_stop]=$last[time_stop];
						$tmp[]=$first;
						return false;
					} else if(($current[time_start]>$first[time_stop])) {
						$tmp[]=$first;
						$last[time_start]=$current[time_start];
						$tmp[]=$last;		
						return false;
					}	

				}
			} 
		}
		return true;
	} else {
		if($first[value]!=0) {
			$tmp[]=$first;
		}
		return true;
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
//    $out		error or warning message
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


// {{{ purge_program($arr)
// ROLE purge and check program 
// IN $arr	array containing value of the program
// RET the array purged
function purge_program($arr) {
	$tmp=array();
	asort($arr);
	if(count($arr)>0) {
		foreach($arr as $val) {
			if(($val[value]!=0)&&($val[time_start]!=$val[time_stop])) {
					$tmp_arr = array(
						"time_start" => $val[time_start],
						"time_stop" => $val[time_stop],
					 	"value" => $val[value]
					);
					$tmp[]=$tmp_arr;
	    		}
		} 
	return $tmp;
	}
}
// }}}}


/// {{{ create program_from_database()
// ROLE read programs from the database and format its to be write into a sd card
// IN none
// RET an array containing datas
function create_program_from_database() {
	$db = db_priv_start();
        $sql = <<<EOF
SELECT * FROM `programs` WHERE time_start != "000000" AND time_stop != "235959" ORDER by `time_start`
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

	foreach($res as $result) {
		$event[]=$result[time_start];
		$event[]=$result[time_stop];
	}
	$event = array_unique ($event);
	
	if(count($event)>0) {
		for($i=0;$i<count($event);$i++) {
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


// {{{ find_value_for_plug($data,$time,$plug)
//ROLE find if a plug is concerned by a time spaces and return its value
// IN	$data	array to look for time space
//	$time	the time to find
//	$plug	the specific plug concerned
// RET	000 if the plug is not concerned or if its value is 0, 0001 else
function find_value_for_plug($data,$time,$plug) {
	for($i=0;$i<count($data);$i++) {
		if(($data[$i][time_start]<=$time)&&($data[$i][time_stop]>=$time)&&($data[$i][plug_id]==$plug)) {
			if($data[$i][time_stop]==$time) {
				if($data[$i][time_stop]=="235959") {
					return "001";
				} else {
					return "000";
				}
			}

			if($data[$i][value] == "1") {
				return "001";
			}
		}
	}
	return "000";
}

?>

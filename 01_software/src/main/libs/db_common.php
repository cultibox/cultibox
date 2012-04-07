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
	$res = $db->loadResult();
	$ret=$db->getErrorMsg();
	if((isset($ret))&&(!empty($ret))) {
		$out=$out.__('ERROR_UPDATE_SQL').$ret;
	}

	if(!db_priv_end($db)) {
		$out=$out.__('PROBLEM_CLOSING_CONNECTION');
	}
}
// }}}

?>

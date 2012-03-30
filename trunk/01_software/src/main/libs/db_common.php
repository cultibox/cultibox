<?php

// {{{ db_priv_start()
function db_priv_start() {
	if (isset($GLOBALS['dbconn'])) {
		$dbconn = &$GLOBALS['dbconn'];
	} else {
		$option = array(); //prevent problems
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
// IN $dbconn as a DB object
// RET none
function db_priv_end($dbconn) {
	$dbconn->disconnect();
}
// }}}


// {{{ db_update_logs($arr,&$out)
function db_update_logs($arr,&$out) {
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
	db_priv_end($db);
}
// }}}


// {{{ get_graph_array(&$res,$key,$startdate,$out)
function get_graph_array(&$res,$key,$startdate,$out) {
	$db = db_priv_start();
        $sql = <<<EOF
SELECT ${key} FROM `logs` WHERE date_catch LIKE "{$startdate}"
EOF;
        $res = $db->getCol($sql);
}
// }}}


// {{{ get_max_value($key,$startdate,&$return)
function get_max_value($key,$startdate,&$return) {
	$db = db_priv_start();
	$sql = <<<EOF
SELECT max(${key}/100) FROM `logs` WHERE date_catch LIKE "{$startdate}" 
EOF;

	$db->setQuery($sql);
	$res = $db->loadResult();
	db_priv_end($db);
	return $res;
}
// }}}


// {{{ get_configuration($key,&$return)
function get_configuration($key,&$return) {
        $db = db_priv_start();
        $sql = <<<EOF
SELECT {$key} FROM `configuration` WHERE id = 1
EOF;
	$db->setQuery($sql);
        $res = $db->loadResult();
	db_priv_end($db);
        return $res;
}
// }}}


// {{{ insert_configuration($key,$value,&$return)
function insert_configuration($key,$value,&$return) {
        $db = db_priv_start();
        $sql = <<<EOF
UPDATE `configuration` SET  {$key} = "{$value}" WHERE id = 1
EOF;
	$db->setQuery($sql);
	$db->query();
	db_priv_end($db);
}
// }}}

?>

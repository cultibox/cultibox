<?php

// {{{ getvar()
// ROLE Used to get access to GET or POST values for '$varname'
// IN $varname as a string
// RET the string value as read in $_GET[] or $_POST[]
function getvar($varname) {
        $tmp = false;

        // The mess with the *x appended is a workaround for some browser
        // versions that append it when you want to get information for some
        // image buttons

        if (isset($_GET["$varname"])) {
                $tmp = $_GET["$varname"];
        } elseif (isset($_GET["$varname"."x"])) {
                $tmp = true;
        } elseif (isset($_GET["$varname"."_x"])) {
                $tmp = true;
        } elseif (isset($_POST["$varname"])) {
                $tmp = $_POST["$varname"];
        } elseif (isset($_POST["$varname"."x"])) {
                $tmp = true;
        } elseif (isset($_POST["$varname"."_x"])) {
                $tmp = true;
        }

        // FIXME: simple html cleanup
        if (is_array($tmp)) {
                return $tmp;
        } else {
                return stripslashes(htmlentities($tmp));
        }
}
// }}}


function check_empty_string($value) {
	$value=str_replace(' ','',$value);
	$value=str_replace(CHR(13).CHR(10),'',$value); 
	
	if("$value" == "") {
		return 0;
	} else {
		return 1;
	}


}

function get_log_value($file,&$array_line) {
	$handle = fopen("$file", 'r');
	$index=0;
	if ($handle)
	{
		while (!feof($handle))
		{
			$buffer = fgets($handle);
			if(!check_empty_string($buffer)) {
				break;
			} else {
				$temp = explode("\t", $buffer);
				$date_catch="20".substr($temp[0], 0, 2)."-".substr($temp[0],2,2)."-".substr($temp[0],4,2);
				$time_catch=substr($temp[0], 8,6);
				$array_line[] = array(
					"timestamp" => $temp[0],
					"temperature" => $temp[1],
					"humidity" => $temp[2],
					"date_catch" => $date_catch,
					"time_catch" => $time_catch
				);
				$index=$index+1;
			}
		}
		fclose($handle);
		if("$index" != "0") {
			clean_log_file($file);
		}
	}
}

function clean_log_file($file) {
	$filetpl = 'templates/data/empty_file.tpl';
	copy($filetpl, $file);
}

function get_format_hours(&$arr) {
	$nb_element=count($arr);
	for($nb=0; $nb <= $nb_element ; $nb++)
	{
		$hours=substr($arr[$nb], 0, 2);
		$minutes=substr($arr[$nb], 2, 2);
		$arr[$nb]="${hours}:${minutes}";
	}
}

function get_format_month(&$arr,$freq,$month,$year) {
	$nb_day=date('t',mktime(0, 0, 0, $month, 1, $year)); 
	$nb_ech=$nb_day*(1440/$freq);
	for($i=1;$i<$nb_ech;$i++) {
		$val=(int)(($i/(1440/$freq))+1);
		$arr[] = "${val}";
	}
}


?>

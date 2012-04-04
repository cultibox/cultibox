<?php

// {{{ __($msgkey, ...)
// ROLE get the translated message corresponding to $msgkey, the parameters may
// be used in $msgkey by using the sprintf syntax (%s)
// IN $msgkey
// IN (optional)
// RET string translated
static $__translations;
static $__translations_fallback;
static $string_lang;
require_once 'main/libs/l10n.php';

function __() {
        global $__translations;
        global $__translations_fallback;
        global $string_lang;

        if (func_num_args() < 1) {
                die("ERROR: __() called without arguments");
                return "";
        }
        $args = func_get_args();

        if (!isset($__translations)) {
                $__translations = __translations_get($_SESSION['LANG']);
                $__translations_fallback = __translations_get(LANG_FALLBACK);
                if (empty($__translations_fallback)) {
                        die("No translation file");
                }
                $string_lang = array($_SESSION['LANG'] => $__translations);
        }

        $msg = $args[0];
        if (isset($__translations["$msg"])) {
                $msg = $__translations["$msg"];
        } elseif (isset($__translations_fallback["$msg"])) {
                $msg = $__translations_fallback["$msg"];
        } else {
                die("WARNING:L10N: no translation for '$msg'");
        }

        $args[0] = $msg;
        $ret = call_user_func_array('sprintf', $args);
        return $ret;
}


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
	$filetpl = 'main/templates/data/empty_file.tpl';
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

function get_format_month($data) {
	$arr = explode(" ", $data);
	$count=0;
	$moy=0;
	$data_month="";
	foreach($arr as $value) {
		if("$value"=="null") {
			$value=0;
		}
		$moy=($moy + $value)/2;
		$count=$count+1;
		if($count==20) {
			if("$data_month"=="") {
				$data_month="$moy";
			} else {
				$data_month="$data_month, $moy";
			}
			$count=0;
			$moy=0;
		}
	}
	return $data_month;
}



function get_current_lang() {
	$lang =& JFactory::getLanguage();
	return str_replace("-","_",$lang->getTag());
}


function set_lang($lang) {
	$lang=str_replace("_","-",$lang);
	$language =& JFactory::getLanguage();
	$language->setLanguage("${lang}");
	$language->load();
	return true;
}


function get_format_graph($arr) {
	$data="";
	foreach($arr as $value) {
		$hh=substr($value[time_catch], 0, 2);
		$mm=substr($value[time_catch], 2, 2);

		if(("$hh:$mm" != "00:00")&&(empty($data))&&(empty($last_value))) {
			$data=fill_data("00","00","$hh","$mm","null","$data");
		} else if((check_empty_record("$last_hh","$last_mm","$hh","$mm"))&&("$hh:$mm" != "00:00")) {
			$data = fill_data("$last_hh","$last_mm","$hh","$mm","$last_value","$data");
		} else {
			if("$hh:$mm" != "00:00") {
				$data = fill_data("$last_hh","$last_mm","$hh","$mm","null","$data");
			}
		}
		$last_value="$value[record]";
		$last_hh=$hh;
		$last_mm=$mm;
	}
	if("$last_hh:$last_mm" != "23:59") {
		$data=fill_data("$last_hh","$last_mm","24","00","$last_value","$data");
	}
	return $data;
}


function fill_data($fhh,$fmm,$lhh,$lmm,$val,$data) {
	while(strcmp("$fhh:$fmm","$lhh:$lmm")<0) {
		if("$data" == "") {
			$data="$val";
		} else {
			$data=$data.", $val";
		}
		$fmm=$fmm+1;
		if($fmm<10) {
			$fmm="0$fmm";
		}
		
		if("$fmm" == "60") {
			$fmm="00";
			$fhh=$fhh+1;
			if($fhh<10) {
				$fhh="0$fhh";
			}
		}
	}
	return $data;
}


function check_empty_record($last_hh,$last_mm,$hh,$mm) {
		$lhh= 60 * $last_hh + $last_mm;
		$chh= 60 * $hh + $mm;

		if($lhh-$chh<=30) {
			return true;
		} else {
			return false;
		}
}


?>

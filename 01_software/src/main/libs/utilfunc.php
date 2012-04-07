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
//}}}


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

// {{{ check_empty_string($value)
// ROLE check is a string is empty or only composed with CR
// IN $value	string to check
// RET false if the string is empty, true else
function check_empty_string($value="") {
	$value=str_replace(' ','',$value);
	$value=str_replace(CHR(13).CHR(10),'',$value); 
	
	if("$value" == "") {
		return 0;
	} else {
		return 1;
	}
}
//}}}


// {{{ get_log_value($file,&$array_line)
// ROLE get log's values from files and clean it
// IN $file		file to explode
//    $array_line	array to store log's values
// RET none
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
//}}}


// {{{ clean_log_file($file)
// ROLE copy an empty file to clean a log file
// IN $file             file to clean
// RET none
function clean_log_file($file) {
	$filetpl = 'main/templates/data/empty_file.tpl';
	copy($filetpl, $file);
}
//}}}


// {{{ get_format_month($data)
// ROLE using a graphics data string containing value to make an average for month graphics
// IN $data	datas from a graphics containing values for a month
// RET return datas string value containing an average of the input fiel data
function get_format_month($data) {
	$arr = explode(" ", $data);
	$count=0;
	$moy=0;
	$data_month="";
	foreach($arr as $value) {
		if("$value"=="null") {
			$value=0;
		}
		$moy=round(($moy + $value)/2,2);
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
//}}}


// {{{ get_current_lang()
// ROLE get the current language selected for the interface
// IN none
// RET current lang using the l10n format, ex: en-GB with joomla format is replaced by en_GB
function get_current_lang() {
	$lang =& JFactory::getLanguage();
	return str_replace("-","_",$lang->getTag());
}
//}}}


// {{{ set_current_lang()
// ROLE set the Joomla language for the interface
// IN $lang	lang to set using the l10n format (ex: en_GB)
// RET true
function set_lang($lang) {
	$lang=str_replace("_","-",$lang);
	$language =& JFactory::getLanguage();
	$language->setLanguage("${lang}");
	$language->load();

	//FIXME check error
	return true;
}


// {{{ get_format_graph($arr)
// ROLE get datas for the highcharts graphics
// IN $arr	array containing datas
// RET $data	data at the highcharts format (a string)
function get_format_graph($arr) {
	$data="";
	foreach($arr as $value) {
		$hh=substr($value[time_catch], 0, 2);
		$mm=substr($value[time_catch], 2, 2);

		if(("$hh:$mm" != "00:00")&&(empty($data))&&(empty($last_value))) {
			$data=fill_data("00","00","$hh","$mm","null","$data");
		} else if((check_empty_record("$last_hh","$last_mm","$hh","$mm"))&&("$hh:$mm" != "00:00")) {
			$data=fill_data("$last_hh","$last_mm","$hh","$mm","$last_value","$data");
		} else {
			if("$hh:$mm" != "00:00") {
				$data=fill_data("$last_hh","$last_mm","$hh","$mm","null","$data");
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
//}}}


// {{{ fill_data($fhh,$fmm,$lhh,$lmm,$val,$data)
// ROLE fill highcharts data,between two time spaces, using a specific value
// IN $fhh	start hours
//    $fmm	start minutes
//    $lhh	end hours
//    $lmm	end minutes
//    $val	value used to fill time spaces
//    $data	data at the highcharts format (a string)
// RET none
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
//}}}



// {{{ check_empty_record($last_hh,$last_mm,$hh,$mm)
// ROLE check if there is an empty record. An empty reccord is defined is the time spaces
// between two values is greatan than 30minutes
// IN $last_hh	last record hours
//    $last_mm	last record minutes
//    $hh	first record hours
//    $mm	first record minutes
// RET true is there isn't an empty record, false else.
function check_empty_record($last_hh,$last_mm,$hh,$mm) {
		$lhh= 60 * $last_hh + $last_mm;
		$chh= 60 * $hh + $mm;

		if($lhh-$chh<=30) {
			return true;
		} else {
			return false;
		}
}
//}}}


// {{{ check_format_date($date,$type)
// ROLE check date format (month with the format MM ou complete date: YYYY-MM-DD)
// IN $date	date to check
//    $type	the type: month or days
// RET true is the format match the type, false else
function check_format_date($date="",$type,&$return="") {
	$date=str_replace(' ','',"$date");
	if("$type"=="days") {
		if(strlen("$date")!=10) {
			$return=$return.__('ERROR_FORMAT_DATE_DAY');
			return 0;
		}

		if(!preg_match('#^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$#', $date)) {
			$return=$return.__('ERROR_FORMAT_DATE_DAY');
                        return 0;
                }
		return 1;
	}

	if("$type" == "month") {
		if(strlen("$date")!=2) {
			$return=$return.__('ERROR_FORMAT_DATE_MONTH');
			return 0;
		}

		if(!preg_match('#^[0-9][0-9]$#', $date)) {
			$return=$return.__('ERROR_FORMAT_DATE_MONTH');
			return 0;
		}

		if(($date < 1)||($date > 12)) {
			$return=$return.__('ERROR_FORMAT_DATE_MONTH');
			return 0;
		}
		return 1;
	}
	return 0;
}
//}}}
?>

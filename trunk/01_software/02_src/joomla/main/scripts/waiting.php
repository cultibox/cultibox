<?php


if (!isset($_SESSION)) {
   session_start();
}


require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$lang=get_configuration("LANG",$error);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');

if(isset($_POST['startyear'])) {
      $_SESSION['startyear']=$_POST['startyear'];
}

if(isset($_POST['startmonth'])) {
      $_SESSION['startmonth']=$_POST['startmonth'];
}

if(isset($_POST['startday'])) {
      $_SESSION['startday']=$_POST['startday'];
}

if(isset($_POST['select_power'])) {
      $_SESSION['select_power']=$_POST['select_power'];
}

if(isset($_POST['select_plug'])) {
      $_SESSION['select_plug']=$_POST['select_plug'];
}
      
header("Refresh: 1;url=./view-logs");

include('main/templates/waiting.html');

?>

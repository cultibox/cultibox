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
$quick_load=false;

if(isset($_POST['startday'])) {
      $_SESSION['startday']=$_POST['startday'];
      if(!empty($_SESSION['startday'])) {
        $quick_load=true;
      }
}

if(isset($_POST['select_power'])) {
      $_SESSION['select_power']=$_POST['select_power'];
      if(!empty($_SESSION['select_power'])) {
        $quick_load=true;
      }
}

if(isset($_POST['select_plug'])) {
      $_SESSION['select_plug']=$_POST['select_plug'];
      if(!empty($_SESSION['select_plug'])) {
        $quick_load=true;
      }
}

if(isset($_POST['select_sensor'])) {
      $_SESSION['select_sensor']=$_POST['select_sensor'];
      if(!empty($_SESSION['select_sensor'])) {
        $quick_load=true;
      }
}

if(isset($_POST['reset_log'])) {
      $_SESSION['reset_log']=$_POST['reset_log'];
      if(!empty($_SESSION['reset_log'])) {
        $quick_load=true;
      }
}

if(isset($_POST['reset_log_power'])) {
      $_SESSION['reset_log_power']=$_POST['reset_log_power'];
      if(!empty($_SESSION['reset_log_power'])) {
        $quick_load=true;
      }
}

if(isset($_POST['import_log'])) {
      $_SESSION['import_log']=$_POST['import_log'];
      $quick_load=false;
}

if(isset($_POST['startyear'])) {
      $_SESSION['startyear']=$_POST['startyear'];
      if(!empty($_SESSION['startyear'])) {
        $quick_load=false;
      }
}

if(isset($_POST['startmonth'])) {
      $_SESSION['startmonth']=$_POST['startmonth'];
      if(!empty($_SESSION['startmonth'])) {
        $quick_load=false;
      }
}




if($quick_load) {
    $_SESSION['quick_load']="True";
}

if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

if((!isset($sd_card))||(empty($sd_card))||($quick_load)) {      
    header( 'Location: ./view-logs' ) ;
} else {
    header("Refresh: 2;url=./view-logs");
}

include('main/templates/waiting.html');

?>

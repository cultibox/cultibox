<?php

require_once('../../libs/config.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');

$main_error=array();

$nb=getvar("number");
$name=html_entity_decode(getvar("plug_name${nb}"));
$type=getvar("plug_type${nb}");
$tolerance=getvar("plug_tolerance{$nb}");
$power=getvar("plug_power${nb}");
$compute_method=getvar("plug_compute_method${nb}");
$second_regul=get_configuration("ADVANCED_REGUL_OPTIONS",$main_error);
    
if((strcmp("$type","lamp")==0)||(strcmp("$type","other")==0)) {
     $regul="False";
} else {
     $regul=getvar("plug_regul${nb}");
     $regul_senss=getvar("plug_senss${nb}");
     $regul_value=getvar("plug_regul_value${nb}");
     $regul_value=str_replace(',','.',$regul_value);
     $regul_value=str_replace(' ','',$regul_value);
     $second_tol=getvar("plug_second_tolerance${nb}");
     $second_tol=str_replace(',','.',$second_tol);
}
   
$power_max=getvar("plug_power_max${nb}");
if(strcmp($power_max,"VARIO")==0) {
    $power_max=getvar("dimmer_canal${nb}"); 
}

$sensor="";
$plug_count_sensor[$nb]=0;
for($j=1;$j<=$GLOBALS['NB_MAX_SENSOR_PLUG'];$j++) { 
    $tmp_sensor=getvar("plug_sensor${nb}${j}");
    if(strcmp($tmp_sensor,"True")==0) {
         $plug_count_sensor[$nb]=$plug_count_sensor[$nb]+1;
         if(strcmp($sensor,"")!=0) {
             $sensor=$sensor."-".$j;
         } else {
             $sensor="$j";
         }
     }
}

if($sensor=="") {
     $sensor="1";
}

$old_name=get_plug_conf("PLUG_NAME",$nb,$main_error);
$old_type=get_plug_conf("PLUG_TYPE",$nb,$main_error);
$old_tolerance=get_plug_conf("PLUG_TOLERANCE",$nb,$main_error);
$old_power=get_plug_conf("PLUG_POWER",$nb,$main_error);
$old_regul=get_plug_conf("PLUG_REGUL",$nb,$main_error);
$old_senso=get_plug_conf("PLUG_SENSO",$nb,$main_error);
$old_senss=get_plug_conf("PLUG_SENSS",$nb,$main_error);
$old_regul_value=get_plug_conf("PLUG_REGUL_VALUE",$nb,$main_error);
$old_power_max=get_plug_conf("PLUG_POWER_MAX",$nb,$main_error);
$old_sensor=get_plug_conf("PLUG_REGUL_SENSOR",$nb,$main_error);
$old_second_tol=get_plug_conf("PLUG_SECOND_TOLERANCE",$nb,$main_error);
$old_compute_method=get_plug_conf("PLUG_COMPUTE_METHOD",$nb,$main_error);


if((!empty($name))&&(isset($name))&&(strcmp("$old_name","$name")!=0)) {
    $name=mysql_escape_string($name);
    insert_plug_conf("PLUG_NAME",$nb,$name,$main_error);
    $update_program=true;
}
   

if((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0)||(strcmp($type,"pump")==0)) {
    insert_plug_conf("PLUG_TOLERANCE",$nb,$tolerance,$main_error);
    $update_program=true;
} 


if((!empty($power))&&(isset($power))&&(strcmp("$old_power","$power")!=0)) {
    insert_plug_conf("PLUG_POWER",$nb,$power,$main_error);
    $update_program=true;
} else {
    if((empty($power))&&(!empty($reccord))&&(strcmp("$old_power","$power")!=0)) {
         insert_plug_conf("PLUG_POWER",$nb,"",$main_error);
         $update_program=true;
    }
}

if((!empty($power_max))&&(isset($power_max))&&(strcmp("$old_power_max","$power_max")!=0)) {
    insert_plug_conf("PLUG_POWER_MAX",$nb,$power_max,$main_error);
    $update_program=true;
} 


if((!empty($regul))&&(isset($regul))&&(strcmp("$old_regul","$regul")!=0)) {
    insert_plug_conf("PLUG_REGUL",$nb,"$regul",$main_error);
    $update_program=true;
}


if(strcmp("$regul","True")==0) {
 if((!empty($regul_senss))&&(isset($regul_senss))&&(strcmp("$old_senss","$regul_senss")!=0)) {
    insert_plug_conf("PLUG_SENSS",$nb,"$regul_senss",$main_error);
    $update_program=true;
 }
}

if((!empty($second_tol))&&(isset($second_tol))&&(strcmp("$old_second_tol","$second_tol")!=0)) {
    insert_plug_conf("PLUG_SECOND_TOLERANCE",$nb,"$second_tol",$main_error);
    $update_program=true;
}


if((strcmp($type,"other")==0)||(strcmp($type,"lamp")==0)) {
    $regul_senso=getvar("plug_senso${nb}");
} elseif((strcmp($type,"heating")==0)||(strcmp($type,"ventilator")==0)||(strcmp($type,"pump")==0)) {
    $regul_senso="H";
} elseif((strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)) {
    $regul_senso="T";
} else {
    $regul_senso="";
}

if((!empty($regul_senso))&&(isset($regul_senso))&&(strcmp("$old_senso","$regul_senso")!=0)) {
     insert_plug_conf("PLUG_SENSO",$nb,"$regul_senso",$main_error);
     $update_program=true;
}


if((!empty($regul_value))&&(isset($regul_value))&&(strcmp("$old_regul_value","$regul_value")!=0)) {
     insert_plug_conf("PLUG_REGUL_VALUE",$nb,"$regul_value",$main_error);
     $update_program=true;
}

if((!empty($sensor))&&(isset($sensor))&&(strcmp("$old_sensor","$sensor")!=0)) {
    insert_plug_conf("PLUG_REGUL_SENSOR",$nb,"$sensor",$main_error);
    $update_program=true;
}


if($plug_count_sensor[$nb]>1) {
 if((!empty($compute_method))&&(isset($compute_method))&&(strcmp("$old_compute_method","$compute_method")!=0)) {
     insert_plug_conf("PLUG_COMPUTE_METHOD",$nb,"$compute_method",$main_error);
     $update_program=true;
    }
} else {
     insert_plug_conf("PLUG_COMPUTE_METHOD",$nb,"M",$main_error);
}



if((!empty($type))&&(isset($type))&&(strcmp("$old_type","$type")!=0)) {
      insert_plug_conf("PLUG_TYPE",$nb,$type,$main_error);
      $update_program=true;


      //If second regulation is deactivated but the type of plug change, we also change default value for second regulation:
      if(strcmp("$second_regul","False")==0) {
            if((strcmp($type,"other")==0)||(strcmp($type,"lamp")==0)) {
                insert_plug_conf("PLUG_REGUL_VALUE",$nb,"70",$main_error);
                insert_plug_conf("PLUG_SENSO",$nb,"H",$main_error);
                insert_plug_conf("PLUG_SENSS",$nb,"+",$main_error);
                insert_plug_conf("PLUG_SECOND_TOLERANCE",$nb,"0",$main_error);
            } elseif((strcmp($type,"heating")==0)||(strcmp($type,"ventilator")==0)||(strcmp($type,"pump")==0)) {
                insert_plug_conf("PLUG_REGUL_VALUE",$nb,"70",$main_error);
                insert_plug_conf("PLUG_SENSO",$nb,"H",$main_error);
                insert_plug_conf("PLUG_SENSS",$nb,"+",$main_error);
                insert_plug_conf("PLUG_SECOND_TOLERANCE",$nb,"0",$main_error);
            } elseif((strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)) {      
                insert_plug_conf("PLUG_REGUL_VALUE",$nb,"35",$main_error);
                insert_plug_conf("PLUG_SENSO",$nb,"T",$main_error);
                insert_plug_conf("PLUG_SENSS",$nb,"+",$main_error);
                insert_plug_conf("PLUG_SECOND_TOLERANCE",$nb,"0",$main_error);
            } 
        }
}




if(count($main_error)>0) {
    echo json_encode("0");
} else {
    echo json_encode("1");
}

?>

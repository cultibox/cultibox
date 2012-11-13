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

$error="";
$error_plug=Array();
$info_plug=Array();
$info="";
$nb_plugs=get_configuration("NB_PLUGS",$error);
$update_program=false;
$reset=getvar('reset');
$reccord=getvar('reccord');
$pop_up=getvar('pop_up');
$pop_up_message="";
$pop_up_error_message="";
$count_err=false;
$program="";
$update=get_configuration("CHECK_UPDATE",$error);
$version=get_configuration("VERSION",$error);

$info=$info.__('WIZARD_ENABLE_FUNCTION');


if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($error);
   if(!compare_program($program,$sd_card)) {
      $info=$info.__('UPDATED_PROGRAM');
      $pop_up_message=clean_popup_message(__('UPDATED_PROGRAM'));
      save_program_on_sd($sd_card,$program,$error);
   }
   check_and_copy_firm($sd_card,$error);
   check_and_copy_log($sd_card,$error);
   $info=$info.__('INFO_SD_CARD').": $sd_card";
} else {
        $error=$error.__('ERROR_SD_CARD_CONF');
}


if(!isset($pop_up)) {
        $pop_up = get_configuration("SHOW_POPUP",$error);
}


if((isset($reset))&&(!empty($reset))) {
   reset_plug_identificator($error);
   $reset=true;
} 

for($nb=1;$nb<=$nb_plugs;$nb++) {
   $id=getvar("plug_id${nb}");
   $name=getvar("plug_name${nb}");
   $type=getvar("plug_type${nb}");
   $tolerance=getvar("plug_tolerance{$nb}");
   $plug_update=false;
   $power=getvar("plug_power${nb}");
   $regul=getvar("plug_regul${nb}");
   $regul_senss=getvar("plug_senss${nb}");
   $regul_value=getvar("plug_regul_value${nb}");
   $regul_value=str_replace(',','.',$regul_value);
   $regul_value=str_replace(' ','',$regul_value);


   $error_plug[$nb]="";
   $old_name=get_plug_conf("PLUG_NAME",$nb,$error_plug[$nb]);
   $old_type=get_plug_conf("PLUG_TYPE",$nb,$error_plug[$nb]);
   $old_tolerance=get_plug_conf("PLUG_TOLERANCE",$nb,$error_plug[$nb]);
   $old_id=get_plug_conf("PLUG_ID",$nb,$error_plug[$nb]);
   $old_power=get_plug_conf("PLUG_POWER",$nb,$error_plug[$nb]);
   $old_regul=get_plug_conf("PLUG_REGUL",$nb,$error_plug[$nb]);
   $old_senso=get_plug_conf("PLUG_SENSO",$nb,$error_plug[$nb]);
   $old_senss=get_plug_conf("PLUG_SENSS",$nb,$error_plug[$nb]);
   $old_regul_value=get_plug_conf("PLUG_REGUL_VALUE",$nb,$error_plug[$nb]);

   

   if((!empty($id))&&(isset($id))&&(!$reset)) {
      if(strcmp("$old_id","$id")!=0) {
         if(check_numeric_value($id)) {
            while(strlen("$id")<3) {
               $id="0$id";
            }
            insert_plug_conf("PLUG_ID",$nb,"$id",$error_plug[$nb]);
            $update_program=true;
            $plug_update=true;
         } else {
                  $error_plug[$nb]=__('ERROR_PLUG_ID');
         }
       }
   } 

   if((!empty($name))&&(isset($name))&&(!$reset)&&(strcmp("$old_name","$name")!=0)) {
      $name= mysql_escape_string($name);
      insert_plug_conf("PLUG_NAME",$nb,$name,$error_plug[$nb]);
      $update_program=true;
      $plug_update=true;
   }
   

   if((!empty($type))&&(isset($type))&&(!$reset)&&(strcmp("$old_type","$type")!=0)) {
      insert_plug_conf("PLUG_TYPE",$nb,$type,$error_plug[$nb]);
      $update_program=true;
      $plug_update=true;
   }

   if(((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0))) {
         if(check_tolerance_value($type,$tolerance,$error_plug[$nb])) {
            insert_plug_conf("PLUG_TOLERANCE",$nb,$tolerance,$error_plug[$nb]);
            $update_program=true;
            $plug_update=true;
         } 
   } 

   $plug_id{$nb}=get_plug_conf("PLUG_ID",$nb,$error_plug[$nb]);
   if((empty($plug_id{$nb}))||(!isset($plug_id{$nb}))) {
            $plug_id{$nb}=$GLOBALS['PLUGA_DEFAULT'][$nb-1];
   }


   if((!empty($power))&&(isset($power))&&(!$reset)&&(strcmp("$old_power","$power")!=0)) {
      if(check_power_value($power,$error_plug[$nb])) {
      	insert_plug_conf("PLUG_POWER",$nb,$power,$error_plug[$nb]);
      	$update_program=true;
      	$plug_update=true;
     }
   } else {
         if((empty($power))&&(!$reset)&&(!empty($reccord))&&(strcmp("$old_power","$power")!=0)) {
            insert_plug_conf("PLUG_POWER",$nb,0,$error_plug[$nb]);
            $update_program=true;
            $plug_update=true;
         }
   }

   if((!empty($regul))&&(isset($regul))&&(!$reset)&&(strcmp("$old_regul","$regul")!=0)) {
         insert_plug_conf("PLUG_REGUL",$nb,"$regul",$error_plug[$nb]);
         $update_program=true;
         $plug_update=true;
   }

   if((!empty($regul_senss))&&(isset($regul_senss))&&(!$reset)&&(strcmp("$old_senss","$regul_senss")!=0)) {
         insert_plug_conf("PLUG_SENSS",$nb,"$regul_senss",$error_plug[$nb]);
         $update_program=true;
         $plug_update=true;
   }


   if((strcmp($type,"unknown")==0)||(strcmp($type,"lamp")==0)) {
            $regul_senso=getvar("plug_senso${nb}");
   } elseif((strcmp($type,"heating")==0)||(strcmp($type,"ventilator")==0)) {
            $regul_senso="H";
   } elseif((strcmp($type,"humidifier")==0)||(strcmp($type,"deshumidifier")==0)) {
            $regul_senso="T";
   } else {
            $regul_senso="";
   }

   if((!empty($regul_senso))&&(isset($regul_senso))&&(!$reset)&&(strcmp("$old_senso","$regul_senso")!=0)) {
         insert_plug_conf("PLUG_SENSO",$nb,"$regul_senso",$error_plug[$nb]);
         $update_program=true;
         $plug_update=true;
   }

   if((!empty($regul_value))&&(isset($regul_value))&&(!$reset)&&(strcmp("$old_regul_value","$regul_value")!=0)) {
         if(check_regul_value("$regul_value")) { 
            insert_plug_conf("PLUG_REGUL_VALUE",$nb,"$regul_value",$error_plug[$nb]);
            $update_program=true;
            $plug_update=true;
         } else {
            $error_plug[$nb]=$error_plug[$nb].__('ERROR_REGUL_VALUE');
        }

   }

   if(!empty($error_plug[$nb])) {
	$pop_up_error_message=clean_popup_message($error_plug[$nb]);
        if((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0)) {
            insert_plug_conf("PLUG_TOLERANCE",$nb,"$old_tolerance",$error_plug[$nb]);
        } else {
            insert_plug_conf("PLUG_TOLERANCE",0,"$old_tolerance",$error_plug[$nb]);
        }
        insert_plug_conf("PLUG_TYPE",$nb,"$old_type",$error_plug[$nb]); 
        insert_plug_conf("PLUG_NAME",$nb,"$old_name",$error_plug[$nb]);
        insert_plug_conf("PLUG_ID",$nb,"$old_id",$error_plug[$nb]);
        $count_err=true;
   } else if($plug_update) {
                if(("$old_name"!="$name")||("$old_type"!="$type")||("$old_tolerance"!="$tolerance")||("$id"!="$old_id")) {
        		$info_plug[$nb]=__('VALID_UPDATE_CONF');
                }
   }

   $plug_name{$nb}=get_plug_conf("PLUG_NAME",$nb,$error_plug[$nb]);
   $plug_type{$nb}=get_plug_conf("PLUG_TYPE",$nb,$error_plug[$nb]);
   $plug_power{$nb}=get_plug_conf("PLUG_POWER",$nb,$error_plug[$nb]);
   $plug_regul{$nb}=get_plug_conf("PLUG_REGUL",$nb,$error_plug[$nb]);
   $plug_senso{$nb}=get_plug_conf("PLUG_SENSO",$nb,$error_plug[$nb]);
   $plug_senss{$nb}=get_plug_conf("PLUG_SENSS",$nb,$error_plug[$nb]);
   $plug_regul_value{$nb}=get_plug_conf("PLUG_REGUL_VALUE",$nb,$error_plug[$nb]);

   $plug_tolerance{$nb}=get_plug_conf("PLUG_TOLERANCE",$nb,$error_plug[$nb]);
   if($plug_tolerance{$nb}==0) {
      $plug_tolerance{$nb}="1.0";
   }

}



if(($update_program)&&(empty($error))&&(!$count_err)) {
          $info=$info.__('VALID_UPDATE_CONF');
          $pop_up_message=clean_popup_message(__('VALID_UPDATE_CONF'));
} 

// Write file plug01 plug02...
if((isset($sd_card))&&(!empty($sd_card))) {
   // build conf plug array
   $plugconf=create_plugconf_from_database(17,$error);
   if(count($plugconf)>0) {
      write_plugconf($plugconf,$sd_card,$error);
   }
   //write pluga file
   write_pluga($sd_card,$error);
}


if(strcmp("$update","True")==0) {
      $ret=array();
      check_update_available($ret,$error);
      foreach($ret as $file) {
         if(count($file)==4) {
               if(strcmp("$version","$file[1]")==0) {
                  $tmp="";
                  $tmp=__('INFO_UPDATE_AVAILABLE');
                  $tmp=str_replace("</li>","<a href=".$file[3]." target='_blank'>".$file[2]."</a></li>",$tmp);
                  $info=$info.$tmp;
               }
            }
      }
}

include('main/templates/plugs.html');

?>

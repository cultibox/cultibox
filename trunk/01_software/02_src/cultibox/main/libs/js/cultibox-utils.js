// {{{ expand()
// ROLE expand or reduce submenu of the configuration menu
// IN div to be expanded 
// HOW IT WORKS: get div id to be expanded and reduced other menu
//               be passed to php.
// USED BY: templates/configuration.html
function expand(div) {
      var divConfig = document.getElementById('div_user_interface');
      var divSystem = document.getElementById('div_system_interface');
      var divAlarm = document.getElementById('div_alarm_interface');
      var divCard = document.getElementById('div_card_interface');
      var divSubmit = document.getElementById('div_submit_interface');

      var divLabelConfig = document.getElementById('div_user_label');
      var divLabelSystem = document.getElementById('div_system_label');
      var divLabelAlarm = document.getElementById('div_alarm_label');
      var divLabelCard = document.getElementById('div_card_label');

      var divAlarmDesc = document.getElementById('div_alarm_description');
      
      var divsynoptic_ui = document.getElementById('div_synoptic_ui');
      var divnetwork_conf_ui = document.getElementById('div_network_conf_ui');
      var divadmin_ui = document.getElementById('div_admin_ui');

      var divLabelSynoptic = document.getElementById('div_synoptic_label');
      var divLabelNetwork = document.getElementById('div_network_label');
      var divLabelAdmin = document.getElementById('div_admin_label');
      

      switch(div) {
         case 'user_interface' : 
            divConfig.style.display = '';
            divSystem.style.display = "none";
            divAlarm.style.display = "none";
            divCard.style.display = "none";
            divSubmit.style.display = "";
            divAlarmDesc.style.display = "none";

            divLabelCard.style.color = "black";
            divLabelConfig.style.color = "#6E8915";
            divLabelSystem.style.color = "black";
            divLabelAlarm.style.color = "black";

            document.configform.submenu.value="user_interface";

            break;
         case 'system_interface' : 
            divConfig.style.display = "none";
            divSystem.style.display = '';
            divAlarm.style.display = "none";
            divCard.style.display = "none";
            divSubmit.style.display = "";
            divAlarmDesc.style.display = "none";

            divLabelCard.style.color = "black";
            divLabelConfig.style.color = "black";
            divLabelSystem.style.color = "#6E8915";
            divLabelAlarm.style.color = "black";

            document.configform.submenu.value="system_interface";

            break;
         case 'alarm_interface' : 
            divConfig.style.display = "none";
            divSystem.style.display = "none";
            divAlarm.style.display = '';
            divCard.style.display = "none";
            divSubmit.style.display = "";
            divAlarmDesc.style.display = "";

            divLabelCard.style.color = "black";
            divLabelConfig.style.color = "black";
            divLabelSystem.style.color = "black";
            divLabelAlarm.style.color = "#6E8915";


            document.configform.submenu.value="alarm_interface";

            break;
         case 'card_interface' : 
            divConfig.style.display = "none";
            divSystem.style.display = "none";
            divAlarm.style.display = "none";
            divCard.style.display = "";
            divSubmit.style.display = "none";
            divAlarmDesc.style.display = "none";

            divLabelCard.style.color = "#6E8915";
            divLabelConfig.style.color = "black";
            divLabelSystem.style.color = "black";
            divLabelAlarm.style.color = "black";

            document.configform.submenu.value="card_interface";

            break;
         case 'synoptic_ui' : 
            divsynoptic_ui.style.display = "";
            divnetwork_conf_ui.style.display = "none";
            divadmin_ui.style.display = "none";

            divLabelSynoptic.style.color = "#6E8915";
            divLabelNetwork.style.color = "black";
            divLabelAdmin.style.color = "black";

            break;
         case 'network_conf_ui' : 
            divsynoptic_ui.style.display = "none";
            divnetwork_conf_ui.style.display = "";
            divadmin_ui.style.display = "none";

            divLabelSynoptic.style.color = "black";
            divLabelNetwork.style.color = "#6E8915";
            divLabelAdmin.style.color = "black";
            break;
         case 'admin_ui' : 
            divsynoptic_ui.style.display = "none";
            divnetwork_conf_ui.style.display = "none";
            divadmin_ui.style.display = "";

            divLabelSynoptic.style.color = "black";
            divLabelNetwork.style.color = "black";
            divLabelAdmin.style.color = "#6E8915";
            break;
      }
}
// }}}




// {{{ expand_plug()
// ROLE expand or reduce submenu of the plug configuration menu
// IN div: number of the plug menu to be expanded
//    nb: number of plug to be checked 
// HOW IT WORKS: get div id to be expanded reduced other menu
// USED BY: templates/plugs.html
function expand_plug(div,nb) {
      for(i=1;i<=nb;i++) {
            div_plug=document.getElementById('div_selected_plug'+i)
            div_label=document.getElementById('div_plug'+i);
            if(div==i) {
                    div_plug.style.display = "";
                    div_label.style.color="#6E8915";
                    
            } else {
                   div_plug.style.display = "none"; 
                   div_label.style.color="black";
            }
            document.getElementById('submenu').value = div;
      }
}


// {{{ expand_wizard()
// ROLE expand or reduce submenu of the wizard menu
// IN step: number of the step to be expanded
//    last: last plus configured (true or false)
//    plug: plug configured
// HOW IT WORKS: get div id to be expanded reduced other menu
// USED BY: templates/wizard.html
function expand_wizard(step,last,plug) {
    previous=document.getElementById('previous');
    next=document.getElementById('next');
    next_plug=document.getElementById("next_plug");
    finish=document.getElementById("finish");
    for(i=1;i<=2;i++) {
        div_step=document.getElementById('step'+i);
        div_subtitle=document.getElementById('subtitle_step'+i);

        if(step>1) {
            previous.style.display = "";
        } else {
            previous.style.display = "none";
        }

        if(step<2) {
            next.style.display = "";
        } else {
            next.style.display = "none";
        }

        if((step==2)&&(!last)) {    
            next_plug.style.display = "";
        } else {
            next_plug.style.display = "none";
        }

        if(step==2) {
            finish.style.display = "";
        } else {
            finish.style.display = "none";
        }

        if(plug==1) {
            div_step.style.display = "";
            div_subtitle.style.display= "";
        } else {
            if(step==i) {
                div_step.style.display = "";
                div_subtitle.style.display= "";
            } else {
                div_step.style.display = "none";
                div_subtitle.style.display= "none";
            }
        }
    }
    
    nb_step=document.getElementById("nb_step");
    nb_step.innerHTML = step+"/2";
}




// {{{ verifDigit()
// ROLE function of veriication for a form. The input value must be a digit.
// IN  input value "e"
// HOW IT WORKS: check ascii code of the input value
// USED BY: templates/configuration.html    templates/plugs.html
function verifDigit(e) {
   if((e.keyCode < 48 || e.keyCode > 57)&&(e.keyCode != 46 && e.keyCode != 44 && e.keyCode != 08 && e.keyCode != 10 && e.keyCode != 13)) e.returnValue = false;
   if((e.which < 48 || e.which > 57)&&(e.which != 46 && e.which != 44 && e.which != 08 && e.which != 10 && e.which != 13)) return false;
}
// }}}


// {{{ verifTime()
// ROLE function of veriication for a timepicker. The input value must be a number or the ':' separator.
// IN  input value "e"
// HOW IT WORKS: check ascii code of the input value
// USED BY: templates/programs.html   
// ASCII code: 48 <[0-9] : < 58
function verifTime(e) {
   if((e.keyCode < 48 || e.keyCode > 58)&&(e.keyCode != 46 && e.keyCode != 44 && e.keyCode != 08 && e.keyCode != 10 && e.keyCode != 13)) e.returnValue = false;
   if((e.which < 48 || e.which > 58)&&(e.which != 46 && e.which != 44 && e.which != 08 && e.which != 10 && e.which != 13)) return false;
}
// }}}


// {{{ getUnity()
// ROLE display the unity depending what kind of value we are managing
// IN  input value: display degree or pourcentage
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/configuration.html      templates/plugs.html
function getUnity(i,j) {
      var divDegree = document.getElementById('label_degree'+j);
      var divPourcent = document.getElementById('label_pourcent'+j);
      var labelSecondDeg = document.getElementById('label_second_degree'+j);
      var labelSecondPct = document.getElementById('label_second_pourcent'+j);

      switch(i) {
         case 0 : divDegree.style.display = ''; divPourcent.style.display = 'none'; labelSecondPct.style.display = 'none'; labelSecondDeg.style.display = ''; break;
         case 1 : divDegree.style.display = 'none'; divPourcent.style.display = '';  labelSecondPct.style.display = ''; labelSecondDeg.style.display = 'none'; break;
         default: divDegree.style.display = ''; divPourcent.style.display = '';  labelSecondPct.style.display = ''; labelSecondDeg.style.display = ''; break;
      }
}
//}}}


// {{{ getCostType()
// ROLE display the cost informations or not from the menu configuration
// IN  input value: display the type of cost information: 0 for standard cost, 1 for full hours/empty hours
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/configuration.html   
function getCostType(i) {
      var labelStandard = document.getElementById('cost_label_standard');
      var labelHP = document.getElementById('cost_label_hp');
      var labelHC = document.getElementById('cost_label_hc');
      var labelInputStandard = document.getElementById('cost_input_standard');
      var labelInputHP = document.getElementById('cost_input_hp');
      var labelInputHC = document.getElementById('cost_input_hc');
      var labelStartHC = document.getElementById('start_label_hc');
      var labelStopHC = document.getElementById('stop_label_hc');
      var valueStartHC = document.getElementById('start_value_hc');
      var valueStopHC = document.getElementById('stop_value_hc');
      var errorHP = document.getElementById('error_cost_price_hp');
      var errorHC= document.getElementById('error_cost_price_hc');
      var errorPrice = document.getElementById('error_cost_price');
      var errorHCstart= document.getElementById('error_start_hc');
      var errorHCstop = document.getElementById('error_stop_hc');



      switch(i) {
         case 0 : labelStandard.style.display = ''; labelHP.style.display = 'none'; labelHC.style.display = 'none'; labelInputStandard.style.display = ''; labelInputHP.style.display = 'none'; labelInputHC.style.display = 'none'; labelStartHC.style.display = 'none'; labelStopHC.style.display = 'none'; valueStartHC.style.display = 'none'; valueStopHC.style.display = 'none'; errorHP.style.display = 'none';errorHC.style.display = 'none'; errorHCstart.style.display = 'none'; errorHCstop.style.display = 'none'; break;
         case 1 : labelStandard.style.display = 'none'; labelHP.style.display = ''; labelHC.style.display = ''; labelInputStandard.style.display = 'none'; labelInputHP.style.display = ''; labelInputHC.style.display = ''; labelStartHC.style.display = ''; labelStopHC.style.display = ''; valueStartHC.style.display = ''; valueStopHC.style.display = ''; errorPrice.style.display = 'none'; break;
         default: labelStandard.style.display = ''; labelHP.style.display = 'none'; labelHC.style.display = 'none'; labelInputStandard.style.display = ''; labelInputHP.style.display = 'none'; labelInputHC.style.display = 'none'; labelStartHC.style.display = 'none'; labelStopHC.style.display = 'none'; valueStartHC.style.display = 'none'; valueStopHC.style.display = 'none'; errorHP.style.display = 'none';errorHC.style.display = 'none'; errorHCstart.style.display = 'none'; errorHCstop.style.display = 'none'; break;
      }
}
//}}}


// {{{ VerifInt()
// ROLE function of verification of an input value
// IN input value "e" to be checked
// HOW IT WORKS: check ascii code of the input value
// USED BY: templates/plugs.html
function verifInt(e) {
   if((e.keyCode < 48 || e.keyCode > 57)&&(e.keyCode != 08 && e.keyCode != 10 && e.keyCode != 13)) e.returnValue = false;
   if((e.which < 48 || e.which > 57)&&(e.which != 08 && e.which != 10 && e.which != 13)) return false;
}
// }}}


// {{{ getRegul()
// ROLE display the regulation informations or not
// IN  input value: display or not the informations
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/plugs.html 
function getRegul(i,j) {
      var divRval = document.getElementById('div_regul_value'+j);
      var divRsenso = document.getElementById('div_regul_senso'+j);
      var divRsenss = document.getElementById('div_regul_senss'+j);
      var labelRsenss = document.getElementById('label_regul_senss'+j);
      var labelRsenso = document.getElementById('label_regul_senso'+j);
      var labelRvalue = document.getElementById('label_regul_value'+j);
      var secondTolLabel = document.getElementById('label_regul_tolerance'+j);
      var secondTolValue = document.getElementById('div_regul_tolerance_value'+j);
      //var tableRegul = document.getElementById('table_regul'+j);

      switch(i) {
         case 0 : divRval.style.display = ''; divRsenso.style.display = ''; divRsenss.style.display = ''; labelRvalue.style.display = ''; labelRsenso.style.display = ''; labelRsenss.style.display = ''; secondTolLabel.style.display = ''; secondTolValue.style.display = ''; /* tableRegul.style.border = "1px solid red"; */break;
         case 1 : divRval.style.display = 'none'; divRsenso.style.display = 'none'; divRsenss.style.display = 'none';  labelRvalue.style.display = 'none'; labelRsenso.style.display = 'none'; labelRsenss.style.display = 'none'; secondTolLabel.style.display = 'none'; secondTolValue.style.display = 'none'; /* tableRegul.style.border = "0px"; */ break;
         default: divRval.style.display = ''; divRsenso.style.display = ''; divRsenss.style.display = '';  labelRvalue.style.display = ''; labelRsenso.style.display = ''; labelRsenss.style.display = ''; secondTolLabel.style.display = ''; secondTolValue.style.display = '';break;
      }
}
// }}}




// {{{ getTolerance()
// ROLE display the tolerance informations or not
// IN  input value: display or not the informations
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/plugs.html 
function getTolerance(i,j,secondR) {
      var divTolerance = document.getElementById('tolerance'+j);
      var divToleranceLabel = document.getElementById('tolerance_label'+j);
      var pDegree = document.getElementById('degree'+j);
      var pPourcent = document.getElementById('pourcent'+j);
      var pCm = document.getElementById('cm'+j);
      var divHumiRegul = document.getElementById('humi_regul_senso'+j);
      var divTempRegul = document.getElementById('temp_regul_senso'+j);
      var divUnknownRegul = document.getElementById('unknown_regul_senso'+j);
      var labelDeg = document.getElementById('label_degree'+j);
      var labelPct = document.getElementById('label_pourcent'+j);
      var seconLabel = document.getElementById('second_regul_label'+j);
      var secondVal = document.getElementById('second_regul'+j);
      var secondParam = document.getElementById('second_regul_param'+j);
      var labelSecondDeg = document.getElementById('label_second_degree'+j);
      var labelSecondPct = document.getElementById('label_second_pourcent'+j);
      var labelSensor = document.getElementById('label_sensor'+j);
      var Sensor = document.getElementById('sensor'+j);
      var labelComputeRegul = document.getElementById('label_regul_compute'+j);    
      var computeRegul = document.getElementById('regul_compute'+j);


      switch(i) {
         case 0 :   divTolerance.style.display = 'none'; 
                    divToleranceLabel.style.display = 'none'; 
                    pDegree.style.display = 'none'; 
                    pPourcent.style.display = 'none'; 
                    if(secondR=="True") { 
                        seconLabel.style.display = 'none'; 
                        secondVal.style.display = 'none'; 
                        secondParam.style.display = 'none'; 
                        divHumiRegul.style.display = 'none'; 
                        divTempRegul.style.display = 'none';
                        divUnknownRegul.style.display = 'none';
                        labelDeg.style.display = 'none';
                        labelPct.style.display = 'none';
                        pCm.style.display = 'none';
                        labelSecondDeg.style.display = 'none';
                        labelSecondPct.style.display = 'none';
                    } 
                    labelSensor.style.display = 'none'; 
                    Sensor.style.display = 'none'; 
                    labelComputeRegul.style.display = 'none';
                    computeRegul.style.display = 'none';
                    break; 

         case 1 :   divTolerance.style.display = 'none'; 
                    divToleranceLabel.style.display = 'none'; 
                    pDegree.style.display = 'none'; 
                    pPourcent.style.display = 'none'; 
                    pCm.style.display = 'none';
                    if(secondR=="True") {
                        divHumiRegul.style.display = 'none'; 
                        divTempRegul.style.display = 'none'; 
                        divUnknownRegul.style.display = 'none'; 
                        labelDeg.style.display = 'none'; 
                        labelPct.style.display = 'none'; 
                        seconLabel.style.display = 'none'; 
                        secondVal.style.display = 'none'; 
                        labelSecondDeg.style.display = 'none';
                        labelSecondPct.style.display = 'none';
                        secondParam.style.display = 'none'; 
                    }
                    labelSensor.style.display = 'none'; 
                    Sensor.style.display = 'none'; 
                    labelComputeRegul.style.display = 'none';
                    computeRegul.style.display = 'none';
                    break;

         case 2 :   divTolerance.style.display = ''; 
                    divToleranceLabel.style.display = ''; 
                    pDegree.style.display = ''; 
                    pPourcent.style.display = 'none'; 
                    pCm.style.display = 'none';
                    if(secondR=="True") {
                        divHumiRegul.style.display = ''; 
                        divTempRegul.style.display = 'none'; 
                        divUnknownRegul.style.display = 'none'; 
                        labelDeg.style.display = 'none'; 
                        labelPct.style.display = ''; 
                        labelSecondDeg.style.display = 'none';
                        labelSecondPct.style.display = '';
                        seconLabel.style.display = ''; 
                        secondVal.style.display = ''; 
                        secondParam.style.display = ''; 
                        Sensor.style.display = '';
                        labelSensor.style.display = '';
                        labelComputeRegul.style.display = '';
                        computeRegul.style.display = '';
                    }

                    break;

         case 3 :   divTolerance.style.display = ''; 
                    divToleranceLabel.style.display = ''; 
                    pDegree.style.display = ''; 
                    pPourcent.style.display = 'none'; 
                    pCm.style.display = 'none';
                    if(secondR=="True") {
                        divHumiRegul.style.display = ''; 
                        divTempRegul.style.display = 'none'; 
                        divUnknownRegul.style.display = 'none'; 
                        labelDeg.style.display = 'none'; 
                        labelPct.style.display = '';            
                        labelSecondDeg.style.display = 'none';
                        labelSecondPct.style.display = '';
                        seconLabel.style.display = ''; 
                        secondVal.style.display = ''; 
                        secondParam.style.display = ''; 
                        Sensor.style.display = '';
                        labelSensor.style.display = '';
                        labelComputeRegul.style.display = '';
                        computeRegul.style.display = '';
                    }
                    break;

          case 4 :      
                    //Pump: no second regulation
                    divTolerance.style.display = '';
                    divToleranceLabel.style.display = '';
                    pDegree.style.display = 'none';
                    pPourcent.style.display = 'none';
                    pCm.style.display = '';

                    if(secondR=="True") {
                        divHumiRegul.style.display = 'none';
                        divTempRegul.style.display = 'none';
                        divUnknownRegul.style.display = 'none';
                        labelDeg.style.display = 'none';
                        labelPct.style.display = 'none';
                        seconLabel.style.display = 'none';
                        secondVal.style.display = 'none';
                        labelSecondDeg.style.display = 'none';
                        labelSecondPct.style.display = 'none';
                        secondParam.style.display = 'none';
                        Sensor.style.display = '';
                        labelSensor.style.display = '';
                        labelComputeRegul.style.display = '';
                        computeRegul.style.display = '';
                    }
                    break;

         case 5 :   divTolerance.style.display = ''; 
                    divToleranceLabel.style.display = ''; 
                    pDegree.style.display = 'none'; 
                    pPourcent.style.display = ''; 
                    pCm.style.display = 'none';
                    if(secondR=="True") {
                        divHumiRegul.style.display = 'none'; 
                        divTempRegul.style.display = ''; 
                        divUnknownRegul.style.display = 'none'; 
                        labelDeg.style.display = ''; 
                        labelPct.style.display = 'none'; 
                        labelSecondDeg.style.display = '';
                        labelSecondPct.style.display = 'none';
                        seconLabel.style.display = ''; 
                        secondVal.style.display = ''; 
                        secondParam.style.display = ''; 
                        Sensor.style.display = '';
                        labelSensor.style.display = '';
                        labelComputeRegul.style.display = '';
                        computeRegul.style.display = '';
                    }
                    break;

         case 6 :   divTolerance.style.display = ''; 
                    divToleranceLabel.style.display = ''; 
                    pDegree.style.display = 'none'; 
                    pPourcent.style.display = ''; 
                    pCm.style.display = 'none';
                    if(secondR=="True") {
                        divHumiRegul.style.display = 'none'; 
                        divTempRegul.style.display = ''; 
                        divUnknownRegul.style.display = 'none'; 
                        labelDeg.style.display = ''; 
                        labelPct.style.display = 'none'; 
                        labelSecondDeg.style.display = '';
                        labelSecondPct.style.display = 'none';
                        seconLabel.style.display = ''; 
                        secondVal.style.display = ''; 
                        secondParam.style.display = '';
                        Sensor.style.display = '';
                        labelSensor.style.display = '';
                        labelComputeRegul.style.display = '';
                        computeRegul.style.display = '';
                    }
                    break;         
    
         default:   divTolerance.style.display = 'none'; 
                    divToleranceLabel.style.display = 'none'; 
                    pDegree.style.display = 'none'; 
                    pPourcent.style.display = 'none'; 
                    if(secondR=="True") {
                        divHumiRegul.style.display = 'none'; 
                        divTempRegul.style.display = 'none'; 
                        divUnknownRegul.style.display = ''; 
                        labelDeg.style.display = ''; 
                        labelPct.style.display = 'none'; 
                        labelSecondDeg.style.display = '';
                        labelSecondPct.style.display = 'none';
                        seconLabel.style.display = 'none'; 
                        secondVal.style.display = 'none'; 
                        secondParam.style.display = 'none';
                    } 
                    labelSensor.style.display = 'none'; 
                    Sensor.style.display = 'none'; 
                    labelComputeRegul.style.display = 'none';
                    computeRegul.style.display = 'none';
                    break;
      }
}
// }}}


// {{{ getSelectedPlug()
// ROLE display the selected plug
// IN  input value: display or not the informations
// HOW IT WORKS: get id from div to be displayed 
// USED BY: templates/plugs.html 
function getSelectedPlug(i,j) {
      for(k = 1; k <= j; k++) {
            var divSelected=document.getElementById('div_selected_plug'+k);
            if(i==j) {
                    divSelected.style.display="";
                    document.getElementById('title_infos_plug').style.display="none";
                    document.getElementById('title_infos_all_plugs'+k).style.display="";
                    document.getElementById('format_style'+k).style.display="";
            } else {
                if(k==(i+1)) {
                    divSelected.style.display="";
                } else {
                    divSelected.style.display="none";
                }
                document.getElementById('title_infos_plug').style.display="";
                document.getElementById('title_infos_all_plugs'+k).style.display="none";
                document.getElementById('format_style'+k).style.display="none";
            }
    }
}
// }}}






// {{{ getRegulation()
// ROLE display the regulation informations or not
// IN  input value: display or not the informations
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/programs.html 
function getRegulation(i, type) {
      var divValTemp = document.getElementById('regul_value_temp');
      var divLabelTemp = document.getElementById('regul_label_temp');
      var divValHumi = document.getElementById('regul_value_humi');
      var divLabelHumi = document.getElementById('regul_label_humi');
      var divLabelWater = document.getElementById('regul_label_water');
      var divValWater = document.getElementById('regul_value_water');

      if(i=="dimmer") {
            divValHumi.style.display = '';
            divLabelHumi.style.display = 'none';
            divValTemp.style.display = 'none';
            divLabelTemp.style.display = 'none';
            divValWater.style.display = 'none';
            divLabelWater.style.display = 'none';
            var divValue = document.getElementById('value_program');
            divValue.value="50";

            var divDimmerLabel=document.getElementById('dimmer_label');
            divDimmerLabel.style.display = '';
      } else {
        var divDimmerLabel=document.getElementById('dimmer_label');
        divDimmerLabel.style.display = 'none';

        if((type=="humidifier")||(type=="dehumidifier")) {
            divValHumi.style.display = '';
            divLabelHumi.style.display = '';
            divValTemp.style.display = 'none';
            divLabelTemp.style.display = 'none';
            divValWater.style.display = 'none';
            divLabelWater.style.display = 'none';
            var divValue = document.getElementById('value_program');
            divValue.value="55";
        } else if((type=="heating")||(type=="ventilator")) {
            divValHumi.style.display = 'none';
            divLabelHumi.style.display = 'none';
            divValTemp.style.display = '';
            divLabelTemp.style.display = '';
            divValWater.style.display = 'none';
            divLabelWater.style.display = 'none';

            var divValue = document.getElementById('value_program');
            divValue.value="22";
        } else if(type=="pump") {
            divValHumi.style.display = 'none';
            divLabelHumi.style.display = 'none';
            divValTemp.style.display = 'none';
            divLabelTemp.style.display = 'none';
            divValWater.style.display = '';
            divLabelWater.style.display = '';

            var divValue = document.getElementById('value_program');
            divValue.value="22";
        } else {
            var divValue = document.getElementById('value_program');
            divValue.value="";
        }
      }

      var divValueRegul = document.getElementById('regul_value');
      var divLabelRegul = document.getElementById('regul_label');
    
      switch(i) {
         case 'regul' : divLabelRegul.style.display = ''; divValueRegul.style.display = ''; break;
         case 'dimmer': divLabelRegul.style.display = 'none'; divValueRegul.style.display = ''; break;
         default: divLabelRegul.style.display = 'none'; divValueRegul.style.display = 'none'; break;
      }

}
// }}}


// {{{ getProgramType()
// ROLE check or uncheck radio button depending of the type
// IN  i the input type 
// USED BY: templates/programs.html 
function getProgramType(i) {
      var PonctualRadio = document.getElementById('ponctual');
      var CyclicRadio = document.getElementById('cyclic');
      var divTimeCyclicField = document.getElementById('time_cyclic_field');

      var startTimeField = document.getElementById('start_time_program_field');
      var startTimeTitle = document.getElementById('start_time_program_title');
      var errorStart = document.getElementById('error_start_time');
      var errorSameStart = document.getElementById('error_same_start');
      var endTimeField = document.getElementById('end_time_program_field');
      var endTimeTitle = document.getElementById('end_time_program_title');
      var errorEnd = document.getElementById('error_end_time');
      var errorSameEnd = document.getElementById('error_same_end');

      var durationCyclic = document.getElementById('duration_cyclic');
      var durationCyclicField = document.getElementById('duration_cyclic_field');
      var errorCyclicDuration = document.getElementById('error_cyclic_duration');
      var divTimeCyclic = document.getElementById('time_cyclic');
      var divTimeCyclicField = document.getElementById('time_cyclic_field');
      var errorCyclic = document.getElementById('error_cyclic_time');
      var errorMinimal = document.getElementById('error_minimal_cyclic');
      var startTimeCyclic = document.getElementById('start_time_cyclic_title');
      var startTimeCyclicField =  document.getElementById('start_time_cyclic_field');
      var endTimeCyclic = document.getElementById('end_time_cyclic_title');
      var endTimeCyclicField =  document.getElementById('end_time_cyclic_field');
      var errorStartTimeCyclic = document.getElementById('error_start_time_cyclic');
      var errorEndTimeCyclic = document.getElementById('error_end_time_cyclic');

    
      switch(i) {
         case 'ponctual': PonctualRadio.checked=true; 
                          CyclicRadio.checked=false; 
                          
                          startTimeField.style.display='';
                          startTimeTitle.style.display='';
                          endTimeField.style.display='';
                          endTimeTitle.style.display='';

                          durationCyclic.style.display='none';
                          durationCyclicField.style.display='none';
                          divTimeCyclic.style.display='none';
                          divTimeCyclicField.style.display='none';
                          startTimeCyclic.style.display='none';
                          startTimeCyclicField.style.display='none';
                          endTimeCyclic.style.display='none';
                          endTimeCyclicField.style.display='none';
                          errorCyclicDuration.style.display='none';
                          errorCyclic.style.display='none';
                          errorMinimal.style.display='none';
                          errorEndTimeCyclic.style.display='none';
                          errorStartTimeCyclic.style.display='none';
        break;
        case 'cyclic': PonctualRadio.checked=false; 
                       CyclicRadio.checked=true; 
                    
                       startTimeField.style.display='none';
                       startTimeTitle.style.display='none';
                       errorStart.style.display='none';
                       errorSameStart.style.display='none';
                       endTimeField.style.display='none';
                       endTimeTitle.style.display='none';
                       errorEnd.style.display='none';
                       errorSameEnd.style.display='none';
                       durationCyclic.style.display='';
                       durationCyclicField.style.display='';
                       startTimeCyclic.style.display='';
                       startTimeCyclicField.style.display='';
                       endTimeCyclic.style.display='';
                       endTimeCyclicField.style.display='';
                       divTimeCyclic.style.display='';
                       divTimeCyclicField.style.display='';
        break;
        default: PonctualRadio.checked=true; 
                          CyclicRadio.checked=false; 

                          startTimeField.style.display='';
                          startTimeTitle.style.display='';
                          endTimeField.style.display='';
                          endTimeTitle.style.display='';

                          durationCyclic.style.display='none';
                          durationCyclicField.style.display='none';
                          divTimeCyclic.style.display='none';
                          divTimeCyclicField.style.display='none';
                          startTimeCyclic.style.display='none';
                          startTimeCyclicField.style.display='none';
                          endTimeCyclic.style.display='none';
                          endTimeCyclicField.style.display='none';

                          errorCyclicDuration.style.display='none';
                          errorCyclic.style.display='none';
                          errorMinimal.style.display='none';
        break;
      }

}
// }}}


// {{{ addHidden()
// ROLE add hidden field to a form
// IN  input value: the form to be used and the value of the field
// USED BY: templates/plugs.html 
function addHidden(theForm, value,name) {
    // Create a hidden input element, and append it to the form:
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    theForm.appendChild(input);
}
// }}}


// {{{ compareDate()
// ROLE compare two date and check that data1 > date2
// USED by libs/js/cutibox.js
function compareDate(date1,date2) {
    d1=date1.split('-').join('')
        d2=date2.split('-').join('')
if(d2>=d1) return true;
    return false;
}
// }}}


// {{{ checkFormatDate()
// ROLE check tht a date has a YYYY-MM-DD format
// USED by libs/js/cutibox.js
function checkFormatDate(date) {
    if(!date.match(/^[0-9]{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])/)) {
         return false;
    }
    return true;
}
// }}}


// {{{ loadCSS()
// ROLE Used to load a css
function loadCSS(filename){ 

   var file = document.createElement("link")
   file.setAttribute("rel", "stylesheet")
   file.setAttribute("type", "text/css")
   file.setAttribute("href", filename)

   if (typeof file !== "undefined")
      document.getElementsByTagName("head")[0].appendChild(file)
}
// }}}


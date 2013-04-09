// {{{ expand()
// ROLE expand or reduce submenu of the configuration menu
// IN div to be expanded or reduced 
// HOW IT WORKS: get div id to be expanded (or reduced) and set value (hidden=0, show=1) of hidden fields to
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

      switch(div) {
         case 'user_interface' : divConfig.style.display = '';
                                 divSystem.style.display = "none";
                                 divAlarm.style.display = "none";
                                 divCard.style.display = "none";
                                 divSubmit.style.display = "";
                        
                                 divLabelConfig.style.color = '#777779';
                                 divLabelSystem.style.color = '';
                                 divLabelAlarm.style.color = '';
                                 divLabelCard.style.color = '';

                                 /*
                                 divLabelCard.style.fontSize = "12px";
                                 divLabelConfig.style.fontSize = "13px";
                                 divLabelSystem.style.fontSize = "12px";
                                 divLabelAlarm.style.fontSize = "12px";
                                 */

                                 document.configform.submenu.value="user_interface";
                                 
                                 break;
         case 'system_interface' : divConfig.style.display = "none";
                                   divSystem.style.display = '';
                                   divAlarm.style.display = "none";
                                   divCard.style.display = "none";
                                   divSubmit.style.display = "";

                                   divLabelConfig.style.color = '';
                                   divLabelSystem.style.color = '#777779';
                                   divLabelAlarm.style.color = '';
                                   divLabelCard.style.color = '';
   
                                   /* 
                                   divLabelCard.style.fontSize = "12px";
                                   divLabelConfig.style.fontSize = "12px";
                                   divLabelSystem.style.fontSize = "13px";
                                   divLabelAlarm.style.fontSize = "12px";
                                   */

                                   document.configform.submenu.value="system_interface";

                                   break;
         case 'alarm_interface' : divConfig.style.display = "none";
                                  divSystem.style.display = "none";
                                  divAlarm.style.display = '';
                                  divCard.style.display = "none";
                                  divSubmit.style.display = "";

                                  divLabelConfig.style.color = '';
                                  divLabelSystem.style.color = '';
                                  divLabelAlarm.style.color = '#777779';
                                  divLabelCard.style.color = '';
                                  /* 
                                  divLabelCard.style.fontSize = "12px";
                                  divLabelConfig.style.fontSize = "12px";
                                  divLabelSystem.style.fontSize = "12px";
                                  divLabelAlarm.style.fontSize = "13px";
                                  */

                                  document.configform.submenu.value="alarm_interface";

                                  break;
         case 'card_interface' : divConfig.style.display = "none";
                                 divSystem.style.display = "none";
                                 divAlarm.style.display = "none";
                                 divCard.style.display = "";
                                 divSubmit.style.display = "none";

                                 divLabelConfig.style.color = '';
                                 divLabelSystem.style.color = '';
                                 divLabelAlarm.style.color = '';
                                 divLabelCard.style.color = '#777779';

                                 /*
                                 divLabelCard.style.fontSize="13px";
                                 divLabelConfig.style.fontSize = "12px";
                                 divLabelSystem.style.fontSize = "12px";
                                 divLabelAlarm.style.fontSize = "12px";
                                 */

                                 document.configform.submenu.value="card_interface";

                                 break;
      }
}
// }}}


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


// {{{ getAlarm()
// ROLE display or not the alarm part from the configuration menu
// IN  input value: display or not 
// HOW IT WORKS: get id from div to be displayed the alarm configuration or not and display it (or not) depending the input value
// USED BY: templates/configuration.html
function getAlarm(i) {
      var divAval = document.getElementById('div_alarm_value');
      var divAsenso = document.getElementById('div_alarm_senso');
      var divAsenss = document.getElementById('div_alarm_senss');
      var labelAsenss = document.getElementById('label_alarm_senss');
      var labelAsenso = document.getElementById('label_alarm_senso');
      var labelAvalue = document.getElementById('label_alarm_value');

      switch(i) {
         case 0 : divAval.style.display = ''; divAsenso.style.display = ''; divAsenss.style.display = ''; labelAvalue.style.display = ''; labelAsenso.style.display = ''; labelAsenss.style.display = '';break;
         case 1 : divAval.style.display = 'none'; divAsenso.style.display = 'none'; divAsenss.style.display = 'none'; labelAvalue.style.display = 'none'; labelAsenso.style.display = 'none'; labelAsenss.style.display = 'none'; break;
         default: divAval.style.display = ''; divAsenso.style.display = ''; divAsenss.style.display = ''; labelAvalue.style.display = ''; labelAsenso.style.display = ''; labelAsenss.style.display = ''; break;
      }
}
//}}}


// {{{ getUnity()
// ROLE display the unity depending what kind of value we are managing
// IN  input value: display degree or pourcentage
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/configuration.html      templates/plugs.html
function getUnity(i,j) {
      var divDegree = document.getElementById('label_degree'+j);
      var divPourcent = document.getElementById('label_pourcent'+j);

      switch(i) {
         case 0 : divDegree.style.display = ''; divPourcent.style.display = 'none'; break;
         case 1 : divDegree.style.display = 'none'; divPourcent.style.display = ''; break;
         default: divDegree.style.display = ''; divPourcent.style.display = ''; break;
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


      switch(i) {
         case 0 : labelStandard.style.display = ''; labelHP.style.display = 'none'; labelHC.style.display = 'none'; labelInputStandard.style.display = ''; labelInputHP.style.display = 'none'; labelInputHC.style.display = 'none'; labelStartHC.style.display = 'none'; labelStopHC.style.display = 'none'; valueStartHC.style.display = 'none'; valueStopHC.style.display = 'none'; break;
         case 1 : labelStandard.style.display = 'none'; labelHP.style.display = ''; labelHC.style.display = ''; labelInputStandard.style.display = 'none'; labelInputHP.style.display = ''; labelInputHC.style.display = ''; labelStartHC.style.display = ''; labelStopHC.style.display = ''; valueStartHC.style.display = ''; valueStopHC.style.display = ''; break;
         default: labelStandard.style.display = ''; labelHP.style.display = 'none'; labelHC.style.display = 'none'; labelInputStandard.style.display = ''; labelInputHP.style.display = 'none'; labelInputHC.style.display = 'none'; labelStartHC.style.display = 'none'; labelStopHC.style.display = 'none'; valueStartHC.style.display = 'none'; valueStopHC.style.display = 'none'; break;
      }
}
//}}}



// {{{ verifDate()
// ROLE function of verification that the input field contains a valid date (a digit and some special caracters "-/"
// IN  input value "e" of the field
// HOW IT WORKS: check ascii code fo the input value
// USED BY: templates/cost.html   templates/logs.html
function verifDate(e) {
   if((e.keyCode < 45 || e.keyCode > 57)&&(e.keyCode != 08 && e.keyCode != 10 && e.keyCode != 13)) e.returnValue = false;
   if((e.which < 45 || e.which > 57)&&(e.which != 08 && e.which != 10 && e.which != 13)) return false;
}
// }}}



// {{{ getType()
// IN  input value: display the type og log: 0 for daily logs, 1 for monthly
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/logs.html  
function getType(i) {
      var divSelectDay = document.getElementById('label_select_day');
      var divSelectMonth = document.getElementById('label_select_month');

      switch(i) {
         case 0 : divSelectDay.style.display = ''; divSelectMonth.style.display = 'none'; break;
         case 1 : divSelectDay.style.display = 'none'; divSelectMonth.style.display = ''; break;
         default: divSelectDay.style.display = ''; divSelectMonth.style.display = 'none'; break;
      }
}
// }}}


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

      switch(i) {
         case 0 : divRval.style.display = ''; divRsenso.style.display = ''; divRsenss.style.display = ''; labelRvalue.style.display = ''; labelRsenso.style.display = ''; labelRsenss.style.display = '';break;
         case 1 : divRval.style.display = 'none'; divRsenso.style.display = 'none'; divRsenss.style.display = 'none';  labelRvalue.style.display = 'none'; labelRsenso.style.display = 'none'; labelRsenss.style.display = 'none'; break;
         default: divRval.style.display = ''; divRsenso.style.display = ''; divRsenss.style.display = '';  labelRvalue.style.display = ''; labelRsenso.style.display = ''; labelRsenss.style.display = ''; break;
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
      var divHumiRegul = document.getElementById('humi_regul_senso'+j);
      var divTempRegul = document.getElementById('temp_regul_senso'+j);
      var divUnknownRegul = document.getElementById('unknown_regul_senso'+j);
      var labelDeg = document.getElementById('label_degree'+j);
      var labelPct = document.getElementById('label_pourcent'+j);
      var seconLabel = document.getElementById('second_regul_label'+j);
      var secondVal = document.getElementById('second_regul'+j);
      var secondParam = document.getElementById('second_regul_param'+j);

      var labelSensor = document.getElementById('label_sensor'+j);
      var Sensor = document.getElementById('sensor'+j);


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
                    } 
                    labelSensor.style.display = 'none'; 
                    Sensor.style.display = 'none'; 
                    break; 

         case 1 :   divTolerance.style.display = 'none'; 
                    divToleranceLabel.style.display = 'none'; 
                    pDegree.style.display = 'none'; 
                    pPourcent.style.display = 'none'; 
                    if(secondR=="True") {
                        divHumiRegul.style.display = 'none'; 
                        divTempRegul.style.display = 'none'; 
                        divUnknownRegul.style.display = 'none'; 
                        labelDeg.style.display = 'none'; 
                        labelPct.style.display = 'none'; 
                        seconLabel.style.display = 'none'; 
                        secondVal.style.display = 'none'; 
                        secondParam.style.display = 'none'; 
                    }
                    labelSensor.style.display = 'none'; 
                    Sensor.style.display = 'none'; 
                    break;

         case 2 :   divTolerance.style.display = ''; 
                    divToleranceLabel.style.display = ''; 
                    pDegree.style.display = ''; 
                    pPourcent.style.display = 'none'; 
                    if(secondR=="True") {
                        divHumiRegul.style.display = ''; 
                        divTempRegul.style.display = 'none'; 
                        divUnknownRegul.style.display = 'none'; 
                        labelDeg.style.display = 'none'; 
                        labelPct.style.display = ''; 
                        seconLabel.style.display = ''; 
                        secondVal.style.display = ''; 
                        secondParam.style.display = '';  
                    }
                    labelSensor.style.display = ''; 
                    Sensor.style.display = ''; 
                    break;

         case 3 :   divTolerance.style.display = ''; 
                    divToleranceLabel.style.display = ''; 
                    pDegree.style.display = ''; 
                    pPourcent.style.display = 'none'; 
                    if(secondR=="True") {
                        divHumiRegul.style.display = ''; 
                        divTempRegul.style.display = 'none'; 
                        divUnknownRegul.style.display = 'none'; 
                        labelDeg.style.display = 'none'; 
                        labelPct.style.display = '';            
                        seconLabel.style.display = ''; 
                        secondVal.style.display = ''; 
                        secondParam.style.display = ''; 
                    }
                    labelSensor.style.display = ''; 
                    Sensor.style.display = ''; break;

         case 4 :   divTolerance.style.display = ''; 
                    divToleranceLabel.style.display = ''; 
                    pDegree.style.display = 'none'; 
                    pPourcent.style.display = ''; 
                    if(secondR=="True") {
                        divHumiRegul.style.display = 'none'; 
                        divTempRegul.style.display = ''; 
                        divUnknownRegul.style.display = 'none'; 
                        labelDeg.style.display = ''; 
                        labelPct.style.display = 'none'; 
                        seconLabel.style.display = ''; 
                        secondVal.style.display = ''; 
                        secondParam.style.display = ''; 
                    }
                    labelSensor.style.display = ''; i
                    Sensor.style.display = ''; i
                    break;

         case 5 :   divTolerance.style.display = ''; 
                    divToleranceLabel.style.display = ''; 
                    pDegree.style.display = 'none'; 
                    pPourcent.style.display = ''; 
                    if(secondR=="True") {
                        divHumiRegul.style.display = 'none'; 
                        divTempRegul.style.display = ''; 
                        divUnknownRegul.style.display = 'none'; 
                        labelDeg.style.display = ''; 
                        labelPct.style.display = 'none'; 
                        seconLabel.style.display = ''; 
                        secondVal.style.display = ''; 
                        secondParam.style.display = '';
                    }
                    labelSensor.style.display = '';
                    Sensor.style.display = ''; 
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
                        seconLabel.style.display = 'none'; 
                        secondVal.style.display = 'none'; 
                        secondParam.style.display = 'none';
                    } 
                    labelSensor.style.display = 'none'; 
                    Sensor.style.display = 'none'; 
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

        if((type=="humidifier")||(type=="dehumidifier")) {
            divValHumi.style.display = '';
            divLabelHumi.style.display = '';
            divValTemp.style.display = 'none';
            divLabelTemp.style.display = 'none';
            var divValue = document.getElementById('value_program');
            divValue.value="55";
      } else if((type=="heating")||(type=="ventilator")) {
            divValHumi.style.display = 'none';
            divLabelHumi.style.display = 'none';
            divValTemp.style.display = '';
            divLabelTemp.style.display = '';

            var divValue = document.getElementById('value_program');
                divValue.value="22";
      } else {
            var divValue = document.getElementById('value_program');
            divValue.value="";
      }

      var divValueRegul = document.getElementById('regul_value');
      var divLabelRegul = document.getElementById('regul_label');


    
      switch(i) {
         case 2 : divLabelRegul.style.display = ''; divValueRegul.style.display = ''; break;
         default: divLabelRegul.style.display = 'none'; divValueRegul.style.display = 'none'; break;
      }

}
// }}}


// {{{ getProgramType()
// ROLE check or uncheck radio button depending of the type
// IN  i the input type and j the index
// USED BY: templates/programs.html 
function getProgramType(i) {
      var PonctualRadio = document.getElementById('ponctual');
      var CyclicRadio = document.getElementById('cyclic');
      var divTimeCyclicField = document.getElementById('time_cyclic_field');
      var divTimeCyclic = document.getElementById('time_cyclic');

      switch(i) {
         case 'ponctual': PonctualRadio.checked=true; CyclicRadio.checked=false; divTimeCyclicField.style.display='none'; divTimeCyclic.style.display='none';  break;
         case 'cyclic': PonctualRadio.checked=false; CyclicRadio.checked=true; divTimeCyclicField.style.display=''; divTimeCyclic.style.display=''; break;
         default: PonctualRadio.checked=true; CyclicRadio.checked=false; divTimeCyclicField.style.display='none'; divTimeCyclic.style.display='none'; break;
      }

}
// }}}


// {{{ DeleteForm()
// ROLE function of confirmation to delete logs from a specific database (logs, power...)
// IN message to be displayed 
// HOW IT WORKS: just ask user to confirm his command
// USED BY: templates/logs.html 
function DeleteForm(message) {
            var answer = confirm(message);
            if (answer) {
                    return true;
            }
            return false;
}
// }}}


// {{{ getEnable()
// ROLE display the plugs informations or not
// IN  input value: display or not the informations
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/plugs.html 
function getEnable(i,j) {
      var divEnable = document.getElementById('state_plug'+j);

      switch(i) {
         case 0 : divEnable.style.display = ''; break;
         case 1 : divEnable.style.display = 'none'; break;
         default: divEnable.style.display = ''; break;
      }
}
// }}}


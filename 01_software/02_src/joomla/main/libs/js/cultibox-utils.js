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
      var divCost = document.getElementById('div_cost_interface');
      var Config = document.getElementById('user_interface');
      var System = document.getElementById('system_interface');
      var Alarm = document.getElementById('alarm_interface');
      var Cost = document.getElementById('cost_interface');


      switch(div) {
         case 'user_interface' : if(divConfig.style.display == "none") {
                                    divConfig.style.display = '';
                                    Config.value='1';
                                 } else {
                                    divConfig.style.display = 'none';
                                    Config.value='';
                                 }
                                 break;
         case 'system_interface' : if(divSystem.style.display == "none") {
                                        divSystem.style.display = '';
                                        System.value='1';
                                    } else {
                                        divSystem.style.display = 'none';
                                        System.value='';
                                    }
                                    break;
         case 'alarm_interface' : if(divAlarm.style.display == "none") {
                                    divAlarm.style.display = '';
                                    Alarm.value='1';
                                  } else {
                                    divAlarm.style.display = 'none';
                                    Alarm.value='';
                                 }
                                 break;
         case 'cost_interface' :  if(divCost.style.display == "none") {
                                    divCost.style.display = '';
                                    Cost.value='1';
                                  } else {
                                    divCost.style.display = 'none';
                                    Cost.value='';
                                  }
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



// {{{ DeleteForm()
// ROLE function of confirmation to delete logs from a specific database (logs, power...)
// IN  
// HOW IT WORKS: just ask user to confirm his command
// USED BY: templates/logs.html 
function DeleteForm() {
            var answer = confirm("<?php echo __('RESET_CONFIRM','javascript'); ?>");
            if (answer) {
                    return true;
            }
            return false;
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
         case 1 : divRval.style.display = 'none'; divRsenso.style.display = 'none'; divRsenss.style.display = 'none'; labelRvalue.style.display = 'none'; labelRsenso.style.display = 'none'; labelRsenss.style.display = 'none'; break;
         default: divRval.style.display = ''; divRsenso.style.display = ''; divRsenss.style.display = ''; labelRvalue.style.display = ''; labelRsenso.style.display = ''; labelRsenss.style.display = ''; break;
      }
}
// }}}




// {{{ getRegul()
// ROLE display the tolerance informations or not
// IN  input value: display or not the informations
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/plugs.html 
function getTolerance(i,j) {
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

      switch(i) {
         case 0 : divTolerance.style.display = 'none'; divToleranceLabel.style.display = 'none'; pDegree.style.display = 'none'; pPourcent.style.display = 'none'; divHumiRegul.style.display = 'none'; divTempRegul.style.display = 'none'; divUnknownRegul.style.display = ''; labelDeg.style.display = ''; labelPct.style.display = 'none'; seconLabel.style.display = ''; secondVal.style.display = ''; secondParam.style.display = ''; break; 
         case 1 : divTolerance.style.display = 'none'; divToleranceLabel.style.display = 'none'; pDegree.style.display = 'none'; pPourcent.style.display = 'none'; divHumiRegul.style.display = 'none'; divTempRegul.style.display = 'none'; divUnknownRegul.style.display = 'none'; labelDeg.style.display = 'none'; labelPct.style.display = 'none'; seconLabel.style.display = 'none'; secondVal.style.display = 'none'; secondParam.style.display = 'none'; break;
         case 2 : divTolerance.style.display = ''; divToleranceLabel.style.display = ''; pDegree.style.display = ''; pPourcent.style.display = 'none'; divHumiRegul.style.display = ''; divTempRegul.style.display = 'none'; divUnknownRegul.style.display = 'none'; labelDeg.style.display = 'none'; labelPct.style.display = ''; seconLabel.style.display = ''; secondVal.style.display = ''; secondParam.style.display = ''; break;
         case 3 : divTolerance.style.display = ''; divToleranceLabel.style.display = ''; pDegree.style.display = ''; pPourcent.style.display = 'none'; divHumiRegul.style.display = ''; divTempRegul.style.display = 'none'; divUnknownRegul.style.display = 'none'; labelDeg.style.display = 'none'; labelPct.style.display = ''; seconLabel.style.display = ''; secondVal.style.display = ''; secondParam.style.display = ''; break;
         case 4 : divTolerance.style.display = ''; divToleranceLabel.style.display = ''; pDegree.style.display = 'none'; pPourcent.style.display = ''; divHumiRegul.style.display = 'none'; divTempRegul.style.display = ''; divUnknownRegul.style.display = 'none'; labelDeg.style.display = ''; labelPct.style.display = 'none'; seconLabel.style.display = ''; secondVal.style.display = ''; secondParam.style.display = ''; break;
         case 5 : divTolerance.style.display = ''; divToleranceLabel.style.display = ''; pDegree.style.display = 'none'; pPourcent.style.display = ''; divHumiRegul.style.display = 'none'; divTempRegul.style.display = ''; divUnknownRegul.style.display = 'none'; labelDeg.style.display = ''; labelPct.style.display = 'none'; seconLabel.style.display = ''; secondVal.style.display = ''; secondParam.style.display = ''; break;         
         default: divTolerance.style.display = 'none'; divToleranceLabel.style.display = 'none'; pDegree.style.display = 'none'; pPourcent.style.display = 'none'; divHumiRegul.style.display = 'none'; divTempRegul.style.display = 'none'; divUnknownRegul.style.display = ''; labelDeg.style.display = ''; labelPct.style.display = 'none'; seconLabel.style.display = 'none'; secondVal.style.display = 'none'; secondParam.style.display = 'none'; break;
      }
}
// }}}



//============== For the programs part ===============\\
// {{{ getRegul()
// ROLE display the regulation informations or not
// IN  input value: display or not the informations
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/programs.html 
function getRegulation(i,j) {
      var divValueRegul = document.getElementById('regul_value'+j);
      var divLabelRegul = document.getElementById('regul_label'+j);

      switch(i) {
         case 0 : divLabelRegul.style.display = ''; divValueRegul.style.display = ''; break;
         default: divLabelRegul.style.display = 'none'; divValueRegul.style.display = 'none'; break;
      }
}
// }}}


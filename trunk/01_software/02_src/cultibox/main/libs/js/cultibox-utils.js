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


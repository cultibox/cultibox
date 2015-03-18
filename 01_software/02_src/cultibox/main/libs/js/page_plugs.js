<script>
<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>


sensors        = <?php echo json_encode($GLOBALS['NB_MAX_SENSOR_PLUG']) ?>;
nb_plugs       = <?php echo json_encode($nb_plugs) ?>;
plugs_infoJS   = <?php echo json_encode($plugs_infos); ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;
var plug_alert_change= {};

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

    // Unshow every element of regulation
    divTolerance.style.display = 'none'; 
    divToleranceLabel.style.display = 'none'; 
    pDegree.style.display = 'none'; 
    pPourcent.style.display = 'none';
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
    labelSensor.style.display = 'none'; 
    Sensor.style.display = 'none'; 
    labelComputeRegul.style.display = 'none';
    computeRegul.style.display = 'none';
    
    // Show it
    switch(i) {
        case "extractor" :
        case "intractor" :
        case "ventilator" :
        case "heating" :
            divTolerance.style.display = ''; 
            divToleranceLabel.style.display = ''; 
            pDegree.style.display = ''; 
            if(secondR=="True") {
                divHumiRegul.style.display = ''; 
                labelPct.style.display = ''; 
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
        case "pump" :
        case "pumpfilling" :
        case "pumpempting" :
            //Pump: no second regulation
            divTolerance.style.display = '';
            divToleranceLabel.style.display = '';
            pCm.style.display = '';
            if(secondR=="True") {
                Sensor.style.display = '';
                labelSensor.style.display = '';
                labelComputeRegul.style.display = '';
                computeRegul.style.display = '';
            }
            break;
        case "humidifier" :
        case "dehumidifier" :
            divTolerance.style.display = ''; 
            divToleranceLabel.style.display = ''; 
            pDegree.style.display = 'none'; 
            pPourcent.style.display = ''; 
            pCm.style.display = 'none';
            if(secondR=="True") {
                divTempRegul.style.display = ''; 
                labelDeg.style.display = '';
                labelSecondDeg.style.display = '';
                seconLabel.style.display = ''; 
                secondVal.style.display = ''; 
                secondParam.style.display = ''; 
                Sensor.style.display = '';
                labelSensor.style.display = '';
                labelComputeRegul.style.display = '';
                computeRegul.style.display = '';
            }
            break;
    }
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
         case "False" :
            divRval.style.display = 'none';
            divRsenso.style.display = 'none';
            divRsenss.style.display = 'none';
            labelRvalue.style.display = 'none';
            labelRsenso.style.display = 'none';
            labelRsenss.style.display = 'none';
            secondTolLabel.style.display = 'none';
            secondTolValue.style.display = 'none';
            break;
         case "True" :
         default:
            divRval.style.display = '';
            divRsenso.style.display = '';
            divRsenss.style.display = '';
            labelRvalue.style.display = '';
            labelRsenso.style.display = '';
            labelRsenss.style.display = '';
            secondTolLabel.style.display = '';
            secondTolValue.style.display = '';
            break;
      }
}
// }}}


$(document).ready(function(){
     pop_up_remove("main_error");
     pop_up_remove("main_info");

    // For each information, show it
    $.each(main_error, function(key, entry) {
            pop_up_add_information(entry,"main_error","error");
    });

    // For each information, show it
    $.each(main_info, function(key, entry) {
            pop_up_add_information(entry,"main_info","information");
    });

    // Update informations shows
    <?php
        for($i=1;$i<=$nb_plugs;$i++) {
            echo "getTolerance('" . $plug_type{$i} . "'," . $i . ",'" . $second_regul . "');";
        }
        
        for($i=1;$i<=$nb_plugs;$i++) {
            echo "getRegul('" . $plug_regul{$i} . "'," . $i . ");";
        }
    ?>
    
    
    
    pop_up_add_information("<?php echo __('WIZARD_ENABLE_FUNCTION'); ?>: <a href='/cultibox/index.php?menu=wizard' class='href-wizard-msgbox'><img src='main/libs/img/wizard.png' alt='<?php echo __('WIZARD'); ?>' title='' id='wizard' /></a>", "jumpto_wizard", "information");

    if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", duration: 36000}
        });
    }

    $("#wizard").click(function(e) {
        e.preventDefault();
        get_content("wizard",getUrlVars("selected_plug=1"));
    });

     $('select[id^="plug_sensor"]').change(function () {
        //Récupération du numéro de la prise en cours d'édition. L'information est contenue dans l'id de l'élément, on découpe donc l'id pour récupérer l'information
        var plug = $(this).attr('id').substring(11,12);
        var nb_sensor=0;

        //sensors: variable globale du nombre de capteurs définit par le fichier config.php
        if(sensors) {
            for (var i = 1  ; i<=sensors; i++) {
                if($("#plug_sensor"+plug+i+" option:selected").val()=="True") {
                    //Compte du nombre de capteur selectionné:
                    nb_sensor=nb_sensor+1;
                }
            }

           if(nb_sensor<=1) {
                // Si un seul capteur pour la régulation, on désactive les options de min/max/moy:
                $("#plug_compute_method"+plug).attr('disabled','disabled');
            } else {
                // Sinon les options sont activées:
                $("#plug_compute_method"+plug).removeAttr('disabled');
            }
        }
    });


    //Display options for selected output :
    $("select[name*='plug_module']").change(function () {
        var id=$(this).attr('name').substr($(this).attr('name').length-1);

        if(!isFinite(String(id))) {
            id="";
        }
        
        // Display correct option for the plug in function of type selected
        $("#select_canal_wireless"+id).css("display","none");
        $("#select_canal_direct"+id).css("display","none");
        $("#select_canal_mcp230xx"+id).css("display","none");
        $("#select_canal_dimmer"+id).css("display","none");
        $("#select_canal_network"+id).css("display","none");
        $("#select_canal_xmax"+id).css("display","none");
        $("#select_canal_" + $(this).val() + id).show();
        
        // Reset value for power
        switch($(this).val()) {
            case 'wireless' : 
                if ($("#plug_power_max"+id).val() != "1000" && $("#plug_power_max"+id).val() != "3500") {
                    $("input[name=plug_power_max" +id + "][value='1000']").prop('checked', true);
                }
                break;
            case 'direct':
                if ($("#plug_power_max"+id).val() > "<?php echo $GLOBALS['NB_MAX_CANAL_DIRECT']; ?>") {
                    $("#plug_power_max"+id).val() = "1";
                }
                break;
            case 'mcp230xx':
                if ($("#plug_power_max"+id).val() > "<?php echo $GLOBALS['NB_MAX_CANAL_MCP230XX']; ?>") {
                    $("#plug_power_max"+id).val() = "1";
                }
                break;  
            case 'dimmer':
                if ($("#plug_power_max"+id).val() > "<?php echo $GLOBALS['NB_MAX_CANAL_DIMMER']; ?>") {
                    $("#plug_power_max"+id).val() = "1";
                }
                break;  
            case 'network':
                if ($("#plug_power_max"+id).val() > "<?php echo $GLOBALS['NB_MAX_CANAL_NETWORK']; ?>") {
                    $("#plug_power_max"+id).val() = "1";
                }
                break;   
            case 'xmax':
                if ($("#plug_power_max"+id).val() > "<?php echo $GLOBALS['NB_MAX_CANAL_XMAX']; ?>") {
                    $("#plug_power_max"+id).val() = "1";
                }
                break;   
            default:
                break;
        }
    });


    //Disable previous selected dimmer canal:
    $("select[name*='dimmer_canal']").focus(function () {
        previous_canal = $(this).attr('value');
    }).change(function() {
        var prev=previous_canal;
        var id=$(this).attr('name').substr($(this).attr('name').length-1);
        var canal=$("#dimmer_canal"+id+" option:selected" ).val();

        $("select[name*='dimmer_canal']").each(function( index ) {
            var new_id=$(this).attr('name').substr($(this).attr('name').length-1);
            if(new_id!=id) {
                var option = $("option[value='" + canal + "']", this);
                option.attr("disabled","disabled");

                var option = $("option[value='" + prev + "']", this);
                option.removeAttr("disabled");
            }
        });

        $("input[name='plug_power_max"+id+"']").focus();
    });


    $('[id*="plug_type"]').change(function() {
        var plug = $(this).attr('id').substring(9,10);

        if(plug!="") {
            if(!(plug in plug_alert_change)) { 
                $.ajax({
                    cache: false,
                    async: true,
                    url: "main/modules/external/get_variable.php",
                    data: {name:'CHECK_PROGRAM',value:plug}
                }).done(function (data) {
                    if(jQuery.parseJSON(data)=="1") {
                        $("#warning_change_type_plug").dialog({
                            resizable: false,
                            height:200,
                            width: 500,
                            closeOnEscape: false,
                            modal: true,
                            dialogClass: "dialog_cultibox",
                            buttons: [{
                            text: OK_button,
                            click: function () {
                                plug_alert_change[plug]=true; 
                                $( this ).dialog( "close" ); 
                            }
                        }, {
                            text: CANCEL_button,
                                click: function () {
                                    $("#plug_type"+plug).val(plugs_infoJS[plug-1]['PLUG_TYPE]']);
                                    $( this ).dialog( "close" );
                                    return false;
                                }
                            }]
                        });
                    }
                }); 
            }
        }
    });

    // Check errors for the plugs part on submit:
    $("#reccord_plugs, [id^='jumpto']").click(function(e) { 
        var button_click=$(this).attr('id');
        e.preventDefault();
        var checked=true;
        var jump_plug=1;

        for(i=1;i<=nb_plugs;i++) {

            $("#error_power_value"+i).css("display","none");
            $("#error_tolerance_value_humi"+i).css("display","none");
            $("#error_tolerance_value_temp"+i).css("display","none");
            $("#error_tolerance_value_water"+i).css("display","none");
            $("#error_second_tolerance_value_humi"+i).css("display","none");
            $("#error_second_tolerance_value_temp"+i).css("display","none");
            $("#error_regul_value"+i).css("display","none");

            if($("#power_value"+i).val()) {
                //Check power value:
                $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/check_value.php",
                        data: {
                            value:$("#power_value"+i).val(),
                            type:'numeric'
                        }
                    }).done(function(data) {
                        if(data!=1) {
                            $("#error_power_value"+i).show(700);
                            checked=false;
                            jump_plug=i;
                        }
                });
            }


            //Check tolerance value
            if($("#plug_type"+i).val()=="heating" ||
               $("#plug_type"+i).val()=="humidifier" ||
               $("#plug_type"+i).val()=="dehumidifier" ||
               $("#plug_type"+i).val()=="ventilator" || 
               $("#plug_type"+i).val()=="pump" || 
               $("#plug_type"+i).val()=="extractor" || 
               $("#plug_type"+i).val()=="intractor" || 
               $("#plug_type"+i).val()=="pumpfiling" || 
               $("#plug_type"+i).val()=="pumpempting")
            {
                if($("#plug_tolerance"+i).val()=="0" || $("#plug_tolerance"+i).val()=="" )
                {
                   $("#plug_tolerance"+i).val('0'); 
                } else { 
                    $("#plug_tolerance"+i).val($("#plug_tolerance"+i).val().replace(",","."));
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/check_value.php",
                        data: {
                            value:$("#plug_tolerance"+i).val(),
                            type:'tolerance',
                            plug: $("#plug_type"+i).val()
                        }
                    }).done(function(data) {
                        if(data!=1) {
                            switch ($("#plug_type"+i).val()) {
                                case 'humidifier' :
                                case 'dehumidifier' :
                                    $("#error_tolerance_value_humi"+i).show(700);
                                    break;
                                case 'extractor' :
                                case 'intractor' :
                                case 'ventilator' :
                                case 'heating' :
                                    $("#error_tolerance_value_temp"+i).show(700);
                                    break;
                                case 'pumpfiling' :
                                case 'pumpempting' :
                                case 'pump' :
                                    $("#error_tolerance_value_water"+i).show(700);
                                    break;
                            }

                            checked=false;
                            jump_plug=i;
                        }
                    });
                }


                //Check the second regul values:
                if($("#plug_regul"+i).val()=="True") {
                    if($("#plug_second_tolerance"+i).val()=="") {
                        $("#plug_second_tolerance"+i).val('0.0');
                    } else {
                        if($("#plug_second_tolerance"+i).val()=="0") $("#plug_second_tolerance"+i).val('0.0');
                        $("#plug_second_tolerance"+i).val($("#plug_second_tolerance"+i).val().replace(",","."));
                        $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/check_value.php",
                        data: {value:$("#plug_second_tolerance"+i).val(),type:'tolerance',plug: $("#plug_type"+i).val()}
                        }).done(function(data) {
                            if(data!=1) {
                            
                                switch ($("#plug_type"+i).val()) {
                                    case 'humidifier' :
                                    case 'dehumidifier' :
                                        $("#error_second_tolerance_value_temp"+i).show(700);
                                        break;
                                    case 'extractor' :
                                    case 'intractor' :
                                    case 'ventilator' :
                                    case 'heating' :
                                        $("#error_second_tolerance_value_humi"+i).show(700);
                                        break;
                                    case 'pumpfiling' :
                                    case 'pumpempting' :
                                    case 'pump' :
                                        // TODO : Normallement la régulation secondaire est désactivé pour la pompe
                                        // Dans ce cas, ce code ne doit pas exister
                                        $("#error_second_tolerance_value_humi"+i).show(700);
                                        break;
                                }

                                checked=false;
                                jump_plug=i;
                            }
                        });
                    } 


                    if(($("#plug_regul_value"+i).val()=="0")||($("#plug_regul_value"+i).val()=="")) {
                        $("#error_regul_value"+i).show(700);
                        checked=false;
                        jump_plug=i;
                    } else {
                        $("#plug_regul_value"+i).val($("#plug_regul_value"+i).val().replace(",","."));
                        $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/check_value.php",
                        data: {value:$("#plug_regul_value"+i).val(),type:'regulation'}
                        }).done(function(data) {
                            if(data!=1) {
                                $("#error_regul_value"+i).show(700);
                                checked=false;
                                jump_plug=i;
                            }
                        });
                    }

                    
                    var nb_sensor=0;

                    //sensors: variable globale du nombre de capteurs définit par le fichier config.php
                    if(sensors) {
                        for (var j = 1  ; j<=sensors; j++) {
                            if($("#plug_sensor"+i+j+" option:selected").val()=="True") {
                                //Compte du nombre de capteur selectionné:
                                nb_sensor=nb_sensor+1;
                            }
                        }

                        if(nb_sensor<=1) {
                            // Si un seul capteur pour la régulation, on désactive les options de min/max/moy:
                            $("#plug_compute_method"+i).attr('disabled','disabled');
                        } else {
                            // Sinon les options sont activées:
                            $("#plug_compute_method"+i).removeAttr('disabled');
                        }

                        if(nb_sensor==0) {
                            // Si aucun capteur n'est selectionné: affichage du message précisant que le capteur 1 sera selectionné + selection automatique du capteur 1
                            $("#error_select_sensor"+i).show();
                            $("#plug_sensor"+i+"1 option[value='True']").prop('selected', 'selected');
                            checked=false;
                            jump_plug=i;
                        } else {
                            // On efface le message d'erreur sinon
                            $("#error_select_sensor"+i).css("display","none");
                        }

                        if(nb_sensor<=1) {
                            // Si un seul capteur pour la régulation, on désactive les options de min/max/moy:
                            $("#plug_compute_method"+i).attr('disabled','disabled');
                        } else {
                            // Sinon les options sont activées:
                            $("#plug_compute_method"+i).removeAttr('disabled');
                        }
                    }
                }

            }
        }

        // Errors have been checked, we can continue
        if(checked) {
            var check_update=true;
            // block user interface during saving;
            $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
                centerY: 0,
                css: {
                    top: '20%',
                    border: 'none',
                    padding: '5px',
                    backgroundColor: 'grey',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .9,
                    color: '#fffff'
                },
                onBlock: function() {
                    for(i=1;i<=nb_plugs;i++) {
                        var data_array = {};
                        
                        // For each input, add this to the array
                        $("#state_plug"+i+" :input").each(function() {
                            data_array[$(this).attr('name')]=$(this).val();
                        }); 

                        // Add Number of plug to the array
                        data_array['number']=i;
                        
                        // Add Plug power max to the array
                        data_array['plug_power_max'+i]=$('input[name=plug_power_max'+i+']:checked').val();
                        
                        // Add module used
                        data_array['plug_module'+i] = $("select[name=plug_module"+i+"]").val();
                        
                        // Add module number used : For mcp230xx, dimmer, xmax
                        data_array['plug_num_module'+i] = 0;
                        if (data_array['plug_module'+i] == "mcp230xx" || 
                            data_array['plug_module'+i] == "dimmer" || 
                            data_array['plug_module'+i] == "xmax") {
                            data_array['plug_num_module'+i] = $("select[name=" + data_array['plug_module'+i] + "_module_num" + i + "]").val();
                        }
                        
                        // Add options only for network
                        data_array['plug_module_options'+i] = "";
                        if (data_array['plug_module'+i] == "network") {
                            data_array['plug_module_options'+i] = $("select[name=" + data_array['plug_module'+i] + "_module_options" + i + "]").val();
                        }

                        // Add output of module used : For direct, mcp230xx, dimmer, network, xmax
                        data_array['plug_module_output'+i] = 0;
                        if (data_array['plug_module'+i] == "direct" || 
                            data_array['plug_module'+i] == "mcp230xx" || 
                            data_array['plug_module'+i] == "dimmer" || 
                            data_array['plug_module'+i] == "network" || 
                            data_array['plug_module'+i] == "xmax") {
                            data_array['plug_module_output'+i] = $("select[name=" + data_array['plug_module'+i] + "_module_ouput" + i + "]").val();
                        }                        

                        $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/save_plugs_configuration.php",
                            data: data_array
                        }).done(function(data) {
                            try {
                                if(jQuery.parseJSON(data)!="1") check_update=false;
                            } catch(err) {
                                check_update=false;
                            }
                        });
                    }         

                    // Update SD Card
                    if(sd_card != "") {
                        $.ajax({
                            type: "GET",
                            url: "main/modules/external/check_and_update_sd.php",
                            data: {
                                sd_card:"<?php echo $sd_card ;?>"
                            },
                            async: false,
                            context: document.body,
                            success: function(data, textStatus, jqXHR) {
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                            // Error during request
                            }
                        });
                    }

                    $.unblockUI();


                    if(check_update) {
                        $("#update_conf").dialog({
                            resizable: false,
                            height:150,
                            width: 500,
                            closeOnEscape: false,
                            modal: true,
                            hide: "fold",
                            dialogClass: "popup_message",
                            buttons: [{
                                text: CLOSE_button,
                                click: function () {
                                    $( this ).dialog( "close" );

                                     $.ajax({
                                        cache: false,
                                        async: false,
                                        url: "main/modules/external/set_variable.php",
                                        data: {name:"UPDATED_CONF", value: "True", duration: 86400 * 365}
                                    });


                                    if(button_click.toLowerCase().indexOf("jumpto") >= 0) {
                                        get_content("programs",getUrlVars("selected_plug="+$("#submenu").val()));
                                    } else {
                                        get_content("plugs",getUrlVars("selected_plug="+$("#submenu").val()+"&submenu="+$("#submenu").val()));
                                    }
                                }
                            }]
                        });
                    } else  {
                        $("#error_update_conf").dialog({
                            resizable: false,
                            height:150,
                            width: 500,
                            closeOnEscape: false,
                            modal: true,
                            dialogClass: "popup_error",
                            hide: "fold",
                            buttons: [{
                                text: CLOSE_button,
                                click: function () {
                                    $( this ).dialog( "close" );
                                    get_content("plugs",getUrlVars("selected_plug=1"));
                                }
                            }]
                        });
                    }
                }
            });
        } else {
            expand_plug(jump_plug,<?php echo $nb_plugs; ?>);
        }
    });

    $('[id^="jump_wizard"]').click(function(e) {
        e.preventDefault();
        get_content("wizard",getUrlVars("selected_plug="+$(this).attr("id").replace("jump_wizard","")));
    });
});
</script>

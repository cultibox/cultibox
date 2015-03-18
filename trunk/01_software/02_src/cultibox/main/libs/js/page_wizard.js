<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>

step=<?php echo json_encode($step); ?>;
nb_plugs=<?php echo json_encode($nb_plugs); ?>;
selected_plug=<?php echo json_encode($selected_plug); ?>;
error_valueJS=<?php echo json_encode($error_value); ?>;
canal_status= <?php echo json_encode($status); ?>;
title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;


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


     pop_up_add_information("<?php echo __('WIZARD_DISABLE_FUNCTION'); ?>: <a href='/cultibox/index.php?menu=programs' class='href-wizard-msgbox'><img src='main/libs/img/wizard.png' alt='<?php echo __('CLASSIC'); ?>' title='' id='classic' /></a>", "jumpto_classic", "information");

   //Event fire when clicking the wizard button:
   $("#next").click(function(e) {
        e.preventDefault(); 
        step=step+1;

        var chk_plg=false; 
        if(selected_plug==nb_plugs) chk_plg=true;
        expand_wizard(step,chk_plg,selected_plug);

        plug_type=$("#plug_type option:selected").val();
        if(selected_plug>1) {
            switch (plug_type) {
                case "ventilator" :
                case "heating" :
                case "extractor" :
                case "intractor" :
                    $("#value_wished").text("<?php echo __('TEMP_WISHED','html').':'; ?>"); 
                    $("#tooltip_value").show();
                    $('#value_prog_div').append('<input type="text" maxlength="4" size="4" name="value_program" id="value_program" value="22" /><label id="label_unity">°C</label>');
                    break;
                case "pumpfiling" :
                case "pumpempting" :
                case "pump" :
                    $("#value_wished").text("<?php echo __('WATER_WISHED','html').':'; ?>");
                    $("#tooltip_value").show();
                    $('#value_prog_div').append('<input type="text" maxlength="4" size="4" name="value_program" id="value_program" value="22" /><label id="label_unity">cm</label>');
                    break;
                case "humidifier" :
                case "dehumidifier" :
                    $("#value_wished").text("<?php echo __('HUMI_WISHED','html').':'; ?>");
                    $("#tooltip_value").show();
                    $('#value_prog_div').append('<input type="text" maxlength="4" size="4" name="value_program" id="value_program" value="70" /><label id="label_unity">%</label>');
                    break;
                default :
                    $("#value_wished").text("");
                    $("#tooltip_value").css("display","none");
                    $('#value_prog_div').append('<input type="hidden" name="value_program" id="value_program" value="99.9" />');
                    $("#label_unity").text("");
                    break;
            }
        }
    });

    $("#previous").click(function(e) {
        e.preventDefault();
        step=step-1;
        
        var chk_plg=false;
        if(selected_plug==nb_plugs) chk_plg=true;
        expand_wizard(step,chk_plg,selected_plug);

        if(selected_plug>1) {
            $("#value_wished").text("");
            $("#tooltip_value").css("display","none");
            $('#value_program').remove();
            $('#label_unity').remove();
            $("#label_unity").text("");
        }
    });

    $('input[name=plug_power_max]').change(function() {
        if($("input[name=plug_power_max]:checked").val()=="VARIO") {
            $("#select_canal_dimmer").show();
        } else {
            $("#select_canal_dimmer").css("display","none");
        }

    });

    


   $("#close").click(function(e) {
       e.preventDefault();
       var get_urls = getUrlVars('selected_plug=<?php echo $selected_plug; ?>');
       get_content("programs",get_urls);
   });

  if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", duration: 36000}
        });
    }

    $("#classic").click(function(e) {
        e.preventDefault();
        get_content("programs",getUrlVars("selected_plug=1"));
    });

$('#start_time').timepicker({
    <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
    showSecond: true,
    showOn: 'both',
    buttonImage: "main/libs/img/datepicker.png",
    buttonImageOnly: 'true',
    <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT')."',"; ?>
    timeFormat: 'hh:mm:ss',
    <?php echo "timeText: '".__('TIMEPICKER_TIME')."',"; ?>
    <?php echo "hourText: '".__('TIMEPICKER_HOUR')."',"; ?>
    <?php echo "minuteText: '".__('TIMEPICKER_MINUTE')."',"; ?>
    <?php echo "secondText: '".__('TIMEPICKER_SECOND')."',"; ?>
    <?php echo "currentText: '".__('TIMEPICKER_ENDDAY')."',"; ?>
    <?php echo "closeText: '".__('TIMEPICKER_CLOSE')."'"; ?>
});
$('#end_time').timepicker({
    <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
    showOn: 'both',
    showSecond: true,
    buttonImage: "main/libs/img/datepicker.png",
    buttonImageOnly: 'true',
    <?php
        echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT')."',";
        echo "timeFormat: 'hh:mm:ss',";
        echo "timeText: '".__('TIMEPICKER_TIME')."',";
        echo "hourText: '".__('TIMEPICKER_HOUR')."',";
        echo "minuteText: '".__('TIMEPICKER_MINUTE')."',";
        echo "secondText: '".__('TIMEPICKER_SECOND')."',";
        echo "currentText: '".__('TIMEPICKER_ENDDAY')."',";
        echo "closeText: '".__('TIMEPICKER_CLOSE')."'";
    ?>
});

$("#value_program").keypress(function(e) {
    if(!VerifNumber(e)) e.preventDefault();
});



    // Check errors for the wizard part:
    $("#finish, #next_plug").click(function(e) {
        e.preventDefault();
        var checked=true;

        $("#error_start_time").css("display","none");
        $("#error_end_time").css("display","none");
        $("#error_same_start").css("display","none");
        $("#error_same_end").css("display","none");
        $("#error_value_program").css("display","none");

        var checked=true;
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/check_value.php",
            data: {value:$("#start_time").val(),type:'time'}
        }).done(function (data) {
            if(data!=1) {
                $("#error_start_time").show(700);
                $("#start_time").val("06:00:00");
                checked=false;
            }
        });

        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/check_value.php",
            data: {value:$("#end_time").val(),type:'time'}
        }).done(function (data) {
            if(data!=1) {
                $("#error_end_time").show(700);
                $("#end_time").val("18:00:00");
                checked=false;
            }
        });


        if(checked) {
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#start_time").val()+"_"+$("#end_time").val(),type:'same_time'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_same_start").show(700);
                    $("#error_same_end").show(700);
                    checked=false;
                }
            });
        }

        plug_type=$("#plug_type option:selected").val();
        switch (plug_type) {
            case "extractor":
            case "intractor":
            case "ventilator":
            case "heating":
            case "pumpfiling":
            case "pumpempting":
            case "pump":
            case "humidifier":
            case "dehumidifier":
                if(($("#value_program").val())&&($("#value_program").val()!="")) {
                    $("#value_program").val($("#value_program").val().replace(",","."));
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/check_value.php",
                        data: {value:$("#value_program").val(),type:'value_program',plug_type:plug_type}
                    }).done(function (data) {
                            var return_array = JSON.parse(data);
                            if(parseInt(return_array['error'])>1) {
                                if(parseInt(return_array['error'])==2) {
                                    $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[parseInt(return_array['error'])]);
                                } else {
                                    $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[parseInt(return_array['error'])]+": "+return_array['min']+return_array['unity']+" <?php echo __('AND'); ?> "+return_array['max']+return_array['unity']);

                                }
                                $("#error_value_program").show(700);

                                switch (plug_type) {
                                    case 'dehumidifier':
                                    case 'humidifier': 
                                        $("#value_program").val("55");
                                        break;
                                    case 'ventilator':
                                    case 'heating':
                                    case 'extractor' :
                                    case 'intractor' :
                                    case 'pump':
                                    case 'pumpfiling' :
                                    case 'pumpempting' :
                                        $("#value_program").val("22");
                                        break;
                                    default: 
                                        break;
                                }
                                checked=false;
                            }
                   });
                } else {
                    $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[2]);
                    $("#error_value_program").show(700);
                    checked=false;

                    switch (plug_type) {
                        case 'dehumidifier':
                        case 'humidifier': 
                            $("#value_program").val("55");
                            break;
                        case 'ventilator':
                        case 'heating':
                        case 'extractor' :
                        case 'intractor' :
                        case 'pump':
                        case 'pumpfiling' :
                        case 'pumpempting' :
                            $("#value_program").val("22");
                            break;
                        default: 
                            break;
                    }
                }
                break;
            default:
                $regul       = getvar("plug_regul${nb}");
                $regul_senss = getvar("plug_senss${nb}");
                $regul_value = getvar("plug_regul_value${nb}");
                $regul_value = str_replace(',','.',$regul_value);
                $regul_value = str_replace(' ','',$regul_value);
                $second_tol  = getvar("plug_second_tolerance${nb}");
                $second_tol  = str_replace(',','.',$second_tol);
                break;
        }

        if(checked) {
            var actionID=$(this).attr('id');
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
                    var data_array = {};
                    $("#submit_wizard :input").each(function() {
                        data_array[$(this).attr('name')]=$(this).val();
                    });

                    if(actionID=="finish") {
                        data_array['type_submit']="submit_close";
                    } else {
                        data_array['type_submit']="submit_next";
                    }

                    
                    //Customization:
                    data_array['plug_power_max']=$('input[name=plug_power_max]:checked').val();
                    data_array['selected_plug']=<?php echo $selected_plug; ?>;

                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/save_wizard_config.php",
                        data: data_array
                    }).done(function(data) {
                         if(sd_card!="") {
                            $.ajax({
                                type: "GET",
                                url: "main/modules/external/check_and_update_sd.php",
                                data: {
                                    sd_card:"<?php echo $sd_card ;?>"
                                },
                                async: false,
                                success: function(data, textStatus, jqXHR) { },
                                error: function(jqXHR, textStatus, errorThrown) { }
                            });
                        }


                        $.unblockUI();

                        try { 
                            if(jQuery.parseJSON(data)=="submit_close") {
                                $.ajax({
                                    cache: false,
                                    async: false,
                                    url: "main/modules/external/set_variable.php",
                                    data: {name:"UPDATED_CONF", value: "True", duration: 86400 * 365}
                                });

                                get_content("programs",getUrlVars('selected_plug=<?php echo $selected_plug; ?>'));
                            } else {
                                get_content("wizard",getUrlVars('selected_plug=<?php echo $selected_plug+1; ?>'));
                            }
                        } catch(err) {
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
                                        get_content("wizard",getUrlVars("selected_plug=<?php echo $selected_plug; ?>"));
                                    }
                                }]
                            });
                        }
                    });
             }});
        }
    });
});
</script>



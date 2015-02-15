<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>


rtc_offset_value=<?php echo json_encode($rtc_offset) ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;
var ajax_format;


formatCard = function(hdd,pourcent) {
    ajax_format = $.ajax({ 
        cache: false,
        url: "main/modules/external/format.php",
        data: {hdd:hdd, progress: parseInt(pourcent)}
    }).done(function (data) {
        $("#progress_bar").progressbar({ value: 4*parseInt(data) });
        if(data==100) { 
            $("#success_format").show();
            $("#btnCancel").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
            return true;
        } else if(data>=0) { 
                formatCard(hdd,parseInt(data)); 
        } else {
            $("#error_format").show();
            $("#btnCancel").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
        }
    });
}


// {{{ getAlarm()
// ROLE display or not the alarm part from the configuration menu
// IN  input value: display or not 
// HOW IT WORKS: get id from div to be displayed the alarm configuration or not and display it (or not) depending the input value
// USED BY: templates/configuration.html
function getAlarm(i) {
      var divAval = document.getElementById('div_alarm_value');
      var labelAvalue = document.getElementById('label_alarm_value');

      switch(i) {
         case 0 : divAval.style.display = ''; labelAvalue.style.display = ''; break;
         case 1 : divAval.style.display = 'none'; labelAvalue.style.display = 'none'; break;
         default: divAval.style.display = ''; labelAvalue.style.display = ''; break;
      }
}
//}}}


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

      if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", duration: 36000}
        });
    }

    $('#reset_minmax').timepicker({
        <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
        showOn: 'both',
        buttonImage: "main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        buttonText: "<?php echo __('TIMEPICKER_BUTTON_TEXT') ;?>",
        timeFormat: 'hh:mm',
        timeText: "<?php echo __('TIMEPICKER_TIME') ;?>",
        hourText: "<?php echo __('TIMEPICKER_HOUR') ;?>",
        minuteText: "<?php echo __('TIMEPICKER_MINUTE') ;?>",
        secondText: "<?php echo __('TIMEPICKER_SECOND') ;?>",
        currentText: "<?php echo __('TIMEPICKER_ENDDAY') ;?>",
        closeText: "<?php echo __('TIMEPICKER_CLOSE') ;?>"
    });

    
    // Call the fileupload widget and set some parameters
     $('#confupload').fileupload({
        dataType: 'json',
        url: 'main/modules/external/files.php',
        add: function (e, data) {
            var name="";
            $.each(data.files, function (index, file) {
                name=file.name;
            });

            $('#import_conf_name').text(name);
            $('#import_conf').val("<?php echo __('IMPORT_PROGRAM'); ?>");
            $('#import_conf').text("<?php echo __('IMPORT_PROGRAM'); ?>");
            $('#import_conf').removeClass("inputDisable");
            $('#import_conf').attr('disabled', false);


            data.context = $('#import_conf').click(function (e) {
                e.preventDefault();
                $.blockUI({
                message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                    data.submit();
                } });
            });
        },
        done: function (e, data) {
            e.preventDefault();

            var name="";
            $.each(data.result.files, function (index, file) {
                name=file.name;
            });


            $.blockUI({
                message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/import_soft_config.php",
                        data: {filename:name}
                    }).done(function (data) {
                        var json = jQuery.parseJSON(data);
                        if(json==0) {
                            $.ajax({
                                type: "GET",
                                url: "main/modules/external/check_and_update_sd.php",
                                data: {
                                    sd_card:sd_card
                                },
                                async: false
                            });
                            $.unblockUI();

                            $("#success_import_conf").dialog({
                                resizable: false,
                                width: 500,
                                modal: true,
                                closeOnEscape: true,
                                dialogClass: "popup_message",
                                buttons: [{
                                    text: CLOSE_button,
                                    click: function () {
                                        $( this ).dialog( "close" );
                                        var get_array = {};
                                        get_content("welcome",get_array);
                                        return false;
                                    }
                                }]
                            });
                        } else {
                            $.unblockUI();
                            $("#error_import_conf").dialog({
                                resizable: false,
                                width: 500,
                                closeOnEscape: true,
                                modal: true,
                                dialogClass: "popup_error",
                                buttons: [{
                                    text: CLOSE_button,
                                    click: function () {
                                        $( this ).dialog( "close" ); return false;
                                    }
                                }]
                            });
                        }
                    });
                }
            });
        }});




    //Export configuration:
     $('#export_conf').click(function(e) {
        e.preventDefault();

         $.blockUI({
                message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/export_conf.php"
                    }).done(function (data) {
                        var json = jQuery.parseJSON(data);
                        $.unblockUI();
                        if(json==1) {
                            $.fileDownload('tmp/export/backup_cultibox.sql');
                        } else if(json==2) {
                            $.fileDownload('tmp/export/backup_cultibox.sql.zip');
                        }
                    });
                }
            });
    });



    // Check errors for the configuration part:
    $("#submit_conf").click(function(e) {
      e.preventDefault();

      // block user interface during checking and saving
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

        var checked=true;

       
        if(typeof($("#reset_minmax").val())!="undefined") { 
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#reset_minmax").val(),type:'short_time'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_min_max").show(700);
                    checked=false;
                    expand('system_interface');
                } else {
                    $("#error_min_max").css("display","none");
                }
            });
        }

        if($("#alarm_activ option:selected").val()=="0001") {
            $("#alarm_value").val($("#alarm_value").val().replace(",","."));
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#alarm_value").val(),type:'alarm_value'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_alarm_value").show(700);
                    checked=false;
                    expand('alarm_interface');
                } else {
                    $("#error_alarm_value").css("display","none");
                }
            });
        }


        if(checked) {
            var check_update=true;
            $("select").each(function() {
                newValue    = $( this ).find(":selected").val();
                varToUpdate = $( this ).attr('name');


                if($.trim(varToUpdate)!="") {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_configuration.php",
                        data: {
                            value:newValue,
                            variable:varToUpdate,
                            sd_card:sd_card
                        }
                    }).done(function (data) {
                        try{
                            if($.parseJSON(data)!="") {  
                                check_update=false;
                            }
                        } catch(err) {
                                check_update=false;
                        }
                    });
                }
            });


            //If advanced regulation is disabled, we use default value:
            if($("#advanced_regul_options option:selected").val()=="False") {
                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "main/modules/external/update_plugs.php",
                    data: {
                        value:"False",
                        id:"all",
                        name:"PLUG_REGUL"
                    }
                }).done(function (data) {
                    try {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    } catch(err) {
                        check_update=false;
                    }
                });


                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "main/modules/external/update_plugs.php",
                    data: {
                        value:"M", 
                        id:"all",
                        name:"PLUG_COMPUTE_METHOD"
                    }
                }).done(function (data) {
                    try {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    } catch(err) {
                        check_update=false;
                    }
                });


                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "main/modules/external/update_plugs.php",
                    data: {
                        value:"1",     
                        id:"all",
                        name:"PLUG_REGUL_SENSOR"
                    }
                }).done(function (data) {
                    try {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    } catch(err) {
                        check_update=false;
                    }
                });
            }



            //RTC OFFSET process:
            newValue    = $("#rtc_offset").val();
            varToUpdate = $("#rtc_offset").attr('name');

             $.ajax({
                type: "GET",
                cache: false,
                async: false,
                url: "main/modules/external/update_configuration.php",
                data: {
                        value:newValue, 
                        variable:varToUpdate,
                        sd_card:sd_card
                    }
            }).done(function (data) {
                try {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    } catch(err) {
                        check_update=false;
                    }
            });

            //RESET MIN MAX process:
            newValue    = $("#reset_minmax").val();
            varToUpdate = $("#reset_minmax").attr('name');

            $.ajax({
                type: "GET",
                cache: false,
                async: false,
                url: "main/modules/external/update_configuration.php",
                data: {
                        value:newValue,
                        variable:varToUpdate,
                        sd_card:sd_card
                    }
            }).done(function (data) {
                try {
                    if($.parseJSON(data)!="") {
                        check_update=false;
                    }
                } catch(err) {
                    check_update=false;
                }
            });


            //ALARM VALUE process:
            if($("#alarm_activ option:selected").val()=="0001") {
                newValue    = $("#alarm_value").val();
            } else {
                newValue    = 60;
            }

            varToUpdate = $("#alarm_value").attr('name');

            $.ajax({
                type: "GET",
                cache: false,
                async: false,
                url: "main/modules/external/update_configuration.php",
                data: {
                        value:newValue,
                        variable:varToUpdate,
                        sd_card:sd_card
                    }
            }).done(function (data) {
                try {
                    if($.parseJSON(data)!="") {
                        check_update=false;
                    }
                } catch(err) {
                    check_update=false;
                }
            });


            if(sd_card!="") {
                $.ajax({
                    type: "GET",
                    url: "main/modules/external/check_and_update_sd.php",
                    data: {
                        force_rtc_offset_value:1,
                        sd_card:sd_card
                    },
                    async: false
                }).done(function(data, textStatus, jqXHR) {
                    try {
                        // Parse result
                        var json = jQuery.parseJSON(data);

                        // For each information, show it
                        json.error.forEach(function(entry) {
                            check_update=false;
                        });
                    } catch(err) {
                        check_update=false;
                    }
                }); 
            }


             $.ajax({
                cache: false,
                url: "main/modules/external/get_variable.php",
                data: {name:"cost"}
            }).done(function (data) {
                try {
                    if(jQuery.parseJSON(data)=="1") {
                        $("#menu-cost").show();
                    } else {
                        $("#menu-cost").css('display','none');
                    }
                } catch(err) {

                }
            });


            <?php if((isset($GLOBALS['MODE']))&&(strcmp($GLOBALS['MODE'],"cultipi")==0)) { ?>
                $.ajax({
                    cache: false,
                    url: "main/modules/external/get_variable.php",
                    data: {name:"webcam"}
                }).done(function (data) {
                    try {
                        if(jQuery.parseJSON(data)=="1") {
                            $("#menu-webcam").show();
                        } else {
                            $("#menu-webcam").css('display','none');
                        }
                    } catch(err) {
                    }
                });
            <?php } ?>


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

                                    var get_array = getUrlVars('submenu='+$("#submenu").val());
                                    get_content("configuration",get_array);
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
                                    var get_array = getUrlVars('submenu='+$("#submenu").val());
                                    get_content("configuration",get_array);
                                }
                            }]
                        });
           }
        }
      } });
      $.unblockUI();
    }); 
   

    <?php if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) { ?>
        $("#rtc_offset_slider").slider({
            max: 100,
            min: -100,
            slide: function( event, ui ) {
                // While sliding, update the value in the div element
                $("#rtc_offset").val(ui.value);
            },
            step: 1,
            value: rtc_offset_value
        });
    <?php } ?> 
    

    $("#reset_sd_card_submit").click(function(e) {
        e.preventDefault();
        $.ajax({
            cache: false,
            url: "main/modules/external/check_sd.php",
            data: {path:$("#selected_hdd").val()}
         }).done(function (data) {
            if(data=="0") {
                $("#locked_sd_card").dialog({ width: 550, resizable: false, closeOnEscape: false, buttons: [{ text: CLOSE_button, click: function() { $( this ).dialog( "close" ); get_content("configuration",getUrlVars("submenu=card_interface")); } }], hide: "fold", modal: true,  dialogClass: "popup_error"  });
            } else {
                $("#format_dialog_sd").dialog({
                    resizable: false,
                    height:200,
                    width: 500,
                    closeOnEscape: false,
                    modal: true,
                    dialogClass: "dialog_cultibox",
                    buttons: [{
                        text: OK_button,
                        click: function () {
                            $( this ).dialog( "close" ); 
                            $("#progress").dialog({
                                resizable: false,
                                height:200,
                                width: 500,
                                closeOnEscape: false,
                                modal: true,
                                dialogClass: "popup_message",
                                buttons: [{
                                    text: CANCEL_button,
                                    "id": "btnCancel",
                                    click: function () {
                                        var get_array = getUrlVars('submenu=card_interface');
                                        $(this).dialog('destroy').remove();
                                        get_content("configuration",get_array);
                                    }
                                }]
                            });
                            stop_format=false;
                            $("#progress_bar").progressbar({value:0});
                            $("#success_format").css("display","none");
                            $("#error_format").css("display","none");
                            formatCard($("#selected_hdd").val(),0);
                        }
                    }, {
                        text: CANCEL_button,
                        click: function () {
                            ajax_format.abort();
                            var get_array = getUrlVars('submenu=card_interface');
                            $(this).dialog('destroy').remove();
                            get_content("configuration",get_array);
                        }
                    }]
                });
            }
        });
    });
    
});

</script>

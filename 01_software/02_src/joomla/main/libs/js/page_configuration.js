<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>


wifi_password=<?php echo(json_encode($wifi_password)); ?>;
rtc_offset_value=<?php echo json_encode($rtc_offset) ?>;
session_id="<?php echo session_id(); ?>";
update_menu=false;


formatCard = function(hdd,pourcent) {
    $.ajax({ 
        cache: false,
        url: "../../main/modules/external/format.php",
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

$(document).ready(function(){
      if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "../../main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", session_id:session_id}
        });
    }

    $('#reset_minmax').timepicker({
        <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
        showOn: 'both',
        buttonImage: "../../main/libs/img/datepicker.png",
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


    $("#eyes").mousedown(function() {
        $("#current_wifi_password").show();
        $("#wifi_password").css("display","none");
    });

    $("#eyes").mouseup(function(){
            $("#wifi_password").show();
            $("#current_wifi_password").css("display","none");
    });

    $("#eyes").mouseleave(function(){
            $("#wifi_password").show();
            $("#current_wifi_password").css("display","none");
    });
    
    
    $("#wifi_ip_manual").click(function(e) {
        if($('#wifi_ip_manual').prop('checked')) { 
            $('#wifi_ip').prop('disabled', false);
        } else {
            $('#wifi_ip').prop('disabled', true);
        }
    });
    
    // Check errors for the configuration part:
    $("#submit_conf").click(function(e) {
      e.preventDefault();

      // block user interface during checking and saving
      $.blockUI({ message: '', onBlock: function() {

        var checked=true;
        
        $.ajax({
            cache: false,
            async: false,
            url: "../../main/modules/external/check_value.php",
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

        if($("#alarm_activ option:selected").val()=="0001") {
            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
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

        if($("#WIFI option:selected").val()=="1") {
            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#wifi_ssid").val(),type:'ssid'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_wifi_ssid").show(700);
                    checked=false;
                    expand('wifi_interface');
                } else {
                    $("#error_wifi_ssid").css("display","none");
                }
            });


            var type_password="";
            switch ($("#wifi_key_type").val()) {
                case 'NONE': type_password="password_none";
                        break;
                case 'WEP': type_password="password_wep"
                        break;
                case 'WPA': type_password="password_wpa";
                        break;
                case 'WPA2': type_password="password_wpa";
                        break;
                case 'WPA-AUTO': type_password="password_wpa";
                        break;
                default: type_password="";
            }


            if($("#wifi_key_type").val()!="NONE") {
                if((wifi_password=="")&&($("#wifi_password").val()=="")) {
                    $("#error_empty_password").css("display","");
                } else if((wifi_password!="")&&($("#wifi_password").val()=="")&&($("#wifi_key_type").val()!="NONE")) {
                     $.ajax({
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/check_value.php",
                        data: {value:wifi_password,type:type_password}
                     }).done(function (data) {
                        if(data!=1)  {
                                checked=false;
                                expand('wifi_interface');
                                switch (type_password) {
                                    case 'password_wep':
                                            $("#error_password_wep").show(700);
                                            $("#error_password_wpa").css("display","none");
                                            break;
                                    case 'password_wpa':
                                            $("#error_password_wep").css("display","none");
                                            $("#error_password_wpa").show(700);
                                            break;
                                    default:
                                            $("#error_password_wep").css("display","none")
                                            $("#error_password_wpa").css("display","none");
                                }
                            } else {
                                $("#error_password_wep").css("display","none");
                                $("#error_password_wpa").css("display","none");
                            }
                    });
                } else if($("#wifi_password").val()!="") {
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/check_value.php",
                        data: {value:$("#wifi_password").val()+"____"+$("#wifi_password_confirm").val(),type:'password'}
                    }).done(function (data) {
                        $("#error_empty_password").css("display","none");
                        if(data!=1) {
                            $("#error_wifi_password").show(700);
                            $("#error_wifi_password_confirm").show(700);
                            $("#error_password_wep").css("display","none");
                            $("#error_password_wpa").css("display","none");
                            checked=false;
                            expand('wifi_interface');
                        } else {
                            $("#error_wifi_password").css("display","none");
                            $("#error_wifi_password_confirm").css("display","none");

                            $.ajax({
                                cache: false,
                                async: false,
                                url: "../../main/modules/external/check_value.php",
                                data: {value:$("#wifi_password").val(),type:type_password}
                            }).done(function (data) {
                                if(data!=1)  {
                                    checked=false;
                                    expand('wifi_interface');
                                    switch (type_password) {
                                        case 'password_wep': 
                                                $("#error_password_wep").show(700);
                                                $("#error_password_wpa").css("display","none");
                                                break;
                                        case 'password_wpa': 
                                                $("#error_password_wep").css("display","none");
                                                $("#error_password_wpa").show(700);
                                                break;
                                        default: 
                                                $("#error_password_wep").css("display","none")
                                                $("#error_password_wpa").css("display","none");
                                    }
                                } else {
                                    $("#error_password_wep").css("display","none");
                                    $("#error_password_wpa").css("display","none");
                                }
                            });
                        }
                    });
                }
                

                if(($("#wifi_password").val()=="")&&(wifi_password=="")) {
                    checked=false;
                    expand('wifi_interface');       
                    $("#error_empty_password").show(700);
                    $("#error_wifi_password").css("display","none");
                    $("#error_wifi_password_confirm").css("display","none");
                } else {
                    $("#error_empty_password").css("display","none");
                } 
            } 
                


            if($('#wifi_ip_manual').prop('checked')) {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#wifi_ip").val(),type:'ip'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_wifi_ip").show(700);
                        checked=false;
                        expand('wifi_interface');
                    } else {
                        $("#error_wifi_ip").css("display","none");
                    }
                });
            }
        }

        if(checked) {
            if(update_menu) {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/configure_menu.php",
                    data: {cost:$("#show_cost").val(),wifi:$("#WIFI").val()}
                });
            }
           
            
            var check_update=true;
            $("select").each(function() {
                newValue    = $( this ).find(":selected").val();
                varToUpdate = $( this ).attr('name');

                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/update_configuration.php",
                    data: {
                        value:newValue,
                        variable:varToUpdate,
                        session_id:session_id,
                        sd_card:sd_card
                    }
                }).done(function (data) {
                    if($.parseJSON(data)!="") {  
                        check_update=false;
                    }
                });
            });


            //If second regulation is disabled, we uses default value:
            if($("#second_regul option:selected").val()=="False") {
                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/update_plugs.php",
                    data: {
                        value:"False", 
                        id:"all",
                        name:"PLUG_REGUL",
                        session_id:session_id
                    }
                }).done(function (data) {
                    if($.parseJSON(data)!="") {
                        check_update=false;
                    }
                });
            }


            //If advanced regulation is disabled, we use default value:
            if($("#advanced_regul_options option:selected").val()=="False") {
                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/update_plugs.php",
                    data: {
                        value:"M", 
                        id:"all",
                        name:"PLUG_COMPUTE_METHOD",
                        session_id:session_id
                    }
                }).done(function (data) {
                    if($.parseJSON(data)!="") {
                        check_update=false;
                    }
                });


                $.ajax({
                    type: "GET",
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/update_plugs.php",
                    data: {
                        value:"1",     
                        id:"all",
                        name:"PLUG_REGUL_SENSOR",
                        session_id:session_id
                    }
                }).done(function (data) {
                    if($.parseJSON(data)!="") {
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
                url: "../../main/modules/external/update_configuration.php",
                data: {
                        value:newValue,     
                        variable:varToUpdate,
                        session_id:session_id,
                        sd_card:sd_card
                    }
            }).done(function (data) {
                if($.parseJSON(data)!="") {
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
                url: "../../main/modules/external/update_configuration.php",
                data: {
                        value:newValue,
                        variable:varToUpdate,
                        session_id:session_id,
                        sd_card:sd_card
                    }
            }).done(function (data) {
                if($.parseJSON(data)!="") {
                    check_update=false;
                }
            });


            //ALARM VALUE process:
            if($("#alarm_activ option:selected").val()=="0001") {
                newValue    = $("#alarm_value").val();
            } else {
                newValue    = 15;
            }

            varToUpdate = $("#alarm_value").attr('name');

            $.ajax({
                type: "GET",
                cache: false,
                async: false,
                url: "../../main/modules/external/update_configuration.php",
                data: {
                        value:newValue,
                        variable:varToUpdate,
                        session_id:session_id,
                        sd_card:sd_card
                    }
            }).done(function (data) {
                if($.parseJSON(data)!="") {
                    check_update=false;
                }
            });


            if($("#WIFI option:selected").val()=="1") {
                    newValue    = $("#wifi_ssid").val();
                    varToUpdate = $("#wifi_ssid").attr('name');

                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/update_configuration.php",
                        data: {
                        value:newValue,
                        variable:varToUpdate,
                        session_id:session_id,
                        sd_card:sd_card
                    }
                    }).done(function (data) {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    });

                    
                    if($('#wifi_ip_manual').prop('checked')) {
                        newValue    = 1;
                        varToUpdate = "wifi_ip_manual";

                        $.ajax({
                            type: "GET",
                            cache: false,
                            async: false,
                            url: "../../main/modules/external/update_configuration.php",
                            data: {
                                value:newValue,
                                variable:varToUpdate,
                                session_id:session_id,
                                sd_card:sd_card
                            }
                        }).done(function (data) {
                            if($.parseJSON(data)!="") {
                                check_update=false;
                            }
                        });


                        newValue    = $("#wifi_ip").val();
                        varToUpdate = $("#wifi_ip").attr('name');

                        $.ajax({
                            type: "GET",
                            cache: false,
                            async: false,
                            url: "../../main/modules/external/update_configuration.php",
                            data: {
                                value:newValue,
                                variable:varToUpdate,
                                session_id:session_id,
                                sd_card:sd_card
                            }
                        }).done(function (data) {
                            if($.parseJSON(data)!="") {
                                check_update=false;
                            }
                        });
                    } else {
                        newValue    = 0;
                        varToUpdate = "wifi_ip_manual";

                        $.ajax({
                            type: "GET",
                            cache: false,
                            async: false,
                            url: "../../main/modules/external/update_configuration.php",
                            data: {
                                value:newValue,
                                variable:varToUpdate,
                                session_id:session_id,
                                sd_card:sd_card
                            }
                        }).done(function (data) {
                            if($.parseJSON(data)!="") {
                                check_update=false;
                            }
                        });


                        newValue    = "000.000.000.000";
                        varToUpdate = $("#wifi_ip").attr('name');

                        $.ajax({
                            type: "GET",
                            cache: false,
                            async: false,
                            url: "../../main/modules/external/update_configuration.php",
                            data: {
                                value:newValue,
                                variable:varToUpdate,
                                session_id:session_id,
                                sd_card:sd_card
                            }
                        }).done(function (data) {
                            if($.parseJSON(data)!="") {
                                check_update=false;
                            }
                        });
                    }


               
                    if($("#wifi_key_type").val()!="NONE") {
                        newValue    = $("#wifi_password").val();
                    } else {
                        newValue    = "";
                    }

                    if((newValue!="")||($("#wifi_key_type").val()=="NONE")) {
                        varToUpdate = $("#wifi_password").attr('name');

                        $.ajax({
                            type: "GET",
                            cache: false,
                            async: false,
                            url: "../../main/modules/external/update_configuration.php",
                            data: {
                                value:newValue,
                                variable:varToUpdate,
                                session_id:session_id,
                                sd_card:sd_card
                            }
                        }).done(function (data) {
                            if($.parseJSON(data)!="") {
                                check_update=false;
                            }
                        });
                    }

                    $("#current_wifi_password").val($("#wifi_password").val());
                    $("#wifi_password").val("");
                    $("#wifi_password_confirm").val("");
            } else {
                    newValue    = "";
                    varToUpdate = "wifi_ssid";

                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/update_configuration.php",
                        data: {
                            value:newValue,
                            variable:varToUpdate,
                            session_id:session_id,
                            sd_card:sd_card
                        }
                    }).done(function (data) {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    });


                    varToUpdate = "wifi_password";
                    newValue    = "";
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/update_configuration.php",
                        data: {
                            value:newValue,
                            variable:varToUpdate,
                            session_id:session_id,
                            sd_card:sd_card
                        }
                    }).done(function (data) {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    });


                    varToUpdate = "wifi_ip";
                    newValue    = "000.000.000.000";
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/update_configuration.php",
                        data: {
                            value:newValue,
                            variable:varToUpdate,
                            session_id:session_id,
                            sd_card:sd_card
                        }
                    }).done(function (data) {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    });


                    varToUpdate = "wifi_ip_manual";
                    newValue    = "0";
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/update_configuration.php",
                        data: {
                            value:newValue,
                            variable:varToUpdate,
                            session_id:session_id,
                            sd_card:sd_card
                        }
                    }).done(function (data) {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    });

    
                    varToUpdate = "wifi_key_type";
                    newValue    = "NONE";
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/update_configuration.php",
                        data: {
                            value:newValue,
                            variable:varToUpdate,
                            session_id:session_id,
                            sd_card:sd_card
                        }
                    }).done(function (data) {
                        if($.parseJSON(data)!="") {
                            check_update=false;
                        }
                    });
            }

            if(sd_card!="") {
                $.ajax({
                    type: "GET",
                    url: "http://localhost:6891/cultibox/main/modules/external/check_and_update_sd.php",
                    data: {
                        sd_card:sd_card,
                        session_id:session_id
                    },
                    async: false
                }).done(function(data, textStatus, jqXHR) {
                    // Check response from server

                    // Parse result
                    var json = jQuery.parseJSON(data);

                    // For each information, show it
                    json.error.forEach(function(entry) {
                        pop_up_add_information(entry,"check_sd_status","information");
                        check_update=false;
                    });
                });
            }

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
                                    //Reload page to load the new menu, the next cultibox version without joomla should avoir to reload the page:
                                    if(update_menu) window.location = "configuration-"+slang;
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
                                    //Reload page to load the new menu, the next cultibox version without joomla should avoir to reload the page:
                                    window.location = "configuration-"+slang;
                                }
                            }]
                        });
           }
        }
      } });
      $.unblockUI();
    }); 
    
    $("#rtc_offset_slider").slider({
        max: 11,
        min: -11,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#rtc_offset").val(ui.value);
        },
        step: 0.1,
        value: rtc_offset_value
    });
    

    $("#reset_sd_card_submit").click(function(e) {
        e.preventDefault();
        $.ajax({
            cache: false,
            url: "../../main/modules/external/check_sd.php",
            data: {path:$("#selected_hdd").val()}
         }).done(function (data) {
            if(data=="0") {
                $("#locked_sd_card").dialog({ width: 550, resizable: false, closeOnEscape: false, buttons: [{ text: CLOSE_button, click: function() { $( this ).dialog( "close" ); } }], hide: "fold", modal: true,  dialogClass: "popup_error"  });
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
                                        $("#progress_bar").progressbar({value:0});
                                        $("#success_format").css("display","none");
                                        $("#error_format").css("display","none");
                                        $( this ).dialog( "close" ); 
                                        $("#btnCancel").html('<span class="ui-button-text">'+CANCEL_button+'</span>');
                                        window.location.href="index.php/"+slang+"/configuration-"+slang;
                                        return false;
                                    }
                                }]
                            });
                            $("#progress_bar").progressbar({value:0});
                            $("#success_format").css("display","none");
                            $("#error_format").css("display","none");
                            formatCard($("#selected_hdd").val(),0);
                        }
                    }, {
                        text: CANCEL_button,
                        click: function () {
                            $("#progress_bar").progressbar({value:0}); 
                            $("#success_format").css("display","none");
                            $("#error_format").css("display","none");
                            $("#btnCancel").html('<span class="ui-button-text">'+CANCEL_button+'</span>');
                            $( this ).dialog( "close" ); return false;
                        }
                    }]
                });
            }
        });
    });



      //Disable password for NONE key type:
     $('#wifi_key_type').change(function() {
        if($('#wifi_key_type').val()=="NONE") {
             $("#wifi_password").attr("disabled", "disabled");
             $("#wifi_password_confirm").attr("disabled", "disabled");
             $("#current_wifi_password").attr("disabled", "disabled");
        } else {
             $("#wifi_password").removeAttr("disabled");
             $("#wifi_password_confirm").removeAttr("disabled");
             $("#current_wifi_password").removeAttr("disabled");
        }
    });


    $("#show_cost").change(function() {
        update_menu=true;
    });

    $("#WIFI").change(function() {
        update_menu=true;
    });

    


    
});

</script>

<script>

wifi_password=<?php echo(json_encode($wifi_password)); ?>;
rtc_offset_value=<?php echo json_encode($rtc_offset) ?>;


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
    $('#reset_min_max').timepicker({
        <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
        showOn: 'both',
        buttonImage: "../../main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT')."',"; ?>
        timeFormat: 'hh:mm',
        <?php echo "timeText: '".__('TIMEPICKER_TIME')."',"; ?>
        <?php echo "hourText: '".__('TIMEPICKER_HOUR')."',"; ?>
        <?php echo "minuteText: '".__('TIMEPICKER_MINUTE')."',"; ?>
        <?php echo "secondText: '".__('TIMEPICKER_SECOND')."',"; ?>
        <?php echo "currentText: '".__('TIMEPICKER_ENDDAY')."',"; ?>
        <?php echo "closeText: '".__('TIMEPICKER_CLOSE')."',"; ?>
    });
    
    // On select change, update conf
    $("select").each(function() {
       
        $(this).on('change', function() {
            newValue    = $( this ).find(":selected").val();
            varToUpdate = $( this ).attr('name');
            updateConf  = $( this ).attr('update_conf');
        
            $.ajax({
                type: "POST",
                cache: false,
                url: "../../main/modules/external/update_configuration.php",
                data: "lang=" + document.location.href.split('/')[document.location.href.split('/').length - 2] + "&value=" + newValue + "&variable=" + varToUpdate + "&updateConf=" + updateConf
            }).done(function (data) {
                //Reload page to print cost menu:
                if(varToUpdate.toUpperCase()=="SHOW_COST") {
                    window.location = "configuration-"+slang;
                }
            });
        });
  
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
        var checked=true;
        $.ajax({
            cache: false,
            url: "../../main/modules/external/check_value.php",
            data: {value:$("#reset_min_max").val(),type:'short_time'}
        }).done(function (data) {
            if(data!=1) {
                $("#error_min_max").show(700);
                checked=false;
                expand('system_interface');
            } else {
                $("#error_min_max").css("display","none");
            }
        });

        if($("#alarm_enable option:selected").val()=="0001") {
            $.ajax({
                cache: false,
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

        if($("#wifi_enable option:selected").val()=="1") {
            $.ajax({
                cache: false,
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

            if((wifi_password!="")&&($("#wifi_password").val()!="")) {
                $.ajax({
                    cache: false,
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

                        $.ajax({
                            cache: false,
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


            if($('#wifi_ip_manual').prop('checked')) {
                $.ajax({
                    cache: false,
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
            $.ajax({
                cache: false,
                url: "../../main/modules/external/configure_menu.php",
                data: {cost:$("#show_cost").val(),wifi:$("#wifi_enable").val()}
            });
            document.forms['configform'].submit();
        }
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

    
});

</script>

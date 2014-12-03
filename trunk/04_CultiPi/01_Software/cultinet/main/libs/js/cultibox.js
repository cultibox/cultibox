<script>

var CLOSE_button="";

var lang="";

$.ajax({
    cache: false,
    async: false,
    url: "main/modules/external/get_variable.php",
    data: {name:"lang"}
}).done(function (data) {
    if(jQuery.parseJSON(data)!="0") lang=jQuery.parseJSON(data);
});

if(lang=="it_IT") {
    CLOSE_button="Chiudere";
} else if(lang=="de_DE") {
    CLOSE_button="Schliessen";
} else if(lang=="en_GB") {
    CLOSE_button="Close";
} else if(lang=="es_ES") {
    CLOSE_button="Cerrar";
} else {
    CLOSE_button="Fermer";
}


$(document).ready(function() {
    $("[title]").tooltip({ position: { my: "left+15 center", at: "right center" } });

    //To change the language dynamically:
    $('li.translate a').click(function(e) {
        e.preventDefault();
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"lang", value: $(this).attr('id'),duration: 31536000}
        });
        window.location = "/cultinet/";
    });



    $("input:radio[name=wire_type]").click(function() {
        if($(this).val()=="static") {
            $("#wire_static").css("display","");
        } else {
            $("#wire_static").css("display","none");
        }
    });

    $("#activ_wire").change(function() {
        if($("#activ_wire").val()=="True") {
            $("#wire_interface").show();
        } else {
            $("#wire_interface").css("display","none");
        }
    });


    $("#activ_wifi").change(function() {
        if($("#activ_wifi").val()=="True") {
            $("#wifi_interface").show();
        } else {
            $("#wifi_interface").css("display","none");
        }
    })

     $("#eyes").mousedown(function() {
        if($('#wifi_key_type').val()!="NONE") {
            $('#wifi_password').replaceWith('<input id="wifi_password" name="wifi_password" type="text" size="15" value="' + $('#wifi_password').attr('value') + '" />');
        }
    });


    $("#eyes").mouseup(function(){
        if($('#wifi_key_type').val()!="NONE") {
            $('#wifi_password').replaceWith('<input id="wifi_password" name="wifi_password" type="password" size="15" value="' + $('#wifi_password').attr('value') + '" />');
        }
    });

    $("#eyes").mouseleave(function(){
        if($('#wifi_key_type').val()!="NONE") {
            $('#wifi_password').replaceWith('<input id="wifi_password" name="wifi_password" type="password" size="15" value="' + $('#wifi_password').attr('value') + '" />');
        }
    });

    $("input:radio[name=wifi_type]").click(function() {
        if($(this).val()=="static") {
            $('#manual_ip_wifi').show();
        } else {
            $('#manual_ip_wifi').css('display', 'none');
        }
    });


    //Disable password for NONE key type:
    $('#wifi_key_type').change(function() {
        if($('#wifi_key_type').val()=="NONE") {
             $("#wifi_password").attr("disabled", "disabled");
             $("#wifi_password_confirm").attr("disabled", "disabled");
             $("#wifi_password").val("");
             $("#wifi_password_confirm").val("");
             $("#eyes").css("display","none");
        } else {
             $("#wifi_password").removeAttr("disabled");
             $("#wifi_password_confirm").removeAttr("disabled");
             $("#eyes").show();
        }
    });


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

                if($("#activ_wifi option:selected").val()=="True") {
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/check_value.php",
                        data: {value:$("#wifi_ssid").val(),type:'ssid'}
                    }).done(function (data) {
                        if(data!=1) {
                            $("#error_wifi_ssid").show(700);
                            checked=false;
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
                        if($("#wifi_password").val()=="") {
                            $("#error_empty_password").css("display","");
                        } else if(($("#wifi_password").val()=="")&&($("#wifi_key_type").val()!="NONE")) {
                            $.ajax({
                                cache: false,
                                async: false,
                                url: "main/modules/external/check_value.php",
                                data: {value:wifi_password,type:type_password}
                            }).done(function (data) {
                                if(data!=1)  {
                                    checked=false;
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

                    /* else if($("#wifi_password").val()!="") {
                            $.ajax({
                                cache: false,
                                async: false,
                                url: "main/modules/external/check_value.php",
                                data: {value:$("#wifi_password").val()+"____"+$("#wifi_password_confirm").val(),type:'password'}
                            }).done(function (data) {
                                $("#error_empty_password").css("display","none");
                                if(data!=1) {
                                    $("#error_wifi_password").show(700);
                                    $("#error_wifi_password_confirm").show(700);
                                    $("#error_password_wep").css("display","none");
                                    $("#error_password_wpa").css("display","none");
                                    checked=false;
                                } else {
                                    $("#error_wifi_password").css("display","none");
                                    $("#error_wifi_password_confirm").css("display","none");

                                    $.ajax({
                                        cache: false,
                                        async: false,
                                        url: "main/modules/external/check_value.php",
                                        data: {value:$("#wifi_password").val(),type:type_password}
                                    }).done(function (data) {
                                        if(data!=1)  {
                                            checked=false;
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
                        }*/
 
                        if(($("#wifi_password").val()=="")&&(wifi_password=="")) {
                            checked=false;
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
                            url: "main/modules/external/check_value.php",

                            data: {value:$("#wifi_ip").val(),type:'ip'}
                        }).done(function (data) {
                            if(data!=1) {
                                $("#error_wifi_ip").show(700);
                                checked=false;
                            } else {
                                $("#error_wifi_ip").css("display","none");
                            }
                        });
                    }
        
                }

                if(checked) {
                    var check_update=true;
                    var dataForm=$("#configform").serialize();
                    dataForm=dataForm+"&wifi_type="+$('input[name=wifi_type]:radio:checked').val()+"&wire_type="+$('input[name=wire_type]:radio:checked').val();

                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/create_configuration.php",
                        data: dataForm
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
                $.unblockUI();
            }
        });
    });
});

</script>




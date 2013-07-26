var OK_button="";
var CANCEL_button="";
var CLOSE_button="";
var DELETE_button="";



var lang="";
lang = window.location.pathname.match(/\/fr\//g);
if(!lang) {
    lang = window.location.pathname.match(/\/en\//g);
    if(!lang) {
        lang = window.location.pathname.match(/\/es\//g);
        if(!lang) {
            lang = window.location.pathname.match(/\/it\//g);
            if(!lang) {
                lang = window.location.pathname.match(/\/de\//g);
            }
        }
    }
}

if(!lang) {
    lang="/fr/";
}

if(lang=="/it/") {
    OK_button="Continuare";
    CANCEL_button="Annullare";
    CLOSE_button="Chiudere";
    DELETE_button="Rimuovere";
} else if(lang=="/de/") {
    OK_button="Weiter";
    CANCEL_button="Stornieren";
    CLOSE_button="Schliessen";
    DELETE_button="Entfernen";
} else if(lang=="/en/") {
    OK_button="OK";
    CANCEL_button="Cancel";
    CLOSE_button="Close";
    DELETE_button="Delete";
} else if(lang=="/es/") {
    OK_button="Continuar";
    CANCEL_button="Cancelar";
    CLOSE_button="Cerrar";
    DELETE_button="Eliminar";
} else {
    OK_button="Continuer";
    CANCEL_button="Annuler";
    CLOSE_button="Fermer";
    DELETE_button="Supprimer";
}


clean_highchart_message = function(message) { {
    message=message.replace("'", "\'");
    message=message.replace("&eacute;", "é");
    message=message.replace("&agrave;", "à");
    message=message.replace("&egrave;", "è");
    message=message.replace("&ecirc;", "ê");
    message=message.replace("&deg;", "°");
    message=message.replace("&ucirc;", "û");
    message=message.replace("&ocirc;", "ô");
    return message;
    }
}


confirmForm = function(SendForm,idDialog) {
    $("#"+idDialog).dialog({
        resizable: false,
        height:200,
        width: 500,
        modal: true,
        dialogClass: "dialog_cultibox",
        buttons: [{
            text: OK_button,
            click: function () {
                $( this ).dialog( "close" ); SendForm.submit();
            }
        }, {
            text: CANCEL_button,
            click: function () {
                $( this ).dialog( "close" ); return false;
            }
        }]
    });
}



formatCard = function(hdd,pourcent) {
            $.ajax({ 
                cache: false,
                url: "../../main/modules/external/format.php",
                data: {hdd:hdd, progress: pourcent}
            }).done(function (data) {
                $("#progress_bar").progressbar({ value: 4*parseInt(data) });
                if(data==100) { 
                    $("#success_format").show();
                    $("#btnCancel").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                    return true;
                } else if(data>=0) { 
                    formatCard(hdd,data); 
                } else {
                    $("#error_format").show();
                }
            });
}


loadLog = function(nb_day,pourcent,type,pourcent,search) {
            $.ajax({
                cache: false,
                url: "../../main/modules/external/load_log.php",
                data: {nb_day:nb_day, type:type,search:search}
            }).done(function (data) {
                if(nb_day!=0) {
                    if(type=="power") {
                        $("#progress_bar_load_power").progressbar({ value: parseInt(((pourcent-nb_day)/pourcent)*100) });
                    } else {
                        $("#progress_bar_load").progressbar({ value: parseInt(((pourcent-nb_day)/pourcent)*100) });
                    }

                    if(!$.isNumeric(data)) {
                        if(type=="power") {
                            $("#error_load_power").show();
                        } else {
                            $("#error_load").show();
                        }
                        return true;
                    }
                    loadLog(nb_day-1,data,type,pourcent,search);
                } else {
                    if(search=="submit") {
                        if(type=="power") {
                            $("#success_load_power").show();
                            $("#progress_bar_load_power").progressbar({ value: 100 });
                        } else {
                            $("#success_load").show();
                            $("#progress_bar_load").progressbar({ value: 100 });
                        }
                        $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        return true;
                    } else {
                        if(data=="-2") {
                            $("#success_load_still_log").show();
                        } 

                        if(type=="power") {
                                $("#success_load_power_auto").show();
                                $("#progress_bar_load_power").progressbar({ value: 100 });
                        } else {
                                $("#success_load_auto").show();
                                $("#progress_bar_load").progressbar({ value: 100 });
                        }
                        $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        return true;
                    }
                } 
            });
}


$(document).ready(function() {
    $.ajax({
        cache: false,
        url: "../../main/modules/external/position.php"
    }).done(function (data) {

    if ( typeof data.split(',')[0] !== "undefined" && data.split(',')[0]) {
        var x = parseInt(data.split(',')[0].replace("\"", ""));
    } else {
        var x = 15;
    }

    if ( typeof data.split(',')[1] !== "undefined" && data.split(',')[1]) {
        var y = parseInt(data.split(',')[1].replace("\"", ""));
    } else {
        var y = 15;
    }

    if ( typeof data.split(',')[2] !== "undefined" && data.split(',')[2]) {
        var wid = parseInt(data.split(',')[2].replace("\"", ""));
    } else {
        var wid = 325;
    }


    $( ".message" ).dialog({ width: wid, resizable: true, buttons: [ { text: CLOSE_button, click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", dialogClass: "dialog_message", position: [x,y], dragStop: function( event, ui ) { 
        if(data!="") { 
            var tmp = $(".message").dialog( "option", "position" );
            var width = $(".message").dialog( "option", "width" );

            $.ajax({
                cache: false,
                url: "../../main/modules/external/position.php",
                data: { POSITION_X: tmp[0], POSITION_Y: tmp[1], WIDTH: width }
                }); 
            }
    },
    resizeStop: function( event, ui ) {
        if(data!="") {
                var tmp = $(".message").dialog( "option", "position" );
                var width = $(".message").dialog( "option", "width" );

                $.ajax({
                cache: false,
                url: "../../main/modules/external/position.php",
                data: { WIDTH: width, POSITION_X: tmp[0], POSITION_Y: tmp[1] }
                });
        }
    } });
                $(".message").dialog().parent().css('position', 'fixed');
    }); 

   jQuery('#jquery-colour-picker-example select').colourPicker({ 
        ico:    'http://localhost:6891/cultibox/main/libs/img/jquery.colourPicker.gif', 
        title:    false
   });


   // Affichage des tooltips sur les éléments avec un title
   $("[title]").tooltip({ position: { my: "left+15 center", at: "right center" } });

    //Portage pour Chrome:
    if(/chrom(e|ium)/.test(navigator.userAgent.toLowerCase())){
        var ytop=0; 
        if(window.location.hash) {
            var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
            var position = $("#"+hash).position();
            ytop = position.top+100;
        }
        if(ytop!=0) {
            $( ".pop_up_message" ).dialog({ 
                position: [($(window).width() / 2) - (550 / 2), ytop],
                width: 550, 
                buttons: [{ text: CLOSE_button, click: function() { $( this ).dialog( "close" ); } }], 
                hide: "fold", 
                modal: true,  
                dialogClass: "popup_message"  
            }); 
        } else {    
            $( ".pop_up_message" ).dialog({ width: 550, buttons: [{ text: CLOSE_button, click: function() { $( this ).dialog( "close" ); } }], hide: "fold", modal: true,  dialogClass: "popup_message"  });
        }
    } else {
        $(".pop_up_message").dialog({ width: 550, buttons: [{ text: CLOSE_button, click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", modal: true,  dialogClass: "popup_message"  });
    }

    //Portage pour Chrome:
    if(/chrom(e|ium)/.test(navigator.userAgent.toLowerCase())){
        var ytop=0;
        if(window.location.hash) {
            var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
            var position = $("#"+hash).position();
            ytop = position.top+100;
        }
        if(ytop!=0) {
            $( ".pop_up_error" ).dialog({
                position: [($(window).width() / 2) - (550 / 2), ytop],
                width: 550,
                buttons: [ { text: CLOSE_button, click: function() { $( this ).dialog( "close" ); } } ],
                hide: "fold",
                modal: true,
                dialogClass: "popup_error"
            });
        } else {
            $( ".pop_up_error" ).dialog({ width: 550, buttons: [ { text: CLOSE_button, click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", modal: true,  dialogClass: "popup_error" });
        }
    } else {
        $( ".pop_up_error" ).dialog({ width: 550, buttons: [ { text: CLOSE_button, click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", modal: true,  dialogClass: "popup_error" });
    }
  

   $(".delete").click(function() {
          var currentForm;
          currentForm = $(this).closest('form');
          var FormId = currentForm.attr('id');
          var DialogId="";

          switch (FormId) {
            case 'reset_program_form':  DialogId="delete_dialog_program";
                                        break;
            case 'delete_historic_form': DialogId="delete_dialog_historic";
                                        break;
            case 'reset_calendar_form': DialogId="reset_dialog_calendar";
                                        break;
            case 'actionprog':  if((start!=$('#start_time').val())||(end!=$('#end_time').val())||(plug_selected!=$('#selected_plug').val())) {
                                    return true;
                                } else {
                                    DialogId="same_dialog_program";
                                }
                                break;
            } 


          if(!confirmForm(currentForm,DialogId)) {
            return false;
          } else {
            return true;    
          }
   });


    $(".download").click(function(e) {
        e.preventDefault();

        $url = 'http://localhost:6891/cultibox/main/modules/external/force-download.php?file='+$(".download").attr("href");
        $.ajax({
            type: 'GET',
            url: $url,
            success: function(data){
                   if(data != true){
                    window.location =""+$url+"";
                   }
            }
        });
    });

    $("#reset_sd_card_submit").click(function(e) {
        e.preventDefault();
        $("#format_dialog_sd").dialog({
            resizable: false,
            height:200,
            width: 500,
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
                                modal: true,
                                dialogClass: "popup_message",
                                buttons: [{
                                    text: CANCEL_button,
                                    "id": "btnCancel",
                                    click: function () {
                                        $( this ).dialog( "close" ); 
                                        $("#btnCancel").html('<span class="ui-button-text">'+CANCEL_button+'</span>');
                                        window.location.reload();
                                        return false;
                                    }
                                }]
                            });
                            $("#progress_bar").progressbar({value:0});
                            formatCard($("#selected_hdd").val(),0);
                        }
                    }, {
                        text: CANCEL_button,
                        click: function () {
                            $( this ).dialog( "close" ); return false;
                        }
                    }]
        });
    });


    $("#import_log").click(function(e) {
        e.preventDefault();
        $("#progress_load").dialog({
            resizable: false,
            width: 550,
            modal: true,
            dialogClass: "popup_message",
            buttons: [{
                    text: CANCEL_button,
                    "id": "btnClose",
                    click: function () {
                        $( this ).dialog("close");
                        $("#error_load_power").css("display","none");
                        $("#error_load").css("display","none");
                        $("#success_load_power").css("display","none");
                        $("#success_load").css("display","none");
                        $("#success_load_still_log").css("display","none");
                        $("#btnClose").html('<span class="ui-button-text">'+CANCEL_button+'</span>');
                        document.forms['display-log-day'].submit();
                    }
            }]

         });
         $("#progress_bar_load").progressbar({value:0});
         $("#progress_bar_load_power").progressbar({value:0});
         loadLog($("#log_search").val()*31,0,"logs",$("#log_search").val()*31,"submit");
         loadLog($("#log_search").val()*31,0,"power",$("#log_search").val()*31,"submit");
    });

    $("input:radio[name=check_type_delete]").click(function() {
        if($(this).val()=="all") {
            $("#div_delete_specific").css("display","none");        
        } else {
            $("#div_delete_specific").css("display","");
        }
    });

    $("input:radio[name=check_type_delete_power]").click(function() {
        if($(this).val()=="all") {
            $("#div_delete_specific_power").css("display","none");
        } else {
            $("#div_delete_specific_power").css("display","");
        }
    });



    $("#reset_log_submit").click(function(e) {
        e.preventDefault();
        $("#delete_log_form").dialog({
            resizable: true,
            width: 750,
            modal: true,
            dialogClass: "popup_message",
            buttons: [{
                    text: CANCEL_button,
                    click: function () {
                        $("#error_delete_logs").css("display","none"); 
                        $("#success_delete_logs").css("display","none"); 
                        $("#error_format_date_logs").css("display","none");
                        $( this ).dialog( "close" ); 
                        return false;
                    }}, {
                    text: DELETE_button,
                        click: function () {
                            $("#error_format_date_logs").css("display","none");
                            if((checkFormatDate($("#datepicker_from").val()))&&(checkFormatDate($("#datepicker_to").val()))&&(compareDate($("#datepicker_from").val(),$("#datepicker_to").val()))) {
                            $.ajax({
                                cache: false,
                                async: false,
                                url: "../../main/modules/external/delete_logs.php",
                                data: {type:"logs",type_reset:$("input:radio[name=check_type_delete]:checked").val(),start:$("#datepicker_from").val(), end:$("#datepicker_to").val()}
                            }).done(function (data) {
                                if(!$.isNumeric(data)) {
                                    $("#error_delete_logs").show();
                                } else {
                                    if(data=="1") {
                                        $("#success_delete_logs").show();
                                    } else {
                                        $("#error_delete_log").show();
                                    }
                                }
                                $("#delete_log_form").dialog({ buttons: [ {
                                        text: CLOSE_button,
                                        click: function() {
                                            $("#error_delete_logs").css("display","none");
                                            $("#success_delete_logs").css("display","none");
                                            $("#error_format_date_logs").css("display","none");
                                            $( this ).dialog( "close" );
                                            document.forms['display-log-day'].submit();
                                            return false;
                               } } ] });
                            });
                            } else {
                                $("#error_format_date_logs").css("display","");
                            }
                        }
            }]
         });
    });

        // Check errors for the configuration part:
        $("#submit_conf").click(function(e) {
            e.preventDefault();
            var checked=true;
            $.ajax({
                cache: false,
                async: false,
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

            if(checked) {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/configure_menu.php",
                    data: {cost:$("#show_cost").val(),historic:$("#show_historic").val()}
                });
                document.forms['configform'].submit();
            }
        }); 

        // Check errors for the display logs part:
        $("#display-log-submit").click(function(e) {
            e.preventDefault();
            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#datepicker").val(),type:'date'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_start_days").show(700);
                } else {
                    document.forms['display-log-day'].submit();
                }
            });
        });


        $("#display-log-month").click(function(e) {
            e.preventDefault();
            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#datepicker").val(),type:'month'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_start_month").show(700);
                } else {
                    document.forms['display-log-month'].submit();
                }
            });
        });


        // Check errors for the programs part:
        $("#apply").click(function(e) {
            $("#error_same_start").css("display","none");
            $("#error_same_end").css("display","none");
            
            e.preventDefault();
            var checked=true;
            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#start_time").val(),type:'time'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_start_time").show(700);
                    checked=false;
                } else {
                    $("#error_start_time").css("display","none");
                }
            });

            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#end_time").val(),type:'time'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_end_time").show(700);
                    checked=false;
                } else {
                    $("#error_end_time").css("display","none");
                }
            });

            
            if(checked) {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#start_time").val()+"_"+$("#end_time").val(),type:'same_time'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_same_start").show(700);
                        $("#error_same_end").show(700);
                        checked=false;
                    } else {
                        $("#error_same_start").css("display","none");
                        $("#error_same_end").css("display","none");
                    }
                });
            }

            if($('#cyclic').is(':checked')) {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#repeat_time").val(),type:'cyclic_time'}
                }).done(function (data) {
                    if(data!="1") {
                        if(data=="2") {
                            $("#error_minimal_cyclic").show(700);
                        } else {
                            $("#error_cyclic_time").show(700);
                        }
                        checked=false;
                    } else {
                        $("#error_cyclic_time").css("display","none");
                        $("#error_minimal_cyclic").css("display","none");
                    }
                });
            }

            if($('#regprog').is(':checked')) {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#value_program").val(),type:'value_program',plug_type:plugs_infoJS[$('#selected_plug option:selected').val()-1]['PLUG_TYPE']}
                }).done(function (data) {
                    if(data!="1") {
                        $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[data]);
                        $("#error_value_program").show(700);
                        checked=false;
                    } else {
                        $("#error_value_program").css("display","none");
                    }
                });
            }

            if(checked) {
                document.forms['actionprog'].submit();
            }
        });


        $("#reset_log_power_submit").click(function(e) {
        e.preventDefault();
        $("#delete_log_form_power").dialog({
            resizable: true,
            width: 750,
            modal: true,
            dialogClass: "popup_message",
            buttons: [{
                    text: CANCEL_button,
                    click: function () {
                        $("#error_delete_logs_power").css("display","none");
                        $("#success_delete_logs_power").css("display","none");
                        $("#error_format_date_logs_power").css("display","none");
                        $( this ).dialog( "close" );
                        return false;
                    }}, {
                    text: DELETE_button,
                        click: function () {
                            $("#error_format_date_logs_power").css("display","none");
                            if((checkFormatDate($("#datepicker_from_power").val()))&&(checkFormatDate($("#datepicker_to_power").val()))&&(compareDate($("#datepicker_from_power").val(),$("#datepicker_to_power").val()))) {
                            $.ajax({
                                cache: false,
                                async: false,
                                url: "../../main/modules/external/delete_logs.php",
                                data: {type:"power",type_reset:$("input:radio[name=check_type_delete_power]:checked").val(),start:$("#datepicker_from_power").val(), end:$("#datepicker_to_power").val()}
                            }).done(function (data) {
                                if(!$.isNumeric(data)) {
                                    $("#error_delete_logs_power").show();
                                } else {
                                    if(data=="1") {
                                        $("#success_delete_logs_power").show();
                                    } else {
                                        $("#error_delete_log_power").show();
                                    }
                                }
                                $("#delete_log_form_power").dialog({ buttons: [ {
                                        text: CLOSE_button,
                                        click: function() {
                                            $("#error_delete_logs_power").css("display","none");
                                            $("#success_delete_logs_power").css("display","none");
                                            $("#error_format_date_logs_power").css("display","none");
                                            $( this ).dialog( "close" );
                                            document.forms['display-log-day'].submit();
                                            return false;
                               } } ] });
                            });
                            } else {
                                $("#error_format_date_logs_power").css("display","");
                            }
                        }
            }]
         });
    });



    var check = window.location.pathname.match(/display-logs/g);
    if(check) {
         var name="load_log";
         $.ajax({
                cache: false,
                async: true,
                url: "../../main/modules/external/get_variable.php",
                data: {name:name}
          }).done(function (data) {
                if(!data) {
                    var name="sd_card";
                    $.ajax({
                        cache: false,
                        async: true,
                        url: "../../main/modules/external/get_variable.php",
                        data: {name:name}
                    }).done(function (data) {
                        if(data) {
                            $("#progress_load").dialog({
                                resizable: false,
                                width: 550,
                                modal: true,
                                dialogClass: "popup_message",
                                buttons: [{ 
                                    text: CANCEL_button,
                                    "id": "btnClose",
                                    click: function () {
                                        $( this ).dialog("close");
                                        $("#error_load_power").css("display","none");
                                        $("#error_load").css("display","none");
                                        $("#success_load_power_auto").css("display","none");
                                        $("#success_load_auto").css("display","none");
                                        $("#success_load_still_log").css("display","none");
                                        $("#btnClose").html('<span class="ui-button-text">'+CANCEL_button+'</span>');
                                        document.forms['display-log-day'].submit();
                                    }
                                }]
                            });

                            $("#progress_bar_load").progressbar({value:0});
                            $("#progress_bar_load_power").progressbar({value:0});
                            loadLog("31",0,"logs","31","auto");
                            loadLog("31",0,"power","31","auto");
                        }
                    });
                }
            });
    }

});

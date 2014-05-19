var OK_button="";
var CANCEL_button="";
var CLOSE_button="";
var DELETE_button="";
var REDUCE_button="";
var EXTEND_button="";
var HIDE_button="";

var lang="";
var reduced="";
var finished=0;

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
    SAVE_button="Registrati";
    REDUCE_button="Abbassare";
    EXTEND_button="Ingrandisci";
    HIDE_button="Nascondere";
    var llang="it_IT";
    var slang="it";
} else if(lang=="/de/") {
    OK_button="Weiter";
    CANCEL_button="Stornieren";
    CLOSE_button="Schliessen";
    DELETE_button="Entfernen";
    SAVE_button="Registrieren";
    REDUCE_button="Senken";
    EXTEND_button="Vergrößern";
    HIDE_button="Verbergen";
    var llang="de_DE";
    var slang="de";
} else if(lang=="/en/") {
    OK_button="OK";
    CANCEL_button="Cancel";
    CLOSE_button="Close";
    DELETE_button="Delete";
    SAVE_button="Save";
    REDUCE_button="Shorten";
    EXTEND_button="Enlarge";
    HIDE_button="Hide";
    var llang="en_GB";
    var slang="en";
} else if(lang=="/es/") {
    OK_button="Continuar";
    CANCEL_button="Cancelar";
    CLOSE_button="Cerrar";
    DELETE_button="Eliminar";
    SAVE_button="Registro";
    REDUCE_button="Bajar";
    EXTEND_button="Agrandar";
    HIDE_button="Ocultar";
    var llang="es_ES";
    var slang="es"
} else {
    OK_button="Continuer";
    CANCEL_button="Annuler";
    CLOSE_button="Fermer";
    DELETE_button="Supprimer";
    SAVE_button="Enregistrer";
    REDUCE_button="Réduire";
    EXTEND_button="Agrandir";
    HIDE_button="Cacher";
    var llang="fr_FR";
    var slang="fr";
}

diffdate = function(d1,d2) {
    var WNbJours = d2.getTime() - d1.getTime();
    return Math.ceil(WNbJours/(1000*60*60*24)+1);
}


addZ = function(n){return n<10? '0'+n:''+n;}


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
        closeOnEscape: false,
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

delete_logs = function(type, type_reset, nb_jours, start,count) {
        var step=100/count;
        var pourcent=(count-nb_jours)*step;

        $.ajax({
              cache: false,
              url: "../../main/modules/external/delete_logs.php",
              data: {type:type,type_reset:type_reset,start:start}
        }).done(function (data) {
              if(!$.isNumeric(data)) {
                if(type=="logs") {
                    $("#error_delete_logs").show();
                } else {
                    $("#error_delete_log_power").show();
                }
              } else {
                 if(data==1) {
                     if(type=="logs") {
                        $("#progress_bar_delete_logs").progressbar({value:pourcent});  
                     } else {
                        $("#progress_bar_delete_logs_power").progressbar({value:pourcent});
                     }

                     if(nb_jours>1) {
                        var date = new Date(Date.parse(start)); 
                        date.setDate(date.getDate()+1);
                        var dateString = (date.getFullYear().toString()+"-"+addZ((date.getMonth() + 1)) + "-" + addZ(date.getDate()));

                        delete_logs(type,type_reset, nb_jours-1,dateString,count);
                     } else {
                        if(type=="logs") {
                            $("#success_delete_logs").show();                              
                            $("#progress_bar_delete_logs").progressbar({value:100});
                        } else {
                            $("#success_delete_logs_power").show(); 
                            $("#progress_bar_delete_logs_power").progressbar({value:100});
                        }
                     }
                 } else {
                     if(type=="logs") {
                        $("#error_delete_log").show();
                     } else {
                        $("#error_delete_log_power").show();
                    }
                 }
              }
        });
}

compute_cost = function(type,startday, select_plug, nb_jours, count, cost,chart,index,plug) {
        var step=100/count;
        var pourcent=(count-nb_jours)*step;
        if(plug>0) {
            var nb=index+1;
        } else {
            var nb="";
        }

        $.ajax({
              cache: false,
              async: true,
              url: "../../main/modules/external/compute_cost.php",
              data: {type:type,startday:startday,select_plug:select_plug,cost:cost}
        }).done(function (data) {
              if(!$.isNumeric(data)) {
                        if(type=="theorical") {
                            $("#error_cost_compute_theorical"+nb).show();
                        } else {
                            $("#error_cost_compute_real"+nb).show();
                        }
              } else {
                     $("#progress_bar_cost_"+type+nb).progressbar({value:pourcent});

                     cost=parseFloat(cost)+parseFloat(data);

                     if(nb_jours>1) {
                        var date = new Date( Date.parse(startday));
                        date.setDate(date.getDate()+1);
                        var dateString = (date.getFullYear().toString()+"-"+addZ((date.getMonth() + 1)) + "-" + addZ(date.getDate()));

                        compute_cost(type,dateString, select_plug, nb_jours-1, count, cost,chart,index,plug);
                     } else {
                        $("#progress_bar_cost_"+type+nb).progressbar({value:100});
                        if(type=="theorical") {
                            chart.series[index].data[1].update(Math.round(cost*100)/100); 
                            $("#valid_cost_compute_theorical"+nb).show();
                        } else {
                            chart.series[index].data[0].update(Math.round(cost*100)/100); 
                            $("#valid_cost_compute_real"+nb).show();
                        }  

                        if(($("#valid_cost_compute_theorical").css("display")!="none")&&($("#valid_cost_compute_theorical").css("display")!="none")) {
                            $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        }
                     }
                 } 
        });
}



loadLog = function(nb_day,pourcent,type,pourcent,search,sd_card) {
            $.ajax({
                cache: false,
                url: "../../main/modules/external/load_log.php",
                data: {nb_day:nb_day, type:type,search:search,sd_card:sd_card}
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
                        finished=finished+1;
                        if(finished==2) {
                            $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        }
                        return true;
                    }
                    loadLog(nb_day-1,data,type,pourcent,search,sd_card);
                } else {
                    if(search=="submit") {
                        if(type=="power") {
                            $("#success_load_power").show();
                            $("#progress_bar_load_power").progressbar({ value: 100 });
                        } else {
                            $("#success_load").show();
                            $("#progress_bar_load").progressbar({ value: 100 });
                        }
                        finished=finished+1;
                        if(finished==2) {
                            $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        }
                        return true;
                    } else {
                        if(data==-2) {
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
                        finished=finished+1;
                        if(finished==2) {
                            $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        }
                        return true;
                    }
                } 
            });
}


//Wifi process:
get_plug_type = function(addr) {
    if(addr<0) return 4;

    if(jQuery.inArray(addr,addr_1000)!=-1) {
        return 1;
    } else if(jQuery.inArray(addr,addr_3500)!=-1) {
        return 2;
    } else if((addr>=100)&&(addr<=115)) {
        return 3;
    } 
    return 4;
}

wifi_process = function(time,ip) {
        setTimeout(function(){
        $.ajax({
            type: "GET",
            url: "http://"+ip+"/info.xml",
            dataType: "xml",
            timeout: 3000,
            success: function(xml) {
                var myPlug = [];
                $(xml).find('plug_state').each( function(){
                    var num=$(this).find('num').text();
                    var value=$(this).find('value').text();
                    var plug_address=$(this).find('plug_address').text();
                    myPlug.push({
                        num: num,
                        value: value,
                        plug_address: plug_address
                    });
                });

                $.each(myPlug, function( index, value ) {
                   if((value['num']!="")&&(value['value']!="")) {
                        if(value['value']!=0) {
                            $("#plug_state_on"+value['num']).show();
                        } else {
                            $("#plug_state_off"+value['num']).show();
                        }
                        $("#plug_state_unk"+value['num']).css("display","none");

                        if(value['plug_address']!="") {
                            var plug_addr=get_plug_type(value['plug_address']);
                            //1 = 1000W:
                            if(plug_addr==1) {
                               $("#plug_type"+value['num']).text("1000W");
                            //2 = 3500W;
                            } else if(plug_addr==2) {
                                $("#plug_type"+value['num']).text("3500W");
                            //3 = DIMMER;
                            } else if(plug_addr==3) {
                                $("#plug_type"+value['num']).text(translate[1]);
                            //Unknown:
                            } else {
                                $("#plug_type"+value['num']).text(translate[0]);
                            }
                            $("#plug_type"+value['num']).css("font-weight","bold");
                        }
                   }
                });

                var mySensor = [];
                $(xml).find('sensor').each( function(){
                    var num=$(this).find('num').text();
                    var type=$(this).find('type').text();
                    var value1=$(this).find('value1').text();
                    var value2=$(this).find('value2').text();
                    var date=$(this).find('date').text();
                    mySensor.push({
                        num: num,
                        type: type,
                        value1: value1,
                        value2: value2,
                        date: date
                    });
                });


                 $.each(mySensor, function( index, value ) {
                    if((value['num']!="")&&(value['type']!="")&&(value['type']>1)&&(value['type']!=4)) {

                        var unity="";
                        switch (value['type']) {
                            case '3': unity="°C";
                                        break;
                            case '5': unity="cm";
                                        break;
                            case '6': unity="cm";
                                        break;
                            default: unity="";
                        }

                        $("#type_sensor"+value['num']).text(type_sensor[value['type']]);
                        $("#type_sensor"+value['num']).css('font-weight', 'bold');

                        if((value['value1']!="")&&(value['value1']!="0")) {
                            if(value['type']!="2") {
                                $("#sensor_value"+value['num']).text(value['value1']+unity);
                            } else {
                                // Temp and humi:
                                if((value['value2']!="")&&(value['value2']!="0")) {
                                    $("#sensor_value"+value['num']).text(value['value1']+"°C / "+value['value2']+"%");
                                } else {
                                    $("#sensor_value"+value['num']).text("N/A");
                                }
                            }

                            if((value['date']!="")&&(value['date']!="0")) {
                                $("#sensor_date"+value['num']).text("20"+value['date']);
                                $("#sensor_date"+value['num']).css('font-weight', 'bold');
                            }
                         } else {
                             $("#sensor_value"+value['num']).text("N/A");
                             $("#sensor_date"+value['num']).text("");
                         }
                         $("#sensor_value"+value['num']).css('font-weight', 'bold');
                   }
                });
            },
                complete: function() {
                    wifi_process(10000,ip);
                }
            });
            }, time);
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

    if ( typeof data.split(',')[3] !== "undefined" && data.split(',')[3]) {
        reduced  = String(data.split(',')[3].replace("\"", ""));
    } else {
        reduced="False";
    }


    $( ".message" ).dialog({ width: wid, closeOnEscape: false, resizable: true, buttons: [ 
        {
            text: HIDE_button,
            id: "button_hide" ,
            click: function() {
                //Action lors de l'enclenchement du bouton "Cacher" de la boîte de messages:
            
                //Fermeture de la boîte de dialogue
                $( this ).dialog( "close" );

                //Mise a True de la variable de session TOOLTIP_MSG_BOX qui définit si on doit afficher ou non la boîte de message lorsqu'on change de page
                $.ajax({
                        cache: false,
                        url: "../../main/modules/external/set_variable.php",
                        data: {name:"tooltip_msg_box", value: "True"}
                });


                //Affichage de l'oeil permettant de réafficher la boîte de message:
                // On affiche l'oeil sur le bord exterieur gauche de l'interface. La position dépend de la résolution de l'écran.
                // On récupère donc la taille de l'écran et la taille du div central de l'interface pour afficher l'oeil au bon endroit:
                // position = 90*(taille de l'écran - taille du div central)/200
                // taille de l'écran - taille du div central /2 ==> correspond à la taille de la marge à gauche (ou à droite) séparant le bord du div central
                // on affiche donc l'oeil a 90% de la marge exterieure
                
                var element_div_width=$("#maininner").width();
                var element_body_width=$( window ).width();

                if((element_body_width!="")&&(element_div_width!="")) {
                    var dist=90*(element_body_width-element_div_width)/200;
                    $("#tooltip_msg_box").css("padding-left",dist+"px");
                }


                //On l'oeil et son tooltip:
                $("#eyes_msgbox").attr('title', title_msgbox);
                $("#tooltip_msg_box").fadeIn("slow");
            }
        },
        {
            text: REDUCE_button, 
            id: "button_reduce" ,
            click: function() { 
                if(reduced=="True") {
                    $(this).dialog('option', 'height', 'auto');
                    $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(REDUCE_button);
                    var tmp_reduced="False";
                } else {
                    $(this).dialog('option', 'height', 10); 
                    $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(EXTEND_button);
                    var tmp_reduced="True";
                }
                var tmp = $(".message").dialog( "option", "position" );
                var width = $(".message").dialog( "option", "width" );

                $.ajax({
                cache: false,
                url: "../../main/modules/external/position.php",
                data: { POSITION_X: tmp[0], POSITION_Y: tmp[1], WIDTH: width, REDUCED: tmp_reduced }
                });
                reduced=tmp_reduced;
        }}], 
        hide: "fold", dialogClass: "dialog_message", position: [x,y], dragStop: function( event, ui ) { 

        if(data!="") { 
            var tmp = $(".message").dialog( "option", "position" );
            var width = $(".message").dialog( "option", "width" );

            $.ajax({
                cache: false,
                url: "../../main/modules/external/position.php",
                data: { POSITION_X: tmp[0], POSITION_Y: tmp[1], WIDTH: width, REDUCED: reduced }
                }); 
        }
    },
    create: function( event, ui ) {
            if(reduced=="True") {
                $(this).dialog('option', 'height', 10);
                $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(EXTEND_button);
            }
    },
    resizeStop: function( event, ui ) {
        if(data!="") {
                var tmp = $(".message").dialog( "option", "position" );
                var width = $(".message").dialog( "option", "width" );

                $.ajax({
                cache: false,
                url: "../../main/modules/external/position.php",
                data: { WIDTH: width, POSITION_X: tmp[0], POSITION_Y: tmp[1], WIDTH: width, REDUCED: reduced }
                });
                reduced="False";
                $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(REDUCE_button);
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

   $(".pop_up_message").dialog({ width: 550, resizable: false, closeOnEscape: false, buttons: [{ text: CLOSE_button, click: function() { $( this ).dialog( "close" ); if(typeof anchor != 'undefined') {  $.scrollTo("#"+anchor,300);  } } } ], hide: "fold", modal: true,  dialogClass: "popup_message"  });
   $( ".pop_up_error" ).dialog({ width: 550, resizable: false, closeOnEscape: false, buttons: [ { text: CLOSE_button, click: function() { $( this ).dialog( "close" ); if(typeof anchor != 'undefined') {  $.scrollTo("#"+anchor,300);  } } } ], hide: "fold", modal: true,  dialogClass: "popup_error" });

   $(".delete").click(function() {
          var currentForm;
          currentForm = $(this).closest('form');
          var FormId = currentForm.attr('id');
          var DialogId="";

          switch (FormId) {
            case 'reset_program_form':  DialogId="delete_dialog_program";
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


    $("input[name*='plug_power_max']").change(function () {
        var id=$(this).attr('name').substr($(this).attr('name').length-1);
       
        if(!isFinite(String(id))) { 
            id="";
        }

        if($(this).val()=="VARIO") {
            $("#select_canal_dimmer"+id).show();
        } else {
            $("#select_canal_dimmer"+id).css("display","none");
        }
    });


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
    


    $("#reset_sd_card_submit").click(function(e) {
        e.preventDefault();
        $.ajax({
            cache: false,
            async: true,
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
                                        window.location.href="configuration-"+slang;
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

    $("#import_log").click(function(e) {
        e.preventDefault();
        $("#progress_load").dialog({
            resizable: false,
            width: 550,
            modal: true,
            closeOnEscape: false,
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
                        $("#import_load").val($("#log_search").val());
                        document.forms['display-log-day'].submit();
                    }
            }]

         });
         $("#progress_bar_load").progressbar({value:0});
         $("#progress_bar_load_power").progressbar({value:0});
		 
		 var name="sd_card";
         $.ajax({
            cache: false,
            async: true,
            url: "../../main/modules/external/get_variable.php",
            data: {name:name}
         }).done(function (data) {
            if(data) {
				loadLog($("#log_search").val()*31,0,"logs",$("#log_search").val()*31,"submit",data);
				loadLog($("#log_search").val()*31,0,"power",$("#log_search").val()*31,"submit",data);
			} else {
                $("#error_load_power").show();
                $("#error_load").show();
            }
		});
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
            resizable: false,
            width: 750,
            modal: true,
            closeOnEscape: false,
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
                        if(((checkFormatDate($("#datepicker_from").val()))&&(checkFormatDate($("#datepicker_to").val()))&&(compareDate($("#datepicker_from").val(),$("#datepicker_to").val())))||($("input:radio[name=check_type_delete]:checked").val()=="all")) {
                            $("#progress_delete_logs").show();
                            $("#progress_bar_delete_logs").progressbar({value:0});

                            var myArray = $("#datepicker_from").val().split('-');
                            var myArray2 = $("#datepicker_to").val().split('-');
                            var Date1 = new Date(myArray[0],myArray[1]-1,myArray[2]);
                            var Date2 = new Date(myArray2[0],myArray2[1]-1,myArray2[2]);

                            if($("input:radio[name=check_type_delete]:checked").val()=="all") {
                                var nb_jours=1;
                            } else {
                                var nb_jours=diffdate(Date1,Date2);
                            }

                            delete_logs("logs",$("input:radio[name=check_type_delete]:checked").val(), nb_jours,$("#datepicker_from").val(),nb_jours);

                            $("#delete_log_form").dialog({ closeOnEscape: false, buttons: [ {
                                        text: CLOSE_button,
                                        click: function() {
                                            $("#error_delete_logs").css("display","none");
                                            $("#success_delete_logs").css("display","none");
                                            $("#error_format_date_logs").css("display","none");
                                            $( this ).dialog( "close" );
                                            document.forms['display-log-day'].submit();
                                            return false;
                          } } ] });
                        } else {
                            $("#error_format_date_logs").css("display","");
                        }
                    }
                }]
            });
        });


        $("#wifi_ip_manual").click(function(e) {
            if($('#wifi_ip_manual').prop('checked')) { 
                    $('#wifi_ip').prop('disabled', false);
            } else {
                    $('#wifi_ip').prop('disabled', true);
            }
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

            if($("#wifi_enable option:selected").val()=="1") {
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

                if((wifi_password!="")&&($("#wifi_password").val()!="")) {
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
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/configure_menu.php",
                    data: {cost:$("#show_cost").val(),wifi:$("#wifi_enable").val()}
                });
                document.forms['configform'].submit();
            }
        }); 

        // Check errors for the display logs part:
        $("#display-log-submit").click(function(e) {
            e.preventDefault();
            if($("input:radio[name=type_select]:checked").val()=="day") {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#datepicker").val(),type:'date'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_start_days").show(700);
                        var current=$("#datepicker").datepicker('getDate').getFullYear()+"-"+('0'+($("#datepicker").datepicker('getDate').getMonth() + 1)).slice(-2)+"-"+('0'+($("#datepicker").datepicker('getDate').getDate())).slice(-2);
                        $("#datepicker").val(current);
                    } else {
                        document.forms['display-log-day'].submit();
                    }
                });
            } else {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#startyear option:selected").val()+"-"+$("#startmonth option:selected").val(),type:'month'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_start_month").show(700);
                    } else {
                        document.forms['display-log-month'].submit();
                    }
                });
            }
        });
            

        // Check errors for the cost part:
        $("#view-cost").click(function(e) {
            e.preventDefault();
            var checked=true;

            $("#error_start_interval").css("display","none");
            $("#error_start_cost").css("display","none");
            $("#error_end_interval").css("display","none");
            $("#error_end_cost").css("display","none");
            $("#error_cost_price").css("display","none");
            $("#error_cost_price_hc").css("display","none");
            $("#error_cost_price_hp").css("display","none");
            $("#error_start_hc").css("display","none");
            $("#error_stop_hc").css("display","none");


            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#datepicker_start").val(),type:'date'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_start_cost").show(700);
                    var current=$("#datepicker_start").datepicker('getDate').getFullYear()+"-"+('0'+($("#datepicker_start").datepicker('getDate').getMonth() + 1)).slice(-2)+"-"+('0'+($("#datepicker_start").datepicker('getDate').getDate())).slice(-2)
                    $("#datepicker_start").val(current);
                    checked=false;
                } 
            });

            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#datepicker_end").val(),type:'date'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_end_cost").show(700);
                    var current=$("#datepicker_end").datepicker('getDate').getFullYear()+"-"+('0'+($("#datepicker_end").datepicker('getDate').getMonth() + 1)).slice(-2)+"-"+('0'+($("#datepicker_end").datepicker('getDate').getDate())).slice(-2)
                    $("#datepicker_end").val(current);
                    checked=false;
                }
            });

            if(checked) {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#datepicker_start").val()+"_"+$("#datepicker_end").val(),type:'date_interval'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_start_interval").show(700);
                        $("#error_end_interval").show(700);
                        checked=false;
                    } 
                });
            }


            //For standard configuration:
            if($("#cost_type option:selected").val()=="standard") {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#cost_price").val(),type:'numeric'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_cost_price").show(700);
                        $("#cost_price").val(default_cost);
                        checked=false;
                    }
                });
            } else {
            // For HPC:
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#cost_price_hp").val(),type:'numeric'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_cost_price_hp").show(700);
                        $("#cost_price_hp").val(default_cost_hp);
                        checked=false;
                    }
                });

                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#cost_price_hc").val(),type:'numeric'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_cost_price_hc").show(700);
                        $("#cost_price_hc").val(default_cost_hc);
                        checked=false;
                    }
                });

                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#start_hc").val(),type:'short_time'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_start_hc").show(700);
                        $("#start_hc").val(default_start_hc);
                        checked=false;
                    }
                });


                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#stop_hc").val(),type:'short_time'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_stop_hc").show(700);
                        $("#stop_hc").val(default_stop_hc);
                        checked=false;
                    }
                });
            }


            if(checked) {
                document.forms['display-cost'].submit();
            }
        });



        // Check errors for the programs part:
        $("#apply").click(function(e) {
            $("#error_same_start").css("display","none");
            $("#error_same_end").css("display","none");
            $("#error_start_time").css("display","none");
            $("#error_end_time").css("display","none");
            $("#error_minimal_cyclic").css("display","none");

            $("#error_cyclic_duration").css("display","none");
            $("#error_cyclic_time").css("display","none");
            $("#error_minimal_cyclic").css("display","none");
            $("#error_start_time_cyclic").css("display","none");
            $("#error_end_time_cyclic").css("display","none");

            $("#error_value_program").css("display","none");

            e.preventDefault();
            var checked=true;

            if($('#cyclic').is(':checked')) {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#cyclic_duration").val(),type:'time'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_cyclic_duration").show(700);
                        $('#cyclic_duration').val("02:00:00");
                        checked=false;
                    } 
                });

                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#repeat_time").val(),type:'cyclic_time'}
                }).done(function (data) {
                    if(data!=1) {
                        if(data==2) {
                            $("#error_minimal_cyclic").show(700);
                            $('#repeat_time').val("01:00:00");
                        } else {
                            $("#error_cyclic_time").show(700);
                            $('#repeat_time').val("01:00:00");
                        }
                        checked=false;
                    }
                });

                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#start_time_cyclic").val(),type:'time'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_start_time_cyclic").show(700);
                        $('#start_time_cyclic').val("00:00:00");
                        checked=false;
                    }
                });


                //Vérification du format (HH:MM:SS) pour l'heure de fin d'un programme cyclique:
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#end_time_cyclic").val(),type:'time'}
                }).done(function (data) {
                    if(data!=1) {
                        //Affichage du massage d'erreur et remise à 0 du champ si le format n'est pas respecté:
                        $("#error_end_time_cyclic").show(700);
                        $('#end_time_cyclic').val("00:00:00");
                        checked=false;
                    }
                });
            } else {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#start_time").val(),type:'time'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_start_time").show(700);
                        $('#start_time').val("00:00:00");
                        checked=false;
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
                        $('#end_time').val("00:00:00");
                        checked=false;
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
                        }
                    });
                }
            }


            if($('#regprog').is(':checked')) {
                if(($("#value_program").val())&&($("#value_program").val()!="0")) { 
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/check_value.php",
                        data: {value:$("#value_program").val(),type:'value_program',plug_type:plugs_infoJS[$('#selected_plug option:selected').val()-1]['PLUG_TYPE']}
                    }).done(function (data) {
                        if(data!=1) {
                            $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[data.toInt()]);
                            $("#error_value_program").show(700);
                            checked=false;
                        } 
                    });
                } else {
                    if((plugs_infoJS[$('#selected_plug option:selected').val()-1]['PLUG_TYPE']=="heating")||(plugs_infoJS[$('#selected_plug option:selected').val()-1]['PLUG_TYPE']=="ventilator")) {
                                var check=3;
                     } else if((plugs_infoJS[$('#selected_plug option:selected').val()-1]['PLUG_TYPE']=="humidifier")||(plugs_infoJS[$('#selected_plug option:selected').val()-1]['PLUG_TYPE']=="dehumidifier")) {
                                var check=4;
                     } else if(plugs_infoJS[$('#selected_plug option:selected').val()-1]['PLUG_TYPE']=="pump") {
                                var check=5;
                     } else {
                                var check=6;
                     }


                     $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[check]);
                     $("#error_value_program").show(700);
                     checked=false;
                }
            }

            if(checked) {
                if((start==$('#start_time').val())&&(end==$('#end_time').val())&&(plug_selected==$('#selected_plug').val())) {
                    currentForm = $(this).closest('form');
                    if(confirmForm(currentForm,"same_dialog_program")) {
                        document.forms['actionprog'].submit();
                    }
                } else {
                    document.forms['actionprog'].submit();
                } 
            }
        });


        $("#reset_log_power_submit").click(function(e) {
        e.preventDefault();
        $("#delete_log_form_power").dialog({
            resizable: true,
            width: 750,
            modal: true,
            closeOnEscape: false,
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
                            if(((checkFormatDate($("#datepicker_from_power").val()))&&(checkFormatDate($("#datepicker_to_power").val()))&&(compareDate($("#datepicker_from_power").val(),$("#datepicker_to_power").val())))||($("input:radio[name=check_type_delete_power]:checked").val()=="all")) {
                            $("#progress_delete_logs_power").show();
                            $("#progress_bar_delete_logs_power").progressbar({value:0});

                            var myArray = $("#datepicker_from_power").val().split('-');
                            var myArray2 = $("#datepicker_to_power").val().split('-');
                            var Date1 = new Date(myArray[0],myArray[1]-1,myArray[2]);
                            var Date2 = new Date(myArray2[0],myArray2[1]-1,myArray2[2]);

                            if($("input:radio[name=check_type_delete_power]:checked").val()=="all") {
                                var nb_jours=1;
                            } else {
                                    var nb_jours=diffdate(Date1,Date2);
                            }

                            delete_logs("power",$("input:radio[name=check_type_delete_power]:checked").val(),nb_jours,$("#datepicker_from_power").val(),nb_jours);

                            $("#delete_log_form_power").dialog({ closeOnEscape: false, buttons: [ {
                                text: CLOSE_button,
                                 click: function() {
                                    $("#error_delete_logs_power").css("display","none");
                                    $("#success_delete_logs_power").css("display","none");
                                    $("#error_format_date_logs_power").css("display","none");
                                    $( this ).dialog( "close" );
                                    document.forms['display-log-day'].submit();
                                    return false;
                            } } ] });

                            } else {
                                $("#error_format_date_logs_power").css("display","");
                            }
                        }
            }]
         });
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

            if(nb_sensor==0) {
               // Si aucun capteur n'est selectionné: affichage du message précisant que le capteur 1 sera selectionné + selection automatique du capteur 1
               $("#error_select_sensor"+plug).show();
               $("#plug_sensor"+plug+"1 option[value='True']").prop('selected', 'selected');
            } else {
               // On efface le message d'erreur sinon
               $("#error_select_sensor"+plug).css("display","none");
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


    $('#substrat').change(function () {
        var i=0;
        var save_index=-1;
        $("#product").find('option').each(function() {
            if($('#substrat').val()!=product_array[i]['substrat']) {
                $(this).attr('disabled','disabled');
            } else {
                $(this).removeAttr('disabled');
                if(save_index==-1) {
                    save_index=$(this).prop('index');
                }
            }
            i=i+1;
        });

        if(save_index==-1) {
            save_index=0;
        }
    
        $("#product").prop('selectedIndex', save_index); 
          
    });

    $('#select_title').change(function () {
        var length = $('#select_title').children('option').length;
        if(($("#select_title").prop('selectedIndex')+1)==length) {
           $("#other_title_div").show();
        } else {
            $("#other_title_div").css("display","none");
        }
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
            url: "../../main/modules/external/check_value.php",
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
            url: "../../main/modules/external/check_value.php",
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
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#start_time").val()+"_"+$("#end_time").val(),type:'same_time'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_same_start").show(700);
                    $("#error_same_end").show(700);
                    checked=false;
                } 
            });
        }

        if(plug_type!="lamp") {
            if(($("#value_program").val())&&($("#value_program").val()!="0")) {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#value_program").val(),type:'value_program',plug_type:plug_type}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[data.toInt()]);
                        $("#error_value_program").show(700);
                        checked=false;
                    }
                });
            } else {
                if((plug_type=="heating")||(plug_type=="ventilator")) {
                    var check=3;
                } else if((plug_type=="humidifier")||(plug_type=="dehumidifier")) {
                    var check=4;
                } else if(plug_type=="pump") {
                    var check=5;
                } else {
                    var check=6;
                }

                $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[check]);
                $("#error_value_program").show(700);
                checked=false;
            }
        }

        if(checked) {
            if($(this).attr('id')=="finish") {
                $('#type_submit').val("submit_close");
            } else {
                $('#type_submit').val("submit_next");
            }
            document.forms['submit_wizard'].submit();
        }
    });

    
    // Check errors for the calendar part:
    $("#calendar-program").click(function(e) {
        e.preventDefault();

        $("#error_calendar_startdate").css("display","none");

        $.ajax({
            cache: false,
            async: false,
            url: "../../main/modules/external/check_value.php",
            data: {value:$("#calendar_startdate").val(),type:'date'}
        }).done(function (data) {
            if(data!=1) {
                $("#error_calendar_startdate").show(700);
                var current=$("#calendar_startdate").datepicker('getDate').getFullYear()+"-"+('0'+($("#calendar_startdate").datepicker('getDate').getMonth() + 1)).slice(-2)+"-"+('0'+($("#calendar_startdate").datepicker('getDate').getDate())).slice(-2)
                $("#calendar_startdate").val(current);
            } else {
                $.ajax({
                    cache: false,
                    url: "../../main/modules/external/update_calendar_external.php",
                    data: {substrat:$("#substrat").val(), product:$("#product").val(), calendar_start:$("#calendar_startdate").val(), sd_card: sd_card, event_name:$("#event_name").val(), select_croissance:$('input[name=select_croissance]:checked', '#create_calendar_program').val()}
                }).done(function (data) {
                   if(data==1) {
                            $('#calendar').fullCalendar( 'refetchEvents' );
                            $("#valid_create_calendar").dialog({
                                resizable: true,
                                width: 450,
                                modal: true,
                                closeOnEscape: false,
                                dialogClass: "popup_message",
                                buttons: [{
                                text: CLOSE_button,
                                click: function () {
                                    $( this ).dialog( "close" );
                                    return false;
                                }
                                }]
                            });
                    } else {
                        $('#calendar').fullCalendar('refetchEvents');
                            $("#error_create_calendar").dialog({
                                resizable: true,
                                width: 450,
                                modal: true,
                                closeOnEscape: false,
                                dialogClass: "popup_error",
                                buttons: [{
                                text: CLOSE_button,
                                click: function () {
                                    $( this ).dialog( "close" );
                                    return false;
                                }
                                }]
                            });
                    }

                    $.ajax({
                        type: "POST",
                        url: "http://localhost:6891/cultibox/main/modules/external/get_title_calendar_list.php",
                        data: {lang: llang}
                    }).done(function (data) {
                        if(data!="") {
                             var objJSON = jQuery.parseJSON(data);
                             $('#select_title').children().remove();
                             $.each(objJSON, function( key, value ) {
                                 $('#select_title').append(new Option(value, value, true, true)); 
                             });
                             $("#select_title").prop('selectedIndex', 0);  
                             $("#other_title_div").css("display","none");
                        }
                    });
                    });
            }
        });
    });


    // Check errors for the plugs part:
    $("#reccord_plugs").click(function(e) {
        selected_plug=$("#selected_plug").val();
        if(selected_plug!="all") { 
            nb_plugs=selected_plug;
        } else {
            selected_plug=1;
        }

        e.preventDefault();
        var checked=true;
        var anchor="";

        for(i=selected_plug;i<=nb_plugs;i++) {
            $("#error_power_value"+i).css("display","none");
            $("#error_tolerance_value_humi"+i).css("display","none");
            $("#error_tolerance_value_temp"+i).css("display","none");
            $("#error_tolerance_value_water"+i).css("display","none");
            $("#error_second_tolerance_value_humi"+i).css("display","none");
            $("#error_second_tolerance_value_temp"+i).css("display","none");
            $("#error_regul_value"+i).css("display","none");

            if($("#plug_enable"+i).val()=="True") {
                if($("#power_value"+i).val()) {
                    //Check power value:
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/check_value.php",
                            data: {value:$("#power_value"+i).val(),type:'numeric'}
                        }).done(function(data) {
                            if(data!=1) {
                                $("#error_power_value"+i).show(700);
                                checked=false;
                            }
                    });
                }


                //Check tolerance value
                if(($("#plug_type"+i).val()=="heating")||($("#plug_type"+i).val()=="humidifier")||($("#plug_type"+i).val()=="dehumidifier")||($("#plug_type"+i).val()=="ventilator")||($("#plug_type"+i).val()=="pump")) {
                    if(($("#plug_tolerance"+i).val()=="0")||($("#plug_tolerance"+i).val()=="")) {
                       $("#plug_tolerance"+i).val('0'); 
                    } else { 
                        $.ajax({
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/check_value.php",
                            data: {value:$("#plug_tolerance"+i).val(),type:'tolerance',plug: $("#plug_type"+i).val()}
                        }).done(function(data) {
                            if(data!=1) {
                                if(($("#plug_type"+i).val()=="humidifier")||($("#plug_type"+i).val()=="dehumidifier")) {
                                    $("#error_tolerance_value_humi"+i).show(700);
                                }

                                if(($("#plug_type"+i).val()=="ventilator")||($("#plug_type"+i).val()=="heating")) {
                                    $("#error_tolerance_value_temp"+i).show(700);
                                }

                                if($("#plug_type"+i).val()=="pump") {
                                    $("#error_tolerance_value_water"+i).show(700);
                                }
                                checked=false;
                            }
                        });
                    }


                    //Check the second regul values:
                    if($("#plug_regul"+i).val()=="True") {
                        if(($("#plug_second_tolerance"+i).val()=="0")||($("#plug_second_tolerance"+i).val()=="")) {
                            $("#plug_second_tolerance"+i).val('0');
                        } else {
                            $.ajax({
                            cache: false,
                            async: false,
                            url: "../../main/modules/external/check_value.php",
                            data: {value:$("#plug_second_tolerance"+i).val(),type:'tolerance',plug: $("#plug_type"+i).val()}
                            }).done(function(data) {
                                if(data!=1) {
                                    if(($("#plug_type"+i).val()=="humidifier")||($("#plug_type"+i).val()=="dehumidifier")) {
                                        $("#error_second_tolerance_value_temp"+i).show(700);
                                    }

                                    if(($("#plug_type"+i).val()=="ventilator")||($("#plug_type"+i).val()=="heating")||($("#plug_type"+i).val()=="pump")) {
                                        $("#error_second_tolerance_value_humi"+i).show(700);
                                    }
                                    checked=false;
                                }
                            });
                        } 


                        if(($("#plug_regul_value"+i).val()=="0")||($("#plug_regul_value"+i).val()=="")) {
                            $("#error_regul_value"+i).show(700);
                            checked=false;
                        } else {
                            $.ajax({
                            cache: false,
                            async: false,
                            url: "../../main/modules/external/check_value.php",
                            data: {value:$("#plug_regul_value"+i).val(),type:'regulation'}
                            }).done(function(data) {
                                if(data!=1) {
                                    $("#error_regul_value"+i).show(700);
                                    checked=false;
                                }
                            });
                        }
                    }

                	
                    if((!checked)&&(anchor=="")) {
                        anchor="anchor"+i;
                    }
                }
            }
         }

        if(checked) {
            document.forms['plugForm'].submit();
        } else if(anchor!="") {
           $.scrollTo("#"+anchor,300); 
        }
    });


    $('[id^="jumpto"]').click(function(e) {
             selected_plug=$("#selected_plug").val();
        if(selected_plug!="all") {
            nb_plugs=selected_plug;
        } else {
            selected_plug=1;
        }

        e.preventDefault();
        var checked=true;
        var anchor="";

        for(i=selected_plug;i<=nb_plugs;i++) {
            $("#error_power_value"+i).css("display","none");
            $("#error_tolerance_value_humi"+i).css("display","none");
            $("#error_tolerance_value_temp"+i).css("display","none");
            $("#error_second_tolerance_value_humi"+i).css("display","none");
            $("#error_second_tolerance_value_temp"+i).css("display","none");
            $("#error_regul_value"+i).css("display","none");

            if($("#plug_enable"+i).val()=="True") {
                if($("#power_value"+i).val()) {
                    //Check power value:
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/check_value.php",
                            data: {value:$("#power_value"+i).val(),type:'numeric'}
                        }).done(function(data) {
                            if(data!=1) {
                                $("#error_power_value"+i).show(700);
                                checked=false;
                            }
                    });
                }


                //Check tolerance value
                if(($("#plug_type"+i).val()=="heating")||($("#plug_type"+i).val()=="humidifier")||($("#plug_type"+i).val()=="dehumidifier")||($("#plug_type"+i).val()=="ventilator")||($("#plug_type"+i).val()=="pump")) {
                    if(($("#plug_tolerance"+i).val()=="0")||($("#plug_tolerance"+i).val()=="")) {
                       $("#plug_tolerance"+i).val('0');
                    } else {
                        $.ajax({
                        cache: false,
                        async: false,
                        url: "../../main/modules/external/check_value.php",
                            data: {value:$("#plug_tolerance"+i).val(),type:'tolerance',plug: $("#plug_type"+i).val()}
                        }).done(function(data) {
                            if(data!=1) {
                                if(($("#plug_type"+i).val()=="humidifier")||($("#plug_type"+i).val()=="dehumidifier")) {
                                    $("#error_tolerance_value_humi"+i).show(700);
                                }

                                if(($("#plug_type"+i).val()=="ventilator")||($("#plug_type"+i).val()=="heating")) {
                                    $("#error_tolerance_value_temp"+i).show(700);
                                }

                                if($("#plug_type"+i).val()=="pump") {
                                    $("#error_tolerance_value_water"+i).show(700);
                                }
                                checked=false;
                            }
                        });
                    }


                      //Check the second regul values:
                    if($("#plug_regul"+i).val()=="True") {
                        if(($("#plug_second_tolerance"+i).val()=="0")||($("#plug_second_tolerance"+i).val()=="")) {
                            $("#plug_second_tolerance"+i).val('0');
                        } else {
                            $.ajax({
                            cache: false,
                            async: false,
                            url: "../../main/modules/external/check_value.php",
                            data: {value:$("#plug_second_tolerance"+i).val(),type:'tolerance',plug: $("#plug_type"+i).val()}
                            }).done(function(data) {
                                if(data!=1) {
                                    if(($("#plug_type"+i).val()=="humidifier")||($("#plug_type"+i).val()=="dehumidifier")) {
                                        $("#error_second_tolerance_value_temp"+i).show(700);
                                    }

                                    if(($("#plug_type"+i).val()=="ventilator")||($("#plug_type"+i).val()=="heating")||($("#plug_type"+i).val()=="pump")) {
                                        $("#error_second_tolerance_value_humi"+i).show(700);
                                    }
                                    checked=false;
                                }
                            });
                        }


                         if(($("#plug_regul_value"+i).val()=="0")||($("#plug_regul_value"+i).val()=="")) {
                            $("#error_regul_value"+i).show(700);
                            checked=false;
                        } else {
                            $.ajax({
                            cache: false,
                            async: false,
                            url: "../../main/modules/external/check_value.php",
                            data: {value:$("#plug_regul_value"+i).val(),type:'regulation'}
                            }).done(function(data) {
                                if(data!=1) {
                                    $("#error_regul_value"+i).show(700);
                                    checked=false;
                                }
                            });
                        }

                    }


                 }
                if((!checked)&&(anchor=="")) {
                    anchor="anchor"+i;
                }
            }
        }

        if(checked) {
            document.forms['plugForm'].submit();
        } else if(anchor!="") {
           $.scrollTo("#"+anchor,300);
        }
    });


    //Wifi process:
    var check = window.location.pathname.match(/wifi/g);
    if(check) {
        wifi_process(300,wifi_ip);
    }
            

   


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
                                closeOnEscape: false,
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
                            loadLog("31",0,"logs","31","auto",data);
                            loadLog("31",0,"power","31","auto",data);
                        }
                    });
                }
            });
    }


    var checkCal = window.location.pathname.match(/calendar/g);
    if((checkCal)&&(important_list.length>0)) {
        $.ajax({
           cache: false,
           url: "../../main/modules/external/get_variable.php",
           data: {name:"important"}
        }).done(function (data) {
                if(jQuery.parseJSON(data)!="True") {
                      $("#dialog_calendar_important").dialog({
                        resizable: true,
                        width: 550,
                        modal: true,
                        closeOnEscape: false,
                        dialogClass: "popup_message",
                        buttons: [{
                            text: CLOSE_button,
                            "id": "btnClose",
                            click: function () {
                                $( this ).dialog("close");
                            }
                        }]
                    });

                    $.ajax({
                        cache: false,
                        url: "../../main/modules/external/set_variable.php",
                        data: {name:"important", value: "True"}
                    });
                }
            });
    }


    if(window.location.pathname.match(/configuration/g)) {
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
    }

    

    $("#display_calendar").click(function(e) {
           e.preventDefault();
           
           $("#manage_external_xml").dialog({
            resizable: false,
            width: 550,
            modal: true,
            closeOnEscape: false,
            dialogClass: "popup_message",
            buttons: [{
                    text: CANCEL_button,
                    "id": "btnClose",
                    click: function () {
                        $( this ).dialog( "close" ); return false;
                    }
            },{
            text: SAVE_button,
            click: function () {
                $( this ).dialog( "close" );
                var list="";
                $('input[name=xml_checkbox]').each(function() {
                    if(list=="") {
                        list=this.id+"*"+this.checked;
                    } else {
                        list=list+"/"+this.id+"*"+this.checked;
                    }
                })

                $.ajax({
                   cache: false,
                   url: "../../main/modules/external/update_config_xml.php",
                   data: {list:list}
                }).done(function (data) {
                    if(data==1) {
                        $('#calendar').fullCalendar( 'refetchEvents' );
                    }
                });
           } } ],
           });
    });


    $("#reset_calendar").click(function(e) {
           e.preventDefault();
           $("#reset_dialog_calendar").dialog({
                resizable: false,
                height:200,
                width: 500,
                modal: true,
                closeOnEscape: false,
                dialogClass: "dialog_cultibox",
                buttons: [{
                    text: OK_button,
                    click: function () {
                        $( this ).dialog("close"); 
                        $.ajax({
                            cache: false,
                            url: "../../main/modules/external/delete_logs.php",
                            data: {type:"calendar",type_reset:"all"}
                        }).done(function (data) {
                            if(data==1) {
                                $('#calendar').fullCalendar( 'refetchEvents' );
                                $("#valid_reset_calendar").dialog({
                                    resizable: true,
                                    width: 450,
                                    modal: true,    
                                    closeOnEscape: false,
                                    dialogClass: "popup_message",
                                    buttons: [{
                                    text: CLOSE_button,
                                    click: function () {
                                        $( this ).dialog( "close" );
                                        window.location = "calendar-"+slang;
                                        return false;
                                    }
                                    }],
                                    open: function(event, ui) {
                                            $("a.ui-dialog-titlebar-close").remove();    
                                    }
                                });
                            } 
                        });
                    }
                }, {
                    text: CANCEL_button,
                    click: function () {
                        $( this ).dialog( "close" ); return false;
                    }
                }],
                open: function(event, ui) {
                    $("a.ui-dialog-titlebar-close").remove();   
                }
         });
    });



    // Au chargement d'une page, on vérifie la variable tooltip_msg_box qui détermine si on doit affiche la boîte de message ou l'oeil:
    $.ajax({
        cache: false,
        url: "../../main/modules/external/get_variable.php",
        data: {name:"tooltip_msg_box"}
    }).done(function (data) {
        //Si l'oeil doit être affiché:
        if(jQuery.parseJSON(data)=="True") {
            //On cache la boîte de message:
            $(".message").dialog("option", "hide",{ effect: "slideUp", duration: 0 } );
            $(".message").dialog('close');


            //Affichage de l'oeil permettant de réafficher la boîte de message:
            // On affiche l'oeil sur le bord exterieur gauche de l'interface. La position dépend de la résolution de l'écran.
            // On récupère donc la taille de l'écran et la taille du div central de l'interface pour afficher l'oeil au bon endroit:
            // position = 90*(taille de l'écran - taille du div central)/200
            // taille de l'écran - taille du div central /2 ==> correspond à la taille de la marge à gauche (ou à droite) séparant le bord du div central
            // on affiche donc l'oeil a 90% de la marge exterieure

			var element_div_width=$("#maininner").width();
			var element_body_width=$( window ).width();
			
			if((element_body_width!="")&&(element_div_width!="")) {
                var dist=90*(element_body_width-element_div_width)/200
                $("#tooltip_msg_box").css("padding-left",dist+"px");
			}	
       

            //Affichage de l'oeil et de son tooltip: 
            $("#eyes_msgbox").attr('title', title_msgbox);
            $("#tooltip_msg_box").show();
        } else {
            //Sinon on cache l'oeil - la boîte de messages étant par défaut affichée:
            $("#tooltip_msg_box").css("display","none");
        }
    });


    //Lors du click sur l'oeil - on doit cacher l'oeil et afficher la boîte de messages:     
    $("#tooltip_msg_box").click(function(e) {
        //On positionne la variable SESSION tooltip_msg_box qui détermine si on doit afficher l'oeil ou pas lors du chargement d'une page
        // en fonction des actions utilisateurs. La variable est positionnée à False, au chargement d'une page l'oeil sera donc caché et 
        // la boîte de messages affichée.
        $.ajax({
            cache: false,
            url: "../../main/modules/external/set_variable.php",
            data: {name:"tooltip_msg_box", value: "False"}
        });

        // On cache l'oeil et on affiche la boîte de messages
        $("#tooltip_msg_box").fadeOut("slow");
        $(".message").dialog("open");
    });

});

// brief : Used to add a message in popup
// message : Message to show
// id to define
// type : information or error
function pop_up_add_information(message, id, type) {

    // Add message
    if (type == "information")
        $("#pop_up_information_part ul").append('<li id="' + id + '">' + message + '</li>');
        
    if (type == "error")
        $("#pop_up_error_part ul").append('<li id="' + id + '">' + message + '</li>');

    // If there is element in error part, show this part
    if ($("#pop_up_error_part ul li").length > 0)
        $("#pop_up_error_container").css("display", "");
        
    if ($("#pop_up_information_part ul li").length > 0)
        $("#pop_up_information_container").css("display", "");
        
    // Not fonctioning
    // Display message box if there are some informations
    //if ($("#pop_up_information_part ul li").length > 0 ||
    //    $("#pop_up_error_part ul li").length > 0)
    //    $(".message").dialog('open');
        
};

// brief : Remove a message in popup
// id to remove
function  pop_up_remove(id) {

    // Delete information
    $("#" + id).remove();
    
    if ($("#pop_up_error_part ul li").length < 1)
        $("#pop_up_error_container").css("display", "none");
        
    if ($("#pop_up_information_part ul li").length < 1)
        $("#pop_up_information_container").css("display", "none");
    
    // Close message dialog bos when there is no informations
    if ($("#pop_up_information_part ul li").length < 1 &&
        $("#pop_up_error_part ul li").length < 1)
        $(".message").dialog('close');
    
};

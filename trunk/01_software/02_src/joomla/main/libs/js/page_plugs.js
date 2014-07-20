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
title_msgbox   = <?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;
plugs_infoJS   = <?php echo json_encode($plugs_infos); ?>;
update_program = <?php echo json_encode($update_program); ?>;
session_id="<?php echo session_id(); ?>";

$(document).ready(function(){
      if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "../../main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", session_id:session_id}
        });
    }

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


    //Display dimmer canal:
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


    if(update_program) {
         $("#valid_update_program").dialog({
                resizable: false,
                height:200,
                width: 500,
                closeOnEscape: false,
                modal: true,
                dialogClass: "popup_message",
                hide: "fold",
                buttons: [
                    {
                    text: CLOSE_button,
                        click: function () {
                            $( this ).dialog( "close" ); return false;
                    }
                }]
            });
    }


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
               $.ajax({
                    cache: false,
                    async: true,
                    url: "../../main/modules/external/get_variable.php",
                    data: {name:'CHECK_PROGRAM',value:plug, session_id:session_id}
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
                                $( this ).dialog( "close" ); 
                            }
                            }, {
                            text: CANCEL_button,
                                click: function () {
                                    $("#plug_type"+plug).val(plugs_infoJS[plug-1]['PLUG_TYPE]']);
                                    $( this ).dialog( "close" ); return false;
                            }
                            }]
                        });
                    }
                });
            }
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

        if(checked) {
            document.forms['plugForm'].submit();
        } else if(anchor!="") {
           $.scrollTo("#"+anchor,300);
        }
    });
});




</script>

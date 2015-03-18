<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>


plugs_infoJS   = <?php echo json_encode($plugs_infos) ?>;
highchart_plug = <?php echo $selected_plug; ?>;
resume_plugs   = <?php echo json_encode($resume) ?>;
resume_regul   = <?php echo json_encode($resume_regul) ?>;
start          = <?php echo json_encode($start) ?>;
end            = <?php echo json_encode($end) ?>;
plug_selected  = <?php echo json_encode($selected_plug) ?>;
error_valueJS  = <?php echo json_encode($error_value) ?>;
apply          = <?php echo json_encode($apply); ?>;
limit          = <?php echo json_encode($limit); ?>;
var reload_page=false;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;

// {{{ getRegulation()
// ROLE display the regulation informations or not
// IN  input value: display or not the informations
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/programs.html 
function getRegulation(i, type) {
      var divValTemp = document.getElementById('regul_value_temp');
      var divLabelTemp = document.getElementById('regul_label_temp');
      var divValHumi = document.getElementById('regul_value_humi');
      var divLabelHumi = document.getElementById('regul_label_humi');
      var divLabelWater = document.getElementById('regul_label_water');
      var divValWater = document.getElementById('regul_value_water');

      if(i=="dimmer") {
            divValHumi.style.display = '';
            divLabelHumi.style.display = 'none';
            divValTemp.style.display = 'none';
            divLabelTemp.style.display = 'none';
            divValWater.style.display = 'none';
            divLabelWater.style.display = 'none';
            var divValue = document.getElementById('value_program');
            divValue.value="50";

            var divDimmerLabel=document.getElementById('dimmer_label');
            divDimmerLabel.style.display = '';
      } else {
        var divDimmerLabel=document.getElementById('dimmer_label');
        divDimmerLabel.style.display = 'none';
        switch(type) {
            case 'humidifier' :
            case 'dehumidifier' : 
                divValHumi.style.display = '';
                divLabelHumi.style.display = '';
                divValTemp.style.display = 'none';
                divLabelTemp.style.display = 'none';
                divValWater.style.display = 'none';
                divLabelWater.style.display = 'none';
                var divValue = document.getElementById('value_program');
                divValue.value="55";
                break;
            case 'extractor' :
            case 'intractor' :
            case 'heating' : 
            case 'ventilator' : 
                divValHumi.style.display = 'none';
                divLabelHumi.style.display = 'none';
                divValTemp.style.display = '';
                divLabelTemp.style.display = '';
                divValWater.style.display = 'none';
                divLabelWater.style.display = 'none';

                var divValue = document.getElementById('value_program');
                divValue.value="22";
                break;
            case 'pumpfiling' :
            case 'pumpempting' :
            case 'pump' :
                divValHumi.style.display = 'none';
                divLabelHumi.style.display = 'none';
                divValTemp.style.display = 'none';
                divLabelTemp.style.display = 'none';
                divValWater.style.display = '';
                divLabelWater.style.display = '';

                var divValue = document.getElementById('value_program');
                divValue.value="22";
                break;
            default:
                var divValue = document.getElementById('value_program');
                divValue.value="";
                break;
        }
      }

      var divValueRegul = document.getElementById('regul_value');
      var divLabelRegul = document.getElementById('regul_label');
    
      switch(i) {
         case 'regul' : divLabelRegul.style.display = ''; divValueRegul.style.display = ''; break;
         case 'dimmer': divLabelRegul.style.display = 'none'; divValueRegul.style.display = ''; break;
         default: divLabelRegul.style.display = 'none'; divValueRegul.style.display = 'none'; break;
      }

}
// }}}

$(document).ready(function(){
     if(limit && apply) {
        $("#error_limit_changes").dialog({
            resizable: false,
            height:200,
            width: 500,
            closeOnEscape: false,
            modal: true,
            dialogClass: "popup_error",
            buttons: [{
                 text: CLOSE_button,
                 click: function () {
                       scrolltodiv("scrollto");
                       $( this ).dialog( "close" );
                 }}]
            });
     } else if(apply) {
        scrolltodiv("scrollto");
     }

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


     pop_up_add_information("<?php echo __('WIZARD_ENABLE_FUNCTION'); ?>: <a href='/cultibox/index.php?menu=wizard' class='href-wizard-msgbox'><img src='main/libs/img/wizard.png' alt='<?php echo __('WIZARD'); ?>' title='' id='wizard' /></a>", "jumpto_wizard", "information");

     if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", duration: 36000 }
        });
    }

    $("#wizard").click(function(e) {
        e.preventDefault();
        get_content("wizard",getUrlVars("selected_plug=1"));
    });

    $("#select_program_index_conf").change(function() {
        get_content("programs",getUrlVars("selected_plug="+$("#selected_plug option:selected").val()+"&program_index_id="+$("#select_program_index_conf option:selected").val()));
    });

    $("#jumpto").click(function(e) {
        e.preventDefault();
        get_content("plugs",getUrlVars("selected_plug="+$("#selected_plug option:selected").val()+"&submenu="+$("#selected_plug option:selected").val()));
    });

     $("#reset_submit").click(function(e) {
        e.preventDefault();

        var prog_reset="";
        var prog="";
        $("#reset_program_form input:checkbox:checked").each(function() {
            if(prog_reset=="") {
                prog_reset=$(this).val();
                prog=$(this).val();
            } else {
                prog_reset=prog_reset+","+$(this).val();
            }
        });

        if(prog_reset!="") {
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/manage_reset_prog.php",
                data: {value:prog_reset,program_index:$("#reset_set_program_index_id option:selected").val()}
            }).done(function (data) {
                get_content("programs",getUrlVars("selected_plug="+prog+"&program_index_id="+$("#reset_set_program_index_id option:selected").val()));
           });
        }
     });


     $('input[id^="reset_selected_plug"]').change(function() {
            var check=false;
            $("#reset_program_form input:checkbox:checked").each(function() {
                check=true;
            });

            if(!check) {
                $("#reset_submit").addClass("inputDisable");
                $("#reset_submit").val("<?php echo __('NO_PLUG_SELECTED','jquery'); ?>");
                $("#reset_submit").prop('disabled', true);
            } else {
                $("#reset_submit").removeClass("inputDisable");
                $("#reset_submit").val("<?php echo __('RESET_PROGRAM','jquery'); ?>");
                $("#reset_submit").prop('disabled', false);
            }
    });

    $("#export_program").click(function(e) {
        e.preventDefault();
        $("#preparing-file-modal").dialog({ modal: true, resizable: false });
        $.ajax({
             cache: false,
             async: false,
             url: "main/modules/external/manage_export_prog.php",
             data: {selected_plug:$("#export_selected_plug option:selected").val(),program_index:$("#export_program_index_id option:selected").val()}
        }).done(function (data) {
            $("#preparing-file-modal").dialog('close');
            if(jQuery.parseJSON(data)!="0") {
                 $.fileDownload('tmp/export/'+jQuery.parseJSON(data));
            }
        });
    });

    $("#export_set_program").click(function(e) {
        e.preventDefault();
        $("#preparing-file-modal").dialog({ modal: true, resizable: false });
        $.ajax({
             cache: false,
             async: false,
             url: "main/modules/external/manage_export_prog.php",
             data: {selected_plug:<?php echo $nb_plugs; ?>,program_index:$("#export_set_program_index_id option:selected").val(),type:"set"}
        }).done(function (data) {
            $("#preparing-file-modal").dialog('close');
            if(jQuery.parseJSON(data)!="0") {
                 $.fileDownload('tmp/export/'+jQuery.parseJSON(data));
            }
        });
    });


    $('#import_program').click(function (e) {
        e.preventDefault();
    });

    $('#import_set_program').click(function (e) {
        e.preventDefault();
    });

    
    
    // Call the fileupload widget and set some parameters
     $('#fileupload').fileupload({
        dataType: 'json',
        url: 'main/modules/external/files.php',
        add: function (e, data) {
            var name="";
            $.each(data.files, function (index, file) {
                name=file.name;
            });

            $('#import_name_file').text(name);
            $('#import_program').val("<?php echo __('IMPORT_PROGRAM'); ?>");
            $('#import_program').removeClass("inputDisable");
            $('#import_program').attr('disabled', false);
            
            
            data.context = $('#import_program').click(function () {
                    data.submit();
            });
        },
        done: function (e, data) {
            e.preventDefault();
            $("#manage_program").dialog('close');

            var name="";
            $.each(data.result.files, function (index, file) {
                name=file.name;
            });
            
            $.ajax({
             cache: false,
             async: false,
             url: "main/modules/external/manage_import_prog.php",
             data: {selected_plug:$("#import_selected_plug option:selected").val(),program_index_id:$("#import_program_index_id option:selected").val(),file:name}
         }).done(function (data) {
             try  {
                get_content("programs",getUrlVars("selected_plug="+$("#import_selected_plug option:selected").val()+"&program_index_id="+$("#import_program_index_id option:selected").val()));
             } catch(err) {
                get_content("programs");
             }
         });

        }
    });


     $('#fileuploadset').fileupload({
        dataType: 'json',
        url: 'main/modules/external/files.php',
        add: function (e, data) {
            var name="";
            $.each(data.files, function (index, file) {
                name=file.name;
            });

            $('#import_set_name_file').text(name);
            $('#import_set_program').val("<?php echo __('IMPORT_PROGRAM'); ?>");
            $('#import_set_program').removeClass("inputDisable");
            $('#import_set_program').attr('disabled', false);


            data.context = $('#import_set_program').click(function () {
                    data.submit();
            });
        },
        done: function (e, data) {
            e.preventDefault();
            $("#manage_program").dialog('close');

            var name="";
            $.each(data.result.files, function (index, file) {
                name=file.name;
            });

            $.ajax({
             cache: false,
             async: false,
             url: "main/modules/external/manage_import_prog.php",
             data: {selected_plug:<?php echo $nb_plugs; ?>,program_index_id:$("#import_set_program_index_id option:selected").val(),file:name,type:"set"}
         }).done(function (data) {
             try  {
                get_content("programs",getUrlVars("selected_plug=1&program_index_id="+$("#import_set_program_index_id option:selected").val()));
             } catch(err) {
                get_content("programs");
             }
         });

        }
    });


    // Check errors for the programs part:
    $("#apply").click(function(e) {
            var checked=true;

            $("#error_same_start").css("display","none");
            $("#error_same_end").css("display","none");
            $("#error_cyclic_same_start").css("display","none");
            $("#error_cyclic_same_end").css("display","none");
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

            if($('#cyclic').is(':checked')) {
                $.ajax({
                    cache: false,
                    url: "main/modules/external/check_value.php",
                    async: false,
                    data: {value:$("#cyclic_duration").val(),type:'time'}
                }).done(function (data) {
                    if($.trim(data)!=1) {
                        $("#error_cyclic_duration").show(700);
                        $('#cyclic_duration').val("02:00:00");
                        checked=false;
                    } 
                });

                $.ajax({
                    cache: false,
                    url: "main/modules/external/check_value.php",
                    async: false,
                    data: {value:$("#repeat_time").val(),type:'cyclic_time'}
                }).done(function (data) {
                    if($.trim(data)!=1) {
                        if($.trim(data)==2) {
                            $("#error_minimal_cyclic").show(700);
                            $('#repeat_time').val("03:00:00");
                        } else {
                            $("#error_cyclic_time").show(700);
                            $('#repeat_time').val("03:00:00");
                        }
                        checked=false;
                    }
                });


                $.ajax({
                    cache: false,
                    url: "main/modules/external/check_value.php",
                    async: false,
                    data: {value:$("#start_time_cyclic").val(),type:'time'}
                }).done(function (data) {
                    if($.trim(data)!=1) {
                        $("#error_start_time_cyclic").show(700);
                        $('#start_time_cyclic').val("00:00:00");
                        checked=false;
                    }
                });


                //Vérification du format (HH:MM:SS) pour l'heure de fin d'un programme cyclique:
                $.ajax({
                    cache: false,
                    url: "main/modules/external/check_value.php",
                    async: false,
                    data: {value:$("#end_time_cyclic").val(),type:'time'}
                }).done(function (data) {
                    if($.trim(data)!=1) {
                        //Affichage du massage d'erreur et remise à 0 du champ si le format n'est pas respecté:
                        $("#error_end_time_cyclic").show(700);
                        $('#end_time_cyclic').val("23:59:59");
                        checked=false;
                    }
                });


                  if(checked) {
                    $.ajax({
                        cache: false,
                        url: "main/modules/external/check_value.php",
                        async: false,
                        data: {value:$("#start_time_cyclic").val()+"_"+$("#end_time_cyclic").val(),type:'same_time'}
                    }).done(function (data) {
                        if($.trim(data)!=1) {
                            $("#error_cyclic_same_start").show(700);
                            $("#error_cyclic_same_end").show(700);
                            checked=false;
                        }
                    });
                }
            } else {
                $.ajax({
                    cache: false,
                    url: "main/modules/external/check_value.php",
                    data: {value:$("#start_time").val(),type:'time'},
                    async: false
                }).done(function (data) {
                    if($.trim(data)!=1) {
                        $("#error_start_time").show(700);
                        $('#start_time').val("00:00:00");
                        checked=false;
                    }
                });

                $.ajax({
                    cache: false,
                    url: "main/modules/external/check_value.php",
                    data: {value:$("#end_time").val(),type:'time'},
                    async: false
                }).done(function (data) {
                    if($.trim(data)!=1) {
                        $("#error_end_time").show(700);
                        $('#end_time').val("00:00:00");
                        checked=false;
                    }
                });


                if(checked) {
                    $.ajax({
                        cache: false,
                        url: "main/modules/external/check_value.php",
                        async: false,
                        data: {value:$("#start_time").val()+"_"+$("#end_time").val(),type:'same_time'}
                    }).done(function (data) {
                        if($.trim(data)!=1) {
                            $("#error_same_start").show(700);
                            $("#error_same_end").show(700);
                            checked=false;
                        }
                    });
                }
            }


            if($('#regprog').is(':checked')) {
                if(($("#value_program").val())&&($("#value_program").val()!="")) { 
                    $("#value_program").val($("#value_program").val().replace(",","."));
                    $.ajax({
                        cache: false,
                        url: "main/modules/external/check_value.php",
                        async: false,
                        data: {value:$("#value_program").val(),type:'value_program',plug_type:plugs_infoJS[$('#selected_plug option:selected').val()-1]['PLUG_TYPE'],plug_tolerance:plugs_infoJS[$('#selected_plug option:selected').val()-1]['PLUG_TOLERANCE']}
                    }).done(function (data) {
                        var return_array = JSON.parse(data);
                        if(parseInt(return_array['error'])>1) {
                            if(parseInt(return_array['error'])==2) {
                                $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[parseInt(return_array['error'])]);
                            } else {
                                $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[parseInt(return_array['error'])]+": "+return_array['min']+return_array['unity']+" <?php echo __('AND'); ?> "+return_array['max']+return_array['unity']);

                            }
                            $("#error_value_program").show(700);
                            checked=false;
                        } 
                    });
                } else {
                     $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[2]);
                     $("#error_value_program").show(700);
                     checked=false;
                }
            }

            if(checked) {
                if((start==$('#start_time').val())&&(end==$('#end_time').val())&&(plug_selected==$('#selected_plug').val())&&($("input[id=ponctual]:checked").val()=="ponctuelle")) {
                    $("#same_dialog_program").dialog({
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
        
                                var data_array = {};
                                $("#actionprog :input").each(function() {
                                    data_array[$(this).attr('name')]=$(this).val();
                                });

                                //Customization for radio button:
                                data_array['regul_program']=$("input[type='radio'][name='regul_program']:checked").val();
                                if($("input[type='radio'][id='ponctual']:checked").length>0) {
                                    data_array['cyclic']="";
                                    data_array['ponctual']=$("input[type='radio'][id='ponctual']:checked").val();
                                } else {
                                    data_array['cyclic']=$("input[type='radio'][id='cyclic']:checked").val();
                                    data_array['ponctual']="";
                                }

                                //Customization of checkbox:
                                data_array['reset_old_program']=$("input[type='checkbox'][name='reset_old_program']:checked").val();

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
                                        $.ajax({
                                            cache: false,
                                            async: false,
                                            url: "main/modules/external/create_program.php",
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
                                                    context: document.body,
                                                    success: function(data, textStatus, jqXHR) {
                                                    },
                                                    error: function(jqXHR, textStatus, errorThrown) {
                                                        // Error during request
                                                    }
                                                });
                                            }

                                            $.ajax({
                                                cache: false,
                                                async: false,
                                                url: "main/modules/external/set_variable.php",
                                                data: {name:"UPDATED_CONF", value: "True", duration: 86400 * 365}
                                            });

                                            get_content("programs",getFormInputs('actionprog'));
                                        });
                                    }
                                });
                                $.unblockUI();
                            }
                        }, {
                            text: CANCEL_button,
                            click: function () {
                                $( this ).dialog( "close" ); return false;
                        }
                        }]
                    });
                } else {
                     var data_array = {};
                        $("#actionprog :input").each(function() {
                        data_array[$(this).attr('name')]=$(this).val();
                    });

                    //Customization for radio button:
                    data_array['regul_program']=$("input[type='radio'][name='regul_program']:checked").val();
                    if($("input[type='radio'][id='ponctual']:checked").length>0) {
                        data_array['cyclic']="";
                        data_array['ponctual']=$("input[type='radio'][id='ponctual']:checked").val();
                    } else {
                        data_array['cyclic']=$("input[type='radio'][id='cyclic']:checked").val();
                        data_array['ponctual']="";
                    }

                    //Customization of checkbox:
                    data_array['reset_old_program']=$("input[type='checkbox'][name='reset_old_program']:checked").val();

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
                    $.ajax({
                        cache: false,
                        async: false,
                        url: "main/modules/external/create_program.php",
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
                                    context: document.body,
                                    success: function(data, textStatus, jqXHR) {
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        // Error during request
                                    }
                                });
                            }

                            $.ajax({
                                cache: false,
                                async: false,
                                url: "main/modules/external/set_variable.php",
                                data: {name:"UPDATED_CONF", value: "True", duration: 86400 * 365}
                            });

                            get_content("programs",getFormInputs('actionprog'));        
                    });
                    }
                    });
                } 
            }
            $.unblockUI();
        });


    // Time pickers definition
    $('#start_time , #end_time , #repeat_time , #cyclic_duration , #start_time_cyclic , #end_time_cyclic').timepicker({
        <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
        showSecond: true,
        showOn: 'both',
        buttonImage: "main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '"  . __('TIMEPICKER_BUTTON_TEXT')."',"; ?>
        timeFormat: 'hh:mm:ss',
        <?php echo "timeText: '"    . __('TIMEPICKER_TIME')."',"; ?>
        <?php echo "hourText: '"    . __('TIMEPICKER_HOUR')."',"; ?>
        <?php echo "minuteText: '"  . __('TIMEPICKER_MINUTE')."',"; ?>
        <?php echo "secondText: '"  . __('TIMEPICKER_SECOND')."',"; ?>
        <?php echo "currentText: '" . __('TIMEPICKER_ENDDAY')."',"; ?>
        <?php echo "closeText: '"   . __('TIMEPICKER_CLOSE')."',"; ?>
    });

    $("#value_program").keypress(function(e) {
        if(!VerifNumber(e)) e.preventDefault();
    });

        
    // Display and control user form for settings
    $("#program_settings").click(function(e) {
        e.preventDefault();

        $("#manage_program").dialog({
            resizable: false,
            width: 800,
            modal: true,
            closeOnEscape: true,
            dialogClass: "popup_message",
            buttons: [{
                text: CLOSE_button,
                "id": "btnClose",
                click: function () {
                    $( this ).dialog( "close" ); 
                    if(reload_page) {   
                         get_content("programs",getUrlVars("selected_plug="+$('#selected_plug option:selected').val()+"&program_index_id="+$("#select_program_index_conf option:selected").val()));
                    } else {
                        //Reset import file:
                        $('#import_name_file').text("");
                        $('#import_program').val("<?php echo __('SELECT_FILE_NAME','html'); ?>");
                        $('#import_program').attr('disabled', true);
                        $('#import_program').addClass("inputDisable");
                        return false;
                    }
                }
            }],
        });
    });


    // Action when user decide to delete a program
    $("#daily_delete_button").click(function(e) {
        e.preventDefault();

        $.ajax({
           cache: false,
           type: "GET",
           url: "main/modules/external/daily_program_delete.php",
           data: {
                program_delete_index:$("#program_delete_index option:selected").val(),
            }
        }).done(function (data) {
              if($.trim(data)=="") { 
                    // Display dialog bow to alert user
                    $("#dialog-form-delete-daily").dialog({
                        resizable: false,
                        closeOnEscape: true,
                        dialogClass: "popup_message",
                        modal: true,
                        buttons: [{
                            text: CLOSE_button,
                            "id": "btnClose",
                            click: function () {
                                $( this ).dialog( "close" ); return false;
                            }
                        }]
                    });
        
                    // If it's the same program, redraw the page
                    if ($("#program_delete_index :selected" ).val() == "<?php echo $program_index_id ?>") {
                        reload_page=true;
                    }
            
                    // remove program from available
                    idToDelete = $( "#program_delete_index" ).val()
                    $("#program_delete_index option[value='" + idToDelete + "']").remove();
                    $("#program_index_id_id option[value='" + idToDelete + "']").remove();

                    $("#export_program_index_id option[value='" + idToDelete + "']").remove();
                    $("#import_program_index_id option[value='" + idToDelete + "']").remove();
                    $("#select_program_index_conf option[value='" + idToDelete + "']").remove();
                    $("#export_set_program_index_id option[value='" + idToDelete + "']").remove();
                    $("#import_set_program_index_id option[value='" + idToDelete + "']").remove();
                    $("#reset_set_program_index_id option[value='" + idToDelete + "']").remove();
                    $("#daily_save_program_index_id option[value='" + idToDelete + "']").remove();

                    if($("#program_delete_index option").length==0) {
                            $("#program_delete_index").css("display","none");
                            $("#daily_delete_button").attr("disabled", "disabled");
                            $("#daily_delete_button").addClass("inputDisable");
                            $("#daily_delete_button").val("<?php echo __('NO_DAILY_PROGRAM_TO_DELETE','html'); ?>");

                            $("#program_index_id_id").css("display","none");
                    }
            } else {
                    $("#dialog-form-delete-daily-error").dialog({
                        resizable: false,
                        closeOnEscape: true,
                        dialogClass: "popup_error",
                        modal: true,
                        buttons: [{
                            text: CLOSE_button,
                            "id": "btnClose",
                            click: function () {
                                $( this ).dialog( "close" ); return false;
                            }
                        }]
                   });
            }
        });
        
    });    
    
    var program_name = $( "#program_name" );
    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            return false;
        } else {
            return true;
        }
    }

    $("#dialog-form-save-daily" ).dialog({
        autoOpen: false,
        closeOnescape: false,
        height: 180,
        width: 600,
        modal: true,
        buttons: [{
          text: SAVE_button,
          click: function () {
            var bValid = true;
            program_name.removeClass( "ui-state-error" );
            bValid = bValid && checkLength( program_name, "program_name", 3, 16 );
            if ( bValid ) {
                $.ajax({
                    type: "GET",
                    url: "main/modules/external/check_daily_program_name.php",
                    data: { name:$('#program_name').val()}
                }).done(function (data) {
                    if(JSON.parse(data)=="") {
                        $.ajax({ 
                            type: "GET",
                            url: "main/modules/external/daily_program_save.php",
                            data: {
                                name:$('#program_name').val(),
                                input: $('#daily_save_program_index_id option:selected').val(),
                                version:1.0
                            },
                            context: document.body,
                            success: function(data, textStatus, jqXHR) {

                                // Check response from server
                                var return_array = JSON.parse(data);
                        
                                // Add program in select
                                $('#program_delete_index').append('<option value="' + return_array.id + '">' + return_array.name + '</option>');
                                $('#program_index_id_id').append('<option value="' + return_array.id + '">' + return_array.name + '</option>');

                                $('#export_program_index_id').append('<option value="' + return_array.id + '">' + return_array.name + '</option>');
                                $('#import_program_index_id').append('<option value="' + return_array.id + '">' + return_array.name + '</option>');
                                $("#select_program_index_conf").append('<option value="' + return_array.id + '">' + return_array.name + '</option>');
                                $('#export_set_program_index_id').append('<option value="' + return_array.id + '">' + return_array.name + '</option>');
                                $('#import_set_program_index_id').append('<option value="' + return_array.id + '">' + return_array.name + '</option>');
                                $('#reset_set_program_index_id').append('<option value="' + return_array.id + '">' + return_array.name + '</option>');
                                $('#daily_save_program_index_id').append('<option value="' + return_array.id + '">' + return_array.name + '</option>');


                                $("#daily_delete_button").removeAttr('disabled');
                                $("#daily_delete_button").removeClass("inputDisable");
                                $("#daily_delete_button").val("<?php echo __('PROGRAM_DAILY_DELETE','html'); ?>");


                                $("#program_delete_index").show();
                                $('#program_index_id_id').show();
                                

                                // Prevent user that's ok
                                $("#dialog-form-copy-daily").dialog({
                                    resizable: false,
                                    closeOnEscape: true,
                                    dialogClass: "popup_message",
                                    modal: true,
                                    buttons: [{
                                        text: CLOSE_button,
                                        "id": "btnClose",
                                        click: function () {
                                            $( this ).dialog( "close" ); return false;
                                        }
                                    }],
                                });
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                // Error during request
                            }
                        });
               
                        $('#program_name').val("");
                        $("#dialog-form-save-daily").dialog( "close" );
                    } else {
                          $("#dialog-form-add-daily-error").dialog({
                                resizable: false,
                                closeOnEscape: true,
                                dialogClass: "popup_error",
                                modal: true,
                                buttons: [{
                                    text: CLOSE_button,
                                    "id": "btnClose",
                                    click: function () {
                                        $( this ).dialog( "close" ); return false;
                                }
                                }]
                        });

                        $('#program_name').val("");
                    }
                });
            } else {
                 $("#dialog-form-add-daily-format-error").dialog({
                      resizable: false,
                      closeOnEscape: true,
                      dialogClass: "popup_error",
                      modal: true,
                      buttons: [{
                          text: CLOSE_button,
                          "id": "btnClose",
                          click: function () {
                               $( this ).dialog( "close" ); return false;
                          }
                      }]
                 });
                 program_name.val( "" ).removeClass( "ui-state-error" );
            }
        }},{
        text: CLOSE_button,
        click: function () {
            $( this ).dialog( "close" );
        }
        }],
        close: function() {
            program_name.val( "" ).removeClass( "ui-state-error" );
        }
    });

    $("#daily_save_button").click(function(e) {
        e.preventDefault();
        $("#dialog-form-save-daily").dialog('open');
    });

    //Ajax to add a plus:
    $('#add_plug').click(function(e) {
        e.preventDefault();
         $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/manage_nb_plugs.php",
            data: {type:"add",nb_plugs:<?php echo $nb_plugs; ?>}
        }).done(function () {   
            get_content("programs",getUrlVars("selected_plug="+<?php echo $nb_plugs+1; ?>));

        });
    });

    //Ajax to remove a plus:
    $('#remove_plug').click(function(e) {
        e.preventDefault();
         $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/manage_nb_plugs.php",
            data: {type:"remove",nb_plugs:<?php echo $nb_plugs; ?>}
        }).done(function() {
            if($('#selected_plug').val()==<?php echo $nb_plugs; ?>) {
                get_content("programs",getUrlVars("selected_plug="+<?php echo $nb_plugs-1; ?>));
            } else {
                get_content("programs",getUrlVars("selected_plug="+$('#selected_plug').val()));
            }
        });
    });
});


Highcharts.setOptions({
    global: {
        useUTC: true
    },
    lang: {
        <?php echo "resetZoom : '".__('RESET_ZOOM_TITLE','highchart')."',"; ?>
        <?php echo "resetZoomTitle : '".__('RESET_ZOOM_TITLE','highchart')."'"; ?>
    }
});

var enabled = false;
var showzoomX=false;
var chart;
$(document).ready(function() {
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
        chart = new Highcharts.Chart({
            chart: {
                backgroundColor: '#F7F7F9',
                borderWidth: 3,
                borderColor: '#959891',
                borderRadius: 20,
                spacingRight: 35,
                renderTo: 'plug',
                type: 'spline',
                zoomType: 'x'
            },
            title: {
                useHTML: true,
                text: <?php echo "'".__('CURRENT_PLUG_PROGRAM').": '"; ?>,
                margin: 40
            },
            subtitle: {
                useHTML: true,
                text: document.ontouchstart === undefined ?
                <?php echo "'".__('DRAG_PLOT')."'"; ?>:
                <?php echo "'".__('DRAG_PLOT')."'"; ?>
            },
            lang: {
             useHTML: true,
             printChart: <?php echo "'".__('PRINT_HIGHCHART')."',"; ?>
             exportButtonTitle: <?php echo "'".__('EXPORT_HIGHCHART')."',"; ?>
             downloadPNG : <?php echo "'".__('EXPORT_PNG')."',"; ?>
             downloadJPEG : <?php echo "'".__('EXPORT_JPEG')."',"; ?>
             downloadPDF : <?php echo "'".__('EXPORT_PDF')."',"; ?>
             downloadSVG : <?php echo "'".__('EXPORT_SVG')."',"; ?>
             resetZoom : <?php echo "'".__('RESET_ZOOM_TITLE')."',"; ?>
             resetZoomTitle : <?php echo "'".__('RESET_ZOOM_TITLE')."'"; ?>,
             contextButtonTitle: <?php echo "'".__('CONTEXT_MENU')."'"; ?>
            },
            legend: {
                layout: 'vertical',
                verticalAlign: 'bottom',
                align: 'right',
                y: -<?php echo ($GLOBALS['NB_MAX_PLUG']-$nb_plugs)*16; ?>,
                x: +25,
                maxHeight: 200,
                navigation: {
                    activeColor: '#3E576F',
                    animation: true,
                    arrowSize: 10,
                    inactiveColor: '#CCC',
                    style: {
                        fontWeight: 'bold',
                        color: '#333',
                        fontSize: '10px'    
                    }
                }
            },
            xAxis: {
             type: 'datetime',
             endOnTick: true,
             showFirstLabel: false,
             showLastLabel: true,
             dateTimeLabelFormats: { // don't display the dummy year
                hours: '%H',
                minutes: '%M',
                seconds: '%S'
            },
            min: 0,
            max: 86399999,
            tickInterval: 6 * 3600 * 1000,
                labels: {
                formatter: function () {
                    var tmp=Highcharts.dateFormat('%H:%M', this.value);
                    if(tmp=="00:00") return "24:00";
                    return tmp;
                }
                },
                events: {
                    afterSetExtremes: function(event){
                        $('#resetXzoom').show();
                        showzoomX=true;
                        if(chart.resetZoomButton) {
                                chart.resetZoomButton.hide();
                        }
                    }
                }
            },
          yAxis: {
                min: -1,
                max: 100,
                startOnTick: false,
                endOnTick: false,
                labels: {
                    useHTML: true,
                    formatter: function() {
                        if(this.value == 0) return "<?php echo __("VALUE_OFF") ;?>";
                        if(this.value >= 0 && this.value <= 100) {
                        <?php
                            switch ($plugs_infos[$selected_plug-1]["PLUG_TYPE"]) {
                                case "ventilator" :
                                case "heating" :
                                case "extractor" :
                                case "intractor" :
                                    echo 'if(this.value == 100) return "' . __("CHART_FORCE_ON_VALUE") . '";';
                                    echo "return this.value+'°C';";
                                    break;
                                case "pumpfiling" :
                                case "pumpempting" :
                                case "pump" :
                                    echo 'if(this.value == 100) return "' . __("CHART_FORCE_ON_VALUE") . '";';
                                    echo "return this.value+'cm';";
                                    break;
                                case "humidifier" :
                                case "dehumidifier" :
                                    echo 'if(this.value == 100) return "' . __("CHART_FORCE_ON_VALUE") . '";';
                                    echo "return this.value+'%';";
                                    break;
                                default :
                                    echo 'if(this.value == 100) return "' . __("VALUE_ON") . '";';
                                    echo "return this.value;";
                                    break;
                            }
                        ?>
                        }
                    }
                },
                title: false
            },
            plotOptions: {
                series: {
                    events: {
                        legendItemClick: function(event) {
                                if(this.index==$('#selected_plug').val()-1) {
                                    return false;
                                }
                        }
                    },
                    marker : {
                        lineWidth : 2,
                        radius : 6,
                        symbol : 'circle'
                    },
                    dataLabels: {
                        enabled: false,
                        borderRadius: 5,
                        backgroundColor: 'rgba(252, 255, 197, 0.7)',
                        borderWidth: 1,
                        borderColor: '#AAA',
                        y: -8,
                        x: +20,
                        useHTML: true,
                        formatter: function(){ 
                            if (this.visible) {                      
                                if(this.y=="99.9") { return "<?php echo __('CHART_FORCE_ON_VALUE'); ?>"; }
                                else if(this.y=="0") { return "<?php echo __('VALUE_OFF'); ?>"; }
                                else { 
                                    var unity="";
                                    switch (plugs_infoJS[this.series.index]["PLUG_TYPE"]) {
                                        case "extractor" :
                                        case "intractor" :
                                        case "ventilator" :
                                        case "heating" :
                                            unity="°C";
                                            break;
                                        case "humidifier" :
                                        case "dehumidifier" :
                                            unity="%";
                                            break;
                                        case "pump" :
                                        case "pumpfiling" :
                                        case "pumpempting" :
                                            unity="cm";
                                            break;
                                    }
                                    return "<?php echo __('VALUE_REGUL'); ?>: "+this.y+unity;
                                }
                            }
                        }
                    }
                }
            },
            credits: {
                enabled: false
            },
            tooltip: {
                useHTML: true,
                formatter: function() {
                    if(this.y == 99.9) {
                        return "<p align='left'><b><?php echo __('XAXIS_LEGEND_DAY'); ?>:  </b>"+Highcharts.dateFormat('%H:%M:%S', this.x) +"<br /><b><?php echo __('BEHAVIOUR'); ?>: </b><?php echo __("CHART_FORCE_ON_VALUE"); ?></p>";
                    } else if(this.y == 0) {
                        return "<p align='left'><b><?php echo __('XAXIS_LEGEND_DAY'); ?>:  </b>"+Highcharts.dateFormat('%H:%M:%S', this.x) +"<br /><b><?php echo __('BEHAVIOUR'); ?>: </b><?php echo __("VALUE_OFF"); ?></p>";
                    } else { 
                        var unity="";
                        switch (plugs_infoJS[this.series.index]["PLUG_TYPE"]) {
                            case "extractor" :
                            case "intractor" :
                            case "ventilator" :
                            case "heating" :
                                unity="°C";
                                break;
                            case "humidifier" :
                            case "dehumidifier" :
                                unity="%";
                                break;
                            case "pump" :
                            case "pumpfiling" :
                            case "pumpempting" :
                                unity="cm";
                                break;
                        }
                        return "<p align='left'><b><?php echo __('XAXIS_LEGEND_DAY'); ?>:  </b>"+Highcharts.dateFormat('%H:%M:%S', this.x) +"<br /><b><?php echo __('BEHAVIOUR'); ?>: </b>"+this.y+unity+" (<?php echo __('REGULATION'); ?>)</p><br />"+resume_regul[this.series.index+1];
                    } 
                }
            },
            series: [
                <?php 
                $count=0;
                foreach($plugs_infos as $plugs) {  
                ?>

                    {
                     <?php if($plugs['id']==$selected_plug) { ?>
                        name: <?php echo "'<b>".clean_highchart_message($plugs_infos[$plugs['id']-1]['PLUG_NAME'])." (".clean_highchart_message($plugs_infos[$plugs['id']-1]['translate']).")</b>',"; ?>
                     <?php } else { ?>
                        name: <?php echo "'".clean_highchart_message($plugs_infos[$plugs['id']-1]['PLUG_NAME'])." (".clean_highchart_message($plugs_infos[$plugs['id']-1]['translate']).")',"; ?>
                     <?php } ?>
                     color: <?php echo "'".$GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'][$plugs['id']-1]."'"; ?>,
                     showCheckbox: false,
                     
                     <?php if($plugs['id']!=$selected_plug) { echo "visible: false,"; } else { echo "selected: true,"; } ?>
                     data: [
                        <?php echo $plugs_infos[$plugs['id']-1]["data"]; ?>
                     ]
                     ,events: {
                        checkboxClick: function(event) {
                            var series = chart.series[<?php echo $count; ?>];
                            if (series.visible) {
                                series.hide();
                            } else {
                                series.show();
                            }
                        }
                   }
                  }
                 <?php 
                 $count=$count+1;
                 if($count!=count($plugs_infos)) echo ","; 
                } ?>
            ] 
        });
    }
    });
    $.unblockUI();


    $('#selected_plug').change(function() {
        $("#error_value_program").css("display","none");
        $.each( chart.series, function() {
            if(this.index==$('#selected_plug').val()-1) {
                chart.series[$('#selected_plug').val()-1].show();
                chart.series[$('#selected_plug').val()-1].select(); 
                chart.series[$('#selected_plug').val()-1].name = "<b>"+clean_highchart_message(plugs_infoJS[$('#selected_plug').val()-1]['PLUG_NAME'])+" ("+clean_highchart_message(plugs_infoJS[$('#selected_plug').val()-1]['translate'])+")</b>";
                chart.series[$('#selected_plug').val()-1].legendItem = chart.series[$('#selected_plug').val()-1].legendItem.destroy();
                tmp_highchart_plug=this.index;

                $("#reset_selected_plug").val($('#selected_plug').val());
                $("#export_selected_plug").val($('#selected_plug').val());
                $("#import_selected_plug").val($('#selected_plug').val());
                $("#submenu").val($('#selected_plug').val());
            } else if(this.index==highchart_plug-1) {
                chart.series[this.index].name = clean_highchart_message(plugs_infoJS[this.index]['PLUG_NAME'])+" ("+clean_highchart_message(plugs_infoJS[this.index]['translate'])+")";
                chart.series[this.index].legendItem = chart.series[this.index].legendItem.destroy();
                chart.series[this.index].select(false);
                chart.series[this.index].hide();
            } else {
                chart.series[this.index].select(false);
                chart.series[this.index].hide();
            }
        });

        chart.yAxis[0].update({
            labels: {
                useHTML: true,
                formatter: function() {
                    if((this.value>=0)&&(this.value<=100)) {
                    
                        switch (plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]) {
                            case "extractor" :
                            case "intractor" :
                            case "ventilator" :
                            case "heating" :
                                if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                                if(this.value>0) return this.value+'°C';
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                                break;
                            case "humidifier" :
                            case "dehumidifier" :
                                if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                                if(this.value>0) return this.value+'%';
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                                break;
                            case "pump" :
                            case "pumpfiling" :
                            case "pumpempting" :
                                if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                                if(this.value>0) return this.value+' cm';
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                                break;
                            default :
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                                if(this.value==100) return '<?php echo __("VALUE_ON"); ?>';
                                break;
                        }
                        return this.value;
                    }
                }
            }
        });

        switch (plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']) {
            case "extractor":
            case "intractor":
            case "ventilator":
            case "heating":
            case "pumpfiling":
            case "pumpempting":
            case "pump":
            case "humidifier":
            case "dehumidifier":
                if($('#regul_program option[value="regul"]').length==0) {
                    $("#regul_program").append('<option value="regul"><?php echo __('VALUE_REGUL'); ?></option>');
                }
                break;
            default:
                if($('#regul_program option[value="regul"]').length!=0) {
                    $("#regul_program option[value='regul']").remove();
                }
                break;
        }

        var regul="";
        if($('#regprog').is(':checked')) {
            regul="regul";
        } else {
            regul="on";
        }
            
        switch(plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']) { 
            case 'other': getRegulation("on",plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']); 
                        if($('#regprog').is(':checked')) {
                           $('#regoff').attr('checked', true); 
                        }  
                        $('#regul_div').hide();
                        break;
            case 'lamp': getRegulation("on",plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']); 
                         $('#regul_div').hide(); 
                         if($('#regprog').is(':checked')) {
                           $('#regoff').attr('checked', true); 
                         }  
                         $('#regul_div').hide(); 
                         break;
            default: getRegulation(regul,plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']); $('#regul_div').show(); break;
        }


        if(plugs_infoJS[$('#selected_plug').val()-1]['PLUG_POWER_MAX']<10) {
            $("#dimmer_div").show();
        } else {
            $("#dimmer_div").css("display", "none");
        }


        if(plugs_infoJS[$('#selected_plug').val()-1]['PLUG_REGUL']=="False") {
            $("#second_regul_plug").html("<?php echo __('SECOND_REGUL_OFF'); ?>");
            $("#second_regul_info").css("display", "none");
        } else {
            $("#second_regul_plug").html("<?php echo __('SECOND_REGUL_ON'); ?>");
            $("#second_regul_img").attr("title", resume_regul[$('#selected_plug').val()]);
            $("#second_regul_info").css("display", "");
        }

        $("#resume_img").prop('title', resume_plugs[$('#selected_plug').val()]+"<br />"+resume_regul[$('#selected_plug').val()]);
        highchart_plug=tmp_highchart_plug+1;
        chart.isDirtyLegend = true;
        chart.redraw(); 

        $("#jumpplug input[name=selected_plug]").val($('#selected_plug').val());
        $('#selected_plug_conf').val($('#selected_plug').val());
        $('#export_selected_plug').val($('#selected_plug').val());
        $('#import_selected_plug').val($('#selected_plug').val());

    });

       $('#resetXzoom').click(function() {
        if(showzoomX) {
            chart.xAxis[0].setExtremes(null, null,true, true); 
            $('#resetXzoom').hide();
            showzoomX=false;
        }

    });


     /*   $('#datalabel').click(function() {
        if (!$(this).is(':checked')) {
            chart.xAxis[0].isDirty = true;
            chart.redraw();
        } else {
                chart.xAxis[0].isDirty = false;
                chart.redraw();
        }
     });
        */


    $('#selected_plug_conf').change(function() {
        $("#error_value_program").css("display","none");
        $.each( chart.series, function() {
            if(this.index==$('#selected_plug_conf').val()-1) {
                chart.series[$('#selected_plug_conf').val()-1].show();
                chart.series[$('#selected_plug_conf').val()-1].select(); 
                chart.series[$('#selected_plug_conf').val()-1].name = "<b>"+clean_highchart_message(plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_NAME'])+" ("+clean_highchart_message(plugs_infoJS[$('#selected_plug_conf').val()-1]['translate'])+")</b>";
                chart.series[$('#selected_plug_conf').val()-1].legendItem = chart.series[$('#selected_plug_conf').val()-1].legendItem.destroy();
                tmp_highchart_plug=this.index;
               
                //Sur le changement de prise selectionnée, on change les listes du reset, import et export: 
                $("#reset_selected_plug").val($('#selected_plug_conf').val());
                $("#export_selected_plug").val($('#selected_plug_conf').val());
                $("#import_selected_plug").val($('#selected_plug_conf').val());
                $("#submenu").val($('#selected_plug').val());
            } else if(this.index==highchart_plug-1) {
                chart.series[this.index].name = clean_highchart_message(plugs_infoJS[this.index]['PLUG_NAME'])+" ("+clean_highchart_message(plugs_infoJS[this.index]['translate'])+")";
                chart.series[this.index].legendItem = chart.series[this.index].legendItem.destroy();
                chart.series[this.index].select(false);
                chart.series[this.index].hide();
            } else {
                chart.series[this.index].select(false);
                chart.series[this.index].hide();
            }
        });

        chart.yAxis[0].update({
            labels: {
                useHTML: true,
                formatter: function() {
                    if((this.value>=0)&&(this.value<=100)) {
                    
                        switch (plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]) {
                            case "extractor" :
                            case "intractor" :
                            case "ventilator" :
                            case "heating" :
                                if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                                if(this.value>0) return this.value+'°C';
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                                break;
                            case "humidifier" :
                            case "dehumidifier" :
                                if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                                if(this.value>0) return this.value+'%';
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                                break;
                            case "pump" :
                            case "pumpfiling" :
                            case "pumpempting" :
                                if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                                if(this.value>0) return this.value+' cm';
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                                break;
                            default :
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                                if(this.value==100) return '<?php echo __("VALUE_ON"); ?>';
                                break;
                        }
                        return this.value;
                    }
                }
            }
        });

        switch (plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']) {
            case "extractor":
            case "intractor":
            case "ventilator":
            case "heating":
            case "pumpfiling":
            case "pumpempting":
            case "pump":
            case "humidifier":
            case "dehumidifier":
                if($('#regul_program option[value="regul"]').length!=0) {
                    $("#regul_program option[value='regul']").remove();
                }
                break;
            default:
                if($('#regul_program option[value="regul"]').length==0) {
                    $("#regul_program").append('<option value="regul"><?php echo __('VALUE_REGUL'); ?></option>');
                }
                break;
        }

        var regul="";
        if($('#regprog').is(':checked')) {
            regul="regul";
        } else {
            regul="on";
        }
            
        switch(plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']) { 
            case 'other': getRegulation("on",plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']); 
                          if($('#regprog').is(':checked')) {
                           $('#regoff').attr('checked', true); 
                          }  
                          $('#regul_div').hide();
                          break;
            case 'lamp': getRegulation("on",plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']); 
                         $('#regul_div').hide(); 
                         if($('#regprog').is(':checked')) {
                           $('#regoff').attr('checked', true); 
                         }  
                         $('#regul_div').hide(); 
                         break;
            default: getRegulation(regul,plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']); $('#regul_div').show(); break;
        }

         if(plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_POWER_MAX']<10) {
            $("#dimmer_div").show();
        } else {
            $("#dimmer_div").css("display", "none");
        }


        if(plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_REGUL']=="False") {
            $("#second_regul_plug").html("<?php echo __('SECOND_REGUL_OFF'); ?>");
            $("#second_regul_info").css("display", "none");
        } else {
            $("#second_regul_plug").html("<?php echo __('SECOND_REGUL_ON'); ?>");
            $("#second_regul_img").attr("title", resume_regul[$('#selected_plug_conf').val()]);
            $("#second_regul_info").css("display", "");
        }

        $("#resume_img").prop('title', resume_plugs[$('#selected_plug_conf').val()]+"<br />"+resume_regul[$('#selected_plug_conf').val()]);

        highchart_plug=tmp_highchart_plug+1;
        chart.isDirtyLegend = true;
        chart.redraw(); 

        $("#jumpplug input[name=selected_plug]").val($('#selected_plug_conf').val());
        $('#selected_plug').val($('#selected_plug_conf').val());
        $('#export_selected_plug').val($('#selected_plug_conf').val());
        $('#import_selected_plug').val($('#selected_plug_conf').val());
    });

       $('#resetXzoom').click(function() {
        if(showzoomX) {
            chart.xAxis[0].setExtremes(null, null,true, true); 
            $('#resetXzoom').hide();
            showzoomX=false;
        }

    });

});


/**
 * Save parameters when user change options.
 */
 $(document).ready(function() {
    // On select change, update conf
    $("#remove_1000_change_limit , #remove_5_minute_limit").each(function() {

        $(this).on('change', function() {
            if($(this).attr('id')=="remove_1000_change_limit") reload_page=true;

            newValue    = $( this ).find(":selected").val();
            varToUpdate = $( this ).attr('name');
            updateConf  = $( this ).attr('update_conf');
            curveTypeModified  = $( this ).attr('curveType');

            // Update database
            $.ajax({
                type: "GET",
                cache: false,
                url: "main/modules/external/update_configuration.php",
                data: {
                        value:newValue,
                        variable:varToUpdate,
                        updateConf:updateConf
                    }
            }).done(function (data) {
            });
        });
    });
});

</script>

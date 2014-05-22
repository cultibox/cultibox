<script>

<?php 
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>

product_array = <?php echo json_encode($product) ?>;
important_list = <?php echo json_encode($important_list) ?>;
title_msgbox = <?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;

$(function() {
   $("#calendar_startdate").datepicker({ 
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        showOn: "both",
        buttonImage: "../../main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
    }).val();

    $("#datepicker_calendar").datepicker({ 
        dateFormat: "yy-mm",
        yearRange: "-20:+0",
        changeMonth: true, 
        changeYear: true,
        showOn: "both",
        showButtonPanel: true,
        buttonImage: "../../main/libs/img/calendar_icon.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."'"; ?>,
        beforeShow: function() { 
                $('#hideday').html('.ui-datepicker-calendar{display:none;}'); 
                $('#hidetoday').html('.ui-datepicker-current{display:none;}');
        },
        onClose: function(dateText, inst){
                setTimeout(function(){$('#hideday').html('');},300);
                setTimeout(function(){$('#hidetoday').html('');},300);
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $("#calendar").fullCalendar( 'gotoDate', year , month );
        }
    }).val();
});

$(document).ready(function() {

    // Display and control user form for XML selection do display
     $("#calendar_xml_selection").click(function(e) {
        e.preventDefault();

        $("#manage_external_xml").dialog({
            resizable: false,
            width: 550,
            modal: true,
            closeOnEscape: true,
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

    // Display and control user form for resetting calendar
    $("#reset_calendar").click(function(e) {
           e.preventDefault();
           $("#reset_dialog_calendar").dialog({
                resizable: false,
                height:200,
                width: 500,
                modal: true,
                closeOnEscape: true,
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
    
    // Display and control user form for nutrient planification
     $("#nutrient_planification").click(function(e) {
           e.preventDefault();

           $("#manage_nutrient_planification").dialog({
                resizable: false,
                width: 750,
                modal: true,
                closeOnEscape: true,
                dialogClass: "popup_message",
                buttons: [{
                    text: CANCEL_button,
                    "id": "btnClose",
                    click: function () {
                        $( this ).dialog( "close" ); return false;
                    }
                },{
                text: "<?php echo __('CREATE_CALENDAR_PROGRAM'); ?>",
                click: function () {
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
                                data: {
                                    substrat:$("#substrat_select").val(),
                                    product:$("#nutrient_select").val(),
                                    calendar_start:$("#calendar_startdate").val(),
                                    sd_card: sd_card, event_name:$("#event_name").val(),
                                    select_croissance:$('input[name=select_croissance]:checked', '#manage_nutrient_planification').val()
                                }
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
                    $( this ).dialog( "close" ); return false;
                }}],
            });
    });
        
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

            
    $('#substrat_select').change(function () {
        // On substrat change, change nutrient allowed to be selected
        var i=0;
        var save_index=-1;
        $("#nutrient_select").find('option').each(function() {
            if($('#substrat_select').val()!=product_array[i]['substrat']) {
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
    
        $("#nutrient_select").prop('selectedIndex', save_index); 
          
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
    
    // Display and control user form for daily program
    $("#daily_program").click(function(e) {
        e.preventDefault();

        $("#manage_daily_program").dialog({
            resizable: false,
            width: 250,
            closeOnEscape: true,
            dialogClass: "popup_message",
            buttons: [{
                text: CANCEL_button,
                click: function () {
                    $( this ).dialog( "close" ); return false;
                }
            }]
        });
    });
    
    
    // Drag and drop into calendar  for daily program
    $('#manage_daily_program div.external-event').each(function() {
    
        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
            // use the element's text as the event title
            title: $.trim($(this).text()), 
            program_index: $.trim($(this).attr('id')) 
        };
        
        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject);
        
        // make the event draggable using jQuery UI
        $(this).draggable({
            revert: false,
            scroll: false,
            helper: 'clone',
            appendTo: '#main',
            cursor: 'move',
            zIndex: 9999,
        });
        
    });

});


    $(document).ready(function() {
        $('#calendar').fullCalendar({
            editable: true,
            dragable: true,
            droppable: true,
            selectable: true,
            unselectable: true,
            theme: true,
            monthNames:["<?php echo __('MONTH01'); ?>","<?php echo __('MONTH02'); ?>","<?php echo __('MONTH03'); ?>","<?php echo __('MONTH04'); ?>","<?php echo __('MONTH05'); ?>","<?php echo __('MONTH06'); ?>","<?php echo __('MONTH07'); ?>","<?php echo __('MONTH08'); ?>","<?php echo __('MONTH09'); ?>","<?php echo __('MONTH10'); ?>","<?php echo __('MONTH11'); ?>","<?php echo __('MONTH12'); ?>"],
            monthNamesShort:["<?php echo __('M01'); ?>","<?php echo __('M02'); ?>","<?php echo __('M03'); ?>","<?php echo __('M04'); ?>","<?php echo __('M05'); ?>","<?php echo __('M06'); ?>","<?php echo __('M07'); ?>","<?php echo __('M08'); ?>","<?php echo __('M09'); ?>","<?php echo __('M10'); ?>","<?php echo __('M11'); ?>","<?php echo __('M12'); ?>"],
            dayNames: ["<?php echo __('DAY07'); ?>","<?php echo __('DAY01'); ?>","<?php echo __('DAY02'); ?>","<?php echo __('DAY03'); ?>","<?php echo __('DAY04'); ?>","<?php echo __('DAY05'); ?>","<?php echo __('DAY06'); ?>"],
            dayNamesShort: ["<?php echo __('D07'); ?>","<?php echo __('D01'); ?>","<?php echo __('D02'); ?>","<?php echo __('D03'); ?>","<?php echo __('D04'); ?>","<?php echo __('D05'); ?>","<?php echo __('D06'); ?>"],
            buttonText: { today: "<?php echo __('TODAY'); ?>"}, 
            select: function(start, end, allDay) {
                $("#edit_start_date").text($.fullCalendar.formatDate(start, "yyyy-MM-dd"));
                
                if(end) {
                    $("#edit_stop_date").text($.fullCalendar.formatDate(end, "yyyy-MM-dd"));
                } else {
                    $("#edit_stop_date").text($.fullCalendar.formatDate(start, "yyyy-MM-dd"));
                }

                $("#select_remark").val("");

                $("#select_color").val("#000000"); 
                $('#colour').css({'background-color' : '#000000'});


                $("#dialog_create").dialog({
                    resizable: true,
                    width: 800,
                    buttons: {
                        "<?php echo __('CREATE_DIALOG_CALENDAR','highchart'); ?>": function() { 
                            $( this ).dialog( "close" ); 
                            <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                $.blockUI({ message: ''});
                            <?php } ?>
    
                            new_start=$.fullCalendar.formatDate( start, "yyyy-MM-dd HH:mm:ss");
                            new_end=$.fullCalendar.formatDate( end, "yyyy-MM-dd 23:59:59");

                            var length = $('#select_title').children('option').length;
                            if(($("#select_title").prop('selectedIndex')+1)==length) {
                                if($("#other_field_title").val()!= "") {
                                    var title=$("#other_field_title").val();
                                } else {
                                    var title=$('#select_title option:selected').val(); 
                                }
                            } else {
                                var title=$('#select_title option:selected').val(); 
                            }
                            var description=$('#select_remark').val();  
                            var color=$('#select_color').val();

                            if($("#event_important").prop('checked') == true) {
                                var important=1; 
                                var text_color="white";
                            } else {
                                var important=0;
                                var text_color="red";
                            }

                            if(!description) {
                                description="";
                            }

                            if(!color) {
                                color="#000000";
                            }

                            if (title) {
                                $('#calendar').fullCalendar('renderEvent', {
                                title: title,
                                start: new_start,
                                end: new_end,
                                description: description,
                                color: color,
                                external: external
                            });

                            $.ajax({
                                type: "POST",
                                url: "http://localhost:6891/cultibox/main/modules/json-events/json-add-events.php",
                                data: 'title='+encodeURIComponent(title)+'&start='+new_start+'&end='+new_end+'&desc='+encodeURIComponent(description)+'&color='+color+'&card=<?php echo $sd_card; ?>'+'&important='+important
                                <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                    ,complete: function() { $.unblockUI(); }
                                <?php } ?> 
                            }).done(function (data) {
                                if(data==1) {
                                    $.ajax({
                                        type: "POST",
                                        url: "http://localhost:6891/cultibox/main/modules/external/get_title_calendar_list.php",
                                        data: 'lang='+encodeURIComponent("<?php echo $lang; ?>")
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
                                } 
                            });

                            }

                            $('#calendar').fullCalendar('unselect');
                            $('#calendar').fullCalendar('refetchEvents');
                            $('#calendar').fullCalendar('rerenderEvents');
                            $("#select_remark").text("");
                            $("#select_remark").val("");
                            $("#other_field_title").text("");
                            $("#other_field_title").val("");
                            $("#select_title").prop('selectedIndex', 0);  
                            $("#event_important").attr('checked', false);
                             
                            delete description;
                            delete important;
                            delete event;
                            return false;
                    },
                    "<?php echo __('CANCEL_DIALOG_CALENDAR','highchart'); ?>": function() { 
                        $( this ).dialog( "close" ); 
                        $("#select_remark").text("");
                        $("#select_remark").val("");
                        $("#other_field_title").text("");
                        $("#other_field_title").val("");
                        $("#select_title").prop('selectedIndex', 0);  
                        $("#event_important").attr('checked', false);
                        delete description;
                        delete important;
                        delete event;
                        return false;}
                    }
                });
            },
            events: "http://localhost:6891/cultibox/main/modules/json-events/json-events.php",
            drop: function(date, allDay) {
                // this function is called when something is dropped
            
                // retrieve the dropped element's stored Event Object
                var originalEventObject = $(this).data('eventObject');
                
                // we need to copy it, so that multiple events don't have a reference to the same object
                var copiedEventObject = $.extend({}, originalEventObject);
                
                // assign it the date that was reported
                copiedEventObject.start = date;
                copiedEventObject.allDay = allDay;
                copiedEventObject.title = "<?php echo __('CALENDAR_DAILY_PROGRAM') ; ?>" + " " + originalEventObject.title;
                copiedEventObject.description = copiedEventObject.title;
                
                // render the event on the calendar
                // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
                
                // Compute date for ajax
                ajaxDate = "";
                ajaxDate = ajaxDate + copiedEventObject.start.getFullYear();
                if ((copiedEventObject.start.getMonth() + 1) < 10) {
                    ajaxDate = ajaxDate + "-0" + (copiedEventObject.start.getMonth() + 1);
                } else {
                    ajaxDate = ajaxDate + "-" + (copiedEventObject.start.getMonth() + 1);
                }
                if (copiedEventObject.start.getDate() < 10) {
                    ajaxDate = ajaxDate + "-0" + copiedEventObject.start.getDate();
                } else {
                    ajaxDate = ajaxDate + "-" + copiedEventObject.start.getDate();
                }
                
                $.ajax({
                    cache: false,
                    url: "../../main/modules/external/update_calendar_external.php",
                    data: {
                        daily_program_name: copiedEventObject.title,
                        calendar_start: ajaxDate,
                        sd_card: sd_card,
                        program_index: copiedEventObject.program_index
                    }
                }).done(function (data) {
                   if(data==1) {
                        //$('#calendar').fullCalendar( 'refetchEvents' );
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
                });
            },
            eventDrop: function(event, delta) {
                if(event.external) {
                    $('#calendar').fullCalendar( 'refetchEvents' );
                    $('#calendar').fullCalendar( 'rerenderEvents' );
                    return false;
                }

                new_start=$.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
                if(!event.end) {
                    event.end=event.start
                }

                new_end=$.fullCalendar.formatDate(event.end, "yyyy-MM-dd 23:59:59");

                <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                    $.blockUI({ message: ''}); 
                <?php } ?> 
                $.ajax({
                    type: "POST",
                    url: "http://localhost:6891/cultibox/main/modules/json-events/json-update-events.php",
                    data: 'title='+encodeURIComponent(event.title)+'&start='+ new_start +'&end='+ new_end +'&id='+event.id+'&color='+event.color+'&card=<?php echo $sd_card; ?>'+'&important='+event.important+'&desc='+encodeURIComponent(event.description)
                     <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                        ,complete: function() { $.unblockUI(); }
                     <?php } ?> 
                });
            },
            eventResize: function(event, dayDelta, minuteDelta) { 
                if(event.external) {
                    $('#calendar').fullCalendar( 'refetchEvents' );
                    $('#calendar').fullCalendar( 'rerenderEvents' );
                    return false;
                }
                new_start=$.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
                if(!event.end) {
                    event.end=event.start
                }

                new_end=$.fullCalendar.formatDate(event.end, "yyyy-MM-dd 23:59:59");

                <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                    $.blockUI({ message: ''}); 
                <?php } ?>
                $.ajax({
                    type: "POST",
                    url: "http://localhost:6891/cultibox/main/modules/json-events/json-update-events.php",
                    data: 'title='+encodeURIComponent(event.title)+'&start='+ new_start +'&end='+ new_end +'&id='+event.id+'&color='+event.color+'&card=<?php echo $sd_card; ?>'+'&important='+event.important
                    <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                      ,complete: function() { $.unblockUI(); }
                    <?php } ?>
                });
            },
            eventClick: function(event, element) {
                $("#title").text(event.title);
                $("#start_date").text($.fullCalendar.formatDate(event.start, "yyyy-MM-dd"));
                if(event.end) {
                $("#stop_date").text($.fullCalendar.formatDate(event.end, "yyyy-MM-dd"));
                }else {
                $("#stop_date").text($.fullCalendar.formatDate(event.start, "yyyy-MM-dd"));
                }
                if(event.description) {
                $("#remark").val(event.description);
                } else {
                $("#remark").val('');
                }

                if(event.important==1) {
                $("#important").attr('checked', true);
                } else {
                $("#important").attr('checked', false);
                }

                $("#dialog_edit").dialog({
                resizable: true,
                width: 800,
                modal: true,
                buttons: {
                    "<?php echo __('EDIT_DIALOG_CALENDAR','highchart'); ?>": function() { 
                        $( this ).dialog( "close" ); 


                        $('#select_title option[value="'+event.title+'"]').prop('selected', true);
                        $("#edit_start_date").text($.fullCalendar.formatDate(event.start, "yyyy-MM-dd"));
                        if(event.end) {
                            $("#edit_stop_date").text($.fullCalendar.formatDate(event.end, "yyyy-MM-dd"));
                        }else {
                             $("#edit_stop_date").text($.fullCalendar.formatDate(event.start, "yyyy-MM-dd"));
                        }
                        if(event.description) {
                             $("#select_remark").val(event.description);
                        } else {
                            $("#select_remark").val('');
                            $("#select_remark").text('');
                        }

                        if(event.important==1) {
                            $("#event_important").attr('checked', true);
                        } else {
                            $("#event_important").attr('checked', false);
                        }

                        if(event.color) {
                           $("#select_color").val(event.color); 
                           $('#colour').css({'background-color' : event.color});
                        }

                        $("#dialog_create").dialog({
                        resizable: true,
                        width: 800,
                        buttons: {
                            "<?php echo __('SAVE_DIALOG_CALENDAR','highchart'); ?>": function() { 
                                $( this ).dialog( "close" ); 

                                new_start=$.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
                                if(!event.end) {
                                    new_end=$.fullCalendar.formatDate(event.start, "yyyy-MM-dd 23:59:59");        
                                } else {
                                    new_end=$.fullCalendar.formatDate(event.end, "yyyy-MM-dd 23:59:59");        
                                }

                                var length = $('#select_title').children('option').length;
                                if(($("#select_title").prop('selectedIndex')+1)==length) {
                                    if($("#other_field_title").val()!= "") {
                                        var title=$("#other_field_title").val();
                                    } else {
                                        var title=$('#select_title option:selected').val(); 
                                    }
                                } else {
                                    var title=$('#select_title option:selected').val(); 
                                }

                                var color=$('#select_color').val();
                                var description=$('#select_remark').val();
                                if(!description) {
                                    description="";
                                }
                                if (title) {
                                    $('#calendar').fullCalendar('renderEvent', {
                                    title: title,
                                    start: new_start,
                                    end: new_end,
                                    description: description,
                                    color: color
                                });

                                if(!color) {
                                    color="#000000";
                                }

                                if($("#event_important").prop('checked') == true) {
                                    var important=1; 
                                } else {
                                    var important=0;
                                }

                               <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                    $.blockUI({ message: ''}); 
                               <?php } ?>

                                $.ajax({
                                    type: "POST",
                                    url: "http://localhost:6891/cultibox/main/modules/json-events/json-update-events.php",
                                    data: 'title='+encodeURIComponent(title)+'&start='+new_start+'&end='+new_end+'&desc='+encodeURIComponent(description)+'&id='+event.id+'&color='+color+'&card=<?php echo $sd_card; ?>'+'&important='+important
                                    <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                        ,complete: function() { $.unblockUI(); }
                                    <?php } ?>
                                }).done(function (data) {
                                    $.ajax({
                                            type: "POST",
                                            url: "http://localhost:6891/cultibox/main/modules/external/get_title_calendar_list.php",
                                            data: 'lang='+encodeURIComponent("<?php echo $lang; ?>")
                                        }).done(function (data) {
                                            if(data!="") {
                                                var myTitle=jQuery.parseJSON(data);
                                                $('#select_title').children().remove();
                                                $.each( myTitle, function( key, value ) {
                                                    $('#select_title').append(new Option(value, value, true, true)); 
                                                });
                                                $("#select_title").prop('selectedIndex', 0);  
                                                $("#other_title_div").css("display","none");
                                            }
                                        });
                                });
                                }
                                event.title=title;
                                event.description=description;
                                event.color=color;
                                $('#calendar').fullCalendar('removeEvents', event.id);
                                $('#calendar').fullCalendar('updateEvent', event);
                                $('#calendar').fullCalendar( 'refetchEvents' );
                                $('#calendar').fullCalendar( 'rerenderEvents' );
                                $("#select_remark").text("");
                                $("#select_remark").val("");
                                $("#other_field_title").text("");
                                $("#other_field_title").val("");
                                $("#select_title").prop('selectedIndex', 0);  
                                $("#event_important").attr('checked', false);
                                
                                delete event;
                                <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                    $.blockUI({ message: '', timeout: 1000 }); 
                                <?php } ?> 

                                return false;
                            },
                            "<?php echo __('CANCEL_DIALOG_CALENDAR','highchart'); ?>": function() {     
                                $( this ).dialog( "close" ); 
                                delete event; 
                                delete description; 
                                delete important;
                                $("#select_remark").text("");
                                $("#select_remark").val(""); 
                                $("#other_field_title").text("");
                                $("#other_field_title").val("");
                                $("#select_title").prop('selectedIndex', 0);
                                $("#event_important").attr('checked', false);
                                return false;
                            }
                        }
                        });
                    },
                    "<?php echo __('REMOVE_DIALOG_CALENDAR','highchart'); ?>": 
                    function() { 
                        $( this ).dialog( "close" ); 
                        <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                            $.blockUI({ message: ''}); 
                        <?php } ?>
                        $.ajax({
                            type: "POST",
                            url: "http://localhost:6891/cultibox/main/modules/json-events/json-remove-events.php",
                            data: 'id='+event.id+'&card=<?php echo $sd_card; ?>'
                            <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                ,complete: function() { $.unblockUI(); }
                            <?php } ?>
                        }).done(function (data) {
                            $.ajax({
                                type: "POST",
                                url: "http://localhost:6891/cultibox/main/modules/external/get_title_calendar_list.php",
                                data: 'lang='+encodeURIComponent("<?php echo $lang; ?>")
                                }).done(function (data) {
                                    if(data!="") {
                                        var myTitle = jQuery.parseJSON(data);
                                        $('#select_title').children().remove();
                                        $.each( myTitle, function( key, value ) {
                                            $('#select_title').append(new Option(value, value, true, true)); 
                                        });
                                        $("#select_title").prop('selectedIndex', 0);  
                                        $("#other_title_div").css("display","none");
                                    }
                                });
                        });

                        $('#calendar').fullCalendar('removeEvents', event.id);
                        $('#calendar').fullCalendar( 'refetchEvents' );
                        $('#calendar').fullCalendar( 'rerenderEvents' );
                        <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                            $.blockUI({ message: '', timeout: 1000 }); 
                        <?php } ?> 
                    },
                    "<?php echo __('CANCEL_DIALOG_CALENDAR','highchart'); ?>": function() { $( this ).dialog( "close" ); return false;}
                }
                });

                if(event.external) {
                    $(".ui-dialog-buttonpane button:contains('<?php echo __('EDIT_DIALOG_CALENDAR','highchart'); ?>')").attr("disabled", true).addClass("ui-state-disabled");
                    $(".ui-dialog-buttonpane button:contains('<?php echo __('REMOVE_DIALOG_CALENDAR','highchart'); ?>')").attr("disabled", true).addClass("ui-state-disabled");
                }

            },
            eventRender: function(event, eventElement) {
                var iconurl=null;
                
                if(event.icon) {
                   if(event.icon.search('http')>-1) {
                        iconurl=event.icon;
                    } else {
                        iconurl="http://localhost:6891/cultibox/main/modules/img/"+event.icon;
                    }

                    if (eventElement.find('span.fc-event-time').length) {
                        eventElement.find('span.fc-event-time').before("<img src='"+iconurl+"'  style='width:15px;height:15px'/>");
                    } else {
                        eventElement.find('span.fc-event-title').before("<img src='"+iconurl+"'  style='width:15px;height:15px'/>");
                    }
                }
                
                if(event.icon0) {
                   if(event.icon0.search('http')>-1) {
                        iconurl=event.icon0;
                    } else {
                        iconurl="http://localhost:6891/cultibox/main/modules/img/"+event.icon0;
                    }

                    if (eventElement.find('span.fc-event-time').length) {
                        eventElement.find('span.fc-event-time').before("<img src='"+iconurl+"'  style='width:15px;height:15px'/>");
                    } else {
                        eventElement.find('span.fc-event-title').before("<img src='"+iconurl+"'  style='width:15px;height:15px'/>");
                    }
                }
                
                if(event.icon1) {
                   if(event.icon1.search('http')>-1) {
                        iconurl=event.icon1;
                    } else {
                        iconurl="http://localhost:6891/cultibox/main/modules/img/"+event.icon1;
                    }

                    if (eventElement.find('span.fc-event-time').length) {
                        eventElement.find('span.fc-event-time').before("<img src='"+iconurl+"'  style='width:15px;height:15px'/>");
                    } else {
                        eventElement.find('span.fc-event-title').before("<img src='"+iconurl+"'  style='width:15px;height:15px'/>");
                    }
                }
                
                if(event.icon2) {
                   if(event.icon2.search('http')>-1) {
                        iconurl=event.icon2;
                    } else {
                        iconurl="http://localhost:6891/cultibox/main/modules/img/"+event.icon2;
                    }

                    if (eventElement.find('span.fc-event-time').length) {
                        eventElement.find('span.fc-event-time').before("<img src='"+iconurl+"'  style='width:15px;height:15px'/>");
                    } else {
                        eventElement.find('span.fc-event-title').before("<img src='"+iconurl+"'  style='width:15px;height:15px'/>");
                    }
                }
                
                if(event.icon3) {
                   if(event.icon3.search('http')>-1) {
                        iconurl=event.icon3;
                    } else {
                        iconurl="http://localhost:6891/cultibox/main/modules/img/"+event.icon3;
                    }

                    if (eventElement.find('span.fc-event-time').length) {
                        eventElement.find('span.fc-event-time').before("<img src='"+iconurl+"'  style='width:15px;height:15px'/>");
                    } else {
                        eventElement.find('span.fc-event-title').before("<img src='"+iconurl+"'  style='width:15px;height:15px'/>");
                    }
                }
                
                
            },

            loading: function(bool) {
                if (bool) $('#loading').show();
                else $('#loading').hide();
            }
            
        });
        
    });

</script>
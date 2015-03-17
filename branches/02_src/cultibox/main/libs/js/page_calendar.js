<script>

<?php 
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>

product_array = <?php echo json_encode($product) ?>;
title_msgbox = <?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;
count_program_index=<?php echo json_encode($count_program_index); ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;


$(function() {
   $("#calendar_startdate").datepicker({
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        showOn: "both",
        buttonImage: "main/libs/img/datepicker.png",
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
        buttonImage: "main/libs/img/calendar_icon.png",
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

    $("#datepicker_edit_start").datepicker({
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        showOn: "both",
        buttonImage: "main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
    }).val();

    $("#datepicker_edit_stop").datepicker({
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        showOn: "both",
        buttonImage: "main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
    }).val();
});

$(document).ready(function() {
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


    jQuery('#jquery-colour-picker-example select').colourPicker({
        ico:    'main/libs/img/jquery.colourPicker.gif',
        title:    false
   });

    if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", duration: 36000}
        });
    }


    $('#select_title').change(function () {
        var length = $('#select_title').children('option').length;
        if(($("#select_title").prop('selectedIndex')+1)==length) {
           $("#other_title_div").show();
        } else {
            $("#other_title_div").css("display","none");
        }
    });

    // Load XML available and there status for manage_external_xml dialog box
    $.ajax({
       cache: false,
       url: "main/modules/external/calendar_get_config_xml.php"
    }).done(function (data) {
    
        // Parse results from ajax
        if(data!="") {
            var objJSON = jQuery.parseJSON(data);

            // Delete already post elements
            $('#manage_external_xml input').children().remove();

            // Foreach XML
            $.each(objJSON, function( key, value ) {

                // Search if eleme is selected
                checked = "";
                if (value.activ == 1)
                    checked = "checked";
                
                // Add into UI
                $("#list_xml_files").append("<li>"+key + "<input type='checkbox' id='" + value.id + "' filename='" + value.name + "' name='xml_checkbox' " + checked + "></li>"); 
                
                // On click on the checkbutton
                $("#" + value.id).ready(function() {
                    $("#" + value.id).change(function(e) {
                        // Change filename directory
                        $.ajax({
                           cache: false,
                           data: {checked:$(this).is(':checked'), filename: $(this).attr('filename')},
                           url: "main/modules/external/update_config_xml.php"
                        }).done(function (data) {
                            // Reload events
                            $('#calendar').fullCalendar( 'refetchEvents' );
                        });
                    });
                });
            });
        }
    });

    // Display and control user form for XML selection do display
    $("#calendar_xml_selection").click(function(e) {
        e.preventDefault();
        
        $("#manage_external_xml").dialog({
            resizable: false,
            width: 600,
            modal: true,
            closeOnEscape: true,
            dialogClass: "popup_message",
            buttons: [{
                    text: CLOSE_button,
                    "id": "btnClose",
                    click: function () {
                        $( this ).dialog( "close" ); return false;
                    }
            }],
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
                            }
                        });
                        $.ajax({
                            cache: false,
                            url: "main/modules/external/delete_logs.php",
                            async: false,
                            data: {type:"calendar",type_reset:"all"}
                        }).done(function (data) {
                            $.ajax({
                                cache: false,
                                url: "main/modules/external/calendar_write_sd_events.php",
                                data: {sd_card:sd_card}
                            }).done(function (data) {
                                $.unblockUI();
                                $('#calendar').fullCalendar( 'refetchEvents' );
                                display_modal_ui("#valid_reset_calendar" , "popup_message");
                            });
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
        var error_nutri=false;

        $("#manage_nutrient_planification").dialog({
            resizable: false,
            width: 980,
            modal: true,
            closeOnEscape: true,
            dialogClass: "popup_message",
            buttons: [{
                text: CLOSE_button,
                "id": "btnClose",
                click: function () {
                    var d = new Date();
                    var month = d.getMonth()+1;
                    var day = d.getDate();

                    var output = d.getFullYear() + '-' +
                    (month<10 ? '0' : '') + month + '-' +
                    (day<10 ? '0' : '') + day;

                    $("#calendar_startdate").val(output);
                    error_nutri=false;
                    $("#error_calendar_startdate").css("display","none");
                    $( this ).dialog( "close" ); return false;
                }
            },{
            text: "<?php echo __('CREATE_CALENDAR_PROGRAM'); ?>",
            click: function () {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "main/modules/external/check_value.php",
                    data: {value:$("#calendar_startdate").val(),type:'date'}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_calendar_startdate").show(700);
                        var d = new Date();
                        var month = d.getMonth()+1;
                        var day = d.getDate();

                        var output = d.getFullYear() + '-' +
                        (month<10 ? '0' : '') + month + '-' +
                        (day<10 ? '0' : '') + day;

                        $("#calendar_startdate").val(output);
                        error_nutri=true;
                    } else {
                        $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/update_calendar_external.php",
                            data: {
                                substrat:$("#substrat_select").val(),
                                product:$("#nutrient_select").val(),
                                calendar_start:$("#calendar_startdate").val(),
                                sd_card: sd_card, 
                                event_name:$("#event_name").val(),
                                select_croissance:$('input[name=select_croissance]:checked', '#manage_nutrient_planification').val()
                            }
                        }).done(function (data) {
                            $('#calendar').fullCalendar( 'refetchEvents' );
                            if(data==1) {
                                display_modal_ui("#valid_create_calendar" , "popup_message");
                            } else {
                                display_modal_ui("#error_create_calendar" , "popup_error");
                            }

                            // Update list of title available
                            update_title_list();
                        });
                        error_nutri=false;
                  }
                });
                if(!error_nutri) {
                    $("#error_calendar_startdate").css("display","none"); 
                    $( this ).dialog( "close" ); return false;
                }
            }}],
        });
    });
        
    
    // If there are some important event, display it
    important_list = <?php echo json_encode($important_list) ?>;

    if(important_list.length > 0) {
        $.ajax({
           cache: false,
           url: "main/modules/external/get_variable.php",
           data: {name:"important"}
        }).done(function (data) {
            if(jQuery.parseJSON(data) != "True") {
                display_modal_ui("#dialog_calendar_important" , "popup_message");

                $.ajax({
                    cache: false,
                    url: "main/modules/external/set_variable.php",
                    data: {name:"important", value: "True", duration: 36000}
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

        if(count_program_index) {

            $("#manage_daily_program").dialog({
                resizable: false,
                width: 300,
                closeOnEscape: true,
                dialogClass: "popup_message",
                buttons: [{
                    text: CLOSE_button,
                    click: function () {
                        $( this ).dialog( "close" ); return false;
                    }
                }]
            });
        } else {
            $("#empty_daily_program").dialog({
                resizable: false,
                width: 400,
                modal: true,
                closeOnEscape: true,
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
            helper: 'clone',
            appendTo: '#main',
            cursor: 'move',
            zIndex: 9999,
        });
        
    });

});

// Function to check overlapping of two daily program
function daily_program_check_overlaping (event) {

    // Check if event is daily program
    if(!event.program_index) {
        return false;
    }


    // gets all event of the calendar
    var allevents = $('#calendar').fullCalendar('clientEvents');
   
    // For each event check if there is one with same date and that is a programm index
    for (index = 0; index < allevents.length; ++index) {
        // Check if it's a program_index
        if(allevents[index].program_index != null && allevents[index].program_index != "") {
        
            // Check if our event is ovelapping
            // (StartA <= EndB) and (EndA >= StartB)
            StartA = $.fullCalendar.formatDate(allevents[index].start, "yyyy-MM-dd");
            StartB = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd");
            EndA = $.fullCalendar.formatDate(allevents[index].end, "yyyy-MM-dd");
            EndB = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd");
            if (EndB == "")
                EndB = StartB;
            

            if ( (allevents[index].id != event.id) && (StartA <= EndB) && (EndA >= StartB) )
            {
                // To debug : val = allevents[index].title;
                return true;
            }
        }
    }

    return false;
}

// Function used to display a simple information or error dialog box
function display_modal_ui (ui_ID, ui_Class) {
    $(ui_ID).dialog({
        resizable: false,
        width: 450,
        modal: true,
        closeOnEscape: false,
        dialogClass: ui_Class,
        buttons: [{
        text: CLOSE_button,
        click: function () {
            $( this ).dialog( "close" );
            return false;
        }
        }]
    });
}

// Funct used to update calendar title list
function update_title_list () {
    $.ajax({
        type: "GET",
        url: "main/modules/external/get_title_calendar_list.php"
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

// Init program index array
<?php
$program_index = array();
program\get_program_index_info($program_index);
?>

// Full calendar
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
            $("#datepicker_edit_start").val($.fullCalendar.formatDate(start, "yyyy-MM-dd"));
            
            if(end) {
                $("#datepicker_edit_stop").val($.fullCalendar.formatDate(end, "yyyy-MM-dd"));
            } else {
                $("#datepicker_edit_stop").val($.fullCalendar.formatDate(start, "yyyy-MM-dd"));
            }

            $("#select_remark").val("");
            $("#select_color").val("#000000"); 
            $('#colour').css({'background-color' : '#000000'});


            $("#dialog_create").dialog({
                resizable: true,
                width: 800,
                modal: false,
                buttons: {
                    "<?php echo __('CREATE_DIALOG_CALENDAR','highchart'); ?>": function() { 
                        dateParts = $("#datepicker_edit_start").val().split('-');
                        date_start = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2]);
                        new_start=$.fullCalendar.formatDate(date_start, "yyyy-MM-dd 02:00:00");

                        dateParts = $("#datepicker_edit_stop").val().split('-');
                        date_end = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2]);
                        new_end=$.fullCalendar.formatDate(date_end, "yyyy-MM-dd 23:59:59");

                        if(date_end.getTime()<date_start.getTime()) {
                                        $("#error_start_interval").show();
                        } else {

                    
                        $( this ).dialog( "close" ); 
                        <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
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
                                }
                            });
                        <?php } ?>

                        // If user has actived daily programm
                        var userAddDailyProgram = false;
                        var selected_DailyProgram = "";
                        <?php
                            if(count($program_index) > 1) {
                        ?>
                            // If checkbox is selected
                            if ($('#create_dayly_program_in_ui').is(':checked') == true) 
                            {
                                userAddDailyProgram = true;
                                
                                // Memorise program
                                selected_DailyProgram = $("#select_daily_program_to_create option:selected").val(); 
                                
                            }
                        <?php 
                            }
                        ?>

                        // There is two cases
                        // - User add an classic event
                        // - User add a daily program
                        
                        // Classic event case :
                        if (userAddDailyProgram == false)
                        {
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
                                    external: 0
                                });

                                $.ajax({
                                    type: "GET",
                                    url: "main/modules/external/calendar_add_events.php",
                                    data: {
                                        title:title,
                                        start:new_start,
                                        end:new_end,
                                        desc:description,
                                        color:color,
                                        card:sd_card,
                                        important:important
                                    }
                                
                                    <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                        ,complete: function() { $.unblockUI(); }
                                    <?php } ?> 
                                }).done(function (data) {
                                    if(data==1) {
                                        // Update list of title available
                                        update_title_list();     
                                    } 
                                    
                                    $('#calendar').fullCalendar('refetchEvents');
                                    
                                });

                            }
                        
                        }
                        else
                        {

                            var tempEvent = {start:start, end:end, id:"3000", program_index: selected_DailyProgram};

                            // Check if there is an other programm on the same day
                            if (daily_program_check_overlaping(tempEvent)) {
                                // Display overlapping alert
                                display_modal_ui("#error_drop_event" , "popup_error");

                                //Release UI
                                <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                    $.unblockUI();
                                <?php } ?> 

                                //Exit function
                                return false;
                            }


                            // Compute date for ajax
                            ajaxDateStart = $.fullCalendar.formatDate( start, "yyyy-MM-dd 02:00:00");
                            ajaxDateEnd = $.fullCalendar.formatDate( end, "yyyy-MM-dd  23:59:59");
                            ajaxTitle = "<?php echo __('CALENDAR_DAILY_PROGRAM') ; ?>" + " " + $("#select_daily_program_to_create option:selected").text();

                            // Update datatbase in ajax
                            $.ajax({
                                cache: false,
                                url: "main/modules/external/update_calendar_external.php",
                                data: {
                                    daily_program_name: ajaxTitle,
                                    calendar_start: ajaxDateStart,
                                    calendar_end: ajaxDateEnd,
                                    sd_card: sd_card,
                                    program_index: selected_DailyProgram
                                }
                            }).done(function (data) {
                                $('#calendar').fullCalendar( 'refetchEvents' );
                                if(data==1) {
                                    display_modal_ui("#valid_create_calendar" , "popup_message");
                                } else {
                                    display_modal_ui("#error_create_calendar" , "popup_error");
                                }
                            });
                           
                           
                           
                            //Release UI
                            <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                $.unblockUI();
                            <?php } ?> 
                        
                        }
                        

                        $("#select_remark").text("");
                        $("#select_remark").val("");
                        $("#other_field_title").text("");
                        $("#other_field_title").val("");
                        $("#select_title").prop('selectedIndex', 0);  
                        $("#event_important").attr('checked', false);
                        $("#error_start_interval").css("display","none");

                        <?php
                            if(count($program_index) > 1) { 
                        ?>
                            // Unselect check box daily program
                            $("#create_dayly_program_in_ui").attr('checked', false);
                        <?php 
                            }
                        ?>
                        delete description;
                        delete important;
                        delete event;

                        $("#select_daily_program_to_create").prop('disabled', true);
                        $("#select_title, #select_remark, #event_important").prop('disabled', false);
                        return false;
                        }
                    },
                    "<?php echo __('CANCEL_DIALOG_CALENDAR','highchart'); ?>": function() {
                        $( this ).dialog( "close" ); 
                        $("#select_remark").text("");
                        $("#select_remark").val("");
                        $("#other_field_title").text("");
                        $("#other_field_title").val("");
                        $("#select_title").prop('selectedIndex', 0);  
                        $("#event_important").attr('checked', false);
                        $("#error_start_interval").css("display","none");
                        <?php
                            if(count($program_index) > 1) { 
                        ?>
                            // Unselect check box daily program
                            $("#create_dayly_program_in_ui").attr('checked', false);
                        <?php 
                            }
                        ?>
                        delete description;
                        delete important;
                        delete event;

                        $("#select_daily_program_to_create").prop('disabled', true);
                        $("#select_title, #select_remark, #event_important").prop('disabled', false);
                        return false;
                    }
                }
            });
        },
        events: "main/modules/external/calendar_get_events.php",
        drop: function(date, allDay) {
            // this function is called when something is dropped
            // retrieve the dropped element's stored Event Object
            var originalEventObject = $(this).data('eventObject');
            if (typeof originalEventObject != "undefined") {
                // we need to copy it, so that multiple events don't have a reference to the same object
                var copiedEventObject = $.extend({}, originalEventObject);

                // assign it the date that was reported
                var start = $.fullCalendar.formatDate(date, "yyyy-MM-dd 02:00:00");
                var end = $.fullCalendar.formatDate(date, "yyyy-MM-dd 23:59:59");
            
                copiedEventObject.allDay = allDay;
                copiedEventObject.title = "<?php echo __('CALENDAR_DAILY_PROGRAM') ; ?>" + " " + originalEventObject.title;
                copiedEventObject.description = copiedEventObject.title;
                copiedEventObject.id = 1000;
                copiedEventObject.start = date;
                copiedEventObject.end = date;

                // Check if there is an other programm on the same day
                if (daily_program_check_overlaping(copiedEventObject)) {
                    // Display overlapping alert
                    display_modal_ui("#error_drop_event" , "popup_error");
                
                    //Exit function
                    return "";
                }

                // Update datatbase in ajax
                $.ajax({
                    cache: false,
                    url: "main/modules/external/update_calendar_external.php",
                    data: {
                        daily_program_name: copiedEventObject.title,
                        calendar_start: start,
                        calendar_end: end,
                        sd_card: sd_card,
                        program_index: copiedEventObject.program_index
                    }
                }).done(function (data) {
                    $('#calendar').fullCalendar( 'refetchEvents' );
                    if(data==1) {
                        display_modal_ui("#valid_create_calendar" , "popup_message");
                    } else {
                        display_modal_ui("#error_create_calendar" , "popup_error");
                    }
                });
            }
        },
        eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
        
            // If the event is external (ie : calendar lunar, hour changing ...)
            if(event.external) {
                // Undo drop
                revertFunc();
                return false;
            }

            // Check if there is an other programm on the same day
            if (daily_program_check_overlaping(event)) {
                // Display overlapping alert
                display_modal_ui("#error_drop_event" , "popup_error");
                
                // Undo drop
                revertFunc();
                
                //Exit function
                return false;
            }
            
            new_start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
            if(!event.end) {
                event.end=event.start
            }

            new_end=$.fullCalendar.formatDate(event.end, "yyyy-MM-dd 23:59:59");

            <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
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
                    }
                });
            <?php } ?> 
            $.ajax({
                type: "GET",
                url: "main/modules/external/calendar_update_events.php",
                data: {
                    title:event.title,
                    start:new_start,
                    end:new_end,
                    id:event.id,
                    color:event.color,
                    card:sd_card,
                    important:event.important,
                    desc:event.description,
               }
                 <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                    ,complete: function() { $.unblockUI(); }
                 <?php } ?> 
            });
        },
        eventResize: function(event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view) { 
            if(event.external) {
                // If event come from an external XML, undo resize
                revertFunc();
                
                return false;
            }
            
            // Check if there is an other programm on the same day
            if (daily_program_check_overlaping(event)) {
                // Display overlapping alert
                display_modal_ui("#error_drop_event" , "popup_error");
                
                // Undo drop
                revertFunc();
                
                //Exit function
                return false;
            }
            
            new_start=$.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
            if(!event.end) {
                event.end=event.start
            }

            new_end=$.fullCalendar.formatDate(event.end, "yyyy-MM-dd 23:59:59");

            <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
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
                     }
                });
            <?php } ?>
            $.ajax({
                type: "GET",
                url: "main/modules/external/calendar_update_events.php",
                data: {
                    title:event.title,
                    start:new_start,
                    end:new_end,
                    id:event.id,
                    color:event.color,
                    card:sd_card,
                    important:event.important,
                    desc:event.description,
               }
                <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                  ,complete: function() { $.unblockUI(); }
                <?php } ?>
            });
        },
        eventClick: function(event, element) {
            var date_ref = new Date();
            var date_ref_formated = date_ref.getFullYear() +'-'+ addZ(date_ref.getMonth()+1) +'-'+ addZ(date_ref.getDate());
            var date_ref_parts=date_ref_formated.split('-');
            date_ref=new Date(date_ref_parts[0], parseInt(date_ref_parts[1], 10) - 1, date_ref_parts[2]).getTime();

            $("#edit_duration_started_title").css("display","none");
            $("#edit_duration_start_title").css("display","none");
            $("#edit_duration_ended_title").css("display","none");
            $("#edit_duration_end_title").css("display","none");

            $("#edit_duration_start").text("");
            $("#edit_duration_end").text("");
            
        
            // Set UI informations from event properties
            $("#title").text(event.title);
            $("#start_date").text($.fullCalendar.formatDate(event.start, "yyyy-MM-dd"));


            var date_start=$.fullCalendar.formatDate(event.start, "yyyy-MM-dd");
            dateParts = date_start.split('-');
            date_start = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2]).getTime();

            var duration_start=((date_start-date_ref)/86400000);

            if(event.end) {
                $("#stop_date").text($.fullCalendar.formatDate(event.end, "yyyy-MM-dd"));
                var date_end=$.fullCalendar.formatDate(event.end, "yyyy-MM-dd");
                dateParts = date_end.split('-');
                date_end = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2]).getTime();

                var duration_end=((date_end-date_ref)/86400000);

            }else {
                $("#stop_date").text($.fullCalendar.formatDate(event.start, "yyyy-MM-dd"));
                var duration_end=duration_start;
            }


            if(duration_start<0) {
                $("#edit_duration_started_title").show();
                if(duration_start==-1) {
                    $("#edit_duration_start").text("1 <?php echo __('DAY_DURATION'); ?>");
                } else {
                    $("#edit_duration_start").text(Math.abs(duration_start)+" <?php echo __('DAYS_DURATION'); ?>");
                }
            } else {
                $("#edit_duration_start_title").show();
                if(duration_start==1) {
                    $("#edit_duration_start").text("1 <?php echo __('DAY_DURATION'); ?>");
                } else {
                    $("#edit_duration_start").text(duration_start+" <?php echo __('DAYS_DURATION'); ?>");
                }
            }


            if(duration_end<0) {
                $("#edit_duration_ended_title").show();
                if(duration_end==-1) {
                    $("#edit_duration_end").text("1 <?php echo __('DAY_DURATION'); ?>");
                } else {
                    $("#edit_duration_end").text(Math.abs(duration_end)+" <?php echo __('DAYS_DURATION'); ?>");
                }
            } else {
                $("#edit_duration_end_title").show();
                if(duration_end==1) {
                    $("#edit_duration_end").text("1 <?php echo __('DAY_DURATION'); ?>");
                } else {
                    $("#edit_duration_end").text(duration_end+" <?php echo __('DAYS_DURATION'); ?>");
                }
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

            // If it's not a daily program
            if (typeof event.program_index === "undefined" || event.program_index == "" || event.program_index == null)
            {

                $("#dialog_edit").dialog({
                    resizable: true,
                    width: 800,
                    modal: true,
                    buttons: {
                        "<?php echo __('EDIT_DIALOG_CALENDAR','highchart'); ?>": function() { 
                            $( this ).dialog( "close" ); 


                            $('#select_title option[value="'+event.title+'"]').prop('selected', true);
                            $("#datepicker_edit_start").val($.fullCalendar.formatDate(event.start, "yyyy-MM-dd"));

                            if(event.end) {
                                $("#datepicker_edit_stop").val($.fullCalendar.formatDate(event.end, "yyyy-MM-dd"));
                            }else {
                                 $("#datepicker_edit_stop").val($.fullCalendar.formatDate(event.start, "yyyy-MM-dd"));
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
                            
                // Hide daily program row
                $('#daily_program_row_in_ui').hide();
                
                            $("#dialog_create").dialog({
                            resizable: true,
                            width: 800,
                            buttons: {
                                "<?php echo __('SAVE_DIALOG_CALENDAR','highchart'); ?>": function() { 
                                    dateParts = $("#datepicker_edit_start").val().split('-');
                                    date_start = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2]);

                                    new_start=$.fullCalendar.formatDate(date_start, "yyyy-MM-dd HH:mm:ss");
                                
                                    if(!$("#datepicker_edit_stop").val()) {
                                        dateParts = $("#datepicker_edit_start").val().split('-');
                                        date_end = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2]);
                                    } else {
                                        dateParts = $("#datepicker_edit_stop").val().split('-');
                                        date_end = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2]);
                                    }
                                    new_end=$.fullCalendar.formatDate(date_end, "yyyy-MM-dd 23:59:59");

                                   
                                    if(date_end.getTime()<date_start.getTime()) {
                                        $("#error_start_interval").show();
                                    } else { 
                                    $( this ).dialog( "close" );
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
                                                }
                                            });
                                       <?php } ?>

                                        $.ajax({
                                            type: "GET",
                                            url: "main/modules/external/calendar_update_events.php",
                                            data: {
                                                title:title,
                                                start:new_start,
                                                end:new_end,
                                                id:event.id,
                                                color:color,
                                                card:sd_card,
                                                important:important,
                                                desc:description,
                                            }
                                            <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                                ,complete: function() { $.unblockUI(); }
                                            <?php } ?>
                                        }).done(function (data) {
                                            // Update list of title available
                                            update_title_list();
                                        });
                                    }
                                    event.title=title;
                                    event.description=description;
                                    event.color=color;
                                    event.start=new_start;
                                    event.end=new_end;
                                    $('#calendar').fullCalendar('removeEvents', event.id);
                                    $('#calendar').fullCalendar('updateEvent', event);
                                    $('#calendar').fullCalendar( 'refetchEvents' );
                                    $("#select_remark").text("");
                                    $("#select_remark").val("");
                                    $("#other_field_title").text("");
                                    $("#other_field_title").val("");
                                    $("#select_title").prop('selectedIndex', 0);  
                                    $("#event_important").attr('checked', false);
                                    $("#error_start_interval").css("display","none");
                                    <?php
                                        if(count($program_index) > 1) { 
                                    ?>
                                        // Unselect check box daily program
                                        $("#create_dayly_program_in_ui").attr('checked', false);
                                    <?php 
                                        }
                                    ?>
                                    //Release UI
                                    <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                        $.blockUI({
                                            message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
                                            centerY: 0,
                                            timeout: 1000,
                                            css: {
                                                top: '20%',
                                                border: 'none',
                                                padding: '5px',
                                                backgroundColor: 'grey',
                                                '-webkit-border-radius': '10px',
                                                '-moz-border-radius': '10px',
                                                opacity: .9,
                                                color: '#fffff'
                                            }
                                        });
                                    <?php } ?> 

                                    return false;

                                    }

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
                                    $("#error_start_interval").css("display","none");
                                    <?php
                                        if(count($program_index) > 1) { 
                                    ?>
                                        // Unselect check box daily program
                                        $("#create_dayly_program_in_ui").attr('checked', false);
                                    <?php 
                                        }
                                    ?>
                                    return false;
                                }
                            }
                            });
                        },
                        "<?php echo __('REMOVE_DIALOG_CALENDAR','highchart'); ?>": 
                        function() { 
                            $( this ).dialog( "close" ); 
                            <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
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
                                }
                            });
                            <?php } ?>
                            
                            $('#calendar').fullCalendar('removeEvents', event.id);
                            
                            $.ajax({
                                type: "GET",
                                url: "main/modules/external/calendar_remove_events.php",
                                data: {
                                    id:event.id,
                                    card:sd_card
                                }
                            }).done(function (data) {
                                // Update list of title available
                                update_title_list();
                                
                                <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                    $.unblockUI();
                                <?php } ?>

                                $('#calendar').fullCalendar( 'refetchEvents' );
                                
                            });
                        },
                        "<?php echo __('CANCEL_DIALOG_CALENDAR','highchart'); ?>": function() {
                            $( this ).dialog( "close" ); return false;
                        }
                    }
                });
            
                // Show daily program row
                $('#daily_program_row_in_ui').show();
            
            } else {
            
                // Daily program case
                $("#dialog_edit").dialog({
                    resizable: true,
                    width: 800,
                    modal: false,
                    buttons: {
                        "<?php echo __('REMOVE_DIALOG_CALENDAR','highchart'); ?>": 
                        function() { 
                            $( this ).dialog( "close" ); 
                            <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
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
                                }
                            });
                            <?php } ?>
                            
                            $('#calendar').fullCalendar('removeEvents', event.id);
                            
                            $.ajax({
                                type: "GET",
                                url: "main/modules/external/calendar_remove_events.php",
                                data:  {
                                    id:event.id,
                                    card:sd_card
                                }
                            }).done(function (data) {
                                // Update list of title available
                                update_title_list();

                                <?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
                                    $.unblockUI();
                                <?php } ?>

                                $('#calendar').fullCalendar( 'refetchEvents' );
                                
                            });
                        },
                        "<?php echo __('CANCEL_DIALOG_CALENDAR','highchart'); ?>": function() {
                            $( this ).dialog( "close" ); return false;
                        }
                    }
                });
            }
            
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
                    iconurl="main/modules/img/"+event.icon;
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
                    iconurl="main/modules/img/"+event.icon0;
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
                    iconurl="main/modules/img/"+event.icon1;
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
                    iconurl="main/modules/img/"+event.icon2;
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
                    iconurl="main/modules/img/"+event.icon3;
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

<?php if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) { ?>
$(document).ready(function() {
    // Rewrite on SD cards every events (and plgXX programm)
    <?php if(isset($sd_card) && !empty($sd_card)) { ?>
    
        // Add message to prevent user
        pop_up_add_information('<?php echo __('CALENDAR_UPDATE_SD_EVENT') ;?> <img src=\"main/libs/img/waiting_small.gif\" />', "update_calendar_progress", "information");
    
        $.ajax({
           cache: false,
           data: {sd_card:sd_card},
           url: "main/modules/external/calendar_write_sd_events.php"
        }).done(function (data) {
            pop_up_remove("update_calendar_progress");
        });
    <?php } ?>
});
<?php } ?>

// When user click on daily program checkbutton in create dialog UI
$(document).ready(function() {

    $("#create_dayly_program_in_ui").click(function(e) {
        
        // If Checked
        if ($('#create_dayly_program_in_ui').is(':checked') == true) 
        {
            // Activ selection of daily programm
            $("#select_daily_program_to_create").prop('disabled', false);
            // Unactiv other options
            $("#select_title, #select_remark, #event_important").prop('disabled', true);
        }
        else
        {
            // Not checked
            // Activ selection of daily programm
            $("#select_daily_program_to_create").prop('disabled', true);
            // Activ other options
            $("#select_title, #select_remark, #event_important").prop('disabled', false);
        }
        
            
    });
});


</script>

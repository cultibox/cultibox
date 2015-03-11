<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>

title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;

// GLobal var for slidder
var syno_configure_element_object = {
    scaleImageId:"",
    scale:1,
    zindexImageId:"",
    z:1,
    element:"",
    rotation:"0",
    image:""
};
var syno_configure_element_object_old = {
    scaleImageId:"",
    scale:1,
    zindexImageId:"",
    z:1,
    element:"",
    rotation:"0",
    image:""
};
var idOfElem = "";

var absolut_X_position = "";
var absolut_Y_position = "";

$(document).ready(function(){
     pop_up_remove("main_error");
     pop_up_remove("main_info");

     $.ajax({
          cache: false,
          async: true,
          url: "main/modules/external/get_soft_version.php"
     }).done(function (data) {
         var objJSON = jQuery.parseJSON(data);
        
        var version="<p class='p_center'><b><i><?php echo __('CULTIPI_SOFT_VERSION'); ?>:</i></b></p><br /><?php echo __('CULTIPI_SOFT'); ?>:  <b>"+objJSON[0]+"</b><br /><?php echo __('CULTIBOX_SOFT'); ?>:  <b>"+objJSON[1]+"</b><br /><?php echo __('CULTIRAZ_SOFT'); ?>:  <b>"+objJSON[2]+"</b><br /><?php echo __('CULTITIME_SOFT'); ?>:  <b>"+objJSON[3]+"</b><br /><?php echo __('CULTICONF_SOFT'); ?>:  <b>"+objJSON[4]+"</b>";

        $('#version_soft').attr('title', version);
     });

    $.ajax({
          cache: false,
          async: true,
          url: "main/modules/external/services_status.php", 
          data: {action:"status_cultipi"}
     }).done(function (data) {
         var objJSON = jQuery.parseJSON(data);

         if(objJSON=="0") {
            $("#cultipi_on").show();
            $("#cultipi_off").css('display','none');
         } else {
            $("#cultipi_off").show();
            $("#cultipi_on").css('display','none');
         }
     });


    
    // For each information, show it
    $.each(main_error, function(key, entry) {
            pop_up_add_information(entry,"main_error","error");
    });

    // For each information, show it
    $.each(main_info, function(key, entry) {
            pop_up_add_information(entry,"main_info","information");
    });



     // Gestion of drag and drop
    $( "#set div" ).draggable({
        distance: 10,
        grid: [ 10, 10 ],
        containment : '#set',
        cursor: "move",
        stop:function(event, ui) {
            $.ajax({
                cache: false,
                type: "POST",
                data: {
                    elem:$(this).attr('id').split("_")[2],
                    x:(parseInt($(this).position().left) / 10) * 10,
                    y:(parseInt($(this).position().top) / 10) * 10,
                    action:"updatePosition"
                },
                url: "main/modules/external/synoptic.php",
                success: function (data) {
                }, error: function(data) {
                }
            });
            absolut_X_position = "";
            absolut_Y_position = "";
        }
    });
    
    // Drag and drop from extern modal UI
    $( "#syno_add_element_ui div" ).draggable({
        revert: false,
        helper: 'clone',
        appendTo: '#set',
        cursor: 'move',
        zIndex: 9999,
        stop:function(event, ui) {
        
            if (absolut_X_position != "" && absolut_Y_position != "")
            {

                $.ajax({
                    cache: false,
                    type: "POST",
                    data: {
                        image:$(this).attr('id'),
                        x:(parseInt(absolut_X_position) / 10) * 10,
                        y:(parseInt(absolut_Y_position) / 10) * 10,
                        action:"addElementOther"
                    },
                    url: "main/modules/external/synoptic.php",
                    success: function (data) {

                    // Add element from database
                    if(data != "") {

                        var objJSON = jQuery.parseJSON(data);
                        
                        // Create the div
                        $("#set").append('<div id="syno_elem_' + objJSON.id + '" class="" style="position:absolute; top:' + objJSON.y + 'px ; left:' + objJSON.x + 'px ;z-index:' + objJSON.z + '" ></div>');
                        
                        var inTable = '<table>';
                        inTable = inTable +  '    <tr>';
                        inTable = inTable +  '    <td id="syno_elem_title" ></td>';
                        inTable = inTable +  '      <td>';
                        inTable = inTable +  '          <input type="image" id="syno_elemConfigur_' + objJSON.id + '" name="syno_elemConfigur_' + objJSON.id + '"';
                        inTable = inTable +  '                 title=""';
                        inTable = inTable +  '                 src="main/libs/img/advancedsettings.png"  width="22"';
                        inTable = inTable +  '                 alt="configure"';
                        inTable = inTable +  '                 class="syno_conf_elem_button"';
                        inTable = inTable +  '          />';
                        inTable = inTable +  '      </td>';
                        inTable = inTable +  '    </tr>';
                        inTable = inTable +  '    <tr>';
                        inTable = inTable +  '    </tr>';
                        inTable = inTable +  '  </table>';
                
                        $("#syno_elem_" + objJSON.id).append(inTable);
                        $("#syno_elem_" + objJSON.id).append('<img id="syno_elemImage_' + objJSON.id + '" src="main/libs/img/images-synoptic-other/' + objJSON.image + '" alt="capteur" style="width:' + objJSON.scale + 'px;cursor: move" class="rotate' + objJSON.rotation + '" >');
                    
                    
                        $( "#syno_elem_" + objJSON.id ).draggable({
                            distance: 10,
                            grid: [ 10, 10 ],
                            containment : '#set',
                            cursor: "move",
                            stop:function(event, ui) {
                                $.ajax({
                                    cache: false,
                                    type: "POST",
                                    data: {
                                        elem:$(this).attr('id').split("_")[2],
                                        x:(parseInt($(this).position().left) / 10) * 10,
                                        y:(parseInt($(this).position().top) / 10) * 10,
                                        action:"updatePosition"
                                    },
                                    url: "main/modules/external/synoptic.php",
                                    success: function (data) {
                                    }, error: function(data) {
                                    }
                                });

                                absolut_X_position = "";
                                absolut_Y_position = "";
                            }
                        });
                    
                    
                    }
                    }, error: function(data) {
                    }
                });
                absolut_X_position = "";
                absolut_Y_position = "";
            }
        
        }
    });
    
    
    $('#set').droppable({
        drop : function(event, ui){
            // Save X and T position of drop
            absolut_X_position = ui.position.left;
            absolut_Y_position = ui.position.top;
        }
    });
    
    // Display and control user form to add an element
    $("#syno_add_element").click(function(e) {
        e.preventDefault();
        $("#syno_add_element_ui").dialog({
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
    });

    //Reset network config:
    $("#reset_network_img").click(function(e) {
        e.preventDefault();
        $("#confirm_reset_network").dialog({
            resizable: false,
            width: 800,
            modal: true,
            closeOnEscape: false,
            dialogClass: "dialog_cultibox",
            buttons: [{
                text: OK_button,
                click: function () {
                    $( this ).dialog( "close" );
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
                                async: true,
                                timeout: 30000,
                                url: "main/modules/external/reset_network.php"
                            }).done(function (data) {
                                $.unblockUI();
                                $("#network_available").dialog({
                                    resizable: false,
                                    width: 500,
                                    modal: true,

                                    dialogClass: "popup_message",
                                    buttons: [{
                                        text: CLOSE_button,
                                        click: function () {
                                            $( this ).dialog( "close" ); return false;
                                        }
                                    }]
                                });
                            }).fail(function (data) {
                                $.unblockUI();
                                $("#network_available").dialog({
                                    resizable: false,
                                    width: 500,
                                    modal: true,
                                    closeOnEscape: true,
                                    dialogClass: "popup_message",
                                    buttons: [{
                                            text: CLOSE_button,
                                            click: function () {
                                            $( this ).dialog( "close" ); return false;
                                    }
                                    }]
                                });
                            });
                        }});
                }
            },{
                text: CLOSE_button,
                click: function () {
                    $( this ).dialog( "close" ); return false;
                }
            }]
        });
   });



    //Change password:
    $("#change_password").click(function(e) {
        e.preventDefault();
        $("#dialog_change_password").dialog({
            resizable: false,
            width: 800,
            modal: true,
            closeOnEscape: false,
            dialogClass: "popup_message",
            buttons: [{
                text: CLOSE_button,
                click: function () {
                    $("#error_same_password").css("display","none");
                    $("#error_empty_password").css("display","none");
                    $("#new_password").val("");
                    $("#confirm_new_password").val("");
                    $( this ).dialog( "close" ); return false;
                }
            }]
        });
    });

    $("#save_password").click(function(e) {
          e.preventDefault();
          $("#error_same_password").css("display","none");
          $("#error_empty_password").css("display","none");

          if($("#new_password").val()=="") {
            $("#error_empty_password").show();
          } else {
            $.ajax({
              cache: false,
              async: false,
              url: "main/modules/external/check_value.php",
              data: {value:$("#new_password").val(),value2:$("#confirm_new_password").val(),type:"password"}
            }).done(function (data) {
              if(data!=1)  {
                  $("#error_same_password").show();
              } else {
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
                            url: "main/modules/external/reset_password.php",
                            data: {pwd:$("#new_password").val()},
                            success: function (data) {
                                $.unblockUI();
                                var objJSON = jQuery.parseJSON(data);

                                if(objJSON=="1") {
                                    $("#success_save_password").dialog({
                                        resizable: false,
                                        width: 800,
                                        modal: true,
                                        closeOnEscape: false,
                                        dialogClass: "popup_message",
                                        buttons: [{
                                            text: CLOSE_button,
                                            click: function () {
                                                $("#error_same_password").css("display","none");
                                                $("#error_empty_password").css("display","none");
                                                $("#new_password").val("");
                                                $("#confirm_new_password").val("");
                                                $( this ).dialog( "close" ); return false;
                                            }
                                        }]
                                    });
                                } else {
                                    $("#error_save_password").dialog({
                                        resizable: false,
                                        width: 800,
                                        modal: true,
                                        closeOnEscape: false,
                                        dialogClass: "popup_error",
                                        buttons: [{
                                            text: CLOSE_button,
                                            click: function () {
                                                $("#error_same_password").css("display","none");
                                                $("#error_empty_password").css("display","none");
                                                $("#new_password").val("");
                                                $("#confirm_new_password").val("");
                                                $( this ).dialog( "close" ); return false;
                                            }
                                        }]
                                    });
                                }
                            }, error: function(data) {
                                $("#error_save_password").dialog({
                                    resizable: false,
                                    width: 800,
                                    modal: true,
                                    closeOnEscape: false,
                                    dialogClass: "popup_error",
                                    buttons: [{
                                        text: CLOSE_button,
                                        click: function () {
                                            $("#error_same_password").css("display","none");
                                            $("#error_empty_password").css("display","none");
                                            $("#new_password").val("");
                                            $("#confirm_new_password").val("");
                                            $( this ).dialog( "close" ); return false;
                                        }
                                    }]
                                });
                                $.unblockUI();
                            }
                        });
                    } });
              }
            });
          }
    });
    
    // Slider for zoom
    $("#syno_configure_element_scale").slider({
        max: 1000,
        min: 50,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_scale_val").val(ui.value);
            $('#' + syno_configure_element_object.scaleImageId).width(ui.value);
        },
        step: 1,
        value: syno_configure_element_object.scale
    });
    
    // Slider for zindex
    $("#syno_configure_element_zindex").slider({
        max: 200,
        min: 1,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_zindex_val").val(ui.value);
            $('#' + syno_configure_element_object.zindexImageId).zIndex(ui.value);
        },
        step: 1,
        value: syno_configure_element_object.z
    });
    
    // Rotation
    $( 'input[name="syno_configure_element_rotate"]:radio' ).change(
        function(){
            // retrieve the class
            var className = $('#' + syno_configure_element_object.scaleImageId).attr('class');
            $('#' + syno_configure_element_object.scaleImageId).removeClass(className);
            var newClass = $('input[name=syno_configure_element_rotate]:checked').val();
            $('#' + syno_configure_element_object.scaleImageId).addClass("rotate" + newClass);
        }
    );
    
    // Image
    $('#syno_configure_element_image_other, #syno_configure_element_image_plug, #syno_configure_element_image_sensor').on('change', function() {
        try {
            $('#syno_elemImage_' + idOfElem).attr('src', 'main/libs/img/images-synoptic-' + syno_configure_element_object.element + '/' + this.value);
        } catch (e) {
            alert(e.message);
        }
    });
    
    // Display and control user form for configuring item
    $('body').on('click', '.syno_conf_elem_button', function(e) {
        e.preventDefault();
        
        idOfElem = $(this).attr('id').split("_")[2];
        
        // retriev name of this element
        elementTitle = $("#syno_elem_title_" + idOfElem).html();
        
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                id:idOfElem,
                action:"getParam"
            },
            url: "main/modules/external/synoptic.php",
            success: function (data) {
            
            if(data != "") {

                syno_configure_element_object = jQuery.parseJSON(data);

                // Parse if needed
                syno_configure_element_object.scale = parseInt(syno_configure_element_object.scale)
                syno_configure_element_object.z     = parseInt(syno_configure_element_object.z)
                
                // Add some elements to the object
                syno_configure_element_object.scaleImageId  = "syno_elemImage_" + idOfElem ;
                syno_configure_element_object.zindexImageId = "syno_elem_" + idOfElem ;
                
                // Save it
                syno_configure_element_object_old = syno_configure_element_object;
                
                // Update style of each configure element
                $("#syno_configure_element_rotate_0" ).prop("checked", false);
                $("#syno_configure_element_rotate_90" ).prop("checked", false);
                $("#syno_configure_element_rotate_180" ).prop("checked", false);
                $("#syno_configure_element_rotate_270" ).prop("checked", false);
                $("#syno_configure_element_rotate_" + syno_configure_element_object.rotation ).prop("checked", true);
                
                $("#syno_configure_element_scale_val").val(syno_configure_element_object.scale);
                $("#syno_configure_element_scale").slider("value",syno_configure_element_object.scale);
                
                $("#syno_configure_element_zindex_val").val(syno_configure_element_object.z);
                $("#syno_configure_element_zindex").slider("value",syno_configure_element_object.z);

                $('#syno_configure_element_image_' + syno_configure_element_object.element + ' option[value="' + syno_configure_element_object.image + '"]').prop('selected', true);

                // Select correct image option
                $("#syno_configure_element_image_other").hide();
                $("#syno_configure_element_image_plug").hide();
                $("#syno_configure_element_image_sensor").hide();
                $("#syno_configure_element_image_" + syno_configure_element_object.element).show();
                


                $("#syno_configure_element").dialog({
                    resizable: false,
                    width: 400,
                    closeOnEscape: true,
                    dialogClass: "popup_message",
                    title:"Configurer " + elementTitle,
                    open: function( event, ui ) {
                        // remove delete button for plugs and sensor
                        if (syno_configure_element_object.element == "other") {
                            $("#DELETE_button").show();
                        } else {
                            $("#DELETE_button").hide();
                        }
                    },
                    buttons: [{
                        id: "DELETE_button",
                        text: DELETE_button,
                        click: function () {
                        
                            $.ajax({
                                cache: false,
                                type: "POST",
                                data: {
                                    id:idOfElem,
                                    action:"deleteElement"
                                },
                                url: "main/modules/external/synoptic.php"
                            }).done(function (data) {
                            });
                            
                            // Delete it
                            $( "#syno_elem_" + idOfElem ).remove();
                            
                            $( this ).dialog( "close" );
                            return false;
                        }
                    } , {
                        text: CANCEL_button,
                        click: function () {
                        
                            // Roll back object value
                            
                            // Image
                            $('#syno_elemImage_' + idOfElem).attr('src', 'main/libs/img/images-synoptic-' + syno_configure_element_object_old.element + '/' + syno_configure_element_object_old.image);
                        
                            //scale
                            $('#' + syno_configure_element_object.scaleImageId).width(syno_configure_element_object_old.scale);
                            
                            //z
                            $('#' + syno_configure_element_object_old.zindexImageId).zIndex(syno_configure_element_object_old.z);
                            
                            //rotation
                            // retrieve the class
                            var className = $('#' + syno_configure_element_object_old.scaleImageId).attr('class');
                            $('#' + syno_configure_element_object_old.scaleImageId).removeClass(className);
                            var newClass = syno_configure_element_object_old.rotation;
                            $('#' + syno_configure_element_object_old.scaleImageId).addClass("rotate" + newClass);
                        
                            $( this ).dialog( "close" );
                            return false;
                        }
                    }, {
                        text: SAVE_button,
                        click: function () {
                        
                            $.ajax({
                                cache: false,
                                type: "POST",
                                data: {
                                    id:idOfElem,
                                    z:$("#syno_configure_element_zindex_val").val(),
                                    scale:$("#syno_configure_element_scale_val").val(),
                                    image:$( "#syno_configure_element_image_" + syno_configure_element_object.element + " option:selected" ).val(),
                                    rotation:$('input[name=syno_configure_element_rotate]:checked').val(),
                                    action:"updateZScaleImageRotation"
                                },
                                url: "main/modules/external/synoptic.php"
                            }).done(function (data) {
                            
                            });
                            $( this ).dialog( "close" );
                            return false;
                        }
                    }]
                });
                
            }
            }, error: function(data) {
            }
        });
    });
    
    // Display and control user form for pilot plug
    $('body').on('click', '.syno_pilot_plug_elem_button', function(e) {
        e.preventDefault();

        idOfElem = $(this).attr('id').split("_")[2];

        // retriev name of this element
        elementTitle = $("#syno_elem_title_" + idOfElem).html();
        
        $("#syno_pilotPlug_element").dialog({
            resizable: false,
            width: 400,
            closeOnEscape: true,
            dialogClass: "popup_message",
            title:"Piloter " + elementTitle,
            buttons: [{
                text: CLOSE_button,
                click: function () {
                    $( this ).dialog( "close" );
                    return false;
                }
            }]
        });
    });
    
    // Function used to pilot a plug
    $("#syno_configure_element_force_plug_pilot").click(function(){
    
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                action:"forcePlug",
                id:idOfElem,
                value:$( "#syno_configure_element_force_plug_value option:selected" ).val(),
                time:$( "#syno_configure_element_force_plug_time option:selected" ).val()
            },
            url: "main/modules/external/synoptic.php",
            success: function (data) {
        
            // Change text
            if ($( "#syno_configure_element_force_plug_value option:selected" ).val() == "on") {
                $('#syno_elemImage_' + idOfElem).attr('title',"<?php echo __('VALUE_ON'); ?>");
            } else {
                $('#syno_elemImage_' + idOfElem).attr('title',"<?php echo __('VALUE_OFF'); ?>");
            }

            // Change image
            if ($( "#syno_configure_element_force_plug_value option:selected" ).val() == "on") {
                $('#syno_elemImage_' + idOfElem ).attr('src',$('#syno_elemImage_' + idOfElem ).attr('src').replace("_OFF", "_ON"));
            } else  {
                $('#syno_elemImage_' + idOfElem ).attr('src',$('#syno_elemImage_' + idOfElem ).attr('src').replace("_ON", "_OFF"));
            }
            
            // Change opacity
            $('#syno_elemImage_' + idOfElem ).css("opacity", "1");


            }, error: function(data) {
            }
        });
    });

    function baseName(str)
    {
       var base = new String(str).substring(str.lastIndexOf('/') + 1); 
       return base;
    }
    
    var updateIsAked = 0;
    // Loop for updating sensors
    function updateSensors() {
    
        if (updateIsAked == 1) {
            return 0;
        }
        updateIsAked = 1;
        $.ajax({
            beforeSend: function(jqXHR) {
                $.xhrPool.push(jqXHR);
            },
            complete: function(jqXHR) {
                var index = $.xhrPool.indexOf(jqXHR);
                if (index > -1) {
                    $.xhrPool.splice(index, 1);
                }
            },
            cache: false,
            type: "POST",
            data: {
                action:"getAllSensorLiveValue"
            },
            url: "main/modules/external/synoptic.php",
            success: function (data) {

            var objJSON = jQuery.parseJSON(data);

            if (objJSON.error == "") {
            
                $.each( objJSON, function( key, value ) {
                    if (key != "error") {
                    
                        // Change text and opacity
                        if (value != "DEFCOM" && value != "TIMEOUT" ) {
                            newBaseName = baseName($('img[name="syno_elemSensorImage_' + key + '"]').attr('src'));
                            var valueSplitted = value.split(" "); 
                            switch(newBaseName) {
                               case 'T_RH_sensor.png' :
                                    $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "°C " + valueSplitted[1] + "RH");
                                    $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "°C " + valueSplitted[1] + "RH");
                                    break;
                                case 'water_T_sensor.png': 
                                    $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "°C");
                                    $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "°C");
                                    break;
                                case 'level_sensor.png': 
                                    $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "cm");
                                    $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "cm");
                                    break;
                                case 'pH-sensor.png': 
                                    $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "ph");
                                    $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "ph");
                                    break;
                                case 'conductivity-sensor.png': 
                                    $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "ec");
                                    $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "ec");
                                    break;
                                case 'dissolved-oxygen-sensor.png': 
                                    $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "OD");
                                    $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "OD");
                                    break;
                                case 'ORP-sensor.png': 
                                    $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "ORP");
                                    $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "ORP");
                                    break;
                                default :
                                    $('#syno_elemValueSensor_val1_' + key).html(valueSplitted[0]  + "???");
                                    $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',valueSplitted[0]  + "???");
                                    break;
                            }
                            $('img[name="syno_elemSensorImage_' + key + '"]').css("opacity", "1");
                        } else if (value == "TIMEOUT") {
                            $('#syno_elemValueSensor_val1_' + key).html("");
                            $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',"<?php echo __('TIMEOUT'); ?>");
                            $('img[name="syno_elemSensorImage_' + key + '"]').css("opacity", "0.4");
                        } else {
                            $('#syno_elemValueSensor_val1_' + key).html("");
                            $('img[name="syno_elemSensorImage_' + key + '"]').attr('title',"<?php echo __('DEFCOM'); ?>");
                            $('img[name="syno_elemSensorImage_' + key + '"]').css("opacity", "0.4");
                        }

                    }
                });
                
                var ladate=new Date();
                $('#synoptic_updateSensorHour').html(addZ(ladate.getHours())+":"+addZ(ladate.getMinutes())+":"+addZ(ladate.getSeconds()));
                
            }
            updateIsAked = 0;
            }, error: function(data) {
            }
        });
    }
    $.timeout.push(setInterval(updateSensors, 13000));

    // Loop for updating plugs
    function updatePlugs() {
        if (updateIsAked == 1) {
            return 0;
        }
        updateIsAked = 1;
        
        $.ajax({
            beforeSend: function(jqXHR) {
                $.xhrPool.push(jqXHR);
            },
            complete: function(jqXHR) {
                var index = $.xhrPool.indexOf(jqXHR);
                if (index > -1) {
                    $.xhrPool.splice(index, 1);
                }
            },
            cache: false,
            type: "POST",
            data: {
                action:"getAllPlugLiveValue"
            },
            url: "main/modules/external/synoptic.php",
            success: function (data) {

            var objJSON = jQuery.parseJSON(data);

            if (objJSON.error == "") {
            
                $.each( objJSON, function( key, value ) {
                    // Check if element exists
                    if ($('img[name="syno_elemPlugImage_' + key + '"]').length != 0 ) {
                    
                        // Change text and opacity
                        if (value == "on") {
                            $('img[name="syno_elemPlugImage_' + key + '"]').attr('title',"<?php echo __('VALUE_ON'); ?>");
                            $('img[name="syno_elemPlugImage_' + key + '"]').css("opacity", "1");
                        } else if (value == "off") {
                            $('img[name="syno_elemPlugImage_' + key + '"]').attr('title',"<?php echo __('VALUE_OFF'); ?>");
                            $('img[name="syno_elemPlugImage_' + key + '"]').css("opacity", "1");
                        } else if (value == "TIMEOUT") {
                            $('img[name="syno_elemPlugImage_' + key + '"]').attr('title',"<?php echo __('TIMEOUT'); ?>");
                            $('img[name="syno_elemPlugImage_' + key + '"]').css("opacity", "0.4");
                        } else {
                            $('img[name="syno_elemPlugImage_' + key + '"]').attr('title',"<?php echo __('DEFCOM'); ?>");
                            $('img[name="syno_elemPlugImage_' + key + '"]').css("opacity", "0.4");
                        }
                        
                        // Update image
                        if (value == "ON") {
                            $('img[name="syno_elemPlugImage_' + key + '"]').attr('src',$('img[name="syno_elemPlugImage_' + key + '"]').attr('src').replace("_OFF", "_ON"));
                        } else  {
                            $('img[name="syno_elemPlugImage_' + key + '"]').attr('src',$('img[name="syno_elemPlugImage_' + key + '"]').attr('src').replace("_ON", "_OFF"));
                        }

                    }
                });
                
                var ladate=new Date();
                $('#synoptic_updatePlugHour').html(addZ(ladate.getHours())+":"+addZ(ladate.getMinutes())+":"+addZ(ladate.getSeconds()));
                
            }
            
            updateIsAked = 0;
            }, error: function(data) {
            }
        });
    }
    $.timeout.push(setInterval(updatePlugs, 7000));
    

    // Display services logs:
    $("a[name='cultipi_logs']").click(function(e) {
        e.preventDefault();
        var id=$(this).attr('id');
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
            $("#output_logs").empty();
            $("#error_logs").empty();
            $.ajax({
                cache: false,
                async: true,
                url: "main/modules/external/get_logs_cultipi.php",
                data: {action:id},
                success: function (data) {
                    var objJSON = jQuery.parseJSON(data);

                    if(objJSON[0].length>0) {
                        $("#div_title_output").show();
                        $.each(objJSON[0], function(i, item) {
                            $("#output_logs").append(item+"<br />");
                        });
                    } else {
                        $("#div_title_output").css("display","none");
                    }

                    if(objJSON[1].length>0) {
                        $("#div_title_error").show();
                        $.each(objJSON[1], function(i, item) {
                            $("#error_logs").append(item+"<br />");
                        });
                    } else {
                        $("#div_title_error").css("display","none");
                    }

                    $.unblockUI(); 

                    $("#dialog_logs_cultipi").dialog({
                        modal: true,
                        width: 800,
                        height: $( window ).height(),
                        closeOnEscape: true,
                        dialogClass: "popup_message",
                        buttons: [{
                            text: CLOSE_button,
                            click: function () {
                                $(this).scrollTop(0);
                                $(this).dialog('close'); 
                                return false;
                            }
                        }]
                    });
                },error: function (data) {
                    $.unblockUI();
                }
            });
        }});
    });


     // Download logs services file:
    $("a[name='dl_cultipi_logs']").click(function(e) {
        e.preventDefault();
        var id=$(this).attr('id');
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
            $("#output_logs").empty();
            $("#error_logs").empty();
            $.ajax({
                cache: false,
                async: true,
                url: "main/modules/external/prepare_dl_logs.php",
                data: {action:id},
                success: function (data) {
                    var objJSON = jQuery.parseJSON(data);

                    if(objJSON!="") {
                        $.fileDownload('tmp/export/'+objJSON);
                    }

                    $.unblockUI();

                },error: function (data) {
                    $.unblockUI();
                }
            });
        }});
    });


    //Dl Bonjour setup
    $("#dl-bonjour").click(function(e) {
        e.preventDefault();
        $.fileDownload('../BonjourPSSetup.exe');
    });

    //Update RPI:
    $("#update_rpi").click(function(e) {
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
                    url: "main/modules/external/check_rpi_update.php",
                    async: false
               }).done(function (data) {
                     $.unblockUI();
                     var objJSON = jQuery.parseJSON(data)
                     if(objJSON=="1") {
                        $("#cultipi_access_update").dialog({
                            resizable: false,
                            height:170,
                            width: 500,
                            modal: true,
                            closeOnEscape: false,
                            dialogClass: "popup_error",
                            buttons: [{
                                text: CLOSE_button,
                                click: function () {
                                    $( this ).dialog("close");
                                }
                            }]
                        });
                     } else if(objJSON=="") {
                        $("#cultipi_no_update").dialog({
                            resizable: false,
                            height:150,
                            width: 500,
                            modal: true,
                            closeOnEscape: false,
                            dialogClass: "popup_message",
                            buttons: [{
                                text: CLOSE_button,
                                click: function () {
                                    $( this ).dialog("close");
                                }
                            }]
                        });
                     } else {
                        $("#cultipi_confirm_update").dialog({
                            resizable: false,
                            height:150,
                            width: 500,
                            modal: true,
                            closeOnEscape: false,
                            dialogClass: "dialog_cultibox",
                            buttons: [{
                                text: OK_button,
                                click: function () {
                                    $( this ).dialog("close");
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
                                                url: "main/modules/external/upgrade_rpi.php",
                                                async: false
                                        }).done(function (data) {
                                            $.unblockUI();
                                                $("#cultipi_updated").dialog({
                                                    resizable: false,
                                                    height:150,
                                                    width: 500,
                                                    modal: true,
                                                    closeOnEscape: false,
                                                    dialogClass: "popup_message",
                                                    buttons: [{
                                                        text: CLOSE_button,
                                                        click: function () {
                                                            $( this ).dialog("close");
                                                            window.location = "/cultibox"
                                                        }
                                                    }]
                                                });
                                        });
                                        }
                                    });
                                }}, {
                                text: CLOSE_button,
                                click: function () {
                                    $( this ).dialog("close");
                                }
                            }]
                        });
                     }
               });
            }
        });
        


    });


    // Restart RPI:
    $("#restart_rpi").click(function(e) {
           e.preventDefault();
           $("#confirm_restart_rpi").dialog({
                resizable: false,
                height:150,
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
                            url: "main/modules/external/services_status.php",
                            async: false,
                            data: {action:"restart_rpi"}
                        }).done(function (data) {
                            $.unblockUI();
                            $("#success_restart_rpi").dialog({
                                resizable: false,
                                height:200,
                                width: 500,
                                modal: true,
                                closeOnEscape: false,
                                dialogClass: "popup_message",
                                buttons: [{
                                    text: OK_button,
                                    click: function () {
                                        $( this ).dialog("close");
                                        return false;
                                    }
                                }]
                            });
                        });
                    }
                }, {
                    text: CANCEL_button,
                    click: function () {
                        $( this ).dialog( "close" ); return false;
                    }
                }]
         });
    });



    $("#restart_cultipi").click(function(e) {
           e.preventDefault();
           $("#confirm_restart_cultipi").dialog({
                resizable: false,
                height:150,
                width: 500,
                modal: true,
                closeOnEscape: false,
                dialogClass: "dialog_cultibox",
                buttons: [{
                    text: OK_button,
                    click: function () {
                        $( this ).dialog("close");

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
                                    async: true,
                                    url: "main/modules/external/services_status.php",
                                    data: {action:"restart_cultipi"},
                                    success: function (data) {
                                        var objJSON = jQuery.parseJSON(data);
                                        if(objJSON=="0") {
                                            $.ajax({
                                                cache: false,
                                                async: true,
                                                url: "main/modules/external/services_status.php",
                                                data: {action:"status_cultipi"}
                                            }).done(function (data) {
                                                var objJSON = jQuery.parseJSON(data);
                                                if(objJSON=="0") {
                                                    $("#cultipi_on").show();
                                                    $("#cultipi_off").css('display','none');
                                                } else {
                                                    $("#cultipi_off").show();
                                                    $("#cultipi_on").css('display','none');
                                                }
                                                $.unblockUI();

                                                $("#success_restart_service").dialog({
                                                    resizable: false,
                                                    width: 400,
                                                    closeOnEscape: true,
                                                    modal: true,
                                                    dialogClass: "popup_message",
                                                    buttons: [{
                                                        text: CLOSE_button,
                                                        click: function () {
                                                            $( this ).dialog( "close" ); return false;
                                                        }
                                                    }]
                                                    });
                                            });
                                        } else {
                                            $("#error_restart_service").dialog({
                                                resizable: false,
                                                width: 400,
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
                                            $.unblockUI();
                                        }
                                    }, error: function (data) {
                                        $("#error_restart_service").dialog({
                                            resizable: false,
                                            width: 400,
                                            closeOnEscape: true,
                                            modal: true,
                                            dialogClass: "popup_error",
                                            buttons: [{
                                                text: CLOSE_button,
                                                click: function () {
                                                    $( this ).dialog( "close" ); return false;
                                            } }]
                                        });
                                        $.unblockUI();
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
                }]
        });
    });
});


$(document).ready(function() {
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

    $("#wifi_scan").click(function(e) {
         e.preventDefault();
         $("#wifi_essid_list").dialog({
            resizable: false,
            width: 500,
            modal: true,
            closeOnEscape: true,
            dialogClass: "popup_message",
            buttons: [{
                text: RELOAD_button,
                click: function () {
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
                            $("#wifi_essid_list").dialog('close');
                            $.ajax({
                                cache: false,
                                async: false,
                                url: "main/modules/external/scan_network.php"
                            }).done(function (data) {
                                $("#wifi_essid_list").empty();
                                $("#wifi_essid_list").append("<p><?php echo __('WIFI_SCAN_SUBTITLE'); ?></p>");
                                $.each($.parseJSON(data),function(index,value) {
                                    checked="";
                                    if($("#wifi_ssid").val()==value) {
                                        checked="checked";
                                    }
                                    $("#wifi_essid_list").append('<b>'+value+' : </b><input type="radio" name="wifi_essid" value="'+value+'" '+checked+' /><br />');
                                });
                                $("#wifi_essid_list").dialog('open');
                            });
                       }
                    }); 
                    $.unblockUI();
                }
             }, {
                text: CLOSE_button,
                click: function () {
                    $( this ).dialog( "close" ); return false;
                }
            },{
                text: SELECT_button,
                click: function () {
                    $("#wifi_ssid").val($("input:radio[name=wifi_essid]:checked").val());
                    $( this ).dialog( "close" ); return false;
                }
            }]
        });
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


        if($('#wifi_key_type').val()=="WEP") {
            $("#hex_password").removeAttr("disabled");
        } else {
            $("#hex_password").attr("disabled", "disabled");
        }
    });


    $("#submit_conf").click(function(e) {
      e.preventDefault();


      if(($("#activ_wire option:selected").val()=="False")&&($("#activ_wifi option:selected").val()=="False")) {
         $("#empty_network_conf").dialog({
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
      } else {
        $("#error_wire_ip").css("display","none");
        $("#error_wire_mask").css("display","none");
        $("#error_wifi_ssid").css("display","none");
        $("#error_wifi_password").css("display","none");
        $("#error_wifi_password_confirm").css("display","none");
        $("#error_password_wep").css("display","none");
        $("#error_password_hexa").css("display","none");
        $("#error_password_wpa").css("display","none");
        $("#error_wifi_ip").css("display","none");
        $("#error_wifi_mask").css("display","none");


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

                if($("#activ_wire option:selected").val()=="True") {            
                    if($('input[name=wire_type]:radio:checked').val()=="static") {
                        $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/check_value.php",

                            data: {value:$("#wire_address").val(),type:'ip'}
                        }).done(function (data) {
                            if(data!=1) {
                                $("#error_wire_ip").show(700);
                                checked=false;
                            } else {
                                $("#error_wire_ip").css("display","none");
                            }
                        });

                        $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/check_value.php",

                            data: {value:$("#wire_mask").val(),type:'ip'}
                        }).done(function (data) {
                            if(data!=1) {
                                $("#error_wire_mask").show(700);
                                checked=false;
                            } else {
                                $("#error_wire_mask").css("display","none");
                            }
                        });

                        $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/check_value.php",

                            data: {value:$("#wire_gw").val(),type:'ip'}
                        }).done(function (data) {
                            if(data!=1) {
                                $("#wire_gw").val("0.0.0.0");
                            } 
                        });
                    }
                }

                var type_password="";
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


                    switch ($("#wifi_key_type").val()) {
                        case 'NONE': type_password="password_none";
                            break;
                        case 'WEP': type_password="password_wep"
                            break;
                        case 'WPA AUTO': type_password="password_wpa";
                            break;
                        default: type_password="";
                    }

                    if($("#wifi_key_type").val()!="NONE") {
                        if($("#wifi_password").val()=="") {
                            $("#error_wifi_empty_password").css("display","");
                            checked=false;
                        } else if($("#wifi_password").val()!="") {
                            $.ajax({
                                cache: false,
                                async: false,
                                url: "main/modules/external/check_value.php",
                                data: {value:$("#wifi_password").val(),value2:$("#wifi_password_confirm").val(),type:'password'}
                            }).done(function (data) {
                                $("#error_wifi_empty_password").css("display","none");
                                if(data!=1) {
                                    $("#error_wifi_password").show(700);
                                    $("#error_wifi_password_confirm").show(700);
                                    $("#error_password_wep").css("display","none");
                                    $("#error_password_wep_hexa").css("display","none");
                                    $("#error_password_wpa").css("display","none");
                                    checked=false;
                                } else {
                                    $("#error_wifi_password").css("display","none");
                                    $("#error_wifi_password_confirm").css("display","none");

                                    if($("#hex_password").attr('checked')) {
                                        var hex="1";
                                    } else {
                                        var hex="0";
                                    }

                                    $.ajax({
                                        cache: false,
                                        async: false,
                                        url: "main/modules/external/check_value.php",
                                        data: {value:$("#wifi_password").val(),type:type_password,hex:hex}
                                    }).done(function (data) {
                                        if(data!=1)  {
                                            checked=false;
                                            switch (type_password) {
                                                case 'password_wep': 
                                                    if($("#hex_password").attr('checked')) {
                                                        $("#error_password_wep_hexa").show(700);
                                                        $("#error_password_wpa").css("display","none");
                                                        $("#error_password_wep").css("display","none");
                                                    } else {
                                                        $("#error_password_wep").show(700);
                                                        $("#error_password_wpa").css("display","none");
                                                        $("#error_password_wep_hexa").css("display","none");   
                                                    }
                                                    break;
                                                case 'password_wpa': 
                                                    $("#error_password_wep").css("display","none");
                                                    $("#error_password_wpa").show(700);
                                                    $("#error_password_wep_hexa").css("display","none");
                                                    break;
                                                default: 
                                                    $("#error_password_wep").css("display","none")
                                                    $("#error_password_wpa").css("display","none");
                                                    $("#error_password_wep_hexa").css("display","none");
                                            }
                                        } else {
                                            $("#error_password_wep").css("display","none");
                                            $("#error_password_wpa").css("display","none");
                                            $("#error_password_wep_hexa").css("display","none");
                                        }
                                    });
                                }
                            });
                        }
                    } 
                

                    if($('input[name=wifi_type]:radio:checked').val()=="static") {
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


                         $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/check_value.php",

                            data: {value:$("#wifi_mask").val(),type:'ip'}
                        }).done(function (data) {
                            if(data!=1) {
                                $("#error_wifi_mask").show(700);
                                checked=false;
                            } else {
                                $("#error_wifi_mask").css("display","none");
                            }
                        });

                         $.ajax({
                            cache: false,
                            async: false,
                            url: "main/modules/external/check_value.php",
                
                            data: {value:$("#wifi_gw").val(),type:'ip'}
                        }).done(function (data) {
                            if(data!=1) {
                                $("#wifi_gw").val("0.0.0.0");
                            } 
                        });
                    }
        
                }

                var check_update=false;
                if(checked) {
                    var dataForm=$("#configform").serialize();
                    if($("#hex_password").attr('checked')) {
                        var hex="1";
                    } else {
                        var hex="0";
                    }

                    dataForm=dataForm+"&wifi_type="+$('input[name=wifi_type]:radio:checked').val()+"&wire_type="+$('input[name=wire_type]:radio:checked').val()+"&hex_password="+hex;

                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/create_configuration.php",
                        data: dataForm
                    }).done(function (data) {
                        try{
                            if($.parseJSON(data)=="1") {  
                                check_update=true;
                            } else {
                                $("#error_network_file").dialog({
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

                        } catch(err) {
                            check_update=false;
                            $("#error_network_file").dialog({
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
                }

                if(check_update) {
                   $.ajax({
                       cache: false,
                       async: true,
                       timeout: 30000,
                       url: "main/modules/external/restart_service.php",
                       data: {type:type_password}
                   }).done(function (data) {
                        try{
                            if($.parseJSON(data)=="1") {
                                $.unblockUI();
                                $("#network_new_addr_set").dialog({
                                    resizable: false,
                                    width: 500,
                                    modal: true,
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
                                $.unblockUI();
                                $("#error_restore_conf").dialog({
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
                        } catch(err) {
                            $.unblockUI();
                            $("#error_restore_conf").dialog({
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
                   })
                  .fail(function() {
                        $.unblockUI();
                        //When restarting the network service, the Ajax call fails:
                        $("#network_new_addr_set").dialog({
                            resizable: false,
                            width: 500,
                            modal: true,
                            closeOnEscape: true,
                            dialogClass: "popup_message",
                            buttons: [{
                                text: CLOSE_button,
                                click: function () {
                                $( this ).dialog( "close" ); return false;
                            }
                         }]
                       });
                  });
                } else {
                    $.unblockUI();
                }
              }
        });
      }
    });
});

</script>




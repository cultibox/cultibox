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
var syno_configure_element_zindex_value = 1;
var syno_configure_element_zindex_imageID = "";
var syno_configure_element_scale_value = 1;
var syno_configure_element_scale_imageID = "";
var syno_configure_element_rotation = "";
var idOfElem = "";
var typeOfElem = "";

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
        
         $("#div_cultipi_soft").append("<b>"+objJSON[0]+"</b>");
         $("#div_cultinet_soft").append("<b>"+objJSON[1]+"</b>");
         $("#div_cultibox_soft").append("<b>"+objJSON[2]+"</b>");
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
                url: "main/modules/external/synoptic.php"
            }).done(function (data) {
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
                    url: "main/modules/external/synoptic.php"
                }).done(function (data) {
                    // Add element from database
                    
                    if(data != "") {

                        var objJSON = jQuery.parseJSON(data);
                        
                        // Create the div
                        $("#set").append('<div id="syno_elem_' + objJSON.id + '" class="" style="position:absolute; top:' + objJSON.y + 'px ; left:' + objJSON.x + 'px ;z-index:' + objJSON.z + '" ></div>');
                        
                        var inTable = '<table>';
                        inTable = inTable +  '    <tr>';
                        inTable = inTable +  '      <td>';
                        inTable = inTable +  '          <input type="image" id="syno_elemConfigur_' + objJSON.id + '" name="syno_elemConfigur_' + objJSON.id + '"';
                        inTable = inTable +  '                 title=""';
                        inTable = inTable +  '                 src="main/libs/img/advancedsettings.png"  width="18"';
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
                                    url: "main/modules/external/synoptic.php"
                                }).done(function (data) {
                                });
                                absolut_X_position = "";
                                absolut_Y_position = "";
                            }
                        });
                    
                    
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
    
    // Slider for zoom
    $("#syno_configure_element_scale").slider({
        max: 1000,
        min: 50,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_scale_val").val(ui.value);
            $('#' + syno_configure_element_scale_imageID).width(ui.value);
        },
        step: 1,
        value: syno_configure_element_scale_value
    });
    
    // Slider for zindex
    $("#syno_configure_element_zindex").slider({
        max: 200,
        min: 1,
        slide: function( event, ui ) {
            // While sliding, update the value in the div element
            $("#syno_configure_element_zindex_val").val(ui.value);
            $('#' + syno_configure_element_zindex_imageID).zIndex(ui.value);
        },
        step: 1,
        value: syno_configure_element_zindex_value
    });
    
    // Rotation
    $( 'input[name="syno_configure_element_rotate"]:radio' ).change(
        function(){
            // retrieve the class
            var className = $('#' + syno_configure_element_scale_imageID).attr('class');
            $('#' + syno_configure_element_scale_imageID).removeClass(className);
            var newClass = $('input[name=syno_configure_element_rotate]:checked').val();
            $('#' + syno_configure_element_scale_imageID).addClass("rotate" + newClass);
        }
    );
    
    // Image
    $('#syno_configure_element_image_other, #syno_configure_element_image_plug, #syno_configure_element_image_sensor').on('change', function() {
        try {
            $('#syno_elemImage_' + idOfElem).attr('src', 'main/libs/img/images-synoptic-' + typeOfElem + '/' + this.value);
        } catch (e) {
            alert(e.message);
        }
    });
    
    // Display and control user form for configuring item
    $('body').on('click', '.syno_conf_elem_button', function(e) {
        e.preventDefault();
        
        idOfElem = $(this).attr('id').split("_")[2];
        
        $.ajax({
           cache: false,
           type: "POST",
           data: {id:idOfElem, action:"getParam"},
           url: "main/modules/external/synoptic.php"
        }).done(function (data) {
            
            if(data != "") {

                var objJSON = jQuery.parseJSON(data);
                
                // Save type of the element
                typeOfElem = objJSON.element;
                
                // Update style of each configure element
                $("#syno_configure_element_rotate_0" ).prop("checked", false);
                $("#syno_configure_element_rotate_90" ).prop("checked", false);
                $("#syno_configure_element_rotate_180" ).prop("checked", false);
                $("#syno_configure_element_rotate_270" ).prop("checked", false);
                $("#syno_configure_element_rotate_" + objJSON.rotation ).prop("checked", true);
                
                syno_configure_element_scale_value = parseInt(objJSON.scale);
                $("#syno_configure_element_scale_val").val(syno_configure_element_scale_value);
                $("#syno_configure_element_scale").slider("value",syno_configure_element_scale_value);
                
                syno_configure_element_zindex_value = parseInt(objJSON.z);
                $("#syno_configure_element_zindex_val").val(objJSON.z);
                $("#syno_configure_element_zindex").slider("value",objJSON.z);
                
                syno_configure_element_scale_imageID = "syno_elemImage_" + idOfElem ;
                syno_configure_element_zindex_imageID = "syno_elem_" + idOfElem ;
                
                syno_configure_element_rotation = objJSON.rotation;
                $('#syno_configure_element_image_' + typeOfElem + ' option[value="' + objJSON.image + '"]').prop('selected', true);

                // Select correct image option
                $("#syno_configure_element_image_other").hide();
                $("#syno_configure_element_image_plug").hide();
                $("#syno_configure_element_image_sensor").hide();
                $("#syno_configure_element_image_" + typeOfElem).show();
                console.debug('Type : ' + typeOfElem);
                console.debug('Image : ' + objJSON.image);
                $("#syno_configure_element").dialog({
                    resizable: false,
                    width: 400,
                    closeOnEscape: true,
                    dialogClass: "popup_message",
                    buttons: [{
                        text: CLOSE_button,
                        click: function () {
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
                                    image:$( "#syno_configure_element_image_" + typeOfElem + " option:selected" ).val(),
                                    rotation:$('input[name=syno_configure_element_rotate]:checked').val(),
                                    action:"updateZScaleImageRotation"
                                },
                                url: "main/modules/external/synoptic.php"
                            }).done(function (data) {
                            
                            });
                            $( this ).dialog( "close" );
                            return false;
                        }
                    }, {
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
                    }]
                });
                
            }
        });
    });

var updateIsAked = 0;
    // Loop for updating sensors and plugs
    function updateSensors() {
    
        if (updateIsAked == 1) {
            return 0;
        }
        updateIsAked = 1;
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                action:"getAllSensorLiveValue"
            },
            url: "main/modules/external/synoptic.php"
        }).done(function (data) {
            var objJSON = jQuery.parseJSON(data);

            if (objJSON.error == "") {
            
                $.each( objJSON, function( key, value ) {
                    if (key != "error") {
                        $('#syno_elemValueSensor_' + key).html(value);
                    }
                });
            }
            
            updateIsAked = 0;

        })
    }
    setInterval(updateSensors, 13000);

    // Loop for updating sensors and plugs
    function updatePlugs() {
        if (updateIsAked == 1) {
            return 0;
        }
        updateIsAked = 1;
        
        $.ajax({
            cache: false,
            type: "POST",
            data: {
                action:"getAllPlugLiveValue"
            },
            url: "main/modules/external/synoptic.php"
        }).done(function (data) {
            var objJSON = jQuery.parseJSON(data);

            if (objJSON.error == "") {
            
                $.each( objJSON, function( key, value ) {
                    if (key != "error") {
                        $('#syno_elemValuePlug_' + key).html(value);
                    }
                });
            }
            
            updateIsAked = 0;
        })
    }
    setInterval(updatePlugs, 7000);
    
    $("#div_network_label").click(function(e) {
        e.preventDefault();
        window.open("/cultinet");

    });


    // Display services logs:
    $(":button[name='cultipi_logs']").click(function() {
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
                                $( this ).dialog( "close" ); return false;
                            }
                        }]
                    });
                },error: function (data) {
                    $.unblockUI();
                }
            });
        }});
    });

    $("#restart_cultipi").click(function(e) {
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
     }});
    });
});
</script>



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
var syno_configure_element_imageName = "";
var syno_configure_element_rotation = "";


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
        cursor: "move",
        stop:function(event, ui) {
            $.ajax({
               cache: false,
               type: "POST",
               data: {elem:$(this).attr('id').split("_")[2], x:(parseInt($(this).position().left) / 10) * 10, y:(parseInt($(this).position().top) / 10) * 10, action:"updatePosition"},
               url: "main/modules/external/synoptic.php"
            }).done(function (data) {
            });
        }
    });

    // Display and control user form for daily program
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
    
    // Display and control user form for configuring item
    $( ".syno_conf_elem_button" ).click(function(e) {
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
                
                // Update style of each configure element
                $("#syno_configure_element_rotate_0" ).prop("checked", false);
                $("#syno_configure_element_rotate_90" ).prop("checked", false);
                $("#syno_configure_element_rotate_180" ).prop("checked", false);
                $("#syno_configure_element_rotate_270" ).prop("checked", false);
                //$("#syno_configure_element_rotate_" . objJSON.rotation ).prop("checked", true);
                
                syno_configure_element_scale_value = parseInt(objJSON.scale);
                $("#syno_configure_element_scale_val").val(objJSON.scale);
                $("#syno_configure_element_scale").slider("value",objJSON.scale);
                
                syno_configure_element_zindex_value = parseInt(objJSON.z);
                $("#syno_configure_element_zindex_val").val(objJSON.z);
                $("#syno_configure_element_zindex").slider("value",objJSON.z);
                
                syno_configure_element_scale_imageID = "syno_elemImage_" + idOfElem ;
                syno_configure_element_zindex_imageID = "syno_elemImage_" + idOfElem ;
                
                syno_configure_element_rotation = objJSON.rotation;
                syno_configure_element_imageName = objJSON.image;

                $("#syno_configure_element").dialog({
                    resizable: false,
                    width: 300,
                    closeOnEscape: true,
                    dialogClass: "popup_message",
                    buttons: [{
                        text: CLOSE_button,
                        click: function () {
                            $( this ).dialog( "close" ); return false;
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
                                    image:syno_configure_element_imageName,
                                    rotation:syno_configure_element_rotation,
                                    action:"updateZScaleImageRotation"
                                },
                                url: "main/modules/external/synoptic.php"
                            }).done(function (data) {
                            })
                            
                        }
                    }]
                });
                
            }
        });
    });

    $("#div_network_label").click(function(e) {
        e.preventDefault();
        window.open("/cultinet");

    });


    // Display services logs:
    $(":button[name='cultipi_logs']").click(function() {
        $("#output_logs").empty();
        $("#error_logs").empty();
        $.ajax({
          cache: false,
          async: true,
          url: "main/modules/external/get_logs_cultipi.php",
          data: {action:$(this).attr('id')}
        }).done(function (data) {
          var objJSON = jQuery.parseJSON(data);

          $.each(objJSON[0], function(i, item) {
            $("#output_logs").append(item+"<br />");
          });

          $.each(objJSON[1], function(i, item) {
            $("#error_logs").append(item+"<br />");
          });

          $("#dialog_logs_cultipi").dialog({
              modal: true,
              width: 800,
              height: 640,
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



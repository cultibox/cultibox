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
        buttons: {
            "OK": function() { $( this ).dialog( "close" ); SendForm.submit(); 
            },
            Cancel: function() { $( this ).dialog( "close" ); return false;}
        }
    });
}



formatCard = function(hdd,pourcent) {
            $.ajax({ 
                cache: false,
                url: "../../main/scripts/format.php",
                data: {hdd:hdd, progress: pourcent}
            }).done(function (data) {
                $("#progress_bar").progressbar({ value: 4*parseInt(data) });
                if(data==100) { 
                    $("#success_format").show();
                    return true 
                } else if(data>=0) { 
                    formatCard(hdd,data); 
                } else {
                    $("#error_format").show();
                }
            });
}




$(document).ready(function() {
   $.ajax({
                cache: false,
                url: "../../main/scripts/position.php"
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

        $( ".message" ).dialog({ width: wid, resizable: true, buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", dialogClass: "dialog_cultibox", position: [x,y], dragStop: function( event, ui ) { 
            if(data!="") { 
                var tmp = $(".message").dialog( "option", "position" );
                var width = $(".message").dialog( "option", "width" );

                $.ajax({
                cache: false,
                url: "../../main/scripts/position.php",
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
                url: "../../main/scripts/position.php",
                data: { WIDTH: width, POSITION_X: tmp[0], POSITION_Y: tmp[1] }
                });
        }
    } });
                $(".message").dialog().parent().css('position', 'fixed');
    }); 
   // Affichage des tooltips sur les éléments avec un title
   jQuery('#jquery-colour-picker-example select').colourPicker({ 
        ico:    'http://localhost:6891/cultibox/main/libs/img/jquery.colourPicker.gif', 
        title:    false
   });
   $("[title]").tooltip({ position: { my: "left+15 center", at: "right center" } });
   $( ".pop_up_error" ).dialog({ width: 550, buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", modal: true,  dialogClass: "popup_error" });
   $( ".pop_up_message" ).dialog({ width: 550, buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", modal: true,  dialogClass: "popup_message"  });
  

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
            case 'reset_log_form': DialogId="reset_dialog_log";
                                        break;
            case 'reset_log_power_form': DialogId="reset_dialog_log_power";
                                        break;
            case 'reset_calendar_form': DialogId="reset_dialog_calendar";
                                        break;
            case 'actionprog':  if((start!=$('#start_time').val())||(end!=$('#end_time').val())) {
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

        $url = 'http://localhost:6891/cultibox/main/scripts/force-download.php?file='+$(".download").attr("href");
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
            buttons: {
                "OK": function() { $( this ).dialog("close"); 
                        $("#progress").dialog({
                            resizable: false,
                            height:200,
                            width: 500,
                            modal: true,
                            dialogClass: "popup_message",
                            buttons: {
                                Close: function() { 
                                    $( this ).dialog("close"); 
                                    window.location.reload();
                                    return false;

                                }
                            }
                        });
                        $("#progress_bar").progressbar({value:0});
                        formatCard($("#selected_hdd").val(),0);
                },
                Cancel: function() { 
                            $( this ).dialog("close"); 
                            return false;
                }
            }
        });
    });
});

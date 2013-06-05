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


$(document).ready(function() {
   // Affichage des tooltips sur les éléments avec un title
   jQuery('#jquery-colour-picker-example select').colourPicker({ 
        ico:    'http://localhost:6891/cultibox/main/libs/img/jquery.colourPicker.gif', 
        title:    false
   });
   $("[title]").tooltip({ position: { my: "left+15 center", at: "right center" } });
   $( ".pop_up_error" ).dialog({ width: 550, buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", modal: true,  dialogClass: "popup_error" });
   $( ".pop_up_message" ).dialog({ width: 550, buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", modal: true,  dialogClass: "popup_message"  });
   
   $( ".info" ).dialog({ width: 325, resizable: false, buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", dialogClass: "popup_message", position: [15,15], title: '<img src="../../main/libs/img/informations.png" alt="" />' });
   $(".info").dialog().parent().css('position', 'fixed');

   $( ".error" ).dialog({ width: 325, resizable: false, buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); }} ], position: [15,360] , hide: "fold", dialogClass: "popup_error", title: '<img src="../../main/libs/img/warning.png" alt="" />'  });
   $(".error").dialog().parent().css('position', 'fixed');


   $(".delete").click(function() {
          var currentForm;
          currentForm = $(this).closest('form');
          var FormId = currentForm.attr('id');
          var DialogId="";

          switch (FormId) {
            case 'reset_program_form':  DialogId="delete_dialog_program";
                                        break;
            case 'format_sd_form': DialogId="format_dialog_sd";
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
});

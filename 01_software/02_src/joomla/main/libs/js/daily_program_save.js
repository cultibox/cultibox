<script type="text/javascript">
$(function() {

    var program_name = $( "#program_name" );

    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            return false;
        } else {
            return true;
        }
    }

    $( "#dialog-form-save-daily" ).dialog({
        autoOpen: false,
        height: 180,
        width: 350,
        modal: true,
        buttons: {
        "Enregistrer": function() {
            var bValid = true;
            program_name.removeClass( "ui-state-error" );
            bValid = bValid && checkLength( program_name, "program_name", 3, 16 );
            if ( bValid ) {
                
                pop_up_add_information("<?php echo __('PROGRAM_DAILY_SAVE_PROGRESS'); ?> <img src='../../main/libs/img/waiting_small.gif' />","daily_program_update","information");
                $.ajax({ 
                    type: "POST",
                    url: "../../main/modules/external/daily_program_save.php",
                    // Lang is the end of the url
                    data: "lang=" + document.location.href.split('/')[document.location.href.split('/').length - 2] + "&name=" + $('#program_name').val() + "&input=1&version=",
                    context: document.body,
                    success: function(data, textStatus, jqXHR) {
                        // Check response from server
                        
                        // Delete information
                        pop_up_remove("daily_program_update");
                        
                        // Prevent user that's ok
                        pop_up_add_information("<?php echo __('PROGRAM_DAILY_SAVE_FINISH'); ?>","daily_program_update","information");
                        
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Error during request
                    }
                });
                
                $( this ).dialog( "close" );
            }
        },
        "Quitter": function() {
            $( this ).dialog( "close" );
        }},
        close: function() {
            program_name.val( "" ).removeClass( "ui-state-error" );
        }
    });

    $( "#daily_save_button" )
        .button()
        .click(function() {
        $( "#dialog-form-save-daily" ).dialog( "open" );
        });
});
</script>
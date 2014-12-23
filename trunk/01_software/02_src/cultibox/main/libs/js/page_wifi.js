<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>

step=<?php echo json_encode($step); ?>;
nb_plugs=<?php echo json_encode($nb_plugs); ?>;
selected_plug=<?php echo json_encode($selected_plug); ?>;
error_valueJS=<?php echo json_encode($error_value); ?>;
canal_status= <?php echo json_encode($status); ?>;
title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;

$(document).ready(function(){
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


     pop_up_add_information("<?php echo __('WIZARD_DISABLE_FUNCTION'); ?>: <a href='/cultibox/index.php?menu=programs' class='href-wizard-msgbox'><img src='main/libs/img/wizard.png' alt='<?php echo __('CLASSIC'); ?>' title='' id='classic' /></a>", "jumpto_classic", "information");

     // Gestion of drag and drop
    $( "#set div" ).draggable({
        distance: 10,
        grid: [ 10, 10 ],
        stack: "#set div" ,
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
    
     
});
</script>



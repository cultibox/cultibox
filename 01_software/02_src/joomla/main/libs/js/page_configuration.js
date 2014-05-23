<script>

wifi_password=<?php echo(json_encode($wifi_password)); ?>;
rtc_offset_value=<?php echo json_encode($rtc_offset) ?>;

$(document).ready(function(){
    $('#reset_min_max').timepicker({
        <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
        showOn: 'both',
        buttonImage: "../../main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT')."',"; ?>
        timeFormat: 'hh:mm',
        <?php echo "timeText: '".__('TIMEPICKER_TIME')."',"; ?>
        <?php echo "hourText: '".__('TIMEPICKER_HOUR')."',"; ?>
        <?php echo "minuteText: '".__('TIMEPICKER_MINUTE')."',"; ?>
        <?php echo "secondText: '".__('TIMEPICKER_SECOND')."',"; ?>
        <?php echo "currentText: '".__('TIMEPICKER_ENDDAY')."',"; ?>
        <?php echo "closeText: '".__('TIMEPICKER_CLOSE')."',"; ?>
    });
    
    // On select change, update conf
    $( "select" ).each(function() {
       
        $(this).on('change', function() {
        
            newValue    = $( this ).find(":selected").val();
            varToUpdate = $( this ).attr('name');
            updateConf  = $( this ).attr('update_conf');
        
            $.ajax({
                type: "POST",
                cache: false,
                url: "../../main/modules/external/update_configuration.php",
                data: "lang=" + document.location.href.split('/')[document.location.href.split('/').length - 2] + "&value=" + newValue + "&variable=" + varToUpdate + "&updateConf=" + updateConf
            }).done(function (data) {
            });
        });
  
    });
    
    
    
});

</script>
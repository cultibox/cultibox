<script type="text/javascript">
<?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
$(document).ready(function(){
pop_up_add_information("<?php echo __('WAIT_UPDATED_PROGRAM') ;?> <img src=\"main/libs/img/waiting_small.gif\" />", "check_sd_progress", "information");

$.ajax({ 
    type: "GET",
    url: "main/modules/external/check_and_update_sd.php",
    data: {
        sd_card:"<?php echo $sd_card ;?>"
    },
    async: true,
    beforeSend: function(jqXHR) {
                $.xhrPool.push(jqXHR);
        },
        complete: function(jqXHR) {
            var index = $.xhrPool.indexOf(jqXHR);
            if (index > -1) {
                $.xhrPool.splice(index, 1);
            }
    },
    success: function(data, textStatus, jqXHR) {
        // Check response from server

        // Parse result
        var json = jQuery.parseJSON(data);
        
        // For each error, show it
        json.error.forEach(function(entry) {
            pop_up_add_information(entry,"check_sd_status","error");
        });

        // For each information, show it
        json.info.forEach(function(entry) {
            pop_up_add_information(entry,"check_sd_status","information");
        });  
        
        // Delete information
        pop_up_remove("check_sd_progress");
    },
    error: function(jqXHR, textStatus, errorThrown) {
        // Error during request
    }
});
});
<?php } else { ?>
    $(document).ready(function(){
    pop_up_add_information("<?php echo __('ERROR_SD_CARD'); ?>","check_sd_status","error");
    });
<?php } ?>
</script>

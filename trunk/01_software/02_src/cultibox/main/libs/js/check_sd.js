<?php if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) { ?>
<script type="text/javascript">
<?php if((isset($sd_card))&&(!empty($sd_card))) { ?>
    <?php if((isset($_COOKIE['CHECK_SD']))&&($_COOKIE['CHECK_SD']!='True')) { ?>
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
         $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"CHECK_SD", value: "True", duration:1800}
        });

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
    
        <?php
        // Send information
        if($send_stat) { ?>
            $.ajax({
                cache: false,
                type: "GET",
                async: true,
                url: "main/modules/external/send_informations.php"
            }).done(function (data) {
                if(jQuery.parseJSON(data)!=0) {
                    $.ajax({
                        cache: false,
                        type: "GET",
                        async: true,
                        url: "main/modules/external/reset_informations.php"
                    });
                }
            });
       <?php } ?>
    },
    error: function(jqXHR, textStatus, errorThrown) {
         $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"CHECK_SD", value: "False", duration:1800}
        });
    }
});
});
    <?php } else { ?>
        <?php if((isset($_COOKIE['CHECK_SD']))&&($_COOKIE['CHECK_SD']=='True')) { ?>
             $(document).ready(function(){
                pop_up_add_information("<?php echo __('INFO_SD_CARD').': '.$sd_card; ?>","check_sd_status","information");
             });
        <?php }  ?>
    <?php } ?>
<?php } else { ?>
    $(document).ready(function(){
        pop_up_add_information("<?php echo __('ERROR_SD_CARD'); ?>","check_sd_status","error");
        <?php
        // Send information
        if($send_stat) { ?>
            $.ajax({
                cache: false,
                type: "GET",
                async: true,
                url: "main/modules/external/send_informations.php"
            }).done(function (data) {
                if(jQuery.parseJSON(data)!=0) {
                    $.ajax({
                        cache: false,
                        type: "GET",
                        async: true,
                        url: "main/modules/external/reset_informations.php"
                    });
                }
            });
       <?php } ?>
    });
<?php } ?>
</script>
<?php } ?>

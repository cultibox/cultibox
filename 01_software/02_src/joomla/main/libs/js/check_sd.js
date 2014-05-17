<script type="text/javascript">
$(document).ready(function(){
$.ajax({ 
    type: "POST",
    url: "../../main/modules/external/check_and_update_sd.php",
    // Lang is the end of the url
    data: "lang=" + document.location.href.split('/')[document.location.href.split('/').length - 2],
    context: document.body,
    success: function(data, textStatus, jqXHR) {
        // Check response from server
        // Delete information
        pop_up_remove("check_sd_progress");

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
    },
    error: function(jqXHR, textStatus, errorThrown) {
        // Error during request
    }
});
});
</script>
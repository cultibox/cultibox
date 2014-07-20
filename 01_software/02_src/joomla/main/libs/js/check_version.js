<script type="text/javascript">
$(document).ready(function(){
$.ajax({ 
    type: "GET",
    url: "../../main/modules/external/check_update.php",
    // Lang is the end of the url
    data: "session_id=" + session_id,
    context: document.body,
    success: function(data, textStatus, jqXHR) {
        // Check response from server
        
        // Parse result
        var json = jQuery.parseJSON(data);
        
        // For each error, show it
        json.error.forEach(function(entry) {
            pop_up_add_information(entry,"check_version_status","error");
        });

        // For each information, show it
        json.info.forEach(function(entry) {
            pop_up_add_information(entry,"check_version_status","information");
        });  
        
        // Delete information
        pop_up_remove("check_version_progress");
        
    },
    error: function(jqXHR, textStatus, errorThrown) {
        // Error during request
    }
});
});
</script>

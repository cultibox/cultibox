<script type="text/javascript">
$(document).ready(function(){
$.ajax({ 
    type: "POST",
    url: "../../main/modules/external/check_update.php",
    // Lang is the end of the url
    data: "lang=" + document.location.href.split('/')[document.location.href.split('/').length - 2],
    context: document.body,
    success: function(data, textStatus, jqXHR) {
        // Check response from server
        // Delete information
        pop_up_remove("check_version_progress");

        // Add response
        switch (data.substring(5,7)) {
            case "fl" :
                pop_up_add_information(data.substring(7,data.length),"check_version_status","error");
                break;
            case "na" :
                break;
            case "ok" :
                pop_up_add_information(data.substring(7,data.length),"check_version_status","information");
                break;
        }
        
    },
    error: function(jqXHR, textStatus, errorThrown) {
        // Error during request
    }
});
});
</script>
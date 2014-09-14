<script type="text/javascript">
$(document).ready(function(){
$.ajax({ 
    cache: false,
    type: "GET",
    async: true,
    url: "main/modules/external/send_informations.php",
    context: document.body,
    success: function(data, textStatus, jqXHR) {
        // Check response from server       
    },
    error: function(jqXHR, textStatus, errorThrown) {
        // Error during request
    }
});
});
</script>

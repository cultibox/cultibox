<script type="text/javascript">
$(document).ready(function(){
$.ajax({ 
    cache: false,
    type: "GET",
    url: "../../main/modules/external/send_informations.php",
    data: {session_id:session_id},
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

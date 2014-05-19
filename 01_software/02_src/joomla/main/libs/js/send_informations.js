<script type="text/javascript">
$(document).ready(function(){
$.ajax({ 
    cache: false,
    async: true,
    type: "POST",
    url: "../../main/modules/external/send_informations.php",
    // Lang is the end of the url
    data: "lang=" + document.location.href.split('/')[document.location.href.split('/').length - 2],
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
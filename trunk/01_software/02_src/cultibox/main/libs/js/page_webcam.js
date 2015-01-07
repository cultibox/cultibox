
<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>

var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;

// {{{ getSnapshot()
// ROLE function to get a snapShot of the Webcam
getSnapshot = function() {
    var width=parseInt($("#content").width()-($("#content").width()*20/100));
    var height=parseInt($("#content").height()-($("#content").height()*20/100));
    $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/get_snapshot.php",
            data: {width: width, height: height},
            succes: function (data) {
                var objJSON = jQuery.parseJSON(data);

            }, error: function (data) {
            } 
    });
}
// }}}



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

    if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", duration: 36000}
        });
    }

    getSnapshot();
});
</script>


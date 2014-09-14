<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>


<?php if(!$compat) { ?>
$(document).ready(
function(){
    $("#compat").fadeIn(3000);
});
<?php } ?>

$(document).ready(function(){
     if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", duration:"1"}
        });
    }

    $("#go_wizard").click(function(e) {
       e.preventDefault();
       get_content("wizard");
    });

    $("#welcome-log-img").click(function(e) {
       e.preventDefault(); 
       get_content("logs");
    });


    $("#welcome-log-txt").click(function(e) {
       e.preventDefault();
       get_content("logs");
    });


    $("#welcome-prog-img").click(function(e) {
       e.preventDefault();
       get_content("programs");
    });


    $("#welcome-prog-txt").click(function(e) {
       e.preventDefault();
       get_content("programs");
    });


    $("#welcome-cal-img").click(function(e) {
       e.preventDefault();
       get_content("calendar");
    });


    $("#welcome-cal-txt").click(function(e) {
       e.preventDefault();
       get_content("calendar");
    });
    
});
</script>


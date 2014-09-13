<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>


session_id="<?php echo session_id(); ?>";

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
            data: {name:"LOAD_LOG", value: "False", session_id:session_id}
        });
    }

    $("#go_wizard").click(function(e) {
       e.preventDefault();
       get_content("wizard",session_id);
    });

    $("#welcome-log-img").click(function(e) {
       e.preventDefault(); 
       get_content("logs",session_id);
    });


    $("#welcome-log-txt").click(function(e) {
       e.preventDefault();
       get_content("logs",session_id);
    });


    $("#welcome-prog-img").click(function(e) {
       e.preventDefault();
       get_content("programs",session_id);
    });


    $("#welcome-prog-txt").click(function(e) {
       e.preventDefault();
       get_content("programs",session_id);
    });


    $("#welcome-cal-img").click(function(e) {
       e.preventDefault();
       get_content("calendar",session_id);
    });


    $("#welcome-cal-txt").click(function(e) {
       e.preventDefault();
       get_content("calendar",session_id);
    });
    
});
</script>


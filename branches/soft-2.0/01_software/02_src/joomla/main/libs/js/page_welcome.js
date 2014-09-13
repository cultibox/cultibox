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
            url: "../../main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", session_id:session_id}
        });
    }
});
</script>


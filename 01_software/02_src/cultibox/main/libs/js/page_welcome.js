
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


<?php if(!$compat) { ?>
$(document).ready(
function(){
    $("#compat").fadeIn(3000);
});
<?php } ?>

function open_wifi_wizard(step) {
    $("#wifi_wizard_step"+step).dialog({
       resizable: false,
       height:150,
       width: 500,
       closeOnEscape: false,
       modal: true,
       dialogClass: "popup_message",
       buttons: [{
           text: CLOSE_button,
           click: function () {
               $( this ).dialog( "close" ); return false;
           }},
           {
           text: PREVIOUS_button,
           click: function () {
               open_wifi_wizard(step-1);
               $( this ).dialog( "close" ); return false;
           }},{
           text: NEXT_button,
           click: function () {
               if(step==2) {
                   $.ajax({
                       cache: false,
                       url: "main/modules/external/get_variable.php",
                       data: {name:"sd_card"}
                  }).done(function (data) {
                      if(jQuery.parseJSON(data) == "") {
                          $("#error_wifi_sd").dialog({
                              resizable: false,
                              height:150,
                              width: 500,
                              closeOnEscape: false,
                              modal: true,
                              dialogClass: "popup_error",
                              buttons: [{
                                text: CLOSE_button,
                                click: function () {
                                    $( this ).dialog( "close" ); return false;
                                }}]
                            });
                      } else {
                          open_wifi_wizard(step+1);
                          $( this ).dialog( "close" ); return false;
                      }
                  });
               } else {
                   open_wifi_wizard(step+1);
                   $( this ).dialog( "close" ); return false;
               }
           }}]
    });
};


<?php if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) { ?>
$(document).ready(function(){
pop_up_add_information("<?php echo __('INFO_UPDATE_CHECKING')."<img src='main/libs/img/waiting_small.gif' />"; ?>","check_version_progress","information");
$.ajax({
    type: "GET",
    url: "main/modules/external/check_update.php",
    async: true,
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
<?php } ?>


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
            data: {name:"LOAD_LOG", value: "False", duration:36000}
        });
    }

     $("#wifi_wizard").click(function(e) {
        e.preventDefault();
        open_wifi_wizard(1);
    });


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

    $("#welcome-synop-img").click(function(e) {
       e.preventDefault();
       get_content("cultipi");
    });


    $("#welcome-synop-txt").click(function(e) {
       e.preventDefault();
       get_content("cultipi");
    });

    
});
</script>


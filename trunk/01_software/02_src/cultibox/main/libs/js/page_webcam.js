
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
var nb_webcam = <?php echo json_encode($GLOBALS['MAX_WEBCAM']); ?>;
var webcam_conf = <?php echo json_encode($webcam_conf); ?>;


// {{{ getSnapshot()
// ROLE function to get a snapShot of the Webcam
getSnapshot = function(first) {
    if(first!=1) $.unblockUI();

    $.ajax({
        beforeSend: function(jqXHR) {
                $.xhrPool.push(jqXHR);
        },
        complete: function(jqXHR) {
            var index = $.xhrPool.indexOf(jqXHR);
            if (index > -1) {
                $.xhrPool.splice(index, 1);
            }
        },
        cache: false,
        async: true,
        url: "main/modules/external/get_snapshot.php"
      }).done(function (data) {
            try {
                var objJSON = jQuery.parseJSON(data);
    
                if(objJSON.length>0) {
                    $.each(objJSON, function(idx, obj) {
                        d = new Date();
                        $("#screen_webcam"+obj).attr("src", "/cultibox/tmp/webcam"+obj+".jpg?"+d.getTime());
                        $("#webcam"+obj).show();
                        $("#error_webcam").css("display","none");
                        $("#div_link_webcam"+obj).show();
                    });
                } else {
                    $("#error_webcam").show();
                    $("#webcam").css("display","none");
                }

                $.timeout.push(setTimeout(function() {
                    getSnapshot(0);
                },5000));
            } catch(err) {
                $.timeout.push(setTimeout(function() {
                    getSnapshot(0);
                },5000));
            }
     });
}
// }}}



$(document).ready(function(){

    pop_up_remove("main_error");
    pop_up_remove("main_info");

    var width=parseInt($("#content").width()-($("#content").width()*20/100));
    var height=parseInt($("#content").height()-($("#content").height()*20/100));

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

    $.blockUI({
        message: "<?php echo __('LOADING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
        centerY: 0,
        css: {
            top: '20%',
            border: 'none',
            padding: '5px',
            backgroundColor: 'grey',
           '-webkit-border-radius': '10px',
           '-moz-border-radius': '10px',
            opacity: .9,
            color: '#fffff'
        }
    });
    
    getSnapshot(1);


    for(i=0;i<nb_webcam;i++) {
        $("#brightness_slider"+i).slider({
            max: 100,
            min: 0,
            slide: function( event, ui ) {
                $("#brightness"+i).val(ui.value);
            },
            step: 1,
            value: webcam_conf[i]['brightness']
        });

        $("#contrast_slider"+i).slider({
            max: 100,
            min: 0,
            slide: function( event, ui ) {
                $("#contrast"+i).val(ui.value);
            },
            step: 1,
            value: webcam_conf[i]['contrast']
        });
    }



    $('a[id^="link_webcam"]').click(function(e) {
        e.preventDefault();
        $("#configure_webcam"+$(this).attr('name')).dialog({
             resizable: false,
             width: 550,
             modal: false,
             closeOnEscape: false,
             dialogClass: "popup_message",
             buttons: [{
                 text: CLOSE_button,
                 "id": "btnClose",
                  click: function () {
                         $(this).dialog('close');
                   }
             }]
        });
    });
});
</script>


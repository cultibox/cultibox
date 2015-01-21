
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
getSnapshot = function(first) {
    if(first!=1) $.unblockUI();

    var width=parseInt($("#content").width()-($("#content").width()*20/100));
    var height=parseInt($("#content").height()-($("#content").height()*20/100));
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
        url: "main/modules/external/get_snapshot.php",
        data: {width: width, height: height}
      }).done(function (data) {
            var objJSON = jQuery.parseJSON(data);

            if(objJSON=="0") {
                d = new Date();
                $("#screen_webcam").attr("src", "/cultibox/tmp/webcam.jpg?"+d.getTime());
                $("#webcam").show();
                $("#error_webcam").css("display","none");

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
                    url: "main/modules/external/get_snapshot_infos.php"
                }).done(function (data) {
                    var objJSON = jQuery.parseJSON(data);
                    if(objJSON!="") {
                        $("#date_creation_webcam").text(objJSON);
                        $("#date_creation_webcam").show();
                    } else {
                        $("#date_creation_webcam").css("display","none");
                    }
                });
            } else {
                $("#error_webcam").show();
                $("#webcam").css("display","none");
            }

            setTimeout(function() {
                getSnapshot(0);
            },5000);
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
});
</script>


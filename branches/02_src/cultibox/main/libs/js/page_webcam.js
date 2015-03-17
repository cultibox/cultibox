
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

var brightness=<?php echo json_encode($brightness) ?>;
var contrast=<?php echo json_encode($contrast) ?>;
var resolution=<?php echo json_encode($resolution) ?>;
var palette=<?php echo json_encode($palette) ?>;



// {{{ getSnapshot()
// ROLE function to get a snapShot of the Webcam
getSnapshot = function(first) {
    if(first!=1) $.unblockUI();

    if(resolution=="-1") {
        var width="";
        var height="";
    } else {
        var resol=resolution.split('x');
        var width=resol[0];
        var height=resol[1];
    }

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
        data: {width: width, height: height,brightness:brightness,contrast:contrast,palette:palette}
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

            $.timeout.push(setTimeout(function() {
                getSnapshot(0);
            },5000));
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



    $("#brightness_slider").slider({
        max: 100,
        min: 0,
        slide: function( event, ui ) {
            $("#brightness").val(ui.value);
        },
        stop: function(event, ui) {
               $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:$("#brightness").val(),
                            variable:"brightness",
                        }
                    }).done(function (data) {
                        brightness=$("#brightness").val();
                        $.unblockUI();
                    });
                }});
        },
        step: 1,
        value: brightness 
    });


    $("#resolution_value").change(function () {
        $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:$("#resolution_value option:selected").val(),
                            variable:"resolution",
                        }
                    }).done(function (data) {
                        resolution=$("#resolution_value option:selected").val()
                        $.unblockUI();
                    });
                }
            });
    });


    $("#palette_value").change(function () {
        $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:$("#palette_value option:selected").val(),
                            variable:"palette",
                        }
                    }).done(function (data) {
                        palette=$("#palette_value option:selected").val()
                        $.unblockUI();
                    });
                }
        });
    });



    $("#contrast_slider").slider({
        max: 100,
        min: 0,
        slide: function( event, ui ) {
            $("#contrast").val(ui.value);
        },
        stop: function(event, ui) {
            $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:$("#contrast").val(),
                            variable:"contrast",
                        }
                    }).done(function (data) {
                        contrast=$("#contrast").val();
                        $.unblockUI();
                    });
                }
            });
        },
        step: 1,
        value: contrast
    });


    $("#resolution_box").change(function() {
        if($("#resolution_box").attr('checked')) {
            $("#resolution_option").show();

            $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:"400x300",
                            variable:"resolution",
                        }
                    }).done(function (data) {
                        resolution="400x300";
                        $.unblockUI();
                    });
                }
            });    
        } else {
            $("#resolution_option").css('display', 'none');

              $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:"-1",
                            variable:"resolution",
                        }
                    }).done(function (data) {
                        resolution="-1";
                        $.unblockUI();
                    });
                }
            });

        }
    });


    $("#palette_box").change(function() {
        if($("#palette_box").attr('checked')) {
            $("#palette_option").show();

            $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:"YUYV",
                            variable:"palette",
                        }
                    }).done(function (data) {
                        palette="YUYV";
                        $.unblockUI();
                    });
                }
            });
        } else {
            $("#palette_option").css('display', 'none');

              $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:"-1",
                            variable:"palette",
                        }
                    }).done(function (data) {
                        palette="-1";
                        $.unblockUI();
                    });
                }
            });

        }
    });





    $("#contrast_box").change(function() {
        if($("#contrast_box").attr('checked')) {
            $("#contrast_slider").show();
            $("#contrast_value").show();


              $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:"0",
                            variable:"contrast",
                        }
                    }).done(function (data) {
                        contrast=0;
                        $.unblockUI();
                    });
                }
            });

        } else {
            $("#contrast_slider").css('display', 'none');
            $("#contrast_value").css('display', 'none');

              $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:"-1",
                            variable:"contrast",
                        }
                    }).done(function (data) {
                        contrast="-1";
                        $.unblockUI();
                    });
                }
            });

        }
    });



    $("#brightness_box").change(function() {
        if($("#brightness_box").attr('checked')) {
            $("#brightness_slider").show();
            $("#brightness_value").show();

              $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:"0",
                            variable:"brightness",
                        }
                    }).done(function (data) {
                        brightness="0";
                        $.unblockUI();
                    });
                }
            });
        } else {
            $("#brightness_slider").css('display', 'none');
            $("#brightness_value").css('display', 'none');

              $.blockUI({
                message: "<?php echo __('SAVING_DATA'); ?>  <img src=\"main/libs/img/waiting_small.gif\" />",
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
                },
                onBlock: function() {
                    $.ajax({
                        type: "GET",
                        cache: false,
                        async: false,
                        url: "main/modules/external/update_webcam.php",
                        data: {
                            value:"-1",
                            variable:"brightness",
                        }
                    }).done(function (data) {
                        brightness="-1";
                        $.unblockUI();
                    });
                }
            });

        }
    });

});
</script>


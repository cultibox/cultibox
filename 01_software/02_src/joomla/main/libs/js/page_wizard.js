<script>
plug_type=<?php echo json_encode($plug_type) ?>;
error_valueJS=<?php echo json_encode($error_value) ?>;
canal_status= <?php echo json_encode($status) ?>;
title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;

$(document).ready(function(){
$('#start_time').timepicker({
    <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
    showSecond: true,
    showOn: 'both',
    buttonImage: "../../main/libs/img/datepicker.png",
    buttonImageOnly: 'true',
    <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT')."',"; ?>
    timeFormat: 'hh:mm:ss',
    <?php echo "timeText: '".__('TIMEPICKER_TIME')."',"; ?>
    <?php echo "hourText: '".__('TIMEPICKER_HOUR')."',"; ?>
    <?php echo "minuteText: '".__('TIMEPICKER_MINUTE')."',"; ?>
    <?php echo "secondText: '".__('TIMEPICKER_SECOND')."',"; ?>
    <?php echo "currentText: '".__('TIMEPICKER_ENDDAY')."',"; ?>
    <?php echo "closeText: '".__('TIMEPICKER_CLOSE')."'"; ?>
});
$('#end_time').timepicker({
    <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
    showOn: 'both',
    showSecond: true,
    buttonImage: "../../main/libs/img/datepicker.png",
    buttonImageOnly: 'true',
    <?php
        echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT')."',";
        echo "timeFormat: 'hh:mm:ss',";
        echo "timeText: '".__('TIMEPICKER_TIME')."',";
        echo "hourText: '".__('TIMEPICKER_HOUR')."',";
        echo "minuteText: '".__('TIMEPICKER_MINUTE')."',";
        echo "secondText: '".__('TIMEPICKER_SECOND')."',";
        echo "currentText: '".__('TIMEPICKER_ENDDAY')."',";
        echo "closeText: '".__('TIMEPICKER_CLOSE')."'";
    ?>
});

$("#value_program").keypress(function(e) {
    if(!VerifNumber(e)) e.preventDefault();
});



    // Check errors for the wizard part:
    $("#finish, #next_plug").click(function(e) {
        e.preventDefault();
        var checked=true;

        $("#error_start_time").css("display","none");
        $("#error_end_time").css("display","none");
        $("#error_same_start").css("display","none");
        $("#error_same_end").css("display","none");
        $("#error_value_program").css("display","none");

        var checked=true;
        $.ajax({
            cache: false,
            async: false,
            url: "../../main/modules/external/check_value.php",
            data: {value:$("#start_time").val(),type:'time'}
        }).done(function (data) {
            if(data!=1) {
                $("#error_start_time").show(700);
                $("#start_time").val("06:00:00");
                checked=false;
            }
        });

        $.ajax({
            cache: false,
            async: false,
            url: "../../main/modules/external/check_value.php",
            data: {value:$("#end_time").val(),type:'time'}
        }).done(function (data) {
            if(data!=1) {
                $("#error_end_time").show(700);
                $("#end_time").val("18:00:00");
                checked=false;
            }
        });


        if(checked) {
            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#start_time").val()+"_"+$("#end_time").val(),type:'same_time'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_same_start").show(700);
                    $("#error_same_end").show(700);
                    checked=false;
                }
            });
        }

        if((plug_type!="lamp") && (plug_type!="other")) {
            if(($("#value_program").val())&&($("#value_program").val()!="0")) {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "../../main/modules/external/check_value.php",
                    data: {value:$("#value_program").val(),type:'value_program',plug_type:plug_type}
                }).done(function (data) {
                    if(data!=1) {
                        $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[data.toInt()]);
                        $("#error_value_program").show(700);
                        switch(plug_type) {
                            case 'dehumidifier':
                            case 'humidifier': $("#value_program").val("55");
                                                break;
                            case 'ventilator':
                            case 'pump':
                            case 'heating':  $("#value_program").val("22");
                                                break;
                       
                            default: break;
                        }
                        checked=false;
                    }
                });
            } else {
                if((plug_type=="heating")||(plug_type=="ventilator")) {
                    var check=3;
                    $("#value_program").val("22");
                } else if((plug_type=="humidifier")||(plug_type=="dehumidifier")) {
                    var check=4;
                    $("#value_program").val("55");
                } else if(plug_type=="pump") {
                    var check=5;
                    $("#value_program").val("22");
                } else {
                    var check=6;
                }

                $("#error_value_program").html("<img src='/cultibox/main/libs/img/arrow_error.png' alt=''>"+error_valueJS[check]);
                $("#error_value_program").show(700);
                checked=false;
            }
        }

        if(checked) {
            if($(this).attr('id')=="finish") {
                $('#type_submit').val("submit_close");
            } else {
                $('#type_submit').val("submit_next");
            }
            document.forms['submit_wizard'].submit();
        }
    });
});
</script>


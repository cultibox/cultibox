<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>


nb_plugs=<?php echo(json_encode($nb_plugs)); ?>;
wifi_ip=<?php echo(json_encode($wifi_ip)); ?>;
type_sensor=<?php echo(json_encode($type_sensor)); ?>;
addr_1000=<?php echo(json_encode($GLOBALS['PLUGA_DEFAULT'])); ?>;
addr_3500=<?php echo(json_encode($GLOBALS['PLUGA_DEFAULT_3500W'])); ?>;
translate=<?php echo(json_encode($translate)); ?>;
title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;
session_id="<?php echo session_id(); ?>";

//Wifi process:
get_plug_type = function(addr) {
    if(addr<0) return 4;

    if(jQuery.inArray(addr,addr_1000)!=-1) {
        return 1;
    } else if(jQuery.inArray(addr,addr_3500)!=-1) {
        return 2;
    } else if((addr>=100)&&(addr<=115)) {
        return 3;
    } 
    return 4;
}

wifi_process = function(time,ip) {
    setTimeout(function(){
    $.ajax({
        type: "GET",
        url: "http://"+ip+"/info.xml",
        dataType: "xml",
        timeout: 3000,
        success: function(xml) {
            var myPlug = [];
            $(xml).find('plug_state').each( function(){
                var num=$(this).find('num').text();
                var value=$(this).find('value').text();
                var plug_address=$(this).find('plug_address').text();
                myPlug.push({
                    num: num,
                    value: value,
                    plug_address: plug_address
                });
            });

            $.each(myPlug, function( index, value ) {
               if((value['num']!="")&&(value['value']!="")) {
                    if(value['value']!=0) {
                        $("#plug_state_on"+value['num']).show();
                    } else {
                        $("#plug_state_off"+value['num']).show();
                    }
                    $("#plug_state_unk"+value['num']).css("display","none");

                    if(value['plug_address']!="") {
                        var plug_addr=get_plug_type(value['plug_address']);
                        //1 = 1000W:
                        if(plug_addr==1) {
                           $("#plug_type"+value['num']).text("1000W");
                        //2 = 3500W;
                        } else if(plug_addr==2) {
                            $("#plug_type"+value['num']).text("3500W");
                        //3 = DIMMER;
                        } else if(plug_addr==3) {
                            $("#plug_type"+value['num']).text(translate[1]);
                        //Unknown:
                        } else {
                            $("#plug_type"+value['num']).text(translate[0]);
                        }
                        $("#plug_type"+value['num']).css("font-weight","bold");
                    }
               }
            });

            var mySensor = [];
            $(xml).find('sensor').each( function(){
                var num=$(this).find('num').text();
                var type=$(this).find('type').text();
                var value1=$(this).find('value1').text();
                var value2=$(this).find('value2').text();
                var date=$(this).find('date').text();
                mySensor.push({
                    num: num,
                    type: type,
                    value1: value1,
                    value2: value2,
                    date: date
                });
            });


             $.each(mySensor, function( index, value ) {
                if((value['num']!="")&&(value['type']!="")&&(value['type']>1)&&(value['type']!=4)) {

                    var unity="";
                    switch (value['type']) {
                        case '3': unity="°C";
                                    break;
                        case '5': unity="cm";
                                    break;
                        case '6': unity="cm";
                                    break;
                        default: unity="";
                    }

                    $("#type_sensor"+value['num']).text(type_sensor[value['type']]);
                    $("#type_sensor"+value['num']).css('font-weight', 'bold');

                    if((value['value1']!="")&&(value['value1']!="0")) {
                        if(value['type']!="2") {
                            $("#sensor_value"+value['num']).text(value['value1']+unity);
                        } else {
                            // Temp and humi:
                            if((value['value2']!="")&&(value['value2']!="0")) {
                                $("#sensor_value"+value['num']).text(value['value1']+"°C / "+value['value2']+"%");
                            } else {
                                $("#sensor_value"+value['num']).text("N/A");
                            }
                        }

                        if((value['date']!="")&&(value['date']!="0")) {
                            $("#sensor_date"+value['num']).text("20"+value['date']);
                            $("#sensor_date"+value['num']).css('font-weight', 'bold');
                        }
                     } else {
                         $("#sensor_value"+value['num']).text("N/A");
                         $("#sensor_date"+value['num']).text("");
                     }
                     $("#sensor_value"+value['num']).css('font-weight', 'bold');
               }
            });
        },
            complete: function() {
                wifi_process(10000,ip);
            }
        });
    }, time);
}

$(document).ready(function() {
    if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", session_id:session_id}
        });
    }

    wifi_process(300,wifi_ip);
});

</script>

<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>


submit_cost = <?php echo json_encode($submit_cost) ?>;
nb_plugs = <?php echo json_encode($nb_plugs) ?>;
default_cost=<?php echo json_encode($cost_price) ?>;
default_cost_hp=<?php echo json_encode($cost_price_hp) ?>;
default_cost_hc=<?php echo json_encode($cost_price_hc) ?>;
default_start_hc=<?php echo json_encode($start_hc) ?>;
default_stop_hc=<?php echo json_encode($stop_hc) ?>;
title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;
var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;

compute_cost = function(type,startday, select_plug, nb_jours, count, cost,chart,index,plug) {
        var step=100/count;
        var pourcent=(count-nb_jours)*step;
        if(plug>0) {
            var nb=index+1;
        } else {
            var nb="";
        }

        $.ajax({
              cache: false,
              async: true,
              url: "main/modules/external/compute_cost.php",
              data: {type:type,startday:startday,select_plug:select_plug,cost:cost}
        }).done(function (data) {
              if(!$.isNumeric(data)) {
                        if(type=="theorical") {
                            $("#error_cost_compute_theorical"+nb).show();
                        } else {
                            $("#error_cost_compute_real"+nb).show();
                        }
              } else {
                     $("#progress_bar_cost_"+type+nb).progressbar({value:pourcent});

                     cost=parseFloat(cost)+parseFloat(data);

                     if(nb_jours>1) {
                        var date = new Date( Date.parse(startday));
                        date.setDate(date.getDate()+1);
                        var dateString = (date.getFullYear().toString()+"-"+addZ((date.getMonth() + 1)) + "-" + addZ(date.getDate()));

                        compute_cost(type,dateString, select_plug, nb_jours-1, count, cost,chart,index,plug);
                     } else {
                        $("#progress_bar_cost_"+type+nb).progressbar({value:100});
                        if(type=="theorical") {
                            chart.series[index].data[1].update(Math.round(cost*100)/100);
                            $("#valid_cost_compute_theorical"+nb).show();
                        } else {
                            chart.series[index].data[0].update(Math.round(cost*100)/100);
                            $("#valid_cost_compute_real"+nb).show();
                        }

                        if(($("#valid_cost_compute_theorical").css("display")!="none")&&($("#valid_cost_compute_theorical").css("display")!="none")) {
                            $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        }
                     }
                 }
        });
}


$(document).ready(function() {
     pop_up_remove("main_error");
     pop_up_remove("main_info");
     pop_up_remove("power_status");

    // For each information, show it
    $.each(main_error, function(key, entry) {
            pop_up_add_information(entry,"main_error","error");
    });

    // For each information, show it
    $.each(main_info, function(key, entry) {
            pop_up_add_information(entry,"main_info","information");
    });


    var list_power="";
         if(($("#select_plug option:selected").val()=="all")||($("#select_plug option:selected").val()=="distinct_all")) {
            for(i=1;i<=nb_plugs;i++) {
               if(list_power=="") {
                    list_power=i;
               } else {
                    list_power=list_power+"-"+i;
               }
            }
        } else {
            list_power=$("#select_plug option:selected").val();
        }

         $.ajax({
               data:{ list_power:list_power },
               url: 'main/modules/external/check_configuration_power.php'}).done(function(data) {
                   if(jQuery.parseJSON(data)!="") {
                        pop_up_remove("power_status");
                        pop_up_add_information(jQuery.parseJSON(data),"power_status","error");
                   } else {
                       pop_up_remove("power_status");
                   }
      });


     $("#select_plug").change(function() {
       pop_up_remove("power_status");
       var list_power="";
         if(($("#select_plug option:selected").val()=="all")||($("#select_plug option:selected").val()=="distinct_all")) {
            for(i=1;i<=nb_plugs;i++) {
               if(list_power=="") {
                    list_power=i;
               } else {
                    list_power=list_power+"-"+i;
               } 
            }
        } else {
            list_power=$("#select_plug option:selected").val();
        }

         $.ajax({
               data:{ list_power:list_power },
               url: 'main/modules/external/check_configuration_power.php'}).done(function(data) {
                   if(jQuery.parseJSON(data)!="") {
                        pop_up_remove("power_status");
                        pop_up_add_information(jQuery.parseJSON(data),"power_status","error");
                   } else {
                       pop_up_remove("power_status");
                   }
      });
    });


     if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", duration: 36000}
        });
    }

   $("#datepicker_start").datepicker({ 
    dateFormat: "yy-mm-dd",
    showButtonPanel: true,
    showOn: "both",
    buttonImage: "main/libs/img/datepicker.png",
    buttonImageOnly: 'true',
    <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
   }).val()


   $("#datepicker_end").datepicker({ 
    dateFormat: "yy-mm-dd",
    showButtonPanel: true,
    showOn: "both",
    buttonImage: "main/libs/img/datepicker.png",
    buttonImageOnly: 'true',
    <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
   }).val()


   $('#start_hc').timepicker({
    <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
    showOn: 'both',
    buttonImage: "main/libs/img/datepicker.png",
    buttonImageOnly: 'true',
    <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT')."',"; ?>
    timeFormat: 'hh:mm',
    <?php echo "timeText: '".__('TIMEPICKER_TIME')."',"; ?>
    <?php echo "hourText: '".__('TIMEPICKER_HOUR')."',"; ?>
    <?php echo "minuteText: '".__('TIMEPICKER_MINUTE')."',"; ?>
    <?php echo "secondText: '".__('TIMEPICKER_SECOND')."',"; ?>
    <?php echo "currentText: '".__('TIMEPICKER_ENDDAY')."',"; ?>
    <?php echo "closeText: '".__('TIMEPICKER_CLOSE')."',"; ?>
   });


   $('#stop_hc').timepicker({
    <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
    showOn: 'both',
    buttonImage: "main/libs/img/datepicker.png",
    buttonImageOnly: 'true',
    <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT')."',"; ?>
    timeFormat: 'hh:mm',
    <?php echo "timeText: '".__('TIMEPICKER_TIME')."',"; ?>
    <?php echo "hourText: '".__('TIMEPICKER_HOUR')."',"; ?>
    <?php echo "minuteText: '".__('TIMEPICKER_MINUTE')."',"; ?>
    <?php echo "secondText: '".__('TIMEPICKER_SECOND')."',"; ?>
    <?php echo "currentText: '".__('TIMEPICKER_ENDDAY')."',"; ?>
    <?php echo "closeText: '".__('TIMEPICKER_CLOSE')."',"; ?>
   });


   chart = new Highcharts.Chart({
      chart: {
         backgroundColor: '#F7F7F9',
         renderTo: 'container',
         borderWidth: 3,
         borderColor: '#959891',
         borderRadius: 20,
         spacingRight: 20,
         type: 'column'
      },
      title: {
         useHTML: true,
         text: <?php if(strcmp("$startday","$endday")!=0) { echo "'".__('COST_PRICE_COMPUTE_GRAPH')." ".__('COST_PRICE_TIME')." ".$startday." ".__('COST_PRICE_TO')." ".$endday."'"; } else {
                    echo "'".__('COST_PRICE_COMPUTE_GRAPH')." ".__('COST_PRICE_FOR_DAY')." ".$startday."'"; } ?>
      },
      subtitle: {
         text: ''
      },
      lang: {
            useHTML: true,
            printChart: <?php echo "'".__('PRINT_HIGHCHART')."',"; ?>
            exportButtonTitle: <?php echo "'".__('EXPORT_HIGHCHART')."',"; ?>
            downloadPNG : <?php echo "'".__('EXPORT_PNG')."',"; ?>
            downloadJPEG : <?php echo "'".__('EXPORT_JPEG')."',"; ?>
            downloadPDF : <?php echo "'".__('EXPORT_PDF')."',"; ?>
            downloadSVG : <?php echo "'".__('EXPORT_SVG')."',"; ?>
            resetZoom : <?php echo "'".__('RESET_ZOOM_TITLE')."',"; ?>
            resetZoomTitle : <?php echo "'".__('RESET_ZOOM_TITLE')."'"; ?>,
            contextButtonTitle: <?php echo "'".__('CONTEXT_MENU')."'"; ?>
      },
      xAxis: {
         categories: [
            '<?php echo __('REAL_PRICE'); ?>',
            '<?php echo __('THEORICAL_PRICE'); ?>'
         ],
     labels: {
        useHTML: true
         },
      },
      yAxis: {
         min: 0,
         title: {
            useHTML: true,
            text: '<?php echo __('INSTALLATION_PRICE'); ?>'
         }
      },
      credits: {
            enabled: false
      },
      tooltip: {
         useHTML: true,
         formatter: function() {
            return ''+ this.y +'<?php if(strcmp("$lang","en_GB")!=0) { echo "€"; } else { echo "£"; } ?>';
         }
      },
      legend: {
        useHTML: true
      },
      plotOptions: {
         column: {
            pointPadding: 0.2,
            borderWidth: 0
         }
      },
        series: [
        <?php foreach($data_price as $data) { ?>
         {
        useHTML: true,
                name: <?php echo "'".html_entity_decode($data['title'])."'," ?>
                color: <?php echo "'".$data['color']."'"; ?>,
                data: [<?php echo $data['real']; ?>,<?php echo $data['theorical']; ?>]
                },
         <?php } ?> 
      ]
   });


   if((typeof(submit_cost)!='undefined')&&(submit_cost)) {
        $("#progress_cost").dialog({
            resizable: false,
            <?php if(strcmp($select_plug,"distinct_all")==0) { ?>
                width: 750,
            <?php } else { ?> 
                width: 550,
            <?php } ?>
            modal: true,
            <?php if(strcmp($select_plug,"distinct_all")==0) { ?>
                position: 'top',
            <?php } ?>
            dialogClass: "popup_message",
            buttons: [{
                    text: CANCEL_button,
                    "id": "btnClose",
                    click: function () {
                        $( this ).dialog( "close" );  
                        scrolltodiv("anchor-cost"); 
                    }
            }],
            open: function( event, ui ) {
                var myArray = $("#datepicker_start").val().split('-');
                var myArray2 = $("#datepicker_end").val().split('-');
                var Date1 = new Date(myArray[0],myArray[1]-1,myArray[2]);
                var Date2 = new Date(myArray2[0],myArray2[1]-1,myArray2[2]);
                var nb_jours=diffdate(Date1,Date2);

                if($("#select_plug").val()!="distinct_all") {
                    compute_cost("theorical",$("#datepicker_start").val(),$("#select_plug").val(), nb_jours,nb_jours,"0",chart,0,0);
                    compute_cost("real",$("#datepicker_start").val(),$("#select_plug").val(), nb_jours,nb_jours,"0",chart,0,0);
                    $("#progress_bar_cost_theorical").progressbar({value:0});
                    $("#progress_bar_cost_real").progressbar({value:0});
                } else {
                    for(plugs=1;plugs<=nb_plugs;plugs++) {
                        compute_cost("theorical",$("#datepicker_start").val(),plugs, nb_jours,nb_jours,"0",chart,plugs-1,nb_plugs);
                        compute_cost("real",$("#datepicker_start").val(),plugs, nb_jours,nb_jours,"0",chart,plugs-1,nb_plugs);
                    }
                    
                }
            }
        });
    }

  // Check errors for the cost part:
    $("#view-cost").click(function(e) {
        e.preventDefault();
        var checked=true;

        $("#error_start_interval").css("display","none");
        $("#error_start_cost").css("display","none");
        $("#error_end_interval").css("display","none");
        $("#error_end_cost").css("display","none");
        $("#error_cost_price").css("display","none");
        $("#error_cost_price_hc").css("display","none");
        $("#error_cost_price_hp").css("display","none");
        $("#error_start_hc").css("display","none");
        $("#error_stop_hc").css("display","none");

         var list_power="";
         if(($("#select_plug option:selected").val()=="all")||($("#select_plug option:selected").val()=="distinct_all")) {
            for(i=1;i<=nb_plugs;i++) {
               if(list_power=="") {
                    list_power=i;
               } else {
                    list_power=list_power+"-"+i;
               }
            }
        } else {
            list_power=$("#select_plug option:selected").val();
        }

         $.ajax({
               data:{ list_power:list_power },
               url: 'main/modules/external/check_configuration_power.php'}).done(function(data) {
                   if(jQuery.parseJSON(data)!="") {
                        pop_up_remove("power_status");
                        pop_up_add_information(jQuery.parseJSON(data),"power_status","error");
                   } else {
                       pop_up_remove("power_status");
                   }
         });


        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/check_value.php",
            data: {value:$("#datepicker_start").val(),type:'date'}
        }).done(function (data) {
            if(data!=1) {
                $("#error_start_cost").show(700);

                var d = new Date();
                var month = d.getMonth()+1;
                var day = d.getDate();

                var output = d.getFullYear() + '-' +
                (month<10 ? '0' : '') + month + '-' +
                (day<10 ? '0' : '') + day;

                $("#datepicker_start").val(output);
                checked=false;
            }
        });

        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/check_value.php",
            data: {value:$("#datepicker_end").val(),type:'date'}
        }).done(function (data) {
            if(data!=1) {
                $("#error_end_cost").show(700);

                var d = new Date();
                var month = d.getMonth()+1;
                var day = d.getDate();

                var output = d.getFullYear() + '-' +
                (month<10 ? '0' : '') + month + '-' +
                (day<10 ? '0' : '') + day;

                $("#datepicker_end").val(output);
                checked=false;
            }
        });

        
         if(checked) {
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#datepicker_start").val()+"_"+$("#datepicker_end").val(),type:'date_interval'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_start_interval").show(700);
                    $("#error_end_interval").show(700);
                    checked=false;
                }
            });
        }


        //For standard configuration:
        if($("#cost_type option:selected").val()=="standard") {
            $("#cost_price").val($("#cost_price").val().replace(",",".")); 
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#cost_price").val(),type:'numeric'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_cost_price").show(700);
                    $("#cost_price").val(default_cost);
                    checked=false;
                }
            });
        } else {
        // For HPC:
            $("#cost_price_hp").val($("#cost_price_hp").val().replace(",","."));
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#cost_price_hp").val(),type:'numeric'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_cost_price_hp").show(700);
                    $("#cost_price_hp").val(default_cost_hp);
                    checked=false;
                }
            });

            $("#cost_price_hc").val($("#cost_price_hc").val().replace(",","."));
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#cost_price_hc").val(),type:'numeric'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_cost_price_hc").show(700);
                    $("#cost_price_hc").val(default_cost_hc);
                    checked=false;
                }
            });

            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#start_hc").val(),type:'short_time'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_start_hc").show(700);
                    $("#start_hc").val(default_start_hc);
                    checked=false;
                }
            });


            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/check_value.php",
                data: {value:$("#stop_hc").val(),type:'short_time'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_stop_hc").show(700);
                    $("#stop_hc").val(default_stop_hc);
                    checked=false;
                }
            });
        }


        if(checked) {
            get_content("cost",getFormInputs('display-cost'));
        }
    });
});

</script>


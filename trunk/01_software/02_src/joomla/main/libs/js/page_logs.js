<script>

<?php if((isset($anchor))&&(!empty($anchor))) { ?>
    $(document).ready(function(){
       $.scrollTo('#<?php echo $anchor; ?>',300); 
    });
<?php } ?>


title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;


delete_logs = function(type, type_reset, nb_jours, start,count) {
        var step=100/count;
        var pourcent=(count-nb_jours)*step;

        $.ajax({
              cache: false,
              url: "../../main/modules/external/delete_logs.php",
              data: {type:type,type_reset:type_reset,start:start}
        }).done(function (data) {
              if(!$.isNumeric(data)) {
                if(type=="logs") {
                    $("#error_delete_logs").show();
                } else {
                    $("#error_delete_log_power").show();
                }
              } else {
                 if(data==1) {
                     if(type=="logs") {
                        $("#progress_bar_delete_logs").progressbar({value:pourcent});  
                     } else {
                        $("#progress_bar_delete_logs_power").progressbar({value:pourcent});
                     }

                     if(nb_jours>1) {
                        var date = new Date(Date.parse(start)); 
                        date.setDate(date.getDate()+1);
                        var dateString = (date.getFullYear().toString()+"-"+addZ((date.getMonth() + 1)) + "-" + addZ(date.getDate()));

                        delete_logs(type,type_reset, nb_jours-1,dateString,count);
                     } else {
                        if(type=="logs") {
                            $("#success_delete_logs").show();                              
                            $("#progress_bar_delete_logs").progressbar({value:100});
                        } else {
                            $("#success_delete_logs_power").show(); 
                            $("#progress_bar_delete_logs_power").progressbar({value:100});
                        }
                     }
                 } else {
                     if(type=="logs") {
                        $("#error_delete_log").show();
                     } else {
                        $("#error_delete_log_power").show();
                    }
                 }
              }
        });
}

loadLog = function(nb_day,pourcent,type,pourcent,search,sd_card) {
            $.ajax({
                cache: false,
                url: "../../main/modules/external/load_log.php",
                data: {nb_day:nb_day, type:type,search:search,sd_card:sd_card}
            }).done(function (data) {
                if(nb_day!=0) {
                    if(type=="power") {
                        $("#progress_bar_load_power").progressbar({ value: parseInt(((pourcent-nb_day)/pourcent)*100) });
                    } else {
                        $("#progress_bar_load").progressbar({ value: parseInt(((pourcent-nb_day)/pourcent)*100) });
                    }

                    if(!$.isNumeric(data)) {
                        if(type=="power") {
                            $("#error_load_power").show();
                        } else {
                            $("#error_load").show();
                        }
                        finished=finished+1;
                        if(finished==2) {
                            $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        }
                        return true;
                    }
                    loadLog(nb_day-1,data,type,pourcent,search,sd_card);
                } else {
                    if(search=="submit") {
                        if(type=="power") {
                            $("#success_load_power").show();
                            $("#progress_bar_load_power").progressbar({ value: 100 });
                        } else {
                            $("#success_load").show();
                            $("#progress_bar_load").progressbar({ value: 100 });
                        }
                        finished=finished+1;
                        if(finished==2) {
                            $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        }
                        return true;
                    } else {
                        if(data==-2) {
                            $("#success_load_still_log").show();
                        } 

                        if(type=="power") {
                                $("#success_load_power_auto").show();
                                $("#progress_bar_load_power").progressbar({ value: 100 });
                        } else {
                                $("#success_load_auto").show();
                                $("#progress_bar_load").progressbar({ value: 100 });
                        }
                        $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        finished=finished+1;
                        if(finished==2) {
                            $("#btnClose").html('<span class="ui-button-text">'+CLOSE_button+'</span>');
                        }
                        return true;
                    }
                } 
            });
}



$(function() {

    $("#datepicker").datepicker({ 
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        showOn: "both",
        buttonImage: "../../main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
    }).val()


     $("#datepicker_from").datepicker({ 
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        showOn: "both",
        buttonImage: "../../main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
    }).val()


    $("#datepicker_to").datepicker({ 
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        showOn: "both",
        buttonImage: "../../main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
    }).val()


    $("#datepicker_from_power").datepicker({ 
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        showOn: "both",
        buttonImage: "../../main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
    }).val()


    $("#datepicker_to_power").datepicker({ 
    dateFormat: "yy-mm-dd",
    showButtonPanel: true,
    showOn: "both",
    buttonImage: "../../main/libs/img/datepicker.png",
    buttonImageOnly: 'true',
    <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
    }).val()


});

var extremes_save = null;

Highcharts.setOptions({
    lang: {
    <?php echo "resetZoom : '".__('RESET_ZOOM_TITLE','highchart')."',"; ?>
    <?php echo "resetZoomTitle : '".__('RESET_ZOOM_TITLE','highchart')."'"; ?>
    }
});

var showzoomX = false;

$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                backgroundColor: '#F7F7F9',
                borderWidth: 3,
                borderColor: '#959891',
                borderRadius: 20,
                renderTo: 'container',
                zoomType: 'xy',
                alignTicks: false,
                spacingRight: 20,
                resetZoomButton: {
                    theme: {
                        display: 'none'
                    }
                },
                events: {
                    load: function() {
                        <?php if($fake_log) echo "this.renderer.image('http://localhost:".$GLOBALS['SOFT_PORT']."/cultibox/main/libs/img/fake_log_".__('LANG').".png', 600, 15, 130, 50)"; ?>
                        <?php if($fake_log)  echo ".add();"; ?>
                    }
                }
            },
            title: {
                margin: 35,
                useHTML: true,
                text: <?php echo "'<b>".__('HISTO_GRAPH')." (".$legend_date.")*</b>'"; ?>,
            },
            subtitle: {
                useHTML: true,
                text: document.ontouchstart === undefined ?
                <?php echo "'".__('DRAG_PLOT')."'"; ?>:
                <?php echo "'".__('DRAG_PLOT')."'"; ?>
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
            scrollbar: {
                enabled: true
            },
        
            rangeSelector: {
                selected: 1
            },
            xAxis: {
                type: 'datetime',
                endOnTick: false,
                showFirstLabel: true,
                <?php if("$type"=="month") { echo "tickInterval: 5 * 24 * 3600 * 1000,"; } ?>
                title: {
                useHTML: true,
                   text: <?php echo "'".__("$xlegend")."'"; ?>
                },
                events: {
                    // Display button for resetting zoom
                    afterSetExtremes: function(event){
                        $('#resetXzoom').show();
                        showzoomX=true;
                    }
                }
            },
            yAxis: [
                <?php $count=1; ?>
                <?php foreach($data_log as $datalog) { ?>
                <?php if($count>1) echo ","; ?>
                {
                    title: {
                        useHTML: true,
                        text: <?php echo "'".clean_highchart_message($datalog['yaxis1_legend'])."'"; ?>,
                        style: {
                            color: <?php echo "'".$datalog['color_record1']."'"; ?>
                        }
                    },
                    startOnTick: false,
                    endOnTick: false, 
                    allowDecimals: true,
                    <?php if($count%2==0) { ?>
                        opposite: true,
                    <?php } ?>
                    events: {
                        afterSetExtremes: function() {
                            // If it's zoomed, display button to unzoom
                            if (this.min <= this.dataMin && this.max >= this.dataMax) {
                                if(chart.resetZoomButton) {
                                    chart.resetZoomButton.hide();
                                }
                            }
                        }
                    },
                    labels: {
                        style: {
                            color: <?php echo "'".$datalog['color_record1']."'"; ?>
                        },
                        formatter: function() {
                            return this.value;
                        }
                    },
                    gridLineWidth: 1,
                    gridLineDashStyle: 'Dot',
                    gridLineColor : <?php echo "'".$datalog['color_grid1']."'"; ?>
                }
                  <?php if(strcmp($datalog['sensor_type'],"2")==0) { 
                        $count=$count+1;
                  ?>
                ,{
                    title: {
                        useHTML: true,
                        text: <?php echo "'".clean_highchart_message($datalog['yaxis2_legend'])."'"; ?>,
                        style: {
                            color: <?php echo "'".$datalog['color_record2']."'"; ?>
                        }
                    },
                    startOnTick: false,
                    endOnTick: false, 
                    allowDecimals: true,
                    <?php if($count%2==0) { ?>
                        opposite: true,
                    <?php } ?>
                    events: {
                        afterSetExtremes: function() {
                        // If it's zoomed, display button to unzoom
                            if (this.min <= this.dataMin && this.max >= this.dataMax) {
                                if(chart.resetZoomButton) {
                                    chart.resetZoomButton.hide();
                                }
                            }
                        }
                    },
                    labels: {
                        style: {
                            color: <?php echo "'".$datalog['color_record2']."'"; ?>
                        },
                        formatter: function() {
                            return this.value;
                        }
                    },
                    gridLineWidth: 1,
                    gridLineDashStyle: 'Dot',
                    gridLineColor : <?php echo "'".$datalog['color_grid2']."'"; ?>
                }
                <?php  
                } 
                    $count=$count+1; 
                } 
                ?>
            ],
            tooltip: {
                useHTML: true,
                formatter: function() {
                    var tab=new Array;

                    var unitArray = {
                        <?php 
                            foreach($data_log as $datalog) { 
                                if((strcmp($datalog['sensor_type'],"POWER")!=0)&&(strcmp($datalog['sensor_type'],"PROGRAM")!=0)) { 
                                    if(strcmp($datalog['sensor_type'],"2")==0) {  
                                        echo "'".clean_highchart_message($datalog['sensor_name_type'][0])." (".__('SENSOR')." ".$datalog['sensor_nb'].")': '°C',"; 
                                        echo "'".clean_highchart_message($datalog['sensor_name_type'][1])." (".__('SENSOR')." ".$datalog['sensor_nb'].")': '%',";
                                    } else { 
                                        echo "'".clean_highchart_message($datalog['sensor_name_type'])." (".__('SENSOR')." ".$datalog['sensor_nb'].")': '".$datalog['unity']."',"; 
                                    } 
                                } else {
                                    echo "'".clean_highchart_message($datalog['sensor_name_type'])."': '".$datalog['unity']."',"; 
                                }
                            } 
                            echo "'".__('HUMI_LEGEND')." (".__('EXAMPLE_LOG').")': '%',";
                            echo "'".__('TEMP_LEGEND')." (".__('EXAMPLE_LOG').")': '%'"; 
                        ?>
                        };
                        
                        // If there is an unit defined
                        index = [this.series.name] in unitArray;
                        
                        if (index != false) {
                            unit = unitArray[this.series.name];
                        } else {
                            unit = "";
                        }

                        // Format hour : Caution / 1 resolve a bug
                        hourYearFormated = Highcharts.dateFormat('%H:%M:%S %Y/%m/%d', this.x / 1);
                        hourFormated = Highcharts.dateFormat('%H:%M:%S', this.x / 1);
                    <?php 
                        if("$type"=="month") { ?>
                            var val="<p align='left'><b><?php echo __('XAXIS_LEGEND_DAY'); ?>:  </b>" + hourYearFormated + "</p>";
                    <?php } else { ?>
                            var val="<p align='left'><b><?php echo __('XAXIS_LEGEND_DAY'); ?>:  </b>" + hourFormated + "</p>"; 
                    <?php } ?>
                        
                        
                    if(unit == 'PROGRAM' || unit == "") {
                        nounity=true; 
                        if(this.y==100) {
                            behaviour='<?php echo __("VALUE_ON"); ?>';
                        } else if(this.y==0){
                            behaviour='<?php echo __("VALUE_OFF"); ?>';
                        } else {
                            behaviour=(this.y)*100/100;
                            behaviour=behaviour.toFixed(1);
                            nounity=false;
                        }
                        <?php
                        $unity="";
                        if((isset($select_plug))&&(!empty($select_plug))) {
                            switch($plugs_infos[$select_plug-1]["PLUG_TYPE"]) {
                                case 'lamp': $unity="";
                                     break;
                                case 'other': $unity="";
                                    break;
                                case 'ventilator': $unity="°C";
                                    break;
                                case 'heating': $unity="°C";
                                    break;
                                case 'pump': $unity="cm";
                                    break;
                                case 'humidifier': $unity="%";
                                    break;
                                case 'dehumidifier': $unity="%";
                                    break;
                            } 
                        } ?>
                        if(!nounity) {
                            var unit="<?php echo $unity; ?>";
                        } else {
                            var unit="";
                        }
                        return val+"<p align='left'><b>"+this.series.name+":</b> "+behaviour+unit+"</p>";
                    } else {
                       return val+"<p align='left'><b>"+this.series.name+":</b> "+this.y+unit+"</p>";
                    }
                }
            },
            legend: {
                width: 450,
                itemWidth: 225,
                useHTML: true,
                    layout: 'horizontal',
                    verticalAlign: 'bottom',
                    navigation: {
                        activeColor: '#3E576F',
                        animation: true,
                        arrowSize: 12,
                        inactiveColor: '#CCC',
                        style: {
                            fontWeight: 'bold',
                            color: '#333',
                            fontSize: '12px'    
                        }
                    }
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                series: {
                    events: {
                        legendItemClick: function () {
                            return false;
                        }
                    }
                },
                area: {
                   fillColor: {
                      linearGradient: { x1: 3, y1: 0, x2: 0, y2: 1}, 
                      stops: [
                          [0, Highcharts.getOptions().colors[0]],
                          [1, 'rgba(2,0,0,0)']
                      ]
                   
                   },
                   lineWidth: 1,
                   marker: {
                      enabled: false,
                      states: {
                          hover: {
                              enabled: true,
                              radius: 5
                          }
                      }
                   },
                   shadow: false,
                   states: {
                      hover: {
                          lineWidth: 1
                      }
                   }
                },
                spline: {
                    lineWidth: 2,
                    states: {
                       hover: {
                          lineWidth: 5
                       }
                    },
                    marker: {
                       enabled: false,
                       states: {
                          hover: {
                             enabled: true,
                             symbol: 'circle',
                             radius: 5,
                             lineWidth: 1
                          }
                       }
                    },
                    pointInterval: 3600000, // one hour
                    pointStart: Date.UTC(2009, 9, 6, 0, 0, 0)
                }
            },
            series: [
                <?php 
                    $count=0;
                    foreach($data_log as $datalog) {
                        if($count>0) 
                            echo ","; 
                ?>
                        {
                        type: <?php echo "'".$datalog['type_graph']."'"; ?>,
                        <?php 
                        if(isset($datalog['record1']) && !empty($datalog['record1']) && strcmp($datalog['record1'],"")!=0) {
                            echo 'showCheckbox: true,';
                        } 
                        ?>

                <?php 
                    if((strcmp($datalog['sensor_type'],"POWER")!=0) && (strcmp($datalog['sensor_type'],"PROGRAM")!=0)) {
                        if(strcmp($datalog['sensor_type'],"2")==0) {  ?>
                            name: <?php echo "'".clean_highchart_message($datalog['sensor_name_type'][0])." (".__('SENSOR')." ".$datalog['sensor_nb'].")'"; ?>,
                  <?php } else { ?>
                        name: <?php echo "'".clean_highchart_message($datalog['sensor_name_type'])." (".__('SENSOR')." ".$datalog['sensor_nb'].")'"; ?>,
                  <?php }
               } else { ?>
                    name: <?php echo "'".clean_highchart_message($datalog['sensor_name_type'])."'"; ?>,
               <?php }
               if(in_array($count,$unselected_graph)) { ?>
               visible: false,
               <?php } ?>
               yAxis: <?php echo $datalog['yaxis_record1']; ?>,
               pointInterval: <?php echo "$next"; ?> * 60 * 1000,
               pointStart: Date.UTC(<?php echo $styear.",".$stmonth.",".$stday; ?>),
                    <?php
                    switch($datalog['color_record1']) {
                        case 'blue': 
                            echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BLUE'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'black': 
                            echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BLACK'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'green': 
                            echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_GREEN'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'red': 
                            echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_RED'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'purple': 
                            echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_PURPLE'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'brown': 
                            echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BROWN'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'yellow': 
                            echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_YELLOW'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'orange':
                            echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_ORANGE'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'pink': 
                            echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_PINK'][$datalog['sensor_nb']-1]."',"; 
                            break;
                    } 
                    ?>   
               selected: true,
               data: [
                  <?php if((isset($datalog['record1']))&&(!empty($datalog['record1']))) echo $datalog['record1']; ?>
               ],
               events: {
                    checkboxClick: function(event) {
                        var series = chart.series[<?php echo $count; ?>];
                        if (series.visible) {
                            var tmp="";
                            tmp=$("input[name='unselected_graph']").val();
                            if(tmp=="") {
                                $("input[name='unselected_graph']").val("<?php echo $count; ?>");
                            } else {
                                $("input[name='unselected_graph']").val(tmp+",<?php echo $count; ?>");
                            }
                            series.hide();
                        } else {
                            series.show();
                            var tmp="";
                            tmp=$("input[name='unselected_graph']").val();
                            if(tmp!="") {
                                var tmp_array=tmp.split(',');
                                tmp_array=jQuery.grep(tmp_array, function(value) {
                                    return value != <?php echo $count; ?>;
                                });
                                $("input[name='unselected_graph']").val(tmp_array.join(","));
                            }
                        }
                    }
               } 
              } 
              <?php if(strcmp($datalog['sensor_type'],"2")==0) { ?>
               , {
               type: <?php echo "'".$datalog['type_graph']."'"; ?>,
               <?php if((isset($datalog["record2"]))&&(!empty($datalog["record2"]))&&(strcmp($datalog["record2"],"")!=0)) { ?>
                showCheckbox: true,
               <?php } ?>
               name: <?php echo "'".clean_highchart_message($datalog['sensor_name_type'][1])." (".__('SENSOR')." ".$datalog["sensor_nb"].")'"; ?>,
               <?php if(in_array($count+1,$unselected_graph)) { ?>
               visible: false,
               <?php } ?>
               pointInterval: <?php echo "$next"; ?> * 60 * 1000,
               pointStart: Date.UTC(<?php echo $styear.",".$stmonth.",".$stday; ?>),
                   <?php switch($datalog['color_record2']) {
                         case 'blue': echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BLUE'][$datalog['sensor_nb']-1]."',"; 
                            break;
                         case 'black': echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BLACK'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'green': echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_GREEN'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'red': echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_RED'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'purple': echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_PURPLE'][$datalog['sensor_nb']-1]."',"; 
                            break;
                         case 'brown': echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_BROWN'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'yellow': echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_YELLOW'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'orange': echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_ORANGE'][$datalog['sensor_nb']-1]."',"; 
                            break;
                        case 'pink': echo "color: '".$GLOBALS['LIST_GRAPHIC_COLOR_SENSOR_PINK'][$datalog['sensor_nb']-1]."',"; 
                            break;
                    } ?>
                    yAxis: <?php echo $datalog['yaxis_record2']; ?>,
                    selected: true,
                    data: [
                      <?php if((isset($datalog['record2']))&&(!empty($datalog['record2']))) echo $datalog['record2']; ?>
                    ],
                    events: {
                        checkboxClick: function(event) {
                            var series = chart.series[<?php echo $count+1; ?>];
                            if (series.visible) {
                                var tmp="";
                                tmp=$("input[name='unselected_graph']").val();
                                if(tmp=="") {
                                    $("input[name='unselected_graph']").val("<?php echo $count+1; ?>");
                                } else {
                                    $("input[name='unselected_graph']").val(tmp+",<?php echo $count+1; ?>");
                                }
                                series.hide();
                            } else {
                                series.show();
                                var tmp="";
                                tmp=$("input[name='unselected_graph']").val();
                                if(tmp!="") {
                                    var tmp_array=tmp.split(',');
                                    tmp_array=jQuery.grep(tmp_array, function(value) {
                                        return value != <?php echo $count+1; ?>;
                                    });
                                    $("input[name='unselected_graph']").val(tmp_array.join(","));
                                }
                            }
                        }
                    }
                } 
              <?php } ?>
                <?php 
                if(strcmp($datalog['sensor_type'],"2")!=0) {
                        $count=$count+1;    
                    } else { 
                        $count=$count+2;
                    }
                } 
                ?>
            ]
        });

    chart.series.each(function (item) {
        if(item.data=="") {
                item.options.showInLegend = false;
                item.legendItem = item.legendItem.destroy();;
                chart.legend.destroyItem(item);
        }
        chart.legend.render();
    });

    <?php foreach($unselected_graph as $i) { ?>
        chart.series[<?php echo $i; ?>].select(false);
    <?php } ?>

     });


    $('#resetXzoom').click(function() {
        if(showzoomX) {
            chart.zoomOut();
            showzoomX=false;
            $('#resetXzoom').css("display","none");
        }

    });
});

$(document).ready(function() {

    var name="load_log";
    $.ajax({
        cache: false,
        async: true,
        url: "../../main/modules/external/get_variable.php",
        data: {name:name}
    }).done(function (data) {
        if(!data) {
            var name="sd_card";
            $.ajax({
                cache: false,
                async: true,
                url: "../../main/modules/external/get_variable.php",
                data: {name:name}
            }).done(function (data) {
                if(data) {
                    $("#progress_load").dialog({
                        resizable: false,
                        width: 550,
                        modal: true,
                        closeOnEscape: false,
                        dialogClass: "popup_message",
                        buttons: [{ 
                            text: CANCEL_button,
                            "id": "btnClose",
                            click: function () {
                                $( this ).dialog("close");
                                $("#error_load_power").css("display","none");
                                $("#error_load").css("display","none");
                                $("#success_load_power_auto").css("display","none");
                                $("#success_load_auto").css("display","none");
                                $("#success_load_still_log").css("display","none");
                                $("#btnClose").html('<span class="ui-button-text">'+CANCEL_button+'</span>');
                                document.forms['display-log-day'].submit();
                            }
                        }]
                    });

                    $("#progress_bar_load").progressbar({value:0});
                    $("#progress_bar_load_power").progressbar({value:0});
                    loadLog("31",0,"logs","31","auto",data);
                    loadLog("31",0,"power","31","auto",data);
                }
            });
        }
    });

    $("#import_log").click(function(e) {
        e.preventDefault();
        $("#progress_load").dialog({
            resizable: false,
            width: 550,
            modal: true,
            closeOnEscape: false,
            dialogClass: "popup_message",
            buttons: [{
                    text: CANCEL_button,
                    "id": "btnClose",
                    click: function () {
                        $( this ).dialog("close");
                        $("#error_load_power").css("display","none");
                        $("#error_load").css("display","none");
                        $("#success_load_power").css("display","none");
                        $("#success_load").css("display","none");
                        $("#success_load_still_log").css("display","none");
                        $("#btnClose").html('<span class="ui-button-text">'+CANCEL_button+'</span>');
                        $("#import_load").val($("#log_search").val());
                        document.forms['display-log-day'].submit();
                    }
            }]

         });
         $("#progress_bar_load").progressbar({value:0});
         $("#progress_bar_load_power").progressbar({value:0});
		 
		 var name="sd_card";
         $.ajax({
            cache: false,
            async: true,
            url: "../../main/modules/external/get_variable.php",
            data: {name:name}
         }).done(function (data) {
            if(data) {
				loadLog($("#log_search").val()*31,0,"logs",$("#log_search").val()*31,"submit",data);
				loadLog($("#log_search").val()*31,0,"power",$("#log_search").val()*31,"submit",data);
			} else {
                $("#error_load_power").show();
                $("#error_load").show();
            }
		});
    });

    
    $("#reset_log_submit").click(function(e) {
        e.preventDefault();
        $("#delete_log_form").dialog({
            resizable: false,
            width: 750,
            modal: true,
            closeOnEscape: false,
            dialogClass: "popup_message",
            buttons: [{
                text: CANCEL_button,
                click: function () {
                    $("#error_delete_logs").css("display","none"); 
                    $("#success_delete_logs").css("display","none"); 
                    $("#error_format_date_logs").css("display","none");
                    $( this ).dialog( "close" ); 
                    return false;
                }}, {
                text: DELETE_button,
                    click: function () {
                        $("#error_format_date_logs").css("display","none");
                        if(((checkFormatDate($("#datepicker_from").val()))&&(checkFormatDate($("#datepicker_to").val()))&&(compareDate($("#datepicker_from").val(),$("#datepicker_to").val())))||($("input:radio[name=check_type_delete]:checked").val()=="all")) {
                            $("#progress_delete_logs").show();
                            $("#progress_bar_delete_logs").progressbar({value:0});

                            var myArray = $("#datepicker_from").val().split('-');
                            var myArray2 = $("#datepicker_to").val().split('-');
                            var Date1 = new Date(myArray[0],myArray[1]-1,myArray[2]);
                            var Date2 = new Date(myArray2[0],myArray2[1]-1,myArray2[2]);

                            if($("input:radio[name=check_type_delete]:checked").val()=="all") {
                                var nb_jours=1;
                            } else {
                                var nb_jours=diffdate(Date1,Date2);
                            }

                            delete_logs("logs",$("input:radio[name=check_type_delete]:checked").val(), nb_jours,$("#datepicker_from").val(),nb_jours);

                            $("#delete_log_form").dialog({ closeOnEscape: false, buttons: [ {
                                        text: CLOSE_button,
                                        click: function() {
                                            $("#error_delete_logs").css("display","none");
                                            $("#success_delete_logs").css("display","none");
                                            $("#error_format_date_logs").css("display","none");
                                            $( this ).dialog( "close" );
                                            document.forms['display-log-day'].submit();
                                            return false;
                          } } ] });
                        } else {
                            $("#error_format_date_logs").css("display","");
                        }
                    }
            }]
        });
    });

    // Check errors for the display logs part:
    $("#display-log-submit").click(function(e) {
        e.preventDefault();
        if($("input:radio[name=type_select]:checked").val()=="day") {
            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#datepicker").val(),type:'date'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_start_days").show(700);
                    var current=$("#datepicker").datepicker('getDate').getFullYear()+"-"+('0'+($("#datepicker").datepicker('getDate').getMonth() + 1)).slice(-2)+"-"+('0'+($("#datepicker").datepicker('getDate').getDate())).slice(-2);
                    $("#datepicker").val(current);
                } else {
                    document.forms['display-log-day'].submit();
                }
            });
        } else {
            $.ajax({
                cache: false,
                async: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#startyear option:selected").val()+"-"+$("#startmonth option:selected").val(),type:'month'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_start_month").show(700);
                } else {
                    document.forms['display-log-month'].submit();
                }
            });
        }
    });
            

    $("#reset_log_power_submit").click(function(e) {
        e.preventDefault();
        $("#delete_log_form_power").dialog({
            resizable: true,
            width: 750,
            modal: true,
            closeOnEscape: false,
            dialogClass: "popup_message",
            buttons: [{
                    text: CANCEL_button,
                    click: function () {
                        $("#error_delete_logs_power").css("display","none");
                        $("#success_delete_logs_power").css("display","none");
                        $("#error_format_date_logs_power").css("display","none");
                        $( this ).dialog( "close" );
                        return false;
                    }}, {
                    text: DELETE_button,
                        click: function () {
                            $("#error_format_date_logs_power").css("display","none");
                            if(((checkFormatDate($("#datepicker_from_power").val()))&&(checkFormatDate($("#datepicker_to_power").val()))&&(compareDate($("#datepicker_from_power").val(),$("#datepicker_to_power").val())))||($("input:radio[name=check_type_delete_power]:checked").val()=="all")) {
                            $("#progress_delete_logs_power").show();
                            $("#progress_bar_delete_logs_power").progressbar({value:0});

                            var myArray = $("#datepicker_from_power").val().split('-');
                            var myArray2 = $("#datepicker_to_power").val().split('-');
                            var Date1 = new Date(myArray[0],myArray[1]-1,myArray[2]);
                            var Date2 = new Date(myArray2[0],myArray2[1]-1,myArray2[2]);

                            if($("input:radio[name=check_type_delete_power]:checked").val()=="all") {
                                var nb_jours=1;
                            } else {
                                    var nb_jours=diffdate(Date1,Date2);
                            }

                            delete_logs("power",$("input:radio[name=check_type_delete_power]:checked").val(),nb_jours,$("#datepicker_from_power").val(),nb_jours);

                            $("#delete_log_form_power").dialog({ closeOnEscape: false, buttons: [ {
                                text: CLOSE_button,
                                 click: function() {
                                    $("#error_delete_logs_power").css("display","none");
                                    $("#success_delete_logs_power").css("display","none");
                                    $("#error_format_date_logs_power").css("display","none");
                                    $( this ).dialog( "close" );
                                    document.forms['display-log-day'].submit();
                                    return false;
                            } } ] });

                            } else {
                                $("#error_format_date_logs_power").css("display","");
                            }
                        }
            }]
         });
    });
        
});

// Function to add a programm on the view
$(document).ready(function() {

    $("#select_plug_program_on_day input[type=checkbox]").click(function() {

        // Init a var on highchart
        var chart = $('#container').highcharts();
        
        var cheBu = $(this);
 
        // If checked
        if ($(this).attr("checked") == "checked") {
            
            // Check if curve already exists
            if (cheBu.attr("serieID") != "") {
                index = cheBu.attr("serieID");
                chart.series[index].show();
            } else {
                // Call logs_get_serie to get programm value
                $.ajax({
                    data:{
                        plug:$(this).attr("plug"),
                        day:1,
                        month:$('input[type=radio][name=type_select]:checked').attr('value'),
                        startDate:$(this).attr("startDate")
                    },
                    url: '../../main/modules/external/logs_get_serie.php',
                    success: function(json) {

                        // Parse result from json
                        var objJSON = jQuery.parseJSON(json);
                            
                    
                        // Init var serie
                        var series = {
                            id: 'series',
                            name: objJSON.name,
                            showCheckbox: true,
                            type: 'area',
                            yAxis: 0,
                            color: '#C18C36', 
                            selected: true,
                            tooltip: {
                                enabled: false
                            },
                            events: {
                                // On click on the check box, show or hide the serie
                                checkboxClick: function(event) {
                                
                                    var chart = $('#container').highcharts();

                                    if (chart.series[this.index].visible) {
                                        chart.series[this.index].hide();
                                    } else {
                                        chart.series[this.index].show();
                                    }
                                }
                            },
                            data: []
                        }

                        // Foreach data add it to serie
                        $.each(objJSON.data, function(date,value) {
                            series.data.push([
                                date,
                                parseFloat(value)
                            ]);
                        });

                        serieID = chart.addSeries(series);
                        
                        cheBu.attr("serieID", serieID.index);

                    },
                    cache: false
                });
            }
            

            
        } else {
            // Un checked
            index = cheBu.attr("serieID");
            chart.series[index].hide();
        }
    });
})


// Function to display curve to show
$(document).ready(function() {
    $("#curve_select_button").click(function(e) {
           e.preventDefault();

           $("#select_curve").dialog({
                resizable: false,
                width: 750,
                closeOnEscape: true,
                dialogClass: "popup_message",
                buttons: [{
                    text: CLOSE_button,
                    "id": "btnClose",
                    click: function () {
                        $( this ).dialog( "close" ); return false;
                    }
                }]
            });
    });
});
</script>
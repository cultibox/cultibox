<script>


title_msgbox=<?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;

// {{{ getType()
// IN  input value: display the type og log: 0 for daily logs, 1 for monthly
// HOW IT WORKS: get id from div to be displayed or not and display it (or not) depending the input value
// USED BY: templates/logs.html  
function getType(i) {
    var divSelectDay = document.getElementById('label_select_day');
    var divSelectMonth = document.getElementById('label_select_month');

    switch(i) {
        case 0 : 
            divSelectDay.style.display = ''; 
            divSelectMonth.style.display = 'none'; 
            break;
        case 1 : divSelectDay.style.display = 'none'; 
            divSelectMonth.style.display = ''; 
            break;
        default: 
            divSelectDay.style.display = ''; 
            divSelectMonth.style.display = 'none'; 
            break;
    }
}
// }}}

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
        if(nb_day != 0) {
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

// Define datepicker
$(function() {

    $("#datepicker, #datepicker_from, #datepicker_to, #datepicker_from_power, #datepicker_to_power").datepicker({ 
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        showOn: "both",
        buttonImage: "../../main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '".__('TIMEPICKER_BUTTON_TEXT_LOG')."',"; ?>
    }).val()

});

Highcharts.setOptions({
    lang: {
    <?php echo "resetZoom : '".__('RESET_ZOOM_TITLE','highchart')."',"; ?>
    <?php echo "resetZoomTitle : '".__('RESET_ZOOM_TITLE','highchart')."'"; ?>
    }
});

var showzoomX = false;
// Global var used to know if fake logs image is displayed
var fakeLogsImageDisplayed = false;

 // This is for all plots, change Date axis to local timezone
Highcharts.setOptions({     
    global : {
        useUTC : false
    }
});

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
                
                        // Init a var on highchart
                        var chart = $('#container').highcharts();
                        
                        // For each selected checkbutton, display the curve
                        $("#select_curve input[type=checkbox], #select_logs_to_display input[type=checkbox], #select_logs_to_display_month input[type=checkbox]").each(function() {

                            var cheBu = $(this);
                            
                            if (cheBu.attr("checked") == "checked") {
                            
                                // Block interface
                                $.blockUI({ message: ''});
                                
                                // Call logs_get_serie to get programm value
                                $.ajax({
                                    data:{
                                        plug:$(this).attr("plug"),
                                        sensor:$(this).attr("sensor"),
                                        day:1,
                                        month:$('input[type=radio][name=type_select]:checked').attr('value'),
                                        datatype:$(this).attr("datatype"),
                                        lang:document.location.href.split('/')[document.location.href.split('/').length - 2],
                                        startDate:$(this).attr("startDate")
                                    },
                                    url: '../../main/modules/external/logs_get_serie.php',
                                    success: function(json) {

                                        // Parse result from json
                                        var objJSON = jQuery.parseJSON(json);
                                            
                                        // Foreach curve add it to serie
                                        $.each(objJSON, function(i, item) {
                                    
                                            // Init var serie
                                            var series = {
                                                id: 'series',
                                                curveType: item.curveType ,
                                                name: item.name,
                                                showCheckbox: true,
                                                type: 'line',
                                                yAxis: item.yaxis ,
                                                color: item.color , 
                                                selected: true,
                                                tooltip: {
                                                    valueSuffix:" " + item.unit
                                                },
                                                events: {
                                                    // On click on the check box, show or hide the serie
                                                    checkboxClick: function(event) {
                                                    
                                                        var chart = $('#container').highcharts();

                                                        if (chart.series[this.index].visible) {
                                                            // Hide the graph
                                                            chart.series[this.index].hide();

                                                            // Hide legend
                                                            chart.yAxis[item.yaxis].update({
                                                                title:{
                                                                    text:""
                                                                }
                                                            });
                                                            
                                                        } else {
                                                            chart.series[this.index].show();
                                                            // Show legend
                                                            chart.yAxis[item.yaxis].update({
                                                                title:{
                                                                    text:item.legend
                                                                }
                                                            });
                                                        }
                                                    }
                                                },
                                                data: []
                                            }

                                            // Foreach data add it to serie
                                            $.each(item.data, function(date,value) {
                                                series.data.push([
                                                    parseFloat(date),
                                                    parseFloat(value)
                                                ]);
                                            });

                                            serieID = chart.addSeries(series);
                                            
                                            // Update legend of yaxis
                                            chart.yAxis[item.yaxis].update({
                                                title:{
                                                    text:item.legend
                                                }
                                            });

                                            // Save serie index displayed
                                            cheBu.attr("serieID" , serieID.index);
                                            cheBu.attr("yAxis" , item.yaxis);
                                            
                                            // Check if Fake Log Image must be displayed
                                            if (fakeLogsImageDisplayed == false)
                                            {
                                                if (item.fake_log != "0")
                                                {
                                                    <?php
                                                    echo "chart.renderer.image('http://localhost:".$GLOBALS['SOFT_PORT']."/cultibox/main/libs/img/fake_log_".__('LANG').".png', 600, 15, 130, 50)";
                                                    echo ".add();";
                                                    ?>
                                                    fakeLogsImageDisplayed = true;
                                                }
                                            }
                                            
                                        });
                                        
                                        // All curve have been rendered : Unblock UI
                                        $.unblockUI();
                                        
                        
                                        // after every curve loaded, update tooltip with min and max
                                        updateTooltipMinMax();

                                    },
                                    cache: false
                                });
                                
                            }
                            
                        });

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
                <?php 
                    // Create every axis available
                    $count = 0;
                    foreach ($yaxis_array as $yaxis)
                    {
                        // Add "," after each axis except the last
                        if ($count != 0)
                        {
                            echo ',';
                        }
                        $count = $count + 1;
                        
                        echo '{' . PHP_EOL;
                        echo '    title: {' . PHP_EOL;
                        echo '        useHTML: true,' . PHP_EOL;
                        // Text is displayed when curve is show
                        echo '        text:"",' . PHP_EOL; 
                        echo '        style: {' . PHP_EOL;
                        echo '            color:"' . $yaxis['color'] . '"' . PHP_EOL;
                        echo '        }' . PHP_EOL;
                        echo '    },' . PHP_EOL;
                        echo '    unit: "' . $yaxis['unit'] . '",' . PHP_EOL;
                        echo '    allowDecimals: true,' . PHP_EOL;
                        if($count % 2 == 0 ) {
                            echo '    opposite: true,' . PHP_EOL;
                        }
                        echo '    events: {' . PHP_EOL;
                        echo '        afterSetExtremes: function() {' . PHP_EOL;
                        // If it's zoomed, display button to unzoom
                        echo '            if (this.min <= this.dataMin && this.max >= this.dataMax) {' . PHP_EOL;
                        echo '                if(chart.resetZoomButton) {' . PHP_EOL;
                        echo '                    chart.resetZoomButton.hide();' . PHP_EOL;
                        echo '                }' . PHP_EOL;
                        echo '            }' . PHP_EOL;
                        echo '        }' . PHP_EOL;
                        echo '    },' . PHP_EOL;
                        echo '    labels: {' . PHP_EOL;
                        echo '        style: {' . PHP_EOL;
                        echo '            color:"' . $yaxis['color'] . '"' . PHP_EOL;
                        echo '        },' . PHP_EOL;
                        echo '        formatter: function() {' . PHP_EOL;
                        echo '            return this.value;' . PHP_EOL;
                        echo '        }' . PHP_EOL;
                        echo '    },' . PHP_EOL;
                        echo '    gridLineWidth: 1,' . PHP_EOL;
                        //echo '    gridLineDashStyle: "Dot",' . PHP_EOL;
                        //echo '    gridLineColor : "' . $yaxis['colorgrid'] . '",' . PHP_EOL;
                        echo '    showEmpty:false,' . PHP_EOL;
                        echo '    tickPositioner: function(min, max) {' . PHP_EOL;
                        // specify an interval for ticks or use max and min to get the interval
                        echo '        if(min == max) {return "";};' . PHP_EOL;
                        
                        // Compute min tick
                        echo 'var nbDizaine = this.dataMin.toString().indexOf(".");' . PHP_EOL;
                        echo 'var mult = Math.pow(10, nbDizaine - 1);' . PHP_EOL;
                        echo 'var dataMin=Math.floor(this.dataMin/mult)*mult;' . PHP_EOL;
                        
                        // Compute max tick
                        echo 'var dataMax=Math.ceil(this.dataMax/mult)*mult;' . PHP_EOL;
                        
                        echo '        var interval = Math.ceil((dataMax-dataMin)/4 * 10)/10;' . PHP_EOL;
                        

                        echo '        var positions = [dataMin];' . PHP_EOL;
                        echo '        for (var i = 1; i < 5; i++) {' . PHP_EOL;
                        echo '                positions.push(Math.ceil((dataMin + i * interval) *100) / 100);' . PHP_EOL;
                        echo '        }' . PHP_EOL;
                        echo '        return positions;' . PHP_EOL;
                        echo '    }' . PHP_EOL;
                        echo '}' . PHP_EOL;
                    }
                ?>
            ],
            tooltip: {
                shared: true,
                useHTML: true,
                formatter: function() {
                    var s = '<p align="center"><b>'+ Highcharts.dateFormat('%Y-%m-%d %H:%M', this.x) +'</b><br />';

                    $.each(this.points, function(i, point) {
                    
                        var valueToTidsplay = point.y;
                    
                        if (point.series.options.curveType == "program")
                        {
                            if (valueToTidsplay == "99.9")
                                valueToTidsplay = "<?php echo __('VALUE_ON') ; ?>";
                            if (valueToTidsplay == "0")
                                valueToTidsplay = "<?php echo __('VALUE_OFF') ; ?>";
                        }
                    
                        s += '<br/><font color="' + point.series.options.color + '">' + point.series.options.name + ' : '+ valueToTidsplay + " " + point.series.options.tooltip.valueSuffix + '</font>';
                    });
                    s=s+"</p>";
                
                    return s;
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
                line: {
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
                    states: {
                        hover: {
                            lineWidth: 1
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
            series: []
        });

        chart.series.each(function (item) {
            if(item.data=="") {
                    item.options.showInLegend = false;
                    item.legendItem = item.legendItem.destroy();;
                    chart.legend.destroyItem(item);
            }
            chart.legend.render();
        });


    });


    $('#resetXzoom').click(function() {
        if(showzoomX) {
            chart.zoomOut();
            showzoomX=false;
            $('#resetXzoom').css("display","none");
        }

    });
});

// Function used at start to load logs
$(document).ready(function() {

    var name="load_log";
    $.ajax({
        cache: false,
        url: "../../main/modules/external/get_variable.php",
        data: {name:name}
    }).done(function (data) {
        if(jQuery.parseJSON(data)!="True") {
            var name="sd_card";
            $.ajax({
                cache: false,
                url: "../../main/modules/external/get_variable.php",
                data: {name:name}
            }).done(function (data) {
                if($.trim(data)!="") {
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
                                document.forms['display-log'].submit();
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
                    document.forms['display-log'].submit();
                }
            }]

         });
         $("#progress_bar_load").progressbar({value:0});
         $("#progress_bar_load_power").progressbar({value:0});
		 
		 var name="sd_card";
         $.ajax({
            cache: false,
            url: "../../main/modules/external/get_variable.php",
            data: {name:name}
         }).done(function (data) {
            if($.trim(data)) {
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

                        $("#delete_log_form").dialog({
                            closeOnEscape: false,
                            buttons: [ {
                                text: CLOSE_button,
                                click: function() {
                                    $("#error_delete_logs").css("display","none");
                                    $("#success_delete_logs").css("display","none");
                                    $("#error_format_date_logs").css("display","none");
                                    $( this ).dialog( "close" );
                                    document.forms['display-log'].submit();
                                    return false;
                                }
                            }] 
                        });
                    } else {
                        $("#error_format_date_logs").css("display","");
                    }
                }
            }]
        });
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

                    $("#delete_log_form_power").dialog({
                        closeOnEscape: false,
                        buttons: [ {
                            text: CLOSE_button,
                            click: function() {
                                $("#error_delete_logs_power").css("display","none");
                                $("#success_delete_logs_power").css("display","none");
                                $("#error_format_date_logs_power").css("display","none");
                                $( this ).dialog( "close" );
                                document.forms['display-log'].submit();
                                return false;
                            }
                        }] 
                    });

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

    $("#select_curve input[type=checkbox], #select_logs_to_display input[type=checkbox], #select_logs_to_display_month input[type=checkbox]").click(function() {

        
        // Init a var on highchart
        var chart = $('#container').highcharts();
        
        var cheBu = $(this);
 
        // If checked
        if (cheBu.attr("checked") == "checked") {

            // Call logs_get_serie to get programm value
            $.ajax({
                data:{
                    plug:$(this).attr("plug"),
                    sensor:$(this).attr("sensor"),
                    day:1,
                    month:$('input[type=radio][name=type_select]:checked').attr('value'),
                    datatype:$(this).attr("datatype"),
                    lang:document.location.href.split('/')[document.location.href.split('/').length - 2],
                    startDate:$(this).attr("startDate")
                },
                url: '../../main/modules/external/logs_get_serie.php',
                success: function(json) {

                    // Parse result from json
                    var objJSON = jQuery.parseJSON(json);
                    var checked=false;
                        
                    // Foreach curve add it to serie
                    $.each(objJSON, function(i, item) {
                        // Init var serie
                        var series = {
                            id: 'series',
                            curveType: item.curveType ,
                            name: item.name,
                            showCheckbox: true,
                            type: 'line',
                            yAxis: item.yaxis ,
                            color: item.color , 
                            selected: true,
                            tooltip: {
                                valueSuffix:" " + item.unit
                            },
                            events: {
                                // On click on the check box, show or hide the serie
                                checkboxClick: function(event) {
                                
                                    var chart = $('#container').highcharts();

                                    if (chart.series[this.index].visible) {
                                        // Hide the graph
                                        chart.series[this.index].hide();
                                        // Hide legend
                                        chart.yAxis[item.yaxis].update({
                                            title:{
                                                text:""
                                            }
                                        });
                                        
                                    } else {
                                        chart.series[this.index].show();
                                        // Show legend
                                        chart.yAxis[item.yaxis].update({
                                            title:{
                                                text:item.legend
                                            }
                                        });
                                    }
                                }
                            },
                            data: []
                        }

                        // Foreach data add it to serie
                        $.each(item.data, function(date,value) {
                            series.data.push([
                                parseFloat(date),
                                parseFloat(value)
                            ]);
                        });

                        serieID = chart.addSeries(series);
                        
                        // Update legend of yaxis
                        chart.yAxis[item.yaxis].update({
                            title:{
                                text:item.legend
                            }
                        });  

                        // Save serie index displayed
                        $(this).attr("serieID" , serieID.index);
                        $(this).attr("yAxis" , item.yaxis);

                        alert($(this).attr("serieID"));
                        if(!checked) {
                            sensor_axis[cheBu.attr("value")]=serieID.index; 
                            checked=true;
                        }
                        

                        // Update tooltip with min and mùax
                        updateTooltipMinMax();
                        
                    });
                },
                cache: false
            });
        } 
        else 
        {

            // HERRRRRREEE
            // Check button is desactived
            //console.log(cheBu.attr("serieID"));
            var temp = cheBu.attr("serieID");
            //alert(cheBu.attr("serieID"));


            if(cheBu.attr("sensortype")==2) {
                 chart.series[sensor_axis[cheBu.attr("value")]+1].remove(false);
            }

            chart.series[sensor_axis[cheBu.attr("value")]].remove(false);
            
            
            // Remove text legend
            /*chart.yAxis[cheBu.attr("yAxis")].update({
                title:{
                    text:""
                }
            });  */

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
            modal: true,
            buttons: [{
                text: CLOSE_button,
                "id": "btnClose_curve",
                click: function () {
                    $( this ).dialog( "close" ); return false;
                }
            }]
        });
    });
});

/**
 * Display parameters UI.
 */
 $(document).ready(function() {
    $("#button_parameters").click(function(e) {
        e.preventDefault();

        // Display parmateres UI
        $("#ui_parameters").dialog({
            resizable: false,
            width: 750,
            closeOnEscape: true,
            modal: true,
            dialogClass: "popup_message",
            buttons: [{
                text: CLOSE_button,
                "id": "btnClose_param",
                click: function () {
                    $( this ).dialog( "close" ); return false;
                }
            }]
        });
    });
});

/**
 * Display database management.
 */
 $(document).ready(function() {
    $("#database_management_button").click(function(e) {
        e.preventDefault();

        // Display parmateres UI
        $("#ui_db_management").dialog({
            resizable: false,
            width: 800,
            closeOnEscape: true,
            dialogClass: "popup_message",
            modal: true,
            buttons: [{
                text: CLOSE_button,
                "id": "btnClose_db",
                click: function () {
                    $( this ).dialog( "close" ); return false;
                }
            }]
        });
    });
});

/**
 * Save parameters when user change colors of graphs.
 */
 $(document).ready(function() {
    // On select change, update conf
    $("#ui_parameters select").each(function() {

        $(this).on('change', function() {
        
            newValue    = $( this ).find(":selected").val();
            varToUpdate = $( this ).attr('name');
            updateConf  = $( this ).attr('update_conf');
            curveTypeModified  = $( this ).attr('curveType');
        
            // Update database
            $.ajax({
                type: "POST",
                cache: false,
                url: "../../main/modules/external/update_configuration.php",
                data: "lang=" + document.location.href.split('/')[document.location.href.split('/').length - 2] + "&value=" + newValue + "&variable=" + varToUpdate + "&updateConf=" + updateConf
            }).done(function (data) {
                // When done, update curve color on page
                var chart = $('#container').highcharts();
                
                // Find all series with this curve type and update color
                $(chart.series).each(function(i, serie){
                    if (serie.options.curveType == curveTypeModified) {
                        serie.graph.attr({stroke: newValue});
                    }
                });
                
                // Change Color legend
                // Create an array with all unit
                <?php 
                $count = 0;
                echo 'var axisArray = {';
                foreach ($yaxis_array as $yaxis)
                {
                    // Add "," after each axis except the last
                    if ($count != 0)
                    {
                        echo ',';
                    }
                    $count = $count + 1;
                    echo $yaxis['curveType'] . ':"' . $yaxis['yaxis'] . '" ';
                }
                echo '};';
                ?>
                
                chart.yAxis[axisArray[curveTypeModified]].update({
                    title:{
                        style:{
                            color:newValue
                        }
                    }
                });
                chart.yAxis[axisArray[curveTypeModified]].update({
                    labels:{
                        style:{
                            color:newValue
                        }
                    }
                });
                                        
                // Change color pointor
                
                // Readraw graph
                chart.redraw();

            });
        });
    });
});

/**
 * On load, display sensors asked
 */
 $(document).ready(function() {
 
    var obj = { 

        attribut: "valeur", 

        attr: function(param) { 
            switch(param) {
                case "plug":
                    return "1";
                    break;
                case "sensor":
                    return "1";
                    break;
                case "datatype":
                    return "logs";
                    break;
                case "startDate":
                    return "2014-06-04";
                    break;                    
            } 
        } 
    } 
    
 });
 
/**
 * After load, when user submit form, add parameters of displayed curve
 */
 $(document).ready(function() {
 
    $("#display-log").submit( function(eventObj) {
    
        // Add for each curve selected
        $("#select_curve input[type=checkbox]").each(function() {

            var cheBu = $(this);
     
            // If checked
            if (cheBu.attr("checked") == "checked") {
            
                if (cheBu.attr("datatype") == "power") {
                    $('<input />').attr('type', 'hidden')
                        .attr('name', "select_power[]")
                        .attr('value', cheBu.attr("plug"))
                        .appendTo('#display-log');
                }
                
                if (cheBu.attr("datatype") == "program") {
                    $('<input />').attr('type', 'hidden')
                        .attr('name', "select_program[]")
                        .attr('value', cheBu.attr("plug"))
                        .appendTo('#display-log');
                }

            }
            
        });
                
            
       if($("input:radio[name=type_select]:checked").val()=="day") {
            $.ajax({
                cache: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#datepicker").val(),type:'date'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_start_days").show(700);
                    var current=$("#datepicker").datepicker('getDate').getFullYear()+"-"+('0'+($("#datepicker").datepicker('getDate').getMonth() + 1)).slice(-2)+"-"+('0'+($("#datepicker").datepicker('getDate').getDate())).slice(-2);
                    $("#datepicker").val(current);
                } else {
                    document.forms['display-log'].submit();
                }
            });
        } else {
            $.ajax({
                cache: false,
                url: "../../main/modules/external/check_value.php",
                data: {value:$("#startyear option:selected").val()+"-"+$("#startmonth option:selected").val(),type:'month'}
            }).done(function (data) {
                if(data!=1) {
                    $("#error_start_month").show(700);
                } else {
                    document.forms['display-log'].submit();
                }
            });
        }

        return true;
    });
    
 });



 //To save selected sensor for previous and next button:
  $(document).ready(function() {
    $("#next").click(function(e) {
        e.preventDefault(); 

        var sensors=Array();
        var programs=Array();
        var powers=Array();
    
        $('input[type=checkbox][name^=select_sensor]').each(function() {
            if($(this).prop('checked')) {
                sensors.push($(this).val());
            }
        });

        $('input[type=hidden][name^=select_sensor]').each(function() {
            $(this).val(sensors);
        });



        $('input[type=checkbox][name^=select_program]').each(function() {
            if($(this).prop('checked')) {
                programs.push($(this).val());
            }
        });

        $('input[type=hidden][name^=select_program]').each(function() {
            $(this).val(programs);
        });


        
        $('input[type=checkbox][name^=select_power]').each(function() {
            if($(this).prop('checked')) {
                powers.push($(this).val());
            }
        });

        $('input[type=hidden][name^=select_power]').each(function() {
            $(this).val(powers);
        });

        document.forms['next'].submit();
    });



    $("#previous").click(function(e) {
        e.preventDefault();

        var sensors=Array();
        var programs=Array();
        var powers=Array();

        $('input[type=checkbox][name^=select_sensor]').each(function() {
            if($(this).prop('checked')) {
                sensors.push($(this).val());
            }
        });

        $('input[type=hidden][name^=select_sensor]').each(function() {
            $(this).val(sensors);
        });



        $('input[type=checkbox][name^=select_program]').each(function() {
            if($(this).prop('checked')) {
                programs.push($(this).val());
            }
        });

        $('input[type=hidden][name^=select_program]').each(function() {
            $(this).val(programs);
        });


        
        $('input[type=checkbox][name^=select_power]').each(function() {
            if($(this).prop('checked')) {
                powers.push($(this).val());
            }
        });

        $('input[type=hidden][name^=select_power]').each(function() {
            $(this).val(powers);
        });

        document.forms['previous'].submit();
    });
 });

 
 // Folowinbg code is used to update tooltip with second regul and min max
 var second_regul = "<?php echo $resume_regul ;?>";
 
 function updateTooltipMinMax () {
 
    var chart = $('#container').highcharts();
    
    var beforeText = "<p align='center'><b><i><?php echo __('SUMARY_RESUME_MINMAX') ; ?>:<br /></i></b></p>";
    var afterText = "<br />";
    var textToDisplay = "";
    
    for (var i = 0; i < 10; i++) {
        // If this curve is used
        if (chart.yAxis[i].userOptions.title.text != "")
        {
            extremes = chart.yAxis[i].getExtremes();
            
            // Sensor informations
            textToDisplay += "<center><b><i>" ;
            textToDisplay += "<font color='"+chart.yAxis[i].options.title.style.color+"'>"+chart.yAxis[i].userOptions.title.text + " : </font></i></b><br />";
            
            textToDisplay += " <?php echo __('SUMARY_MIN'); ?> : ";
            textToDisplay +=    "<b>" + extremes.dataMin + " " + chart.yAxis[i].userOptions.unit + "</b>";
            textToDisplay += " - <?php echo __('SUMARY_MAX'); ?> : ";
            textToDisplay +=    "<b>" + extremes.dataMax + " " + chart.yAxis[i].userOptions.unit + "</b>";
            
            textToDisplay += "</center>";
            textToDisplay += "<br />";

        }
    
    }

    $("#regul_and_minmax_summary").attr("title",second_regul + beforeText + textToDisplay + afterText);

 }
 
 
</script>

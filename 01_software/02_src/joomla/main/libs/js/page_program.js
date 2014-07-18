<script>

plugs_infoJS   = <?php echo json_encode($plugs_infos) ?>;
highchart_plug = <?php echo $selected_plug; ?>;
resume_plugs   = <?php echo json_encode($resume) ?>;
resume_regul   = <?php echo json_encode($resume_regul) ?>;
start          = <?php echo json_encode($start) ?>;
end            = <?php echo json_encode($end) ?>;
plug_selected  = <?php echo json_encode($selected_plug) ?>;
rep            = <?php echo json_encode($rep) ?>;
error_valueJS  = <?php echo json_encode($error_value) ?>;


$(document).ready(function(){

    // Time pickers definition
    $('#start_time , #end_time , #repeat_time , #cyclic_duration , #start_time_cyclic , #end_time_cyclic').timepicker({
        <?php echo "timeOnlyTitle: '".__('TIMEPICKER_SELECT_TIME')."',"; ?>
        showSecond: true,
        showOn: 'both',
        buttonImage: "../../main/libs/img/datepicker.png",
        buttonImageOnly: 'true',
        <?php echo "buttonText: '"  . __('TIMEPICKER_BUTTON_TEXT')."',"; ?>
        timeFormat: 'hh:mm:ss',
        <?php echo "timeText: '"    . __('TIMEPICKER_TIME')."',"; ?>
        <?php echo "hourText: '"    . __('TIMEPICKER_HOUR')."',"; ?>
        <?php echo "minuteText: '"  . __('TIMEPICKER_MINUTE')."',"; ?>
        <?php echo "secondText: '"  . __('TIMEPICKER_SECOND')."',"; ?>
        <?php echo "currentText: '" . __('TIMEPICKER_ENDDAY')."',"; ?>
        <?php echo "closeText: '"   . __('TIMEPICKER_CLOSE')."',"; ?>
    });

    $("#value_program").keypress(function(e) {
        if(!VerifNumber(e)) e.preventDefault();
    });

        
    // Display and control user form for settings
    $("#program_settings").click(function(e) {
        e.preventDefault();

        $("#manage_program").dialog({
            resizable: false,
            width: 800,
            modal: true,
            closeOnEscape: true,
            dialogClass: "popup_message",
            buttons: [{
                text: CLOSE_button,
                "id": "btnClose",
                click: function () {
                    $( this ).dialog( "close" ); return false;
                }
            }],
        });
    });


    // Action when user decide to delete a program
    $("#daily_delete_button").click(function(e) {
        e.preventDefault();

        $.ajax({
           cache: false,
           type: "POST",
           url: "../../main/modules/external/daily_program_delete.php",
           data: "lang=" + document.location.href.split('/')[document.location.href.split('/').length - 2] + "&program_delete_index=" + $("#program_delete_index option:selected").val()
        }).done(function (data) {
              if($.trim(data)=="") { 
                    // Display dialog bow to alert user
                    $("#dialog-form-delete-daily").dialog({
                        resizable: false,
                        closeOnEscape: true,
                        dialogClass: "popup_message",
                        modal: true,
                        buttons: [{
                            text: CLOSE_button,
                            "id": "btnClose",
                            click: function () {
                                $( this ).dialog( "close" ); return false;
                            }
                        }],
                    });
        
                    // If it's the same program, redraw the page
                    if ($( "#program_delete_index" ).val() == <?php echo $program_index ?>) {
                        window.location = "program-"+slang;
                    }
            
                    // remove program from available
                    idToDelete = $( "#program_delete_index" ).val()
                    $("#program_delete_index option[value='" + idToDelete + "']").remove();
                    $("#program_index_id_id option[value='" + idToDelete + "']").remove();

                    if($("#program_delete_index option").length==0) {
                            $("#program_delete_index" ).css("display","none");
                            $("#daily_delete_button").attr("disabled", "disabled");
                            $("#daily_delete_button").addClass("inputDisable");
                            $("#daily_delete_button").val("<?php echo __('NO_DAILY_PROGRAM_TO_DELETE','html'); ?>");
                    }
            } else {

                    $("#dialog-form-delete-daily-error").dialog({
                        resizable: false,
                        closeOnEscape: true,
                        dialogClass: "popup_error_message",
                        modal: true,
                        buttons: [{
                            text: CLOSE_button,
                            "id": "btnClose",
                            click: function () {
                                $( this ).dialog( "close" ); return false;
                            }
                        }],
                   });
            }
        });
        
    });    
    
    var program_name = $( "#program_name" );
    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            return false;
        } else {
            return true;
        }
    }

    $( "#dialog-form-save-daily" ).dialog({
        autoOpen: false,
        height: 180,
        width: 400,
        modal: true,
        buttons: {
        "Enregistrer": function() {
            var bValid = true;
            program_name.removeClass( "ui-state-error" );
            bValid = bValid && checkLength( program_name, "program_name", 3, 16 );
            if ( bValid ) {
                $.ajax({ 
                    type: "POST",
                    url: "../../main/modules/external/daily_program_save.php",
                    // Lang is the end of the url
                    data: "lang=" + document.location.href.split('/')[document.location.href.split('/').length - 2] + "&name=" + $('#program_name').val() + "&input=1&version=1.0",
                    context: document.body,
                    success: function(data, textStatus, jqXHR) {
                        // Check response from server
                        var return_array = JSON.parse(data);
                        
                        // Add program in select
                        $('#program_delete_index').append('<option value="' + return_array.id + '">' + return_array.name + '</option>');
                        $('#program_index_id_id').append('<option value="' + return_array.id + '">' + return_array.name + '</option>');

                        $("#daily_delete_button").removeAttr('disabled');
                        $("#daily_delete_button").removeClass("inputDisable");
                        $("#daily_delete_button").val("<?php echo __('PROGRAM_DAILY_DELETE','html'); ?>");
                        $("#program_delete_index").show();

                        // Prevent user that's ok
                        $("#dialog-form-copy-daily").dialog({
                            resizable: false,
                            closeOnEscape: true,
                            dialogClass: "popup_message",
                            modal: true,
                            buttons: [{
                                text: CLOSE_button,
                                "id": "btnClose",
                                click: function () {
                                    $( this ).dialog( "close" ); return false;
                                }
                            }],
                        });
                        
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Error during request
                    }
                });
                
                $( this ).dialog( "close" );
            }
        },
        "Quitter": function() {
            $( this ).dialog( "close" );
        }},
        close: function() {
            program_name.val( "" ).removeClass( "ui-state-error" );
        }
    });

    $( "#daily_save_button" )
        .button()
        .click(function() {
        $( "#dialog-form-save-daily" ).dialog( "open" );
        });
        
        
});


Highcharts.setOptions({
    lang: {
        <?php echo "resetZoom : '".__('RESET_ZOOM_TITLE','highchart')."',"; ?>
        <?php echo "resetZoomTitle : '".__('RESET_ZOOM_TITLE','highchart')."'"; ?>
    }
});

var enabled = false;
var showzoomX=false;
var chart;
$(document).ready(function() {
   chart = new Highcharts.Chart({
      chart: {
         backgroundColor: '#F7F7F9',
         borderWidth: 3,
         borderColor: '#959891',
         borderRadius: 20,
         spacingRight: 35,
         renderTo: 'plug',
         type: 'spline',
         zoomType: 'x'
      },
      title: {
        useHTML: true,
        text: <?php echo "'".__('CURRENT_PLUG_PROGRAM').": '"; ?>,
        margin: 40
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
      legend: {
            layout: 'vertical',
            verticalAlign: 'bottom',
            align: 'right',
            y: -<?php echo ($GLOBALS['NB_MAX_PLUG']-count($active_plugs))*16; ?>,
            x: +25,
            maxHeight: 200,
            navigation: {
                activeColor: '#3E576F',
                animation: true,
                arrowSize: 10,
                inactiveColor: '#CCC',
                style: {
                    fontWeight: 'bold',
                    color: '#333',
                    fontSize: '10px'    
                }
            }
         },
        xAxis: {
         type: 'datetime',
         endOnTick: true,
         showFirstLabel: false,
         showLastLabel: true,
         dateTimeLabelFormats: { // don't display the dummy year
            hours: '%H',
            minutes: '%M',
            seconds: '%S'
         },
         min: 0,
         max: 86399999,
         tickInterval: 6 * 3600 * 1000,
         labels: {
            formatter: function () {
                var tmp=Highcharts.dateFormat('%H:%M', this.value);
                if(tmp=="00:00") return "24:00";
                return tmp;
            }
         },
         events: {
                afterSetExtremes: function(event){
                    $('#resetXzoom').show();
                    showzoomX=true;
                    if(chart.resetZoomButton) {
                            chart.resetZoomButton.hide();
                    }
            }
        }
      },
      yAxis: {
            min: -1,
            max: 100,
            startOnTick: false,
            endOnTick: false,
            labels: {
                     useHTML: true,
                     formatter: function() {
    if((this.value>=0)&&(this.value<=100)) {      
               <?php if((strcmp($plugs_infos[$selected_plug-1]["PLUG_TYPE"],"lamp")==0)||(strcmp($plugs_infos[$selected_plug-1]["PLUG_TYPE"],"other")==0)) { ?>
               if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
               if(this.value==100) return '<?php echo __("VALUE_ON"); ?>';
<?php } else if((strcmp($plugs_infos[$selected_plug-1]["PLUG_TYPE"],"ventilator")==0)||(strcmp($plugs_infos[$selected_plug-1]["PLUG_TYPE"],"heating")==0)) { ?>
               if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
               if(this.value>0) return this.value+'°C';
               if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
<?php } else if((strcmp($plugs_infos[$selected_plug-1]["PLUG_TYPE"],"pump")==0)) { ?>
               if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
               if(this.value>0) return this.value+' cm';
               if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
<?php } else if((strcmp($plugs_infos[$selected_plug-1]["PLUG_TYPE"],"humidifier")==0)||(strcmp($plugs_infos[$selected_plug-1]["PLUG_TYPE"],"dehumidifier")==0)) { ?>
               if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
               if(this.value>0) return this.value+'%';
               if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';

<?php } else { ?>
               return this.value;

<?php } ?>
            }
                     }
              },
         title: false
      },
      plotOptions: {
            series: {
                events: {
                    legendItemClick: function(event) {
                            if(this.index==$('#selected_plug').val()-1) {
                                return false;
                            }
                    }
                },
                marker : {
                    lineWidth : 2,
                    radius : 6,
                    symbol : 'circle'
                },
                dataLabels: {
                    enabled: false,
                    borderRadius: 5,
                    backgroundColor: 'rgba(252, 255, 197, 0.7)',
                    borderWidth: 1,
                    borderColor: '#AAA',
                    y: -8,
                    x: +20,
                    useHTML: true,
                    formatter: function(){ 
                        if (this.visible) {                      
                            if(this.y=="99.9") { return "<?php echo __('CHART_FORCE_ON_VALUE'); ?>"; }
                            else if(this.y=="0") { return "<?php echo __('VALUE_OFF'); ?>"; }
                            else { 
                                var unity="";
                                if((plugs_infoJS[this.series.index]["PLUG_TYPE"]=="heating")||(plugs_infoJS[this.series.index]["PLUG_TYPE"]=="ventilator")) {
                                    unity="°C";
                                } else if((plugs_infoJS[this.series.index]["PLUG_TYPE"]=="humidifier")||(plugs_infoJS[this.series.index]["PLUG_TYPE"]=="dehumidifier")) {
                                    unity="%";
                                } else if((plugs_infoJS[this.series.index]["PLUG_TYPE"]=="pump")) {
                                    unity=" cm"; 
                                }
                                return "<?php echo __('VALUE_REGUL'); ?>: "+this.y+unity;
                            }
                        }
                    }
                }
            }
      },
      credits: {
                   enabled: false
      },
      tooltip: {
         useHTML: true,
         formatter: function() {
            if(this.y == 99.9) {
                return "<p align='left'><b><?php echo __('XAXIS_LEGEND_DAY'); ?>:  </b>"+Highcharts.dateFormat('%H:%M:%S', this.x) +"<br /><b><?php echo __('BEHAVIOUR'); ?>: </b><?php echo __("CHART_FORCE_ON_VALUE"); ?></p><br />"+resume_regul[this.series.index];
            } else if(this.y == 0) {
                return "<p align='left'><b><?php echo __('XAXIS_LEGEND_DAY'); ?>:  </b>"+Highcharts.dateFormat('%H:%M:%S', this.x) +"<br /><b><?php echo __('BEHAVIOUR'); ?>: </b><?php echo __("VALUE_OFF"); ?></p><br />"+resume_regul[this.series.index];
            } else { 
                var unity="";
                if((plugs_infoJS[this.series.index]["PLUG_TYPE"]=="heating")||(plugs_infoJS[this.series.index]["PLUG_TYPE"]=="ventilator")) {
                    unity="°C";
                } else if((plugs_infoJS[this.series.index]["PLUG_TYPE"]=="humidifier")||(plugs_infoJS[this.series.index]["PLUG_TYPE"]=="dehumidifier")) {
                    unity="%";
                } else if((plugs_infoJS[this.series.index]["PLUG_TYPE"]=="pump")) {
                    unity=" cm";
                }
                return "<p align='left'><b><?php echo __('XAXIS_LEGEND_DAY'); ?>:  </b>"+Highcharts.dateFormat('%H:%M:%S', this.x) +"<br /><b><?php echo __('BEHAVIOUR'); ?>: </b>"+this.y+unity+" (<?php echo __('REGULATION'); ?>)</p><br />"+resume_regul[this.series.index];
            } 
         }
      },
      series: [
            <?php 
            $count=0;
            foreach($plugs_infos as $plugs) {  
            ?>

        {
         <?php if($plugs['id']==$selected_plug) { ?>
            name: <?php echo "'<b>".clean_highchart_message($plugs_infos[$plugs['id']-1]['PLUG_NAME'])." (".clean_highchart_message($plugs_infos[$plugs['id']-1]['translate']).")</b>',"; ?>
         <?php } else { ?>
            name: <?php echo "'".clean_highchart_message($plugs_infos[$plugs['id']-1]['PLUG_NAME'])." (".clean_highchart_message($plugs_infos[$plugs['id']-1]['translate']).")',"; ?>
         <?php } ?>
         color: <?php echo "'".$GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'][$plugs['id']-1]."'"; ?>,
         showCheckbox: false,
         
         <?php if($plugs['id']!=$selected_plug) { echo "visible: false,"; } else { echo "selected: true,"; } ?>
         data: [
            <?php echo $plugs_infos[$plugs['id']-1]["data"]; ?>
         ]
         ,events: {
                    checkboxClick: function(event) {
                        var series = chart.series[<?php echo $count; ?>];
                        if (series.visible) {
                            series.hide();
                        } else {
                            series.show();
                        }
                    }
               }
      }
     <?php 
     $count=$count+1;
     if($count!=count($plugs_infos)) echo ","; 
    } ?>
] 
   });

    $('#selected_plug').change(function() {
        $("#error_value_program").css("display","none");
        $.each( chart.series, function() {
            if(this.index==$('#selected_plug').val()-1) {
                chart.series[$('#selected_plug').val()-1].show();
                chart.series[$('#selected_plug').val()-1].select(); 
                chart.series[$('#selected_plug').val()-1].name = "<b>"+clean_highchart_message(plugs_infoJS[$('#selected_plug').val()-1]['PLUG_NAME'])+" ("+clean_highchart_message(plugs_infoJS[$('#selected_plug').val()-1]['translate'])+")</b>";
                chart.series[$('#selected_plug').val()-1].legendItem = chart.series[$('#selected_plug').val()-1].legendItem.destroy();
                tmp_highchart_plug=this.index;

                $("#reset_selected_plug").val($('#selected_plug').val());
                $("#export_selected_plug").val($('#selected_plug').val());
                $("#import_selected_plug").val($('#selected_plug').val());
                $("#submenu").val($('#selected_plug').val());
            } else if(this.index==highchart_plug-1) {
                chart.series[this.index].name = clean_highchart_message(plugs_infoJS[this.index]['PLUG_NAME'])+" ("+clean_highchart_message(plugs_infoJS[this.index]['translate'])+")";
                chart.series[this.index].legendItem = chart.series[this.index].legendItem.destroy();
                chart.series[this.index].select(false);
                chart.series[this.index].hide();
            } else {
                chart.series[this.index].select(false);
                chart.series[this.index].hide();
            }
        });

        chart.yAxis[0].update({
            labels: {
                useHTML: true,
                formatter: function() {
                    if((this.value>=0)&&(this.value<=100)) {      
                        if((plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="lamp")||(plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="other")) {
                            if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                            if(this.value==100) return '<?php echo __("VALUE_ON"); ?>';
                        } else if((plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="ventilator")||(plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="heating")) { 
                            if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                            if(this.value>0) return this.value+'°C';
                            if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                        } else if((plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="pump")) { 
                            if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                            if(this.value>0) return this.value+' cm';
                            if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                        } else if((plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="humidifier")||(plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="dehumidifier")) { 
                            if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                            if(this.value>0) return this.value+'%';
                            if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                        } else { 
                            return this.value;
                        }
                    }
                }
           }
        });

        if((plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']=="other")||(plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']=="lamp")) {
            if($('#regul_program option[value="regul"]').length!=0) {
                $("#regul_program option[value='regul']").remove();
            }
        } else {
            if($('#regul_program option[value="regul"]').length==0) {
                $("#regul_program").append('<option value="regul"><?php echo __('VALUE_REGUL'); ?></option>');
            }
        }

        var regul="";
        if($('#regprog').is(':checked')) {
            regul="regul";
        } else {
            regul="on";
        }
            
        switch(plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']) { 
            case 'other': getRegulation("on",plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']); 
                        if($('#regprog').is(':checked')) {
                           $('#regoff').attr('checked', true); 
                        }  
                        $('#regul_div').hide();
                        break;
            case 'lamp': getRegulation("on",plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']); 
                         $('#regul_div').hide(); 
                         if($('#regprog').is(':checked')) {
                           $('#regoff').attr('checked', true); 
                         }  
                         $('#regul_div').hide(); 
                         break;
            default: getRegulation(regul,plugs_infoJS[$('#selected_plug').val()-1]['PLUG_TYPE']); $('#regul_div').show(); break;
        }


        if(plugs_infoJS[$('#selected_plug').val()-1]['PLUG_POWER_MAX']<10) {
            $("#dimmer_div").show();
        } else {
            $("#dimmer_div").css("display", "none");
        }


        if(plugs_infoJS[$('#selected_plug').val()-1]['PLUG_REGUL']=="False") {
            $("#second_regul_plug").html("<?php echo __('SECOND_REGUL_OFF'); ?>");
            $("#second_regul_info").css("display", "none");
        } else {
            $("#second_regul_plug").html("<?php echo __('SECOND_REGUL_ON'); ?>");
            $("#second_regul_img").attr("title", resume_regul[$('#selected_plug').val()-1]);
            $("#second_regul_info").css("display", "");
        }

        $("#resume_img").prop('title', resume_plugs[$('#selected_plug').val()]+"<br />"+resume_regul[$('#selected_plug').val()-1]);
        highchart_plug=tmp_highchart_plug+1;
        chart.isDirtyLegend = true;
        chart.redraw(); 

        $("#jumpplug input[name=selected_plug]").val($('#selected_plug').val());
        $('#selected_plug_conf').val($('#selected_plug').val());
    });

       $('#resetXzoom').click(function() {
        if(showzoomX) {
            chart.xAxis[0].setExtremes(null, null,true, true); 
            $('#resetXzoom').hide();
            showzoomX=false;
        }

    });


     /*   $('#datalabel').click(function() {
        if (!$(this).is(':checked')) {
            chart.xAxis[0].isDirty = true;
            chart.redraw();
        } else {
                chart.xAxis[0].isDirty = false;
                chart.redraw();
        }
     });
        */


    $('#selected_plug_conf').change(function() {
        $("#error_value_program").css("display","none");
        $.each( chart.series, function() {
            if(this.index==$('#selected_plug_conf').val()-1) {
                chart.series[$('#selected_plug_conf').val()-1].show();
                chart.series[$('#selected_plug_conf').val()-1].select(); 
                chart.series[$('#selected_plug_conf').val()-1].name = "<b>"+clean_highchart_message(plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_NAME'])+" ("+clean_highchart_message(plugs_infoJS[$('#selected_plug_conf').val()-1]['translate'])+")</b>";
                chart.series[$('#selected_plug_conf').val()-1].legendItem = chart.series[$('#selected_plug_conf').val()-1].legendItem.destroy();
                tmp_highchart_plug=this.index;
               
                //Sur le changement de prise selectionnée, on change les listes du reset, import et export: 
                $("#reset_selected_plug").val($('#selected_plug_conf').val());
                $("#export_selected_plug").val($('#selected_plug_conf').val());
                $("#import_selected_plug").val($('#selected_plug_conf').val());
                $("#submenu").val($('#selected_plug').val());
            } else if(this.index==highchart_plug-1) {
                chart.series[this.index].name = clean_highchart_message(plugs_infoJS[this.index]['PLUG_NAME'])+" ("+clean_highchart_message(plugs_infoJS[this.index]['translate'])+")";
                chart.series[this.index].legendItem = chart.series[this.index].legendItem.destroy();
                chart.series[this.index].select(false);
                chart.series[this.index].hide();
            } else {
                chart.series[this.index].select(false);
                chart.series[this.index].hide();
            }
        });

        chart.yAxis[0].update({
            labels: {
                     useHTML: true,
                     formatter: function() {
                        if((this.value>=0)&&(this.value<=100)) {      
                            if((plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="lamp")||(plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="other")) {
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                                if(this.value==100) return '<?php echo __("VALUE_ON"); ?>';
                            } else if((plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="ventilator")||(plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="heating")) { 
                                if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                                if(this.value>0) return this.value+'°C';
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                            } else if((plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="pump")) { 
                                if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                                if(this.value>0) return this.value+' cm';
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                            } else if((plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="humidifier")||(plugs_infoJS[tmp_highchart_plug]["PLUG_TYPE"]=="dehumidifier")) { 
                                if(this.value==100) return '<?php echo __("CHART_FORCE_ON_VALUE"); ?>';
                                if(this.value>0) return this.value+'%';
                                if(this.value==0) return '<?php echo __("VALUE_OFF"); ?>';
                            } else { 
                                return this.value;
                            }
                        }
                  }
           }
        });

        if((plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']=="other")||(plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']=="lamp")) {
            if($('#regul_program option[value="regul"]').length!=0) {
                $("#regul_program option[value='regul']").remove();
            }
        } else {
            if($('#regul_program option[value="regul"]').length==0) {
                $("#regul_program").append('<option value="regul"><?php echo __('VALUE_REGUL'); ?></option>');
            }
        }

        var regul="";
        if($('#regprog').is(':checked')) {
            regul="regul";
        } else {
            regul="on";
        }
            
        switch(plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']) { 
            case 'other': getRegulation("on",plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']); 
                          if($('#regprog').is(':checked')) {
                           $('#regoff').attr('checked', true); 
                          }  
                          $('#regul_div').hide();
                          break;
            case 'lamp': getRegulation("on",plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']); 
                         $('#regul_div').hide(); 
                         if($('#regprog').is(':checked')) {
                           $('#regoff').attr('checked', true); 
                         }  
                         $('#regul_div').hide(); 
                         break;
            default: getRegulation(regul,plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_TYPE']); $('#regul_div').show(); break;
        }

         if(plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_POWER_MAX']<10) {
            $("#dimmer_div").show();
        } else {
            $("#dimmer_div").css("display", "none");
        }


        if(plugs_infoJS[$('#selected_plug_conf').val()-1]['PLUG_REGUL']=="False") {
            $("#second_regul_plug").html("<?php echo __('SECOND_REGUL_OFF'); ?>");
            $("#second_regul_info").css("display", "none");
        } else {
            $("#second_regul_plug").html("<?php echo __('SECOND_REGUL_ON'); ?>");
            $("#second_regul_img").attr("title", resume_regul[$('#selected_plug_conf').val()-1]);
            $("#second_regul_info").css("display", "");
        }

        $("#resume_img").prop('title', resume_plugs[$('#selected_plug_conf').val()]+"<br />"+resume_regul[$('#selected_plug_conf').val()-1]);

        highchart_plug=tmp_highchart_plug+1;
        chart.isDirtyLegend = true;
        chart.redraw(); 

        $("#jumpplug input[name=selected_plug]").val($('#selected_plug_conf').val());
        $('#selected_plug').val($('#selected_plug_conf').val());
    });

       $('#resetXzoom').click(function() {
        if(showzoomX) {
            chart.xAxis[0].setExtremes(null, null,true, true); 
            $('#resetXzoom').hide();
            showzoomX=false;
        }

    });

});


</script>

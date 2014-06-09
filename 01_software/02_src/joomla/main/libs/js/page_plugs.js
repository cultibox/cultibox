<script>

sensors        = <?php echo json_encode($GLOBALS['NB_MAX_SENSOR_PLUG']) ?>;
nb_plugs       = <?php echo json_encode($nb_plugs) ?>;
title_msgbox   = <?php echo json_encode(__('TOOLTIP_MSGBOX_EYES')); ?>;
plugs_infoJS   = <?php echo json_encode($plugs_infos) ?>;

$(document).ready(function(){
    //Display dimmer canal:
    $("input[name*='plug_power_max']").change(function () {
        var id=$(this).attr('name').substr($(this).attr('name').length-1);

        if(!isFinite(String(id))) {
            id="";
        }

        if($(this).val()=="VARIO") {
            $("#select_canal_dimmer"+id).show();
        } else {
            $("#select_canal_dimmer"+id).css("display","none");
        }
    });


    //Disable previous selected dimmer canal:
    $("select[name*='dimmer_canal']").focus(function () {
        previous_canal = $(this).attr('value');
    }).change(function() {
        var prev=previous_canal;
        var id=$(this).attr('name').substr($(this).attr('name').length-1);
        var canal=$("#dimmer_canal"+id+" option:selected" ).val();

        $("select[name*='dimmer_canal']").each(function( index ) {
                var new_id=$(this).attr('name').substr($(this).attr('name').length-1);
                if(new_id!=id) {
                    var option = $("option[value='" + canal + "']", this);
                    option.attr("disabled","disabled");

                    var option = $("option[value='" + prev + "']", this);
                    option.removeAttr("disabled");
                }
        });

        $("input[name='plug_power_max"+id+"']").focus();
    });


    $('[id*="plug_type"]').change(function() {
           var plug = $(this).attr('id').substring(9,10);

           if(plug!="") {
               $.ajax({
                    cache: false,
                    async: true,
                    url: "../../main/modules/external/get_variable.php",
                    data: {name:'CHECK_PROGRAM',value:plug}
                }).done(function (data) {
                    if(jQuery.parseJSON(data)=="1") {
                        $("#warning_change_type_plug").dialog({
                            resizable: false,
                            height:200,
                            width: 500,
                            closeOnEscape: false,
                            modal: true,
                            dialogClass: "dialog_cultibox",
                            buttons: [{
                            text: OK_button,
                            click: function () {
                                $( this ).dialog( "close" ); 
                            }
                            }, {
                            text: CANCEL_button,
                                click: function () {
                                    $("#plug_type"+plug).val(plugs_infoJS[plug-1]['PLUG_TYPE]']);
                                    $( this ).dialog( "close" ); return false;
                            }
                            }]
                        });
                    }
                });
            }
   });
});

</script>

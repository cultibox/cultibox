var OK_button="";
var CANCEL_button="";
var CLOSE_button="";
var DELETE_button="";
var REDUCE_button="";
var EXTEND_button="";
var HIDE_button="";

var lang="";
var reduced="";
var finished=0;

//To delete current ajax call:
$.xhrPool = [];
$.xhrPool.abortAll = function() {
    $(this).each(function(idx, jqXHR) {
        jqXHR.abort();
    });
    $.xhrPool.length = 0
};


$.ajax({
    cache: false,
    async: false,
    url: "main/modules/external/get_variable.php",
    data: {name:"lang"}
}).done(function (data) {
    if(jQuery.parseJSON(data)!="0") lang=jQuery.parseJSON(data);
});

if(lang=="it_IT") {
    OK_button="Continuare";
    CANCEL_button="Annullare";
    CLOSE_button="Chiudere";
    DELETE_button="Rimuovere";
    SAVE_button="Registrati";
    REDUCE_button="Abbassare";
    EXTEND_button="Ingrandisci";
    HIDE_button="Nascondere";
} else if(lang=="de_DE") {
    OK_button="Weiter";
    CANCEL_button="Stornieren";
    CLOSE_button="Schliessen";
    DELETE_button="Entfernen";
    SAVE_button="Registrieren";
    REDUCE_button="Senken";
    EXTEND_button="Vergrößern";
    HIDE_button="Verbergen";
} else if(lang=="en_GB") {
    OK_button="OK";
    CANCEL_button="Cancel";
    CLOSE_button="Close";
    DELETE_button="Delete";
    SAVE_button="Save";
    REDUCE_button="Shorten";
    EXTEND_button="Enlarge";
    HIDE_button="Hide";
} else if(lang=="es_ES") {
    OK_button="Continuar";
    CANCEL_button="Cancelar";
    CLOSE_button="Cerrar";
    DELETE_button="Eliminar";
    SAVE_button="Registro";
    REDUCE_button="Bajar";
    EXTEND_button="Agrandar";
    HIDE_button="Ocultar";
    APPLY_CHANGE="";
    DISCARD_CHANGE="";
} else {
    OK_button="Continuer";
    CANCEL_button="Annuler";
    CLOSE_button="Fermer";
    DELETE_button="Supprimer";
    SAVE_button="Enregistrer";
    REDUCE_button="Réduire";
    EXTEND_button="Agrandir";
    HIDE_button="Cacher";
}


// {{{ VerifNumber()
// ROLE function of verification of an input value
// IN input value "e" to be checked
// HOW IT WORKS: check ascii code of the input value
// USED BY: templates/programs.html and templates/wizard.html
VerifNumber = function(e) {
    if((e.which > 57) || ((e.which > 31 ) && (e.which < 44)) || (e.which == 45) || (e.which == 47)) {
        return false;
    } 
    return true;
}
// }}}



diffdate = function(d1,d2) {
    var WNbJours = d2.getTime() - d1.getTime();
    return Math.ceil(WNbJours/(1000*60*60*24)+1);
}


addZ = function(n){return n<10? '0'+n:''+n;}


clean_highchart_message = function(message) { 
    message=message.replace("'", "\'");
    return $('<div>').html(message).text();
}


//To get url param into object:
getUrlVars = function(url) {
    var hash;
    var myJson = {};
    var hashes = url.slice(url.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        myJson[hash[0]] = hash[1];
    }
    return myJson;
}   


//Return object containing form's input and value:
getFormInputs = function(form) {
    var values = {};
    var name_map = {};

    $.each($('#'+form).serializeArray(), function(i, field) {
        values[field.name] = field.value;
    });

    return values;
}


// brief : Used to clean messages
function clean_pop_up_messages() {
    $("#pop_up_information_part li").remove();
    $("#pop_up_error_part li").remove();
};


// brief : Used to add a message in popup
// message : Message to show
// id to define
// type : information or error
function pop_up_add_information(message, id, type) {
    // Add message
    if (type == "information")
        $("#pop_up_information_part ul").append('<li id="' + id + '">' + message + '</li>');
        
    if (type == "error")
        $("#pop_up_error_part ul").append('<li id="' + id + '">' + message + '</li>');

    // If there is element in error part, show this part
    if ($("#pop_up_error_part ul li").length > 0)
        $("#pop_up_error_container").css("display", "");
        
    if ($("#pop_up_information_part ul li").length > 0)
        $("#pop_up_information_container").css("display", "");
                
};

// brief : Remove a message in popup
// id to remove
function  pop_up_remove(id) {

    // Delete information
    $("#" + id).remove();
    
    if ($("#pop_up_error_part ul li").length < 1)
        $("#pop_up_error_container").css("display", "none");
        
    if ($("#pop_up_information_part ul li").length < 1)
        $("#pop_up_information_container").css("display", "none");
    
};

function scrolltodiv(div) {
   $.scrollTo("#"+div,300); 
}


// brief : select the matching menu
// menu to be activated
function active_menu(menu) {
    $("#menubar-ul").find('li').each(function(){
        if($(this).attr('id')=="menu-"+menu) {
            $(this).addClass("active");
            $(this).addClass("current");
            $(this).find('span').each(function(){
                $(this).addClass("active");
            });
        } else {
            $(this).removeClass("active");
            $(this).removeClass("current");
            $(this).find('span').each(function(){
                $(this).removeClass("active");
            });
        }
    });
}


// brief : get content and display in it the main content div
// page: page to be displayed in the content div
function get_content(page,get_array) {
   //Clean AJAX calls:
   $.xhrPool.abortAll();

   //Clean popup message:
   clean_pop_up_messages();

   //Clean dialog box except the software dialog box:
   $(".ui-dialog-content").not('.message').dialog('destroy').remove();

   $.ajax({
        cache: false,
        async: false,
        url: "main/modules/external/get_content.php",
        data: {page:page, get_array:JSON.stringify(get_array)}
    }).done(function (data) {
        //Some odd chars appear when including php files due to echo include that returns true value, removing them:
        $("#content").html(data);
        $("#content").load();
        active_menu(page);
    });
}


$(document).ready(function() {
    var search = location.search.substring(1);
    var get_array = getUrlVars(search);

    if(( typeof get_array['menu']!="undefined")&&(['menu']!="")) { 
        get_content(get_array['menu'],get_array);
    } else {
        get_content("welcome",get_array);
    } 


    $(".href-welcome").click(function(e) {
        e.preventDefault();
        get_content("welcome",get_array);
    });

    $(".href-configuration").click(function(e) {
       e.preventDefault();
       get_content("configuration",get_array);
    });

    $(".href-logs").click(function(e) {
       e.preventDefault();
       get_content("logs",get_array);
    });

    $(".href-plugs").click(function(e) {
       e.preventDefault();
       get_content("plugs",get_array);
    });

    $(".href-programs").click(function(e) {
       e.preventDefault();
       get_content("programs",get_array);
    });

    $(".href-calendar").click(function(e) {
       e.preventDefault();
       get_content("calendar",get_array);
    });

    $(".href-wifi").click(function(e) {
       e.preventDefault();
       get_content("wifi",get_array);
    });

    $(".href-cost").click(function(e) {
       e.preventDefault();
       get_content("cost",get_array);
    });

    $(".href-wizard").click(function(e) {
       e.preventDefault();
       get_content("wizard",get_array);
    });

    $(".welcome-logo").click(function(e) {
       e.preventDefault();
       get_content("welcome",get_array);
    });


    //To deal with dynamic content with href in message box
    $('div.error').on('click', 'a', function(e) {
        e.preventDefault();
         var url_vars=getUrlVars($(this).attr('href'));
         get_content(url_vars['menu'],url_vars);
    });


    //To change the language dynamically:
    $('li.translate a').click(function(e) {
        e.preventDefault();
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"lang", value: $(this).attr('id'),duration: 31536000}
        });
        window.location = "/cultibox/";
    });

     // Au chargement d'une page, on vérifie la variable tooltip_msg_box qui détermine si on doit affiche la boîte de message ou l'oeil:
    $.ajax({
        cache: false,
        url: "main/modules/external/get_variable.php",
        data: {name:"tooltip_msg_box"}
    }).done(function (data) {
        //Si l'oeil doit être affiché:
        if(jQuery.parseJSON(data)=="True") {
            //On cache la boîte de message:
            $(".message").dialog("option", "hide",{ effect: "slideUp", duration: 0 } );
            $(".message").dialog('close');


            //Affichage de l'oeil permettant de réafficher la boîte de message:
            // On affiche l'oeil sur le bord exterieur gauche de l'interface. La position dépend de la résolution de l'écran.
            // On récupère donc la taille de l'écran et la taille du div central de l'interface pour afficher l'oeil au bon endroit:
            // position = 90*(taille de l'écran - taille du div central)/200
            // taille de l'écran - taille du div central /2 ==> correspond à la taille de la marge à gauche (ou à droite) séparant le bord du div central
            // on affiche donc l'oeil a 90% de la marge exterieure

            var element_div_width=$("#maininner").width();
            var element_body_width=$( window ).width();
            
            if((element_body_width!="")&&(element_div_width!="")) {
                var dist=90*(element_body_width-element_div_width)/200
                $("#tooltip_msg_box").css("padding-left",dist+"px");
            }   
       

            //Affichage de l'oeil et de son tooltip: 
            $("#eyes_msgbox").attr('title', title_msgbox);
            $("#tooltip_msg_box").show();
        } else {
            //Sinon on cache l'oeil - la boîte de messages étant par défaut affichée:
            $("#tooltip_msg_box").css("display","none");
        }
    });


    $.ajax({
        cache: false,
        url: "main/modules/external/position.php"
    }).done(function (data) {

    if ( typeof data.split(',')[0] !== "undefined" && data.split(',')[0]) {
        var x = parseInt(data.split(',')[0].replace("\"", ""));
    } else {
        var x = 15;
    }

    if ( typeof data.split(',')[1] !== "undefined" && data.split(',')[1]) {
        var y = parseInt(data.split(',')[1].replace("\"", ""));
    } else {
        var y = 15;
    }

    if ( typeof data.split(',')[2] !== "undefined" && data.split(',')[2]) {
        var wid = parseInt(data.split(',')[2].replace("\"", ""));
    } else {
        var wid = 325;
    }

    if ( typeof data.split(',')[3] !== "undefined" && data.split(',')[3]) {
        reduced  = String(data.split(',')[3].replace("\"", ""));
    } else {
        reduced="False";
    }

    $( ".message" ).dialog({ width: wid, closeOnEscape: false, resizable: true, buttons: [ 
        {
            text: HIDE_button,
            id: "button_hide" ,
            click: function() {
                //Action lors de l'enclenchement du bouton "Cacher" de la boîte de messages:
            
                //Fermeture de la boîte de dialogue
                $( this ).dialog( "close" );

                //Mise a True de la variable de session TOOLTIP_MSG_BOX qui définit si on doit afficher ou non la boîte de message lorsqu'on change de page
                $.ajax({
                        cache: false,
                        url: "main/modules/external/set_variable.php",
                        data: {name:"tooltip_msg_box", value: "True", duration: 2592000}
                });


                //Affichage de l'oeil permettant de réafficher la boîte de message:
                // On affiche l'oeil sur le bord exterieur gauche de l'interface. La position dépend de la résolution de l'écran.
                // On récupère donc la taille de l'écran et la taille du div central de l'interface pour afficher l'oeil au bon endroit:
                // position = 90*(taille de l'écran - taille du div central)/200
                // taille de l'écran - taille du div central /2 ==> correspond à la taille de la marge à gauche (ou à droite) séparant le bord du div central
                // on affiche donc l'oeil a 90% de la marge exterieure
                
                var element_div_width=$("#maininner").width();
                var element_body_width=$( window ).width();

                if((element_body_width!="")&&(element_div_width!="")) {
                    var dist=90*(element_body_width-element_div_width)/200;
                    $("#tooltip_msg_box").css("padding-left",dist+"px");
                }


                //On l'oeil et son tooltip:
                $("#eyes_msgbox").attr('title', title_msgbox);
                $("#tooltip_msg_box").fadeIn("slow");
            }
        },
        {
            text: REDUCE_button, 
            id: "button_reduce" ,
            click: function() { 
                if(reduced=="True") {
                    $(this).dialog('option', 'height', 'auto');
                    $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(REDUCE_button);
                    var tmp_reduced="False";
                } else {
                    $(this).dialog('option', 'height', 10); 
                    $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(EXTEND_button);
                    var tmp_reduced="True";
                }
                var tmp = $(".message").dialog( "option", "position" );
                var width = $(".message").dialog( "option", "width" );

                $.ajax({
                cache: false,
                url: "main/modules/external/position.php",
                data: { POSITION_X: tmp[0], POSITION_Y: tmp[1], WIDTH: width, REDUCED: tmp_reduced }
                });
                reduced=tmp_reduced;
        }}], 
        hide: "fold", dialogClass: "dialog_message", position: [x,y], dragStop: function( event, ui ) { 

        if(data!="") { 
            var tmp = $(".message").dialog( "option", "position" );
            var width = $(".message").dialog( "option", "width" );
            $.ajax({
                cache: false,
                url: "main/modules/external/position.php",
                data: { POSITION_X: tmp[0], POSITION_Y: tmp[1], WIDTH: width, REDUCED: reduced }
                }); 
        }
    },
    create: function( event, ui ) {
            if(reduced=="True") {
                $(this).dialog('option', 'height', 10);
                $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(EXTEND_button);
            }
    },
    resizeStop: function( event, ui ) {
        if(data!="") {
                var tmp = $(".message").dialog( "option", "position" );
                var width = $(".message").dialog( "option", "width" );

                $.ajax({
                    cache: false,
                    url: "main/modules/external/position.php",
                    data: { WIDTH: width, POSITION_X: tmp[0], POSITION_Y: tmp[1],WIDTH: width, REDUCED: reduced }
                });

                reduced="False";
                $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(REDUCE_button);
     
       }
    }  });
                $(".message").dialog().parent().css('position', 'fixed');
    }); 


  $(".pop_up_message").dialog({ width: 550, resizable: false, closeOnEscape: false, buttons: [{ text: CLOSE_button, click: function() { $( this ).dialog( "close" ); if(typeof anchor != 'undefined') {  $.scrollTo("#"+anchor,300);  } } } ], hide: "fold", modal: true,  dialogClass: "popup_message"  });
   $( ".pop_up_error" ).dialog({ width: 550, resizable: false, closeOnEscape: false, buttons: [ { text: CLOSE_button, click: function() { $( this ).dialog( "close" ); if(typeof anchor != 'undefined') {  $.scrollTo("#"+anchor,300);  } } } ], hide: "fold", modal: true,  dialogClass: "popup_error" });


    //Lors du click sur l'oeil - on doit cacher l'oeil et afficher la boîte de messages:     
    $("#tooltip_msg_box").click(function(e) {
        //On positionne le COOKIE tooltip_msg_box qui détermine si on doit afficher l'oeil ou pas lors du chargement d'une page
        // en fonction des actions utilisateurs. La variable est positionnée à False, au chargement d'une page l'oeil sera donc caché et 
        // la boîte de messages affichée.
        $.ajax({
            cache: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"tooltip_msg_box", value: "False", duration: 2592000}
        });

        // On cache l'oeil et on affiche la boîte de messages
        $("#tooltip_msg_box").fadeOut("slow");
        $(".message").dialog("open");
    });


    if ($("#pop_up_error_part ul li").length < 1)
        $("#pop_up_error_container").css("display", "none");

    if ($("#pop_up_information_part ul li").length < 1)
        $("#pop_up_information_container").css("display", "none");

});

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


confirmForm = function(SendForm,idDialog) {
    $("#"+idDialog).dialog({
        resizable: false,
        height:200,
        width: 500,
        closeOnEscape: false,
        modal: true,
        dialogClass: "dialog_cultibox",
        buttons: [{
            text: OK_button,
            click: function () {
                $( this ).dialog( "close" ); SendForm.submit();
            }
        }, {
            text: CANCEL_button,
            click: function () {
                $( this ).dialog( "close" ); return false;
            }
        }]
    });
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

    /*$("#"+form+" input[type=checkbox]").each(function() {  
        var name = this.name;
        name_map[name] = (name_map[name]) ? name : name;
    }).each(function() {  
        alert(name_map[this.name]);
    });*/

    return values;
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


    //To change the language dynamically:
    $('li.translate a').click(function(e) {
        e.preventDefault();
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"lang", value: $(this).attr('id'),duration: 365}
        });
        window.location = "/cultibox/";
    });


   jQuery('#jquery-colour-picker-example select').colourPicker({ 
        ico:    'http://localhost:6891/cultibox/main/libs/img/jquery.colourPicker.gif', 
        title:    false
   });


   $(".delete").click(function() {
        var currentForm;
        currentForm = $(this).closest('form');
        var FormId = currentForm.attr('id');
        var DialogId="";

        switch (FormId) {
        case 'reset_program_form':
            DialogId="delete_dialog_program";
            break;
        } 


        if(!confirmForm(currentForm,DialogId)) {
            return false;
        } else {
            return true;    
        }
   });


    $(".download").click(function(e) {
        e.preventDefault();

        $url = 'http://localhost:6891/cultibox/main/modules/external/force_download.php?file='+$(".download").attr("href");
        $.ajax({
            type: 'GET',
            url: $url,
            success: function(data){
                   if(data != true){
                    window.location =""+$url+"";
                   }
            }
        });
    });
   

    //Lors du click sur l'oeil - on doit cacher l'oeil et afficher la boîte de messages:     
    $("#tooltip_msg_box").click(function(e) {
        //On positionne le COOKIE tooltip_msg_box qui détermine si on doit afficher l'oeil ou pas lors du chargement d'une page
        // en fonction des actions utilisateurs. La variable est positionnée à False, au chargement d'une page l'oeil sera donc caché et 
        // la boîte de messages affichée.
        $.ajax({
            cache: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"tooltip_msg_box", value: "False", duration: 30}
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

   //Clean dialog box:
   $(".ui-dialog-content").dialog('destroy').remove();

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


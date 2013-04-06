AfficherInfoBulle = function(e) {
   var text = $(this).next('.info-bulle-contenu');
   if (text.attr('class') != 'info-bulle-contenu') return false;
   text.fadeIn()
   .css('top', e.pageY)
   .css('left', e.pageX+10);
   return false;
}

CacherInfoBulle = function(e) {
   var text = $(this).next('.info-bulle-contenu');
   if (text.attr('class') != 'info-bulle-contenu') return false;
   text.fadeOut();
}

InstallationInfoBulle = function() {
   $('.info-bulle-css')
   .each(function(){
      $(this)
      .after($('<span/>')
      .attr('class', 'info-bulle-contenu')
      .html($(this).attr('title')))
      .attr('title', '');
   })
   .hover(AfficherInfoBulle, CacherInfoBulle);
}

$(document).ready(function() {
   InstallationInfoBulle();
   $( ".pop_up_error" ).dialog({ width: 550, buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", modal: true,  dialogClass: "popup_error" });
   $( ".pop_up_message" ).dialog({ width: 550, buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ], hide: "fold", modal: true,  dialogClass: "popup_message"  });
});


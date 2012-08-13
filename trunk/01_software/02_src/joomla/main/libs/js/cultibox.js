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
});

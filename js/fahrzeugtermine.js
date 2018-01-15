$(document).ready(function() {
  // Show custom popups.
  $( '.Popup' ).on( 'click', function() {
    var infoContainer = document.getElementById( this.getAttribute( 'href' ).substr(1) );
    var html = infoContainer.innerHTML;
    $.popup (
      {},
      html
    );
  });

});
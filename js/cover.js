/*
  **********************************************************
  * OPAQUE NAVBAR SCRIPT
  **********************************************************
  */

  // Toggle tranparent navbar when the user scrolls the page

(function(){ $(window).scroll(function() {
    if($(this).scrollTop() > 10)  /*height in pixels when the navbar becomes non opaque*/ 
    {
        $('.opaque-navbar').addClass('opaque');
    } else {
        $('.opaque-navbar').removeClass('opaque');
    }
});
})();
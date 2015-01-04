$(function () {
    $('[data-toggle="dropdown"]').dropdown('toggle');
});

// jQuery stuff
jQuery(document).ready(function($) {
    // Switch section
    $("a", '.mainmenu').click(function(e) 
    {
        e.stopPropagation();
    });		
});

$(document).ready(funciton(){

    /* Hide FOUC */
    $(window).bind("load", function () {
        
        $('#customList > div').masonry({
            // options
            itemSelector: '.grid-item',
            gutton: 30,
            columnWidth: 350
        });

        $('body').fadeIn(200);
    });

});
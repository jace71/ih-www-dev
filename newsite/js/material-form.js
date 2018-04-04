$(document).ready(function(){

    $.getScript('/assets/js/stretchy.js')
        .done(function(){
            console.log('Stretchy loaded');
            Stretchy.selectors.filter = '.stretchy';
        })
        .fail(function(){
            console.log('Stretchy not loaded');
        });

    $.getScript('/assets/js/jquery.maskedinput.js')
        .done(function(){
            console.log('Masked Input loaded');
            /* auto format for phone # */
            $('.phone').mask('999-999-9999', {placeholder: "   -   -    "});
        })
        .fail(function(){
            console.log('Masked Input not loaded');
        });

    $.getScript('/assets/js/jquery.flexdatalist.min.js')
        .done(function(){
            console.log('Flexdatalist loaded');
            $('.flexdatalist').flexdatalist({
                minLength: 1,
                focusFirstResult: true
            });
            $('.flexdatalist').on('change:flexdatalist', function(event, set, options){                
                if(set.text.length < 1) {
                    $(this).next('input').addClass('empty');
                    $(this).next('input').removeClass('not-empty');
                } else {
                    $(this).next('input').addClass('not-empty');
                    $(this).next('input').removeClass('empty');
                }
            });
        })
        .fail(function(){
            console.log('Flexdatalist not loaded');
        });
    
    /* change label styling on form field change */
    $('.form-group input, .form-group textarea').blur(function(){
        tmpval = $(this).val();
        if(tmpval == '') {
            $(this).addClass('empty');
            $(this).removeClass('not-empty');
        } else {
            $(this).addClass('not-empty');
            $(this).removeClass('empty');
        }
    });

    /* Check for previously filled fields on page load */
    $(window).bind("load", function () {
        $('.WcoForm input, .WcoForm textarea').each(function(){
            enteredVal = $(this)[0].value;
            if(enteredVal == '') {
                $(this).addClass('empty');
                $(this).removeClass('not-empty');
            } else {
                $(this).addClass('not-empty');
                $(this).removeClass('empty');
            }
        });
    });


});
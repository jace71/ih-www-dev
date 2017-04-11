$(document).ready(function(e){

    //initialize countdown clock
    $('#clock').countdown('2017/09/27', {
        strftime: ('%W weeks %-d days %-H h %M min %S sec')
    })
        .on('update.countdown', function(event){
            $(this).html(event.strftime('<div class="col-md-3 col-sm-3"><div class="num big">%D</div><div class="numlabel small">days</div></div><div class="col-md-3 col-sm-3"><div class="num big">%H</div><div class="numlabel small">Hrs</div></div><div class="col-md-3 col-sm-3"><div class="num big">%M</div><div class="numlabel small">Min</div></div><div class="col-md-3 col-sm-3"><div class="num big">%S<span>!</span></div><div class="numlabel small">Sec</div></div>'));
            //console.log(event.offset.totalSeconds);
        });

    // make header Register Now button appear on scroll
    if ($('#top-banner.jumbotron > .container.overlay').length > 0) {
        $(window).scroll(function(){
            var scrollNum = $(document).scrollTop();
            //console.log(scrollNum);
            if (scrollNum > 137) {
                $('a.header-register-btn').css('opacity', 1);
            } else {
                $('a.header-register-btn').css('opacity', 0);
            }
        });
    } else if ($(location).attr('href').indexOf('register') >= 0) {
        // do nothing
    } else { // make header register button appear on interior pages
        $('#navbar > ul.navbar-right > li > a.header-register-btn').css('opacity','1');
    }

    // layout fixes .removeClass('img-responsive')
    $('#navbar img.img-responsive')
        .addClass('header-logo')
        .addClass('desktop');
    
    $('ul.level1').removeClass('level1')
        .addClass('text-center')
        .unwrap()
        .unwrap();

    //social icons
    $('.social-icons .fa-facebook-square').parent().attr('href','https://www.facebook.com/InfluenceHealth').attr('target','_blank');
    $('.social-icons .fa-twitter-square').parent().attr('href','https://twitter.com/InfluenceHlth').attr('target','_blank');
    $('.social-icons .fa-linkedin-square').parent().attr('href','https://www.linkedin.com/company/influence-health').attr('target','_blank');
    $('.social-icons .fa-vimeo-square').parent().attr('href','https://vimeo.com/user16745977').attr('target','_blank');

    // footer links
    $('.footer-bottom a').attr('href','http://www.influencehealth.com');
    $('.footer-bottom .logo-footer').wrap('<a href="http://www.influencehealth.com" target="_blank"/>');

    // show 'more to do' section on travel page
    if ($(location).attr('href').indexOf('travel') >= 0) {
        $('.container.speakers.three-col').css('display','inherit');
    }

    // crownpeak fixes

        // change class of content wrapper
    if ($('#site-body > .container').length > 0) {
        $('#site-body > .container').removeClass('container').addClass('container-fluid');
        $('div#ih-page-body').css('margin-top','70px');
    }

});
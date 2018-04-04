$(document).ready(function() {

    $('.topic-dropdown').remove();

    $('.row.ih-header-wrap').attr('data-spy','affix').attr('data-offset-top','222.86');
    
    $('#ih-page-header').append('<div class="row"><div class="col-md-12"><ul class="ktg-nav"><li><a href="/knowledge-to-go" class="closed">All</a></li><li><a href="/white-papers" class="selected closed">White Papers <i class="fa fa-chevron-down"></i></a></li><li><a href="/blog" class="closed">Blog Posts</a></li><li><a href="/client-stories" class="closed">Client Story</a></li><li><a href="/infographics" class="closed">Infographics</a></li><li><a href="/webinars" class="closed">Webinars</a></li></ul></div></div>');

    $('.ih-header-column').append('<ul class="ktg-nav-mobile"><li><a href="/knowledge-to-go">All</a></li><li class="selected"><a class="selected" href="/white-papers">White Papers</a></li><li><a href="/blog">Blog Posts</a></li><li><a href="/client-stories">Client Story</a></li><li><a href="/infographics">Infographics</a></li><li><a href="/webinars">Webinars</a></li></ul>');
    
    $('.landing-featured > .row').prepend('<div class="social-icon-wrapper" id="sticky-social"><ul class="social-icons"><li><a href="https://www.facebook.com/InfluenceHealth" target="_blank" class="facebook"><i class="fa fa-facebook-square"></i></a></li><li><a href="https://twitter.com/InfluenceHlth" target="_blank" class="twitter"><i class="fa fa-twitter-square"></i></a></li><li><a href="https://www.youtube.com/channel/UC9HhGk6jn1yBOwVJTNMdsSA/featured" target="_blank" class="youtube"><i class="fa fa-youtube-square"></i></a></li><li><a href="https://www.linkedin.com/company/influence-health" target="_blank" class="linkedin"><i class="fa fa-linkedin-square"></i></a></li><li><a href="mailto:marketing@influencehealth.com" class="email"><i class="fa fa-envelope-square"></i></a></li></ul></div>');

    //$('.landing-pg-row .row.feed').append('<a href="#" class="view-more btn view-more-btn">View More</a>');
    $('.landing-pg-row').append('<div class="back-to-top back"><i class="fa fa-chevron-up"></i> Top</div>');

    var queryNum = 6;
    var numVisible = 6;
    if ($(window).width() > 992) {
        var numToAdd = 3;
    } else {
        var numToAdd = 2;
    }

    $('.view-more').on('click',function(e){
        e.preventDefault();

        $('#resultsWrapper_ktg-whitepaper-row > div').slice(numVisible, numVisible + numToAdd).fadeIn(800);
            
        console.log('slideDown fired');
        var pageTop = $(window).scrollTop();
        console.log(pageTop);
        $('html,body').stop().animate({
            scrollTop: ($('#resultsWrapper_ktg-whitepaper-row > div:nth-child(' + numVisible + ')').offset().top +350)
        }, 600);

        numVisible = numVisible + numToAdd;

        if (numVisible >= ($('.row.feed #customList > div > div').length)) {
            $('.view-more').off('click').css('background','#e9e9e9').attr('disabled','true').removeAttr('href');
        } else {
            //numVisible = numVisible + 3;
        }
        
    });

    const currentDate = new Date();

    // Whitepaper landing page featured item
    contentquery.search('ktg-whitepaper-featured', 
    'custom_s_content_type:("White Paper") AND custom_b_featured:true AND !custom_b_archived:true&sort=custom_dt_publish_date_226894+desc,title+asc', 
    '&lt;div class=&quot;card&quot;&gt; &lt;div class=&quot;text-holder {custom_b_award_239912}&quot;&gt; &lt;a href=&quot;{custom_s_url}&quot;&gt; &lt;div class=&quot;card-bg-holder&quot; style=&#39;background-image: url(&quot;{custom_s_thumbnail_image}&quot;);&#39;&gt;&lt;/div&gt; &lt;/a&gt; &lt;div class=&quot;text&quot;&gt; &lt;div class=&quot;type&quot;&gt;{custom_s_content_type}&lt;/div&gt; &lt;div class=&quot;pubdate&quot;&gt;{custom_dt_publish_date_226894} &lt;span class=&quot;reading-time&quot;&gt; &lt;i&gt;|&lt;/i&gt; &lt;span&gt;{custom_d_word_count_238538}&lt;/span&gt; min. read&lt;/span&gt; &lt;/div&gt; &lt;div class=&quot;title-holder&quot;&gt; &lt;h3&gt; &lt;a href=&quot;{custom_s_url}&quot;&gt;{title}&lt;/a&gt; &lt;/h3&gt; &lt;a href=&quot;{custom_s_url}&quot;&gt;  &lt;p&gt;{custom_s_excerpt}&lt;/p&gt; &lt;/a&gt;  &lt;strong class=&quot;learning-obj&quot;&gt;Learning Objectives:&lt;/strong&gt; &lt;div class=&quot;learning-obj-list&quot;&gt;{custom_s_learning_objectives_238526}&lt;/div&gt;   &lt;p&gt;&lt;a class=&quot;btn view-more-btn&quot; href=&quot;#&quot;&gt;Download&lt;/a&gt;&lt;/p&gt;   &lt;div class=&quot;form-holder&quot;&gt; {custom_s_form_content_226932} &lt;a class=&quot;close-link&quot; href=&quot;#&quot;&gt;Close Form&lt;/a&gt; &lt;/div&gt;  &lt;/div&gt; &lt;/div&gt; &lt;/div&gt; &lt;/div&gt;', 
    1, 
    '');

    // Featured whitepaper download button
    $(document).on('click','.title-holder a.btn',function(e){
        e.preventDefault();
        if ($(window).width() >= 1200) {
            $('.title-holder .form-holder').animate({
                bottom:0
            }, 500, 'swing');
        } else {
            var webinarLink = $('.featured-wrap .title-holder h3 > a').attr('href');
            window.location.href = webinarLink;
        }
    });
    $(document).on('click','.close-link',function(e){
        e.preventDefault();
        $('.title-holder .form-holder').animate({
            bottom:'100%'
        }, 500, 'swing');
    });

    // blog row
    if ($(window).width() > 768) {
        contentRowQuery('ktg-whitepaper-row', 'White Paper', 111);
    } else {
        contentRowQuery('ktg-whitepaper-row', 'White Paper', 111);
    }
    $(window).load(function(){
        $('#resultsWrapper_ktg-whitepaper-row > div:lt(6)').show();
    });

    // ktg whitepaper page search functions
    function contentRowQuery(contentHolder, contentType, numEntries) {
        console.log('firing query for ' + contentType);
        contentquery.search(contentHolder, 
        'custom_s_content_type:("' + contentType + '") AND !custom_b_featured:true AND (custom_dt_publish_date_226894:([* TO ' + currentDate.getFullYear() + '\\-' + (currentDate.getMonth() + 1) + '\\-' + currentDate.getDate() + 'T00\:00\:00.000Z])) AND !custom_b_archived:true&sort=custom_dt_publish_date_226894+desc,title+asc', '&lt;div class=&quot;col-md-4 col-sm-6 col-xs-6&quot;&gt;                    &lt;div class=&quot;card&quot;&gt;                        &lt;a href=&quot;{custom_s_url}&quot;&gt;                            &lt;div class=&quot;text-holder {custom_b_award_239912}&quot;&gt; &lt;div class=&quot;card-bg-holder&quot; style=&#39;background-image: url(&quot;{custom_s_thumbnail_image}&quot;);&#39;&gt;&lt;/div&gt; &lt;div class=&quot;text&quot;&gt;                                    &lt;div class=&quot;type&quot;&gt;{custom_s_content_type}&lt;/div&gt;                                    &lt;div class=&quot;pubdate&quot;&gt;{custom_dt_publish_date_226894} &lt;span class=&quot;reading-time&quot;&gt; &lt;i&gt;|&lt;/i&gt; &lt;span&gt;{custom_d_word_count_238538}&lt;/span&gt; min. read&lt;/span&gt; &lt;/div&gt;                                    &lt;div class=&quot;title-holder&quot;&gt;                                        &lt;h3&gt;{title}&lt;/h3&gt;                                    &lt;/div&gt;                                &lt;/div&gt;                            &lt;/div&gt;                        &lt;/a&gt;                    &lt;/div&gt;                &lt;/div&gt;', 
        numEntries, 
        '');
    }

    // ScrollMagic - init controller
    var controller = new ScrollMagic.Controller();
    var scene = new ScrollMagic.Scene({
      offset: 226  
    })
    .setPin('#sticky-social');

    if ($(window).width() > 991) {
        scene.addTo(controller);
        var checkDurationHeight = setInterval(function(){
            if ($('body').is(':visible')){
                clearInterval(checkDurationHeight);
                var numDuration = ($('.landing-featured').height() + $('.landing-pg-row').height() - 200);
                console.log('Height: ' + numDuration);
                scene.duration(numDuration);
            } else {
                console.log('Checked content height');
            }
        }, 100);
    }

    $(window).resize(function(){
        console.log('Width: ' + $(window).width());
        var numDuration = ($('.landing-featured').height() + $('.landing-pg-row').height() - 200);
        console.log('Duration: ' + numDuration);
        console.log('Enabled: ' + scene.enabled());
        if ($(window).width() < 992 && scene.enabled()) {
            scene.enabled(false);
            scene.removePin(true);
        } else if ($(window).width() >= 992 && !scene.enabled()) {
            scene.enabled(true);
            scene.setPin('#sticky-social');
            scene.addTo(controller);
            scene.duration(numDuration);
        } else if ($(window).width() >= 992 && scene.enabled()) {
            scene.duration(numDuration);
            console.log('Resetting duration: ' + numDuration);
        }

    });

    function addRow() {
        $('#resultsWrapper_ktg-whitepaper-row > div').slice(numVisible, numVisible + numToAdd).fadeIn(800, function(){
            var numDuration = ($('.landing-featured').height() + $('.landing-pg-row').height() - 200);
            scene.duration(numDuration);
        });
        
        console.log('slideDown fired');
        var pageTop = $(window).scrollTop();
        console.log(pageTop);
        /*$('html,body').stop().animate({
            scrollTop: ($('#resultsWrapper_ktg-blog-row > div:nth-child(' + numVisible + ')').offset().top + 450)
        }, 600);*/

        numVisible = numVisible + numToAdd;

        if (numVisible >= ($('.row.feed #customList > div > div').length)) {
            $('.view-more').off('click').css('background','#e9e9e9').attr('disabled','true').removeAttr('href');
        } else {
            //numVisible = numVisible + 3;
        }
    }

    // Minute read changes and truncating text excerpts
    var checkBodyLoaded = setInterval(function(){
        if ($('body').is(':visible')){
            console.log('Body exists');
            clearInterval(checkBodyLoaded);
            //read time
            $('.reading-time > span').each(function(){
                if ($(this).text()) {
                    $(this).text(Math.floor(parseInt($(this).text()) / 265));
                } else {
                    $(this).parent().remove();
                }
            });
            // ellipsis
            $('.title-holder > a > p').succinct({
                size: 150
            });
            // add bug holder
            $('.text-holder.true').each(function(){
                $(this).prepend("<div class='bug-holder'></div>");
            });
            // check learning objectives
            if ($('.learning-obj-list > *').length <= 0) {
                $('.learning-obj, .learning-obj-list').remove();
            }
            if ($(window).width() <= 600) {
                addEllipsisTitle();
                // open mobile menu
                $('.ktg-nav a.selected').click(function(e){
                    e.preventDefault();
                    if ($('.ktg-nav-mobile').hasClass('open')){
                        $('.ktg-nav-mobile').slideUp('slow',function(){                        
                            $('.ktg-nav-mobile').removeClass('open');
                        });
                    } else {
                        $('.ktg-nav-mobile').slideDown('slow', function(){
                            $('.ktg-nav-mobile').addClass('open');
                        });
                    }
                });
            }
            // Back to top button
            var visibleContentHeight = $('#ih-page-body').height();
            $(window).scroll(function(){
                //console.log($(document).scrollTop());
                if ($(document).scrollTop() >= (visibleContentHeight - 600)) {
                    $('.back-to-top').addClass('show');
                } else {
                    $('.back-to-top').removeClass('show');
                }                
            });
            $('.back-to-top').on('click', function (event) {
                event.preventDefault();
                $('html,body').stop().animate({
                    scrollTop: $('body').offset().top
                }, 1000);
            });
            // ScrollMagic - infinite scroll
            var infiniteController = new ScrollMagic.Controller();
            var infiniteScene = new ScrollMagic.Scene({
                triggerElement: "footer",
                triggerHook: "onEnter"
            })
            .addTo(infiniteController)
            .on("enter", function(e) {
                console.log('Loading new content');

                addRow();
            });
        } else {
            console.log('Checked body');
        }
    }, 100);

    function addEllipsisTitle() {
        $('.title-holder > h3').succinct({
            size: 50
        });
    }
    

});
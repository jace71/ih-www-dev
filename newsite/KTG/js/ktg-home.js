$(document).ready(function(){

    $('.row.ih-header-wrap').attr('data-spy','affix').attr('data-offset-top','222.86');
    
    $('#ih-page-header').append('<div class="row"><div class="col-md-12"><ul class="ktg-nav"><li><a href="/knowledge-to-go" class="selected">All <i class="fa fa-chevron-down"></i></a></li><li><a href="/white-papers">White Papers</a></li><li><a href="/blog">Blog Posts</a></li><li><a href="/client-stories">Client Story</a></li><li><a href="/infographics">Infographics</a></li><li><a href="/webinars">Webinars</a></li></ul></div></div>');

    $('.ih-header-column').append('<ul class="ktg-nav-mobile"><li class="selected"><a href="/knowledge-to-go" class="selected">All</a></li><li><a href="/white-papers">White Papers</a></li><li><a href="/blog">Blog Posts</a></li><li><a href="/client-stories">Client Story</a></li><li><a href="/infographics">Infographics</a></li><li><a href="/webinars">Webinars</a></li></ul>');
    
    $('.body-row > .row').prepend('<div class="social-icon-wrapper" id="sticky-social"><ul class="social-icons"><li><a href="https://www.facebook.com/InfluenceHealth" target="_blank" class="facebook"><i class="fa fa-facebook-square"></i></a></li><li><a href="https://twitter.com/InfluenceHlth" target="_blank" class="twitter"><i class="fa fa-twitter-square"></i></a></li><li><a href="https://www.youtube.com/channel/UC9HhGk6jn1yBOwVJTNMdsSA/featured" target="_blank" class="youtube"><i class="fa fa-youtube-square"></i></a></li><li><a href="https://www.linkedin.com/company/influence-health" target="_blank" class="linkedin"><i class="fa fa-linkedin-square"></i></a></li><li><a href="mailto:marketing@influencehealth.com" class="email"><i class="fa fa-envelope-square"></i></a></li></ul></div>');

    const currentDate = new Date();

    // featured 2 x 2 section
    contentquery.search('2x2webinar', 'custom_s_content_type:("Webinar") AND !custom_b_archived:true&sort=custom_dt_publish_date_226894+desc,title+asc', '&lt;div class=&quot;col-md-6 col-sm-6 col-xs-6&quot;&gt;                                        &lt;div class=&quot;card&quot;&gt                        &lt;a href=&quot;{custom_s_url}&quot;&gt;                            &lt;div class=&quot;text-holder {custom_b_award_239912}&quot;&gt; &lt;div class=&quot;card-bg-holder&quot; style=&#39;background-image: url(&quot;{custom_s_thumbnail_image}&quot;);&#39;&gt;&lt;/div&gt;                                &lt;div class=&quot;text&quot;&gt;                                    &lt;div class=&quot;type&quot;&gt;{custom_s_content_type}&lt;/div&gt;                                    &lt;div class=&quot;pubdate&quot;&gt;{custom_dt_publish_date_226894} &lt;span class=&quot;reading-time&quot;&gt; &lt;i&gt;|&lt;/i&gt; &lt;span&gt;{custom_d_word_count_238538}&lt;/span&gt; min. read&lt;/span&gt; &lt;/div&gt;                                    &lt;div class=&quot;title-holder&quot;&gt;                                        &lt;h3&gt;{title}&lt;/h3&gt;                                    &lt;/div&gt;                                &lt;/div&gt;                            &lt;/div&gt;                        &lt;/a&gt;                    &lt;/div&gt;                &lt;/div&gt;', 1, '');

    contentquery.search('2x2infographic', 'custom_s_content_type:("Infographic") AND !custom_b_archived:true&sort=custom_dt_publish_date_226894+desc,title+asc', '&lt;div class=&quot;col-md-6 col-sm-6 col-xs-6&quot;&gt;                                        &lt;div class=&quot;card&quot;&gt;                        &lt;a href=&quot;{custom_s_url}&quot;&gt;                            &lt;div class=&quot;text-holder {custom_b_award_239912}&quot;&gt; &lt;div class=&quot;card-bg-holder&quot; style=&#39;background-image: url(&quot;{custom_s_thumbnail_image_232415}&quot;);&#39;&gt;&lt;/div&gt;                                &lt;div class=&quot;text&quot;&gt;                                    &lt;div class=&quot;type&quot;&gt;{custom_s_content_type}&lt;/div&gt;                                    &lt;div class=&quot;pubdate&quot;&gt;{custom_dt_publish_date_226894} &lt;span class=&quot;reading-time&quot;&gt; &lt;i&gt;|&lt;/i&gt; &lt;span&gt;{custom_d_word_count_238538}&lt;/span&gt; min. read&lt;/span&gt; &lt;/div&gt;                                    &lt;div class=&quot;title-holder&quot;&gt;                                        &lt;h3&gt;{title}&lt;/h3&gt;                                    &lt;/div&gt;                                &lt;/div&gt;                            &lt;/div&gt;                        &lt;/a&gt;                    &lt;/div&gt;                &lt;/div&gt;', 1, '');

    contentquery.search('2x2blog', 'custom_s_content_type:("Blog") AND !custom_b_archived:true&sort=custom_dt_publish_date_226894+desc,title+asc', '&lt;div class=&quot;col-md-6 col-sm-6 col-xs-6&quot;&gt;                                        &lt;div class=&quot;card&quot;&gt;                        &lt;a href=&quot;{custom_s_url}&quot;&gt;                            &lt;div class=&quot;text-holder {custom_b_award_239912}&quot;&gt; &lt;div class=&quot;card-bg-holder&quot; style=&#39;background-image: url(&quot;{custom_s_thumbnail_image}&quot;);&#39;&gt;&lt;/div&gt;                                &lt;div class=&quot;text&quot;&gt;                                    &lt;div class=&quot;type&quot;&gt;{custom_s_content_type}&lt;/div&gt;                                    &lt;div class=&quot;pubdate&quot;&gt;{custom_dt_publish_date_226894} &lt;span class=&quot;reading-time&quot;&gt; &lt;i&gt;|&lt;/i&gt; &lt;span&gt;{custom_d_word_count_238538}&lt;/span&gt; min. read&lt;/span&gt; &lt;/div&gt;                                    &lt;div class=&quot;title-holder&quot;&gt;                                        &lt;h3&gt;{title}&lt;/h3&gt;                                    &lt;/div&gt;                                &lt;/div&gt;                            &lt;/div&gt;                        &lt;/a&gt;                    &lt;/div&gt;                &lt;/div&gt;', 1, '');

    contentquery.search('2x2clientstory', 'custom_s_content_type:("Client Story") AND !custom_b_archived:true&sort=custom_dt_publish_date_226894+desc,title+asc', '&lt;div class=&quot;col-md-6 col-sm-6 col-xs-6&quot;&gt;                                        &lt;div class=&quot;card&quot;&gt;                        &lt;a href=&quot;{custom_s_url}&quot;&gt;                            &lt;div class=&quot;text-holder {custom_b_award_239912}&quot;&gt; &lt;div class=&quot;card-bg-holder&quot; style=&#39;background-image: url(&quot;{custom_s_thumbnail_image}&quot;);&#39;&gt;&lt;/div&gt;                                &lt;div class=&quot;text&quot;&gt;                                    &lt;div class=&quot;type&quot;&gt;{custom_s_content_type}&lt;/div&gt;                                    &lt;div class=&quot;pubdate&quot;&gt;{custom_dt_publish_date_226894} &lt;span class=&quot;reading-time&quot;&gt; &lt;i&gt;|&lt;/i&gt; &lt;span&gt;{custom_d_word_count_238538}&lt;/span&gt; min. read&lt;/span&gt; &lt;/div&gt;                                    &lt;div class=&quot;title-holder&quot;&gt;                                        &lt;h3&gt;{title}&lt;/h3&gt;                                    &lt;/div&gt;                                &lt;/div&gt;                            &lt;/div&gt;                        &lt;/a&gt;                    &lt;/div&gt;                &lt;/div&gt;', 1, '');
    
    
    // blog row
    if ($(window).width() > 992) {
        contentRowQuery('ktg-blog-row', 'Blog', 3);
    } else if ($(window).width() > 768) {
        contentRowQuery('ktg-blog-row', 'Blog', 4);
    } else {
        contentRowQuery('ktg-blog-row', 'Blog', 6);
    }

    // whitepapers
    if ($(window).width() > 992) {
        contentRowQuery('ktg-whitepaper-row', 'White Paper', 3);
    } else if ($(window).width() > 768) {
        contentRowQuery('ktg-whitepaper-row', 'White Paper', 4);
    } else {
        contentRowQuery('ktg-whitepaper-row', 'White Paper', 6);
    }

    // webinar
    if ($(window).width() > 992) {
        contentRowQuery('ktg-webinar-row', 'Webinar', 3);
    } else if ($(window).width() > 768) {
        contentRowQuery('ktg-webinar-row', 'Webinar', 4);
    } else {
        contentRowQuery('ktg-webinar-row', 'Webinar', 6);
    }

    // infographics
    if ($(window).width() > 992) {
        contentRowQuery('ktg-infographic-row', 'Infographic', 3);
    } else if ($(window).width() > 768) {
        contentRowQuery('ktg-infographic-row', 'Infographic', 4);
    } else {
        contentRowQuery('ktg-infographic-row', 'Infographic', 6);
    }

    // client stories
    if ($(window).width() > 992) {
        contentRowQuery('ktg-client-story-row', 'Client Story', 3);
    } else if ($(window).width() > 768) {
        contentRowQuery('ktg-client-story-row', 'Client Story', 4);
    } else {
        contentRowQuery('ktg-client-story-row', 'Client Story', 6);
    }

    // ktg home page search functions
    function contentRowQuery(contentHolder, contentType, numEntries) {
        console.log('firing query for ' + contentType);
        contentquery.search(contentHolder, 
        'custom_s_content_type:("' + contentType + '") AND (custom_dt_publish_date_226894:([* TO ' + currentDate.getFullYear() + '\\-' + (currentDate.getMonth() + 1) + '\\-' + currentDate.getDate() + 'T00\:00\:00.000Z])) AND !custom_b_archived:true&sort=custom_dt_publish_date_226894+desc,title+asc', '&lt;div class=&quot;col-md-4 col-sm-6&quot;&gt;                    &lt;div class=&quot;card&quot; &gt;                        &lt;a href=&quot;{custom_s_url}&quot;&gt;                            &lt;div class=&quot;text-holder {custom_b_award_239912}&quot;&gt; &lt;div class=&quot;card-bg-holder&quot; style=&#39;background-image: url(&quot;{custom_s_thumbnail_image}&quot;);&#39;&gt;&lt;/div&gt; &lt;div class=&quot;text&quot;&gt;                                    &lt;div class=&quot;type&quot;&gt;{custom_s_content_type}&lt;/div&gt;                                    &lt;div class=&quot;pubdate&quot;&gt;{custom_dt_publish_date_226894} &lt;span class=&quot;reading-time&quot;&gt; &lt;i&gt;|&lt;/i&gt; &lt;span&gt;{custom_d_word_count_238538}&lt;/span&gt; min. read&lt;/span&gt; &lt;/div&gt;                                    &lt;div class=&quot;title-holder&quot;&gt;                                        &lt;h3&gt;{title}&lt;/h3&gt;                                    &lt;/div&gt;                                &lt;/div&gt;                            &lt;/div&gt;                        &lt;/a&gt;                    &lt;/div&gt;                &lt;/div&gt;', 
        numEntries, 
        '');
    }

    // flexslider

    $(window).load(function() {
        if ($(window).width() <= 768) {
            console.log($(window).width());

            $('.flexslider-blog').flexslider({
                selector:".slides > div",
                animation:"slide",
                animationLoop:false,
                slideshow:false,
                itemWidth:400,
                itemMargin:5,
                controlNav:false,
                directionNav:false,
                move:1
            });

            $('.flexslider-whitepaper, .flexslider-webinar, .flexslider-infographic, .flexslider-client-story').flexslider({
                selector: ".slides > div",
                animation:"slide",
                animationLoop:false,
                slideshow:false,
                itemWidth:400,
                itemMargin:5,
                controlNav:false,
                directionNav:false,
                move:1
            });
        }

    });

    // ScrollMagic implementation for variable viewport widths
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
                var numDuration = ($('.body-row').height() + $('.feeds-row').height() - 200);
                console.log('Height: ' + numDuration);
                scene.duration(numDuration);
            } else {
                console.log('Checked content height');
            }
        }, 100);
    }

    $(window).resize(function(){
        console.log('Width: ' + $(window).width());
        var numDuration = ($('.body-row').height() + $('.feeds-row').height() - 200);
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

    // ScrollMagic - init controller
    /*if ($(window).width() > 991) {
        setTimeout(function(){
            var controller = new ScrollMagic.Controller();
            var sceneFunction = function () { // wait for document ready
                // build scene        
                if ($(window).width() > 991) {
                    var scene = new ScrollMagic.Scene({
                    //duration: 1000,
                    offset: 226
                    //triggerElement:"#ih-page-body"
                    })
                    .setPin("#sticky-social")
                    .addTo(controller);
                }
                setTimeout(function(){
                    var numDuration = $('.feeds-row').outerHeight() + 200;
                    console.log('Height: ' + numDuration);
                    scene.duration(numDuration);
                    console.log('Computed duration: ' + scene.duration());
                },1000);*/

                /*$(window).resize(function(){
                    //console.log(scene.enabled());
                    if ($(window).width() < 992) {
                        if (scene) {
                            scene = scene.destroy(true);
                        }
                    } else {
                        if (!scene) {
                            var scene = new ScrollMagic.Scene({
                            duration: 1000,
                            offset: 226
                            })
                            .setPin("#sticky-social")
                            .addTo(controller);
                        }
                    }
                //});*/


            /*};
            sceneFunction();            
        },2000);
    }*/

    /*$(window).resize(function(){
        if ($(window).width() <= 600) {
            $('.ktg-nav a').on('click', function(e){
                e.preventDefault();
                $('.ktg-nav-mobile').slideToggle('slow');
            });
        }
    });*/

    // Minute read changes; mobile menu dropdown
    var checkBodyLoaded = setInterval(function(){
        if ($('body').is(':visible')){
            console.log('Body exists');
            clearInterval(checkBodyLoaded);
            $('.reading-time > span').each(function(){
                if ($(this).text()) {
                    $(this).text(Math.floor(parseInt($(this).text()) / 265));
                } else {
                    $(this).parent().remove();
                }
            });
            // twoxtwo ellipsis
            $('.twoxtwo .title-holder > h3').succinct({
                size: 45
            });
            // add bug holder
            $('.text-holder.true').each(function(){
                $(this).prepend("<div class='bug-holder'></div>");
            });
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
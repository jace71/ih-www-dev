$(document).ready(function(){
    console.log('Document Ready');

    var startDate = $('#start').text();
    var endDate = $('#end').text();

    $('#start,#end').hide();
    
    jQuery.ajax('http://influencehealth.curatasite.com/api/v1/articles.js?in_category=32&start_approval_time=' + startDate + 'T00:00:00&end_approval_time=' + endDate + 'T00:00:00',
        {
            dataType : 'jsonp',
            success : function(data,textStatus,jqXHR){
                var articles = data.articles, article = null;
                var $articles = $('#articles');
                $('feed_description').html(data.title);
                $articles.html('');
                for (var i = 0, length = articles.length; i < length; i++) {
                    article = articles[i];
                    if (article.image != null) {
                        var imageHtml = '<p style="padding:0;"><img src="' + article.image + '" style="float:left;width:250px; margin:0 12px 8px 0;"/></p>';
                    } else {
                        var imageHtml = '';
                    }
                    $articles.append('<div style="float:left; clear:left; margin-bottom:24px;">' + '<h3 style="color:#00b299;"><a href="' + article.url + '" target="_blank">' + article.title + '</a></h3><p>From: <strong><a href="http://' + article.publisher.domain + '" target="_blank">' + article.publisher.name + '</a></strong></p>' + imageHtml + article.snippet + '</div>');
                }
            }
        });

});
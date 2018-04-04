$(document).ready(function(){

    // featured whitepaper
    contentquery.search('home-featured-whitepaper', 
    'custom_s_content_type:("White Paper") AND !custom_b_archived:true&sort=custom_dt_publish_date_226894+desc,title+asc',
    '&lt;div class=&quot;col-xs-12 col-sm-6 col-md-6 left&quot;&gt;                &lt;p&gt;&lt;a href=&quot;{custom_s_url}&quot;&gt;&lt;img width=&quot;400&quot; height=&quot;285&quot; class=&quot;img-responsive&quot; alt=&quot;{custom_s_thumbnail_image_alt_text_229317}&quot; src=&quot;{custom_s_thumbnail_image_232415}&quot; caption=&quot;false&quot;&gt;&lt;/a&gt;&lt;/p&gt;            &lt;/div&gt;&lt;div class=&quot;col-xs-12 col-sm-6 col-md-6 right&quot;&gt;                &lt;h2&gt;&lt;a href=&quot;{custom_s_url}&quot;&gt;{title}&lt;/a&gt;&lt;/h2&gt;                &lt;p&gt;{custom_s_excerpt}&lt;/p&gt;            &lt;/div&gt;', 
    1, 
    '');
    
    // featured webinar
    contentquery.search('home-featured-webinar', 
    'custom_s_content_type:("Webinar") AND !custom_b_archived:true&sort=custom_dt_publish_date_226894+desc,title+asc',
    '&lt;div class=&quot;col-xs-12 col-sm-6 col-md-6 left&quot;&gt;                &lt;p&gt;&lt;a href=&quot;{custom_s_url}&quot;&gt;&lt;img width=&quot;400&quot; height=&quot;285&quot; class=&quot;img-responsive&quot; alt=&quot;{custom_s_thumbnail_image_alt_text_229317}&quot; src=&quot;{custom_s_thumbnail_image_232415}&quot; caption=&quot;false&quot;&gt;&lt;/a&gt;&lt;/p&gt;            &lt;/div&gt;&lt;div class=&quot;col-xs-12 col-sm-6 col-md-6 right&quot;&gt;                &lt;h2&gt;&lt;a href=&quot;{custom_s_url}&quot;&gt;{title}&lt;/a&gt;&lt;/h2&gt;                &lt;p&gt;{custom_s_excerpt}&lt;/p&gt;            &lt;/div&gt;', 
    1, 
    '');

    // In the News
    

});
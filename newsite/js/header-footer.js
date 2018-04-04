$(document).ready(function(){

    // add social icons and search to mobile
    <div class="navbar-additional-content visible-xs nav navbar-nav">
        <ul id="social-media-links" class="social-media-links">
            <li>
                <a href="https://vimeo.com/user16745977" target="_blank">
                    <img src="/assets/images/homepage/vimeo-dark.png" alt="Influence Health Vimeo" border="0" class="">
                </a>
            </li>
            <li>
                <a href="https://www.linkedin.com/company/influence-health" target="_blank">
                    <img src="/assets/images/homepage/linkedin-dark.png" alt="Influence Health LinkedIn" border="0" class="">
                </a>
            </li>
            <li>
                <a href="https://twitter.com/InfluenceHlth" target="_blank">
                    <img src="/assets/images/homepage/twitter-dark.png" alt="Influence Health Twitter" border="0" class="">
                </a>
            </li>
            <li>
                <a href="https://www.facebook.com/InfluenceHealth" target="_blank">
                    <img src="/assets/images/homepage/facebook-dark.png" alt="Influence Health Facebook" border="0" class="">
                </a>
            </li>
            <li>
                <a href="tel:855-432-9182" target="_self"></a>
            </li>
        </ul>

<form id="search" action="/sitesearch" method="get" target="_parent" class="active" onsubmit="if (window.navigating) return false">
	<input type="text" id="search-box" name="q" placeholder="search" onfocus="this.placeholder=''" onblur="this.placeholder='search'">
</form></div>




// Mobile Navigation
    $('<ul />').attr('id', 'toggleMenu').addClass('visible-xs').append(
      $('<li />').append(
        $('<a />').append(
          [$('<i />').addClass('fa fa-bars'), $('<i />').addClass('fa fa-search')]
        )
      )
    ).bind('click', function () {
        if ($('#global-menu-output').hasClass('in')) {
            $('#global-menu-output').removeClass('collapsing').removeClass('in');
        } else {
            $('#global-menu-output').addClass('collapse').addClass('in');
        }
    }).appendTo($('#container-utility .pull-right'));
    // Mobile Search
    $('<form />').addClass('form-inline').attr('id', 'search').attr('role', 'form').attr('action', '/search').attr('method', 'GET').attr('onsubmit', '$("#globalSiteSearchBox").val($("#mobileSiteSearchBox").val());$("button#siteSearchButton").click();return false;').append(
        $('<div />').addClass('form-group').append(
            $('<div />').addClass('input-group').append(
                $('<input />').addClass('form-control').attr('type', 'text').attr('name', 'q').attr('id', 'mobileSiteSearchBox').attr('placeholder', 'Search this site...').attr('autocomplete', 'off')
            )
        )
    ).prependTo('#global-menu-output');
    $("a[class^='ih-fa-']").each(function () {
        $(this).prepend($('<i />').addClass('fa ' + $(this).attr('class').replace('ih-', '') + ' visible-xs'));
    });


});
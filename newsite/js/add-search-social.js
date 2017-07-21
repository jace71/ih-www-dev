// Mobile Navigation
$('<ul />').attr('id','toggleMenu').addClass('visible-xs').append(
  $('<li />').append(
    $('<a />').append(
      [$('<i />').addClass('fa fa-bars'),$('<i />').addClass('fa fa-search')]
    )
  )
).bind('click', function(){
    if($('#global-menu-output').hasClass('in')){
        $('#global-menu-output').removeClass('collapsing').removeClass('in');
    }else{
        $('#global-menu-output').addClass('collapse').addClass('in');
    }
}).appendTo($('#container-utility .pull-right'));
// Mobile Search
$('<form />').addClass('form-inline').attr('id','search').attr('role','form').attr('action','/search').attr('method','GET').attr('onsubmit','$("#globalSiteSearchBox").val($("#mobileSiteSearchBox").val());$("button#siteSearchButton").click();return false;').append(
    $('<div />').addClass('form-group').append(
        $('<div />').addClass('input-group').append(
            $('<input />').addClass('form-control').attr('type','text').attr('name','q').attr('id','mobileSiteSearchBox').attr('placeholder','Search this site...').attr('autocomplete','off')
        )
    )
).prependTo('#global-menu-output');
$("a[class^='ih-fa-']").each(function(){
    $(this).prepend($('<i />').addClass('fa ' + $(this).attr('class').replace('ih-','') + ' visible-xs'));
});
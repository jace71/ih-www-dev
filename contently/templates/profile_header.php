<?php
    if(!isset($template_name)){
        $template_name = 'profile_api_key';
    }
    $menu_items = array(
        'profile_api_key'               => 'api',
        'profile_publishing_options'    => 'publish',
        'profile_post_type_mapping'     => 'post_type',
        'profile_configure_mapping'     => 'post_type',
    );
?>

<div class="cl-row cl-text-center cl-profile-nav">
    <a class="cl-col-4 js-ajax-redirect <?php echo $menu_items[$template_name] == 'api' ? 'cl-active' : '' ?>"  data-action="get_profile_api_key"> <?php echo _e('API key', 'contently') ?> </a>
    <a class="cl-col-4 js-ajax-redirect <?php echo $menu_items[$template_name] == 'publish' ? 'cl-active' : '' ?>"  data-action="get_profile_publishing_options"> <?php echo _e('Publishing settings', 'contently') ?> </a>
    <a class="cl-col-4 js-ajax-redirect <?php echo $menu_items[$template_name] == 'post_type' ? 'cl-active' : '' ?>"  data-action="get_profile_post_type_mapping"> <?php echo _e('Post type mapping', 'contently') ?> </a>
</div>
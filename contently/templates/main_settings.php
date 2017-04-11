<?php
if(!isset($profiles) || !is_array($profiles) || count($profiles) < 1) {
    $profiles = self::$default_profile;
}
?>

<h3 class="hndle cl_container_box" style="">
    <span style=""><?php echo _e('Publications', 'contently') ; ?></span>&nbsp;&nbsp;
    <a class=" js-add-api-key" style="color:#f18500; cursor: pointer;"><?php echo _e('Add new', 'contently') ?></a>
    <a class=" js-add-api-key-hidden" style="display:none"><?php echo _e('Add new', 'contently') ?></a>
</h3>
<section class="contently-main-settings">
<form method="post" action="">
    <div class="cl_postbox">
        <?php
        foreach($profiles as $api_key=> $profile_data) {
            //we have one default profile with API Key = string 'api_key'
            if(count($profiles) > 1 && $api_key == 'api_key')
                continue;
            ?>
            <div class="cl_inside js-profile">
                <div class="cl-row">
                    <div>
                        <div class="cl-profile-name"><?php echo isset($profile_data['name']) ? $profile_data['name'] : _e('Publication', 'contently'); ?></div>
                        <input value="<?php echo ($api_key != 'api_key') ? $api_key : ''; ?>" type="hidden" name="api_keys[]" style="width: 300px;" />
                    </div>
                    <div class="cl-right cl-form-control">
                        &nbsp;&nbsp;
                        <a  class=" button button-small js-open-current-profile"><?php echo _e('Open publication', 'contently') ?></a>
                        <a  class=" button button-small js-close-profile" style="display:none"><?php echo _e('Close publication', 'contently') ?></a>
                        &nbsp;&nbsp;
                        <a class="  button button-small js-delete-api-key "><?php echo _e('Delete', 'contently') ?></a>
                    </div>
                </div>
                <div class="js-profile-options"></div>
            </div>
        <?php
        } ?>
        <div class="js-last-block"></div>
    </div>
    <br />
    <div class="cl-row">
    </div>
</form>

<div class="cl_inside js-add-api-key-template hidden js-profile">
    <div class="cl-row">
        <div class="">
            <div class="cl-profile-name"><?php echo _e('Publication', 'contently'); ?></div>
            <input value="" type="hidden" name="api_keys[]" style="width: 300px;" />
        </div>
        <div class="cl-right cl-form-control">
            &nbsp;&nbsp;
            <a  class=" button button-small js-open-current-profile"><?php echo _e('Open publication', 'contently') ?></a>
            <a  class=" button button-small js-close-profile" style="display:none"><?php echo _e('Close publication', 'contently') ?></a>
            &nbsp;&nbsp;
            <a class=" button button-small js-delete-api-key "><?php echo _e('Delete', 'contently') ?></a>
        </div>
    </div>
    <div class="js-profile-options"></div>
</div>
</section>
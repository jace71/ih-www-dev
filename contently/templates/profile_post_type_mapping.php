<?php
$taxonomy           = $profile['author_attributes'];
$mapping_type       = (isset($profile['mapping_type']) && !empty($profile['mapping_type'])) ? $profile['mapping_type'] : self::$default_profile['api_key']['mapping_type'];
$story_post_mapping = (isset($profile['story_post_mapping']) && !empty($profile['story_post_mapping'])) ? $profile['story_post_mapping'] : self::$default_profile['api_key']['story_post_mapping'];
$exlude_posttypes   = array('attachment','revision','nav_menu_item','acf','acf-field-group','acf-field','nf_sub');

$args = array(
    'public'   => true,
    '_builtin' => false
);
$post_types = get_post_types($args);
$post_types += get_post_types();

?>

<div class="width100 js-profile-form">
    <input type="hidden" name="action" value="set_profile_post_type_mapping">
    <input type="hidden" name="api_key" value="<?php echo $api_key;?>">
    <div class="cl_inside">
        <div>
            <input type="radio" <?php if($mapping_type == 'all') { ?> checked="checked" <?php } ?> class="selbox" id="single" name="mapping_type" value="all" /><label for="single" style="vertical-align: 1px;"><?php echo _e('Map all Contently story types to one specific Wordpress post type', 'contently') ?></label><br />
        </div>
        <div style="clear:both;">&nbsp;</div>
        <div>
            <input type="radio" <?php if($mapping_type == 'individual') { ?> checked="checked" <?php } ?>  class="selbox" id="double" name="mapping_type" value="individual"/><label for="double" style="vertical-align: 1px;"> <?php echo _e('Map individual types', 'contently') ?></label><br />
        </div>
        <hr>
        <div class="single_box cl_allboxes" <?php if($mapping_type != 'all') { ?> style="display:none;" <?php } ?>>
            <div class="cl-row">
                <div class="cl-col-3">
                    <strong><?php echo _e('Post type', 'contently') ?></strong>
                </div>
                <div class="cl-col-5">
                    <strong><?php echo _e('Contently story type', 'contently') ?></strong>
                </div>
                <div class="cl-col-3">
                    <strong><?php echo _e('Action', 'contently') ?></strong>
                </div>
            </div>
            <div class="cl-row">
                <div class="cl-col-3">
                    <select name="story_post_mapping[all]" class="js-choose-post-type">
                        <option value=""><?php echo _e('Select post type', 'contently') ?></option>
                        <?php
                        foreach($post_types as $key=>$post_type) {
                            if(!in_array($key, $exlude_posttypes)) {
                                if($story_post_mapping['all']==$key) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                ?>
                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $key; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="cl-col-5">
                    <?php echo _e('All Types', 'contently') ?>
                </div>
                <div class="cl-col-3">
                    <?php
                    if(!empty($story_post_mapping['all'])) { ?>
                        <a class="js-ajax-redirect" data-type="<?php echo $story_post_mapping['all']; ?>" data-action="get_profile_configure_mapping"><?php echo _e('Configure mapping', 'contently') ?></a>
                    <?php } else { echo "Please Update Wordpress Post to Configure mapping"; }?>
                </div>
            </div>
        </div>

        <div class="double_box cl_allboxes" <?php if($mapping_type != 'individual') { ?> style="display:none;" <?php } ?> >

            <div class="cl-row">
                <div class="cl-col-2">
                    <strong><?php echo _e('Post type', 'contently') ?></strong>
                </div>
                <div class="cl-col-5">
                    <strong><?php echo _e('Contently story type', 'contently') ?></strong>
                </div>
                <div class="cl-col-3">
                    <strong><?php echo _e('Action', 'contently') ?></strong>
                </div>
            </div>
            <?php

            foreach($post_types as $key=>$post_type) {
                if(!in_array($key, $exlude_posttypes)) {
                    ?>
                    <div class="cl-row">
                        <div class="cl-col-2">
                            <?php echo $key; ?>
                        </div>
                        <div  class="cl-col-5"><select name="story_post_mapping[<?php echo $key; ?>]">
                                <option value=""><?php echo _e('select', 'contently') ?></option>

                                <?php foreach($taxonomy['story_formats'] as $story_format){
                                    if(!empty($story_post_mapping[$key]) && $story_post_mapping[$key]==$story_format) {
                                        $selected = 'selected="selected"';
                                    } else {
                                        $selected = '';
                                    }
                                    ?>
                                    <option value="<?php echo $story_format; ?>" <?php echo $selected; ?>><?php echo $story_format; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="cl-col-3">
                            <?php if(!empty($story_post_mapping[$key])){ ?>
                                <a class="js-ajax-redirect" data-type="<?php echo $key; ?>" data-action="get_profile_configure_mapping"><?php echo _e('Configure mapping', 'contently') ?></a>
                            <?php } else { echo _e("Please update Wordpress post type to configure mapping", 'contently'); } ?>
                        </div>
                    </div>
                    <?php
                }
            } ?>
        </div>
    </div>
</div>
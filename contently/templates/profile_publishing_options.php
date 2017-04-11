<?php

if( !isset($profile)) {
    die();
}
$publishing_settings = $profile['publishing_settings'];
$is_planned 	     = $publishing_settings['is_planned'];
$is_not_planned      = $publishing_settings['is_not_planned'];
$save_as_draft	     = $publishing_settings['save_as_draft'];
$author_settings     = $profile['author_settings'];
$is_blocked          = $author_settings['is_blocked'];
$uid                 = isset($author_settings['uid']) ? $author_settings['uid'] : '';
$uname               = isset($author_settings['uname']) ? $author_settings['uname'] : '';
$name_format         = $author_settings['name_format'];
$name_formats        = array(
    "@f @l" => 'Full name',
    "@l, @f" => 'Last name, First name',
    "@f" => 'First name only',
    "@l" => 'Last name only',
    "@i" => 'Numeric ID',
)
?>

<div class="width100 js-profile-form">
    <input type="hidden" name="action" value="set_profile_publishing_options">
    <input type="hidden" name="api_key" value="<?php echo $api_key;?>">
    <div class="cl_inside">
        <div style="height:30px;"><strong><?php echo _e('Set how a story is published between Contently and Wordpress.', 'contently') ?></strong></div>
        <div class="cl-row">
            <div class="cl-col-5">
                <div class="cl-row">
                    <label for=""><strong><?php echo _e('When a planned publish date is set', 'contently') ?></strong> </label>
                </div>
            </div>
            <div class="cl-col-7">
                <div class="cl-row">
                    <div class="cl-col-1">
                        <input class="cl-mt-3" id="cl_is_planned_1" type="radio" <?php if($is_planned==1) { ?> checked="checked"<?php } ?> value="1" name="publishing_settings[is_planned]">
                    </div>
                    <div class="cl-col-11">
                        <label for="cl_is_planned_1" ><?php echo _e('Publish story on the planned publish date/time - it can be edited or rescheduled before it\'s published.', 'contently') ?></label>
                    </div>
                </div>
                <div class="cl-row">
                    <div class="cl-col-1">
                        <input  class="cl-mt-3" id="cl_is_planned_2" type="radio" class="form-radio" <?php if($is_planned==0) { ?> checked="checked"<?php } ?>  value="0" name="publishing_settings[is_planned]">
                    </div>
                    <div class="cl-col-11">
                        <label for="cl_is_planned_2" ><?php echo _e('Ignore publish date and save story as a draft - will be manually published.', 'contently') ?> </label>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear:both; height:20px;"><br /></div>
        <div class="cl-row">
            <div class="cl-col-5">
                <div class="cl-row">
                    <label><strong><?php echo _e('When a planned publish date is not set', 'contently') ?></strong> </label>
                </div>
            </div>
            <div class="cl-col-7">
                <div class="cl-row">
                    <div class="cl-col-1">
                        <input  class="cl-mt-3" id="cl_is_not_planned_1" type="radio" class="form-radio" <?php if($is_not_planned==1) { ?> checked="checked"<?php } ?> value="1" name="publishing_settings[is_not_planned]">
                    </div>
                    <div class="cl-col-11">
                        <label for="cl_is_not_planned_1"><?php echo _e('Save story as a draft - will be manually published.', 'contently') ?> </label>
                    </div>
                </div>
                <div class="cl-row">
                    <div class="cl-col-1">
                        <input  class="cl-mt-3" id="cl_is_not_planned_2" type="radio" class="form-radio" <?php if($is_not_planned==0) { ?> checked="checked"<?php } ?>  value="0" name="publishing_settings[is_not_planned]">
                    </div>
                    <div class="cl-col-11">
                        <label for="cl_is_not_planned_2"><?php echo _e('Publish to live site immediately once the story is approved on Contently.', 'contently') ?></label>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear:both; height:20px;"><br /></div>
        <div class="cl-row">
            <div class="cl-col-5">
                <div class="cl-row">
                    <label><strong><?php echo _e('When revisions are made to an existing story', 'contently') ?> </strong></label><br />
                </div>
            </div>
            <div class="cl-col-7">
                <div class="cl-row">
                    <div class="cl-col-1">
                        <input  class="cl-mt-3" id="cl_save_as_draft_1" type="radio" class="form-radio" <?php if($save_as_draft==1) { ?> checked="checked"<?php } ?> value="1" name="publishing_settings[save_as_draft]">
                    </div>
                    <div class="cl-col-11">
                        <label for="cl_save_as_draft_1"><?php echo _e('Revisions are saved as a draft and need to be manually republished.', 'contently') ?></label>
                    </div>
                </div>
                <div class="cl-row">
                    <div class="cl-col-1">
                        <input  class="cl-mt-3" id="cl_save_as_draft_2" type="radio" class="form-radio" <?php if($save_as_draft==0) { ?> checked="checked"<?php } ?>  value="0" name="publishing_settings[save_as_draft]">
                    </div>
                    <div class="cl-col-11">
                        <label for="cl_save_as_draft_2"><?php echo _e('Save and republish the revisions automatically.', 'contently') ?> </label>
                    </div>
                </div>

            </div>
        </div>
<!--        Post Author Settings-->
        <div style="clear:both; height:20px;"><br /></div>
        <div class="fieldset-wrapper">
            <div>
                <div class="cl-row">
                    <div style="height:30px;">
                        <strong><?php echo _e('Choose how an author is assigned to a story.', 'contently') ?></strong>
                    </div>
                </div>
                <div class="cl-row">
                    <div class="cl-col-5">
                        <div class="cl-row">
                            <label><strong><?php echo _e('Select author', 'contently') ?></strong> </label>
                        </div>
                    </div>
                    <div class="cl-col-7">
                        <div class="cl-row">
                            <div class="cl-col-1">
                                <input  class="cl-mt-3" id="is_blocked_1" type="radio" name="author_settings[is_blocked]" value="0" <?php if($is_blocked==0) { ?> checked="checked"<?php } ?> class="form-radio js-author-settings"  data-target="uid"  <?php if($is_blocked == 0) { ?> checked="checked"<?php } ?>>
                            </div>
                            <div class="cl-col-11">
                                <label  for="is_blocked_1"><?php echo _e('Use an existing WP user', 'contently') ?> </label>
                            </div>
                        </div>
                        <div class="cl-row">
                            <div class="cl-col-1">
                                <input  class="cl-mt-3" id="is_blocked_2" type="radio" name="author_settings[is_blocked]" value="1" <?php if($is_blocked==1) { ?> checked="checked"<?php } ?> class="form-radio js-author-settings" data-target="blocked" <?php if($is_blocked == 1) { ?> checked="checked"<?php } ?>>
                            </div>
                            <div class="cl-col-11">
                                <label for="is_blocked_2"><?php echo _e('Create a blocked WP user for Contently users', 'contently') ?> </label>
                            </div>
                        </div>
                    </div>
                </div>

            <div role="application"  class="js-author-uid" <?php echo $is_blocked == 0 ? 'style="display: block;"' : 'style="display: none;"'  ?>>
                <div style="clear:both; height:20px;"><br /></div>
                <div class="cl-row">
                    <div class="cl-col-5">
                        <label for="edit-contently-mapping-settings-author-uid"><strong><?php echo _e('WP user', 'contently') ?></strong> </label>
                    </div>
                    <div class="cl-col-7">
                        <div class="description">
                            <?php echo _e('Choose a WP user to be the author for all your content from Contently. Allowed only existing users.', 'contently') ?>
                        </div>

                        <input type="text" name="author_settings[uname]" value="<?php echo $uname ?>" maxlength="128" class="form-text form-autocomplete" >
                        <input type="text" name="author_settings[uid]" value="<?php echo $uid ?>" class="form-text form-autocomplete-target hidden" >
                        <span id=""></span>
                    </div>
                </div>
            </div>
            <div class="js-author-blocked" <?php echo $is_blocked == 1 ? 'style="display: block;"' : 'style="display: none;"'  ?>>
                <div class="cl-row">
                    <div class="cl-col-5">
                        &nbsp;&nbsp;
                    </div>
                    <div class="cl-col-7">
                        <div class="description">
                            <?php echo _e('A blocked user account will be created for each contributor of the story, which means users won\'t be given login credentials. If there are multiple contributors, the first will be selected since WP only supports one author.', 'contently') ?>
                        </div>
                    </div>
                </div>
                <div style="clear:both; height:20px;"><br /></div>
                <div class="cl-row">
                    <div class="cl-col-5">
                        <div class="cl-row">
                            <label><strong><?php echo _e('Name format', 'contently') ?> </strong></label>
                        </div>
                    </div>
                    <div class="cl-col-7">
                        <div class="description">
                            <?php echo _e('Select how the author and contributor names are displayed when used in the author or other fields. Only for new users', 'contently') ?>
                        </div>
                        <select name="author_settings[name_format]" class="form-select">
                            <?php
                            foreach ($name_formats as $value => $label){
                                $selected = $value == $name_format? 'selected="selected"': '';
                                echo '<option value="' . $value .'" ' . $selected . ' >' . $label . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
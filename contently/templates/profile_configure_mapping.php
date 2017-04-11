<?php
$taxonomy           	= $profile['author_attributes'];
$story_post_mapping 	= $profile['story_post_mapping'];
$mapping_type       	= $profile['mapping_type'];
$obj_post_lab       	= get_post_type_object( $type );
$wp_taxonomy_names  	= get_object_taxonomies( $type );
$fields             	= $this->default_fields;
$wp_custom_fields   	= $this->custom_fields_helper->cl_get_wp_custom_fields( $type );
$cl_custom_fields   	= $this->custom_fields_helper->cl_get_self_custom_fields( $type );
$wp_custom_taxonomies 	= $this->custom_taxonomies_helper->cl_get_wp_custom_taxonomies( $type );

// Create map of ACF fields
$acf_groups_fields = array();
if ( $this->plugins_helper->plugin_acf ) {
	$acf_groups = $this->plugins_helper->get_acf_groups();
	foreach ( $acf_groups as $key => $group ) {
		$acf_groups_fields[ $group->ID ] = array(
			'group'  => $group,
			'fields' => $this->plugins_helper->get_acf_fields( $group->ID )
		);
		// Filter $wp_custom_fields
		foreach ( $acf_groups_fields[ $group->ID ]['fields'] as $field ) {
			if ( $key_wp_cf = array_search( $field['name'], $wp_custom_fields ) ) {
				unset( $wp_custom_fields[ $key_wp_cf ] );
			}
		}
	}
}
?>

<div class="js-profile-form">
	<input type="hidden" name="action" value="set_profile_configure_mapping">
	<input type="hidden" name="target_type" value="<?php echo $type; ?>">
	<input type="hidden" name="api_key" value="<?php echo $api_key; ?>">
	<div class="cl_inside">
		<h3 class="title">
			<?php echo $obj_post_lab->labels->singular_name; ?>: <?php if ( $mapping_type == 'all' ) {
				echo "All Contently story type";
			} else {
				echo $story_post_mapping[ $type ];
			} ?>
		</h3>
		<?php $mapped_fields = $this->get_fields_mapping( $profile, $type ); ?>
		<div class="cl-row head">
			<div class="cl-col-3">WordPress</div>
			<div class="cl-col-6">Contently</div>
		</div>

		<?php
		foreach ( $fields as $key => $name ) {
			//if post type not support this field, continue
			if ( ( $key == 'category' || $key == 'post_tag' ) && ! in_array( $key, $wp_taxonomy_names ) ) {
				continue;
			}
			if ( $key == 'featured_img' && ! post_type_supports( $type, 'thumbnail' ) ) {
				continue;
			}
			if ( $key == 'excerpt' && ! post_type_supports( $type, 'excerpt' ) ) {
				continue;
			}
			?>
			<div class="cl-row">
				<div class="cl-col-3 cl-label"><?php echo $name ?>
					<?php
					$delete = in_array( $key, $cl_custom_fields ) ? '<a class="js-delete-custom-field delete-ico cl-right" > x </a>' : '';
					echo $delete;
					?>
				</div>

				<div
					class="cl-col-6 js-cl-target-<?php echo $key ?>" <?php echo isset( $mapped_fields[ 'use_wp_' . $key ] ) ? 'style="display: none"' : '' ?>>
					<?php $this->get_dropdown_list( $key, $type, $profile ); ?>
				</div>
				<?php if ( $key == 'category' ) { ?>
					<div
						class="cl-col-6 js-target-<?php echo $key ?>" <?php echo ! isset( $mapped_fields[ 'use_wp_' . $key ] ) ? 'style="display: none"' : '' ?>>
						<?php $this->get_dropdown_categories( $type, $profile ); ?>
					</div>
				<?php } ?>
				<?php if ( $key == 'post_tag' ) { ?>
					<div
						class="cl-col-6 js-target-<?php echo $key ?>" <?php echo ! isset( $mapped_fields[ 'use_wp_' . $key ] ) ? 'style="display: none"' : '' ?>>
						<?php $this->get_dropdown_tags( $type, $profile ); ?>
						<!--                            <p>Press and hold "Ctrl" or "Shift" for select multiple items</p>-->
					</div>
				<?php } ?>
				<?php if ( $key == 'post_tag' || $key == 'category' ) { ?>
					<div class="cl-right">
						<input type="checkbox" class="checkbox js-use-wp" data-target-wp="js-target-<?php echo $key ?>"
						       data-target-cl="js-cl-target-<?php echo $key ?>"
						       name="mapping_fields[use_wp_<?php echo $key ?>]" <?php echo isset( $mapped_fields[ 'use_wp_' . $key ] ) ? 'checked="checked"' : '' ?>> <?php echo _e( 'use WP', 'contently' ) ?>
					</div>
				<?php } ?>
			</div>
			<?php
		}

		if ( count( $wp_custom_taxonomies ) >= 1 ) {
			?>
			<div style="clear:both; height:20px;"></div>
			<h3 class="title"> <?php echo _e( 'WP custom taxonomies', 'contently' ) ?> </h3>
			<div class="cl-row head">
				<div class="cl-col-3">WordPress</div>
				<div class="cl-col-6">Contently</div>
			</div>

			<?php foreach ( $wp_custom_taxonomies as $key => $name ) { ?>
				<div class="cl-row">
					<div class="cl-col-3 cl-label"><?php echo $name ?></div>
					<div class="cl-col-6 js-cl-target-<?php echo $key ?>">
						<?php $this->get_dropdown_list( $key, $type, $profile ); ?>
					</div>
				</div>
			<?php } ?>

			<?php
		}

		if ( count( $wp_custom_fields ) >= 1 ) {
			?>
			<div style="clear:both; height:20px;"></div>
			<h3 class="title"> <?php echo _e( 'WP custom fields', 'contently' ) ?> </h3>
			<div class="cl-row head">
				<div class="cl-col-3">WordPress</div>
				<div class="cl-col-6">Contently</div>
			</div>

			<?php
			foreach ( $wp_custom_fields as $key => $name ) {
				if ( is_integer( $key ) ) {
					$key = $name;
				}
				?>
				<div class="cl-row">
					<div class="cl-col-3 cl-label"><?php echo $name ?>
						<?php
						$delete = in_array( $key, $cl_custom_fields ) ? '<a class="js-delete-custom-field delete-ico cl-right" > x </a>' : '';
						echo $delete;
						?>
					</div>
					<div class="cl-col-6 js-cl-target-<?php echo $key ?>">
						<?php $this->get_dropdown_list( $key, $type, $profile ); ?>
					</div>
				</div>
				<?php
			} ?>

			<?php
		} ?>

		<?php if ( $this->plugins_helper->plugin_acf ) { ?>
			<div style="clear:both; height:20px;"></div>
			<h3 class="title"><?php echo _e( 'Advance custom fields plugin', 'contently' ) ?></h3>
			<?php
			//$selected_acf_group_id = $this->get_fields_mapping($profile, $type, $fields_key = 'acf_field_group');
			foreach ( $acf_groups_fields as $group ) {
				echo "<h4 style='margin: 5px 0;'><span style='font-weight: normal;'>Group:</span> " . $group['group']->post_title . "</h4>";
				?>

				<div class="cl-row head">
					<div class="cl-col-3">ACF</div>
					<div class="cl-col-6">Contently</div>
				</div>

				<?php
				$cl_profiles = get_option( 'cl_profiles' );
				foreach ( $group['fields'] as $field ) {
					if ( $field['type'] == 'flexible_content' ) {
						$this->plugins_helper->get_html_acf_flexible_content_field( $field, $type, $cl_profiles[ $api_key ] );
						continue;
					}
					echo '<div class="cl-row">';
					echo '<div class="cl-col-3 cl-label">' . $field['label'] . '</div>';
					echo '<div class="cl-col-6">';
					$this->plugins_helper->get_dropdown_list( $field['key'], $type, $cl_profiles[ $api_key ] );
					echo '</div>';
					echo '</div>';
				}
				echo '<div style="clear:both;"></div>';
			}
			?>
		<?php } ?>
		<?php
		if ( $this->plugins_helper->plugin_yoast_seo ) {
			$yoast_options = $options = get_option('wpseo_titles');
			$meta_keywords_enabled = false;
			if ($yoast_options && isset($yoast_options['usemetakeywords'])){
				$meta_keywords_enabled = $yoast_options['usemetakeywords'];
			}
			?>
			<div style="clear:both; height:20px;"></div>
			<h3 class="cl-pl-0 cl_container_box" style="">
				<span style=""><strong><?php echo _e( 'SEO Yoast plugin fields', 'contently' ) ?></strong></span>
			</h3>
			<div class="cl-row head">
				<div class="cl-col-3">Yoast</div>
				<div class="cl-col-6">Contently</div>
			</div>
			<div>
				<dvi class="cl-row">
					<div class="cl-col-3"> <?php echo _e( 'SEO title:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<?php $this->plugins_helper->get_dropdown_list( '_yoast_wpseo_title', $type, $profile, 'yoast' ); ?>
					</div>
				</dvi>
				<div class="cl-row">
					<div class="cl-col-3"> <?php echo _e( 'Meta description:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<?php $this->plugins_helper->get_dropdown_list( '_yoast_wpseo_metadesc', $type, $profile, 'yoast' ); ?>
					</div>
				</div>
				<!-- Show this section if wpseo_titles[usemetakeywords] option is true -->
				<?php if ($meta_keywords_enabled) { ?>
					<div class="cl-row">
						<div class="cl-col-3"> <?php echo _e( 'Meta keywords:', 'contently' ) ?> </div>
						<div class="cl-col-6">
							<?php $this->plugins_helper->get_dropdown_list( '_yoast_wpseo_metakeywords', $type, $profile, 'yoast' ); ?>
						</div>
					</div>
				<?php } ?>
				<div class="cl-row">
					<div class="cl-col-3"> <?php echo _e( 'Focus keyword:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<?php $this->plugins_helper->get_dropdown_list( '_yoast_wpseo_focuskw', $type, $profile, 'yoast' ); ?>
					</div>
				</div>
			</div>
		<?php }
		if ( $this->plugins_helper->plugin_all_in_one_seo ) {
			$aoisp_fields = $this->get_fields_mapping( $profile, $type, 'mapping_fields_aiosp' );
			?>
			<div style="clear:both; height:20px;"></div>
			<h3 class="title"> <?php echo _e( 'All in One SEO Pack Plugin Fields', 'contently' ) ?> </h3>
			<div>
				<div class="cl-row">
					<div class="cl-col-3"> <?php echo _e( 'Title:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<?php $this->plugins_helper->get_dropdown_list( '_aioseop_title', $type, $profile, 'aiosp' ); ?>
					</div>
				</div>
				<div class="cl-row">
					<div class="cl-col-3"> <?php echo _e( 'Description:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<?php $this->plugins_helper->get_dropdown_list( '_aioseop_description', $type, $profile, 'aiosp' ); ?>
					</div>
				</div>
				<div class="cl-row">
					<div class="cl-col-3"> <?php echo _e( 'Keyword:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<?php $this->plugins_helper->get_dropdown_list( '_aioseop_keywords', $type, $profile, 'aiosp' ); ?>
					</div>
				</div>
				<div class="cl-row" style="height: 30px;">
					<div class="cl-col-3"> <?php echo _e( 'Robots Meta NOINDEX:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<input name="mapping_fields_aiosp[_aioseop_noindex]"
						       type="checkbox" <?php if ( isset( $aoisp_fields['_aioseop_noindex'] ) ) { ?> checked="checked"<?php } ?> >
					</div>
				</div>
				<div class="cl-row" style="height: 30px;">
					<div class="cl-col-3"> <?php echo _e( 'Robots Meta NOFOLLOW:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<input name="mapping_fields_aiosp[_aioseop_nofollow]"
						       type="checkbox" <?php if ( isset( $aoisp_fields['_aioseop_nofollow'] ) ) { ?> checked="checked"<?php } ?>>
					</div>
				</div>
				<div class="cl-row" style="height: 30px;">
					<div class="cl-col-3"> <?php echo _e( 'Robots Meta NOODP:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<input name="mapping_fields_aiosp[_aioseop_noodp]"
						       type="checkbox" <?php if ( isset( $aoisp_fields['_aioseop_noodp'] ) ) { ?> checked="checked"<?php } ?>>
					</div>
				</div>
				<div class="cl-row" style="height: 30px;">
					<div class="cl-col-3"> <?php echo _e( 'Robots Meta NOYDIR:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<input name="mapping_fields_aiosp[_aioseop_noydir]"
						       type="checkbox" <?php if ( isset( $aoisp_fields['_aioseop_noydir'] ) ) { ?> checked="checked"<?php } ?>>
					</div>
				</div>
				<div class="cl-row" style="height: 30px;">
					<div class="cl-col-3"> <?php echo _e( 'Disable on this page/post:', 'contently' ) ?> </div>
					<div class="cl-col-6">
						<input name="mapping_fields_aiosp[_aioseop_disable]"
						       type="checkbox" <?php if ( isset( $aoisp_fields['_aioseop_disable'] ) ) { ?> checked="checked"<?php } ?>>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
<div class="cl-right">
	<a class="js-ajax-redirect button button-primary button-large"
	   data-action="get_profile_post_type_mapping"> <?php echo _e( 'Cancel', 'contently' ) ?> </a>
	&nbsp;&nbsp;
	<a class="js-update button button-primary button-large"> <?php echo _e( 'Save', 'contently' ) ?> </a>
</div>
<div class="cl-row"></div>
<hr>
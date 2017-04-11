<?php
/**
 * Class Contently_Custom_Fields
 *
 * This is a library-helper for working with custom post types
 * Version: 1.0
 * Author: Contently
 * Author URI: http://www.contently.com
 * License: GPL2
 *
 */


Class Contently_Custom_Fields {

	/**
	 * Get existing wordpress custom fields
	 *
	 * @param string $type
	 * @return array
	 */
	public function cl_get_wp_custom_fields($type = 'post'){
		$fields = array();
		global $wpdb;

		$metaKeys = $wpdb->get_results( "SELECT DISTINCT 
  (`$wpdb->postmeta`.`meta_key`) 
FROM
  $wpdb->posts,
  $wpdb->postmeta 
WHERE $wpdb->posts.post_type = '$type' 
  AND (
    $wpdb->posts.post_status = 'publish' 
    OR $wpdb->posts.post_status = 'acf-disabled' 
    OR $wpdb->posts.post_status = 'future' 
    OR $wpdb->posts.post_status = 'draft' 
    OR $wpdb->posts.post_status = 'pending' 
    OR $wpdb->posts.post_status = 'private'
  ) 
  AND `$wpdb->posts`.`ID` = `$wpdb->postmeta`.`post_id` 
  AND `$wpdb->postmeta`.`meta_key` not LIKE '\_%' 
  AND `$wpdb->postmeta`.`meta_key` not LIKE 'cl\_%' 
  AND `$wpdb->postmeta`.`meta_key` not LIKE 'wp\_%'" );

		foreach ($metaKeys as $metaKey){
			$fields['wp_cf_' . $metaKey->meta_key] = $metaKey->meta_key;
		}

		return $fields;
	}

	/**
	 * Get contently custom fields
	 *
	 * @param string $post_type
	 * @param string $mapping_type
	 * @return array
	 */

	public function cl_get_self_custom_fields($post_type = 'post', $mapping_type = 'individual') {

		if($mapping_type != 'individual'){
			$post_type = 'all';
		}
		$result = get_option('cl_custom_fields');
		return isset($result[$post_type]) ? $result[$post_type] : array();
	}

	/**
	 * Save contently custom fields into wp db
	 *
	 * @param $custom_fields
	 * @param string $type
	 */

	public function cl_set_self_custom_fields($custom_fields, $type = 'post') {

		$existing_cl_custom_fields = get_option('cl_custom_fields');
		$existing_cl_custom_fields[$type] = $custom_fields;
		update_option('cl_custom_fields', $existing_cl_custom_fields );
	}

	/**
	 * Find in configuration new custom fields, validate and save them into db
	 *
	 * @param $configuration
	 * @param string $type
	 * @return mixed
	 */
	public function cl_check_custom_fields($configuration, $type = 'post') {

		$existing_fields = $this->cl_get_self_custom_fields($type);
		$cl_cf_fields = array();
		foreach($configuration['mapping_fields'] as $field_name => $value) {
			if (strpos($field_name, "cl_cf") === 0) {
				$name = substr($field_name, 6);
				$cl_cf_fields[$field_name] = $name;
			}
		}
		$new_fields = array_diff_key($cl_cf_fields, $existing_fields);
		foreach($new_fields as $new_field) {
			$name = substr($new_field, 6);
			if (!ctype_alnum($name)) {
				unset($cl_cf_fields[$new_field]);
				unset($configuration['mapping_fields'][$new_field]);
			}
		}
		$this->cl_set_self_custom_fields($cl_cf_fields, $type);

		return $configuration;
	}


}
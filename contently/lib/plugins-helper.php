<?php

/**
 * Class Contently_Plugins_Helper
 *
 * This is a library-helper for working with plugins: SEO Yoast Plugin, Advanced Custom Fields
 * Version: 1.0
 * Author: Contently
 * Author URI: http://www.contently.com
 * License: GPL2
 *
 */
Class Contently_Plugins {

	public $plugin_yoast_seo = false;
	public $plugin_all_in_one_seo = false;
	public $plugin_acf = false;
	public $plugin_acf_version = 'free';

	/**
	 * Check is plugins activated?
	 */

	public function __construct() {

		//verification plugins that have been installed incorrectly
		$active_plugins = get_option( 'active_plugins' );

		if ( ! is_array( $active_plugins ) ) {
			return true;
		}
		foreach ( $active_plugins as $plugin_path ) {
			if ( strpos( $plugin_path, 'acf.php' ) ) {
				$this->plugin_acf = true;
				if ( strpos( $plugin_path, 'pro/acf.php' ) ) {
					$this->plugin_acf_version = 'paid';
				}
			}
			if ( strpos( $plugin_path, 'wp-seo.php' ) ) {
				$this->plugin_yoast_seo = true;
			}
			if ( strpos( $plugin_path, 'all_in_one_seo_pack.php' ) ) {
				$this->plugin_all_in_one_seo = true;
			}
		}
	}

	static function getProtocol() {
		$protocol = 'http';
		if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
			$protocol = 'https';
		}
		if ( isset( $_SERVER['HTTP_REFERER'] ) && preg_match( '%^https://%m', $_SERVER['HTTP_REFERER'] ) ) {
			$protocol = 'https';
		}
		if ( isset( $_SERVER['SERVER_PORT'] ) && (int) $_SERVER['SERVER_PORT'] === 443 ) {
			$protocol = 'https';
		}
		if ( isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
			$protocol = 'https';
		}

		if ( isset( $_SERVER ) && is_array( $_SERVER ) ) {
// @todo Check once
//			$variables = $_SERVER;
//			unset( $variables['HTTP_COOKIE'] );
//			\Contently\Log::instance()->info( 'Check HTTP/HTTPS protocol.', array(
//				'json!__server'    => $variables,
//				'current_protocol' => $protocol
//			) );
		}

		return $protocol;
	}

	//Functions for working with Advanced Custom Fields

	public function rebuild_acf_mapping() {
		$rebuild  = false;
		$profiles = get_option( 'cl_profiles', array() );
		foreach ( $profiles as $key_profile => $profile ) {
			if ( isset( $profile['mapping_array'] ) && is_array( $profile['mapping_array'] ) ) {
				foreach ( $profile['mapping_array'] as $key_map => $map ) {
					if ( isset( $map['mapping_fields_acf'] ) and is_array( $map['mapping_fields_acf'] ) ) {
						foreach ( $map['mapping_fields_acf'] as $acf_key => $acf_value ) {
							if ( strpos( $acf_key, 'field_' ) !== 0 ) {
							    $acf_field = function_exists('acf_get_field') ? acf_get_field( $acf_key ) : $this->get_acf_field_by_name( $acf_key );
								if ( $acf_field ) {
									$new_key = $acf_field['key'];
									if ( $new_key ) {
										$profiles[ $key_profile ]['mapping_array'][ $key_map ]['mapping_fields_acf'][ $new_key ] = $acf_value;
										unset( $profiles[ $key_profile ]['mapping_array'][ $key_map ]['mapping_fields_acf'][ $acf_key ] );
										$rebuild = true;
									}
								}
							}
						}
					}
				}
			}
		}

		if ( $rebuild ) {
			\Contently\Log::instance()->info( 'Rebuild profiles', array( 'json!__new_profile' => $profiles ) );
			update_option( 'cl_profiles', $profiles );
		} else {
			\Contently\Log::instance()->info( 'No rebuild profiles' );
		}
	}

    /**
     * Get acf field by name
     * @param string $name
     * @return array
     */

	public function get_acf_field_by_name( $name ) {
        foreach ( $this->get_acf_groups() as $group ) {
            foreach ( $this->get_acf_fields( $group->ID ) as $field ) {
                if ( strcmp($field['name'], $name) === 0 ) {
                    return $field;
                }
            }
        }
        return null;
    }

    /**
     * Get acf field by key data
     * @param string $key
     * @return array
     */

    public function get_acf_field_by_key( $key ) {
        global $wpdb;
        $field = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s", $key ) );
        return unserialize( $field );
    }

	/**
	 * Get html select with table cells
	 * can get values from ajax
	 *
	 * @param string $selected_acf_group
	 * @param array  $profile
	 * @param string $type
	 */

	public function get_html_acf_fields( $selected_acf_group = '', $profile = array(), $type = '' ) {

		if ( isset( $_REQUEST['api_key'] ) && isset( $_REQUEST['acf_id'] ) ) {
			$selected_acf_group = $_REQUEST['acf_id'];
			$api_key            = $_REQUEST['api_key'];
			$profiles           = get_option( 'cl_profiles' );
			$profile            = $profiles[ $api_key ];
		}

		$fields = $this->get_acf_fields( $selected_acf_group );
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				echo '<div class="cl-row">';
				echo '<div class="cl-col-3" >' . $field['label'] . ':</div>';
				echo '<div class="cl-col-6">';
				$this->get_dropdown_list( $field['name'], $type, $profile );
				echo '</div>';
				echo '</div>';
			}
		} else {
			echo "<div><span colspan='2'>No Fields Found.</span></div>";
		}
	}

    /**
     * Get html ACF Flexible Content field
     * can get values from ajax
     *
     * @param array $field
     * @param string $type
     * @param array $profile
     * @param boolean $children
     */

    public function get_html_acf_flexible_content_field( &$field, &$type, &$profile, $children = false ) {
        if ( empty( $field ) ) return;
        if ( $field['type'] == 'flexible_content' && $this->plugin_acf_version == 'paid' ) {
            if ( ! isset( $field['layouts'] ) || empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) return;
            echo $children ? '<div class="cl-children">' : '<div class="cl-flexible-content-wrap">';
            foreach ( $field['layouts'] as $layout ) {
                if ( isset( $layout['sub_fields'] ) && ! empty( $layout['sub_fields'] ) && is_array( $layout['sub_fields'] ) ) {
                    echo '<div class="cl-row">';
                    echo '<div class="cl-col-3"><strong>' . $field['label'] . '</strong></div>';
                    echo '<div class="cl-col-6">' . $layout['label'] . ':</div>';
                    echo '</div>';
                    foreach ( $layout['sub_fields'] as $sub_field ) {
                        if ( $sub_field['type'] == 'flexible_content' ) {
                            $this->get_html_acf_flexible_content_field( $sub_field, $type, $profile, true );
                        }
                        else {
                            echo '<div class="cl-row">';
                            echo '<div class="cl-col-3 cl-label">' . $sub_field['label'] . '</div>';
                            echo '<div class="cl-col-6">';
                            $this->get_dropdown_list( $sub_field['key'], $type, $profile );
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                }
            }
            echo '</div>';
        }
    }

    /**
     * Prepare field name for Flexible Content type
     * @param array $field
     */

    public function prepare_flexible_content_field( &$field, &$post_id ) {
        if ( function_exists( 'acf_get_field' ) ) {
            if ( isset( $field['parent_layout'] ) && $parent_field = acf_get_field( (int) $field['parent'] ) ) {
                switch ( $parent_field['type'] ) {
                    case 'flexible_content':
                        if ( isset( $parent_field['layouts'] ) && is_array( $parent_field['layouts'] ) && ! empty( $parent_field['layouts'] ) ) {
                            foreach ( $parent_field['layouts'] as $index => $layout ) {
                                if ( strcmp( $layout['key'], $field['parent_layout'] ) === 0 ) {
                                    $this->prepare_flexible_content_field( $parent_field, $post_id );
                                    $field['name'] = "{$parent_field['name']}_{$index}_{$field['name']}";

                                    $layouts = get_post_meta( $post_id, $parent_field['name'], true );
                                    $layouts = ( empty( $layouts ) || ! is_array( $layouts ) ) ? array() : $layouts;

                                    if ( ! in_array( $layout['name'], $layouts ) ) {
                                        $layouts[$index] = $layout['name'];
                                    }

                                    $updatePostMeta = ! update_post_meta( $post_id, $parent_field['name'], $layouts );
                                    if ( $updatePostMeta ) {
                                        add_post_meta( $post_id, $parent_field['name'], $layouts, true );
                                    }

                                    $updatePostMeta = ! update_post_meta( $post_id, '_' . $parent_field['name'], $parent_field['key'] );
                                    if ( $updatePostMeta ) {
                                        add_post_meta( $post_id, '_' . $parent_field['name'], $parent_field['key'], true );
                                    }
                                    break;
                                }
                            }
                        }
                        break;
                }
            }
            else {
                if ( function_exists( 'acf_delete_value' ) && $field['type'] == 'flexible_content' ) {
                    global $cl_fc_clear_fields;
                    if ( ! isset( $cl_fc_clear_fields[$field['key']] ) ) {
                        $cl_fc_clear_fields[$field['key']] = acf_delete_value( $post_id, $field );
                    }
                }
            }
        }
    }

	/**
	 * Get data fir acf group
	 * @return array
	 */

	public function get_acf_groups() {

		if ( $this->plugin_acf_version == 'paid' ) {
			$post_type_acf = 'acf-field-group';
		} else {
			$post_type_acf = 'acf';
		}
		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => $post_type_acf,
			'post_status'    => 'publish'
		);

		return get_posts( $args );
	}

	/**
	 * Get html options for acf group select
	 * can get values from ajax
	 *
	 * @param int $selected_acf_group_id
	 */

	public function get_html_acf_groups( $selected_acf_group_id ) {

		$posts_array = $this->get_acf_groups();

		foreach ( $posts_array as $post_array ) {
			$selected = $selected_acf_group_id == $post_array->ID ? 'selected="selected"' : '';
			echo '<option value="' . $post_array->ID . '" ' . $selected . ' >' . $post_array->post_title . '</option>';
		}
	}

	/**
	 * Get ACF field type
	 *
	 * @param integer $acf_id
	 * @param string   $field_key
	 *
	 * @return string
	 */

	public function get_acf_field_type( $acf_id, $field_key ) {

		$fields_acf = $this->get_acf_fields( $acf_id );
		$f_type     = '';
		foreach ( $fields_acf as $field ) {
			if ( $field['name'] === $field_key ) {

				\Contently\Log::instance()->info( 'Mapping field ACF', array( 'key' => $field_key, 'type' => $field['type'], 'json!__field' => $field ) );

				return $field['type'];
				break;
			}
		}

		return $f_type;
	}

	/**
	 * Get fields
	 *
	 * @param $selected_acf_group
	 *
	 * @return mixed|void
	 */

	public function get_acf_fields( $selected_acf_group ) {

		if ( $this->plugin_acf_version === 'paid' ) {
			$fields = acf_get_fields( $selected_acf_group );
		} else {
			$fields = apply_filters( 'acf/field_group/get_fields', array(), $selected_acf_group );
		}

		return $fields;
	}

	/**
	 * Get html select with options
	 *
	 * @param string $field_name
	 * @param string $type
	 * @param array  $profile
	 * @param string $plugin
	 *
	 * @return html
	 */

	public function get_dropdown_list( $field_name, $type, $profile, $plugin = 'acf' ) {

		$array_constants = array(
			'title'        => 'Title',
			'content'      => 'Content',
			'creator'      => 'Creator',
			'contributors' => 'Contributors'
		);

		$story_fields     = $profile['author_attributes']['publication_custom_fields'];
		$story_attributes = $profile['author_attributes']['publication_tags'];

		if ( $profile['mapping_type'] == 'all' ) {
			$mapping_array = isset( $profile['mapping_array']['all'][ 'mapping_fields_' . $plugin ] ) ? $profile['mapping_array']['all'][ 'mapping_fields_' . $plugin ] : array();
		} else {
			$mapping_array = isset( $profile['mapping_array'][ $type ][ 'mapping_fields_' . $plugin ] ) ? $profile['mapping_array'][ $type ][ 'mapping_fields_' . $plugin ] : array();
		}

		$selected_value = isset( $mapping_array[ $field_name ] ) ? $mapping_array[ $field_name ] : '';

		echo '<select class="cl-form-select" name="mapping_fields_' . $plugin . '[' . $field_name . ']" >';
		echo '<option value="">select</option>';
		foreach ( $array_constants as $key => $array_constant ) {

			if ( $selected_value === "cm_" . $key ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}

			echo '<option value="cm_' . $key . '" ' . $selected . '> ' . $array_constant . '</option>';
		}
		echo '<optgroup label="Custom Fields">';
		foreach ( $story_fields as $story_fields ) {

			if ( $selected_value === 'cf_' . $story_fields['name'] ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			echo '<option value="cf_' . $story_fields['name'] . '" ' . $selected . '> ' . $story_fields['name'] . '</option>';
		}

		echo ' </optgroup>';
		echo ' <optgroup label="Tags">';

		foreach ( $story_attributes as $attributes_values ) {
			if ( $selected_value === 'attr_' . $attributes_values['name'] ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			echo '<option value="attr_' . $attributes_values['name'] . '" ' . $selected . '> ' . $attributes_values['name'] . '</option>';
		}
		echo '</optgroup>';
		echo ' </select>';
	}
}
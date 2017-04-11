<?php

/**
 * Class Contently_Webhook_Response
 *
 * This is a library-helper for working with custom post types
 * Version: 1.0
 * Author: Contently
 * Author URI: http://www.contently.com
 * License: GPL2
 *
 */
Class Contently_Webhook_Response {

	public $plugins_helper;

	/**
	 * Constructor
	 *
	 */

	public function __construct( Contently_Plugins $plugins_helper ) {
		$this->plugins_helper = $plugins_helper;
	}

	/**
	 * Create or update the post with contently story
	 *
	 * @param $contently_story
	 * @param $profile
	 * @param $api_key
	 */

	public function create_update_post_from_cl( $contently_story, $profile, $api_key ) {
		$this->plugins_helper->rebuild_acf_mapping();

		global $post, $wp_rewrite;
		$logger = \Contently\Log::instance();

		// Get the Post Type
		$story_post_type = false;
		if ( isset( $contently_story->tags ) ) {
			foreach ( $contently_story->tags as $tag ) {
				if ( strtoupper( $tag->name ) === 'WP POST TYPE' && is_array( $tag->values ) ) {
					foreach ( $tag->values as $value ) {
						$story_post_type = $value->name;
					}
				}
			}
		}

		// @todo Move to new function
		$exclude_post_types = array( 'attachment', 'revision', 'nav_men u_item', 'acf', 'acf-field-group', 'acf-field', 'nf_sub' );
		$args               = array(
			'public'   => true,
			'_builtin' => false
		);
		$post_types         = get_post_types( $args );
		$post_types += get_post_types();
		foreach ( $exclude_post_types as $key => $post_type ) {
			if ( array_key_exists( $post_type, $post_types ) ) {
				unset( $post_types[ $post_type ] );
			}
		}

		if ( ! in_array( $story_post_type, $post_types ) ) {
			//@todo If $post_types not exist
			$logger->info( 'Post type not exist', array(
				'json!__$story_post_type' => $story_post_type,
				'json!__$post_types'      => $post_types,
			) );
		} else {
			$logger->info( 'Post type was found', array(
				'json!__$story_post_type' => $story_post_type,
				'json!__$post_types'      => $post_types,
			) );
		}


		//get saved post type
		if ( $profile['mapping_type'] == 'all' ) {
			$mapped_post_type = $profile['story_post_mapping']['all'];
		} else {
			if ( $story_post_type ) {
				$mapped_post_type = $story_post_type;
			} else {
				$mapped_post_type = array_search( $contently_story->story_format, $profile['story_post_mapping'] );
			}
		}

		$post_status = $this->get_post_status( $profile['publishing_settings'], $contently_story );
		$logger->info( 'Get post status', array( $post_status ) );

		//get saved mapping options
		if ( $profile['mapping_type'] === 'all' ) {
			$mapping_array = $profile['mapping_array']['all'];
		} elseif ( isset( $profile['mapping_array'][ $mapped_post_type ] ) ) {
			$mapping_array = $profile['mapping_array'][ $mapped_post_type ];
		} else {
			//@todo if no mapping array
			\Contently\Log::instance()->alert( 'No mapping array.', array(
				'mapping_type'       => $profile['mapping_type'],
				'post_type_selected' => $mapped_post_type
			) );
			\Contently\Log::instance()->info( 'Check the mapping for the post type "' . $mapped_post_type . '"', '' );
			die();
		}

		$logger->info( 'Use mapping options', array(
			'json!__mapping' => $mapping_array
		) );

		//prepare required data
		if ( ! isset( $mapping_array['mapping_fields']['title'] ) ) {
			$mapping_array['mapping_fields']['title'] = 'cm_title';
		}

		if ( ! isset( $mapping_array['mapping_fields']['body'] ) ) {
			$mapping_array['mapping_fields']['body'] = 'cm_content';
		}

		if ( ! isset( $mapping_array['mapping_fields']['author'] ) ) //            $mapping_array['mapping_fields']['author'] = 'cm_creator';
		{
			$mapping_array['mapping_fields']['author'] = 'cm_contributors';
		}


		$title_field  = $this->get_mapped_value( $mapping_array['mapping_fields']['title'], $contently_story );
		$body_field   = $this->get_mapped_value( $mapping_array['mapping_fields']['body'], $contently_story );
		$author_field = $this->get_mapped_value( $mapping_array['mapping_fields']['author'], $contently_story );

		$author_id     = $this->get_existing_or_add_new_user( $author_field, $profile );
		$story_content = $this->find_replace_external_urls( $body_field );

		$prepared_post = array(
			'post_title'   => $title_field,
			'post_content' => $story_content,
			'post_status'  => $post_status,
			'post_author'  => $author_id,
			'post_type'    => $mapped_post_type,
		);

		if ( $prepared_post['post_author'] == 0 ) {
			unset( $prepared_post['post_author'] );
		}

		$logger->info( 'Prepared post', array(
			'json!__post_data' => $prepared_post
		) );

		//if story exist, get story-post id
		$check_existing_story = $this->check_existing_story( $contently_story->id );

		if ( ! empty( $check_existing_story->post_id ) ) {

			if ( $profile['publishing_settings']['save_as_draft'] == 1 ) {
				$post_status = 'draft';
			} else {
				$post_status = 'publish';
			}
			$prepared_post['ID']          = $check_existing_story->post_id;
			$prepared_post['post_status'] = $post_status;
			wp_update_post( $prepared_post, true );
			$post_id = $check_existing_story->post_id;

			//create new story-post, get story-post id
		} else {
			if ( $post_status == 'future' ) {
				$post_publish_date              = get_date_from_gmt( date( 'Y-m-d H:i:s', $contently_story->publish_at ), 'Y-m-d H:i:s' );
				$prepared_post['post_date']     = $post_publish_date;
				$prepared_post['post_date_gmt'] = $post_publish_date;
			}
			$post_id = wp_insert_post( $prepared_post, true );
		}

		if ( is_wp_error( $post_id ) ) {
			$logger->error( 'Error processing request', array( 'json!__error' => json_encode( $post_id ) ) );
			die();
		}

		update_post_meta( $post_id, '_cl_story_id', $contently_story->id );
		update_post_meta( $post_id, '_cl_api_key', $api_key );

		$logger->info( 'Content mapping data', array(
			'post_id'                => $post_id,
			'json!__mapping_array'   => $mapping_array,
			'json!__contently_story' => $contently_story
		) );

		$logger->info( 'taxonomy_mapping' );
		$this->taxonomy_mapping( $post_id, $mapping_array, $contently_story );

		$logger->info( 'featured_image_mapping' );
		$this->featured_image_mapping( $post_id, $mapping_array, $contently_story );

		$logger->info( 'excerpt_mapping' );
		$this->excerpt_mapping( $post_id, $mapping_array, $contently_story );

        $logger->info( 'custom_taxonomies_mapping' );
        $this->custom_taxonomies_mapping( $post_id, $mapping_array, $contently_story );

		$logger->info( 'custom_fields_mapping' );
		$this->custom_fields_mapping( $post_id, $mapping_array, $contently_story );

		$logger->info( 'plugins_fields_mapping' );
		$this->plugins_fields_mapping( $post_id, $mapping_array, $contently_story );

		if ( $post_status == 'publish' ) {
			$this->mark_published( $post_id, $api_key );
		}
	}

	/**
	 * Mark Contently story as published and set published url
	 *
	 * @param $post_id
	 * @param $api_key
	 */

	public function mark_published( $post_id, $api_key ) {
		$published_url = get_permalink( $post_id );
		$story_id      = get_post_meta( $post_id, '_cl_story_id', true );

		$API = new \Contently\Connector( array( 'key' => $api_key ) );
		$API->setPublished( $story_id, $published_url );
	}

	/**
	 * Mapping taxonomies category and tags
	 *
	 * @param $post_id
	 * @param $mapping_array
	 * @param $contently_story
	 */

	public function taxonomy_mapping( $post_id, $mapping_array, $contently_story ) {

		if ( isset( $mapping_array['mapping_fields']['use_wp_category'] ) ) {
			$categories_ids = $mapping_array['mapping_fields']['wp_category'];
		} else {
			if ( isset( $mapping_array['mapping_fields']['category'] ) ) {
				$category       = $this->get_mapped_value( $mapping_array['mapping_fields']['category'], $contently_story );
				$categories_ids = $this->prepare_tags_ids_from_string( $category );
			}
		}

		if ( isset( $categories_ids ) && count( $categories_ids ) > 0 ) {
			wp_set_post_categories( $post_id, $categories_ids, false );
		}

		if ( isset( $mapping_array['mapping_fields']['use_wp_post_tag'] ) ) {
			$tags = $mapping_array['mapping_fields']['wp_post_tag'];
		} else {
			if ( isset( $mapping_array['mapping_fields']['post_tag'] ) ) {
				$tags = $this->get_mapped_value( $mapping_array['mapping_fields']['post_tag'], $contently_story );
			}
		}

		if ( isset( $tags ) ) {
			$tags_ids = $this->prepare_tags_ids_from_string( $tags, 'post_tag' );

			if ( count( $tags_ids ) > 0 ) {
				wp_set_post_terms( $post_id, $tags_ids, 'post_tag', false );
			}
		}

	}

	/**
	 * Mapping thumbnail
	 *
	 * @param $post_id
	 * @param $mapping_array
	 * @param $contently_story
	 */

	public function featured_image_mapping( $post_id, $mapping_array, $contently_story ) {
		$featured_img_field = $this->get_mapped_value( $mapping_array['mapping_fields']['featured_img'], $contently_story, true );
		// Setting the featured image

		if ( ! empty( $featured_img_field ) ) {
			$attachment_id = $this->get_image_byurl( $featured_img_field );
			if ( $attachment_id ) {
				set_post_thumbnail( $post_id, $attachment_id );
			}
		}
	}

	/**
	 * Mapping excerpt
	 *
	 * @param $post_id
	 * @param $mapping_array
	 * @param $contently_story
	 */

	public function excerpt_mapping( $post_id, $mapping_array, $contently_story ) {
		if ( isset( $mapping_array['mapping_fields']['excerpt'] ) ) {
			$post_excerpt = $this->get_mapped_value( $mapping_array['mapping_fields']['excerpt'], $contently_story );
			$post         = array(
				'ID'           => $post_id,
				'post_excerpt' => $post_excerpt,
			);
			wp_update_post( $post );
		}
	}

    /**
     * Mapping wp and self custom taxonomies
     *
     * @param $post_id
     * @param $mapping_array
     * @param $contently_story
     */

    public function custom_taxonomies_mapping( $post_id, $mapping_array, $contently_story ) {
        foreach ( $mapping_array['mapping_fields'] as $key => $value ) {
            $tags = $taxonomy = '';
            if ( substr( $key, 0, 6 ) == 'wp_ct_' ) {
                $taxonomy = substr( $key, 6 );
            }
            if ( ! empty( $taxonomy ) ) {
                $tags = $this->get_mapped_value( $mapping_array['mapping_fields'][ $key ], $contently_story );
            }
            if ( ! empty( $tags ) ) {
                $tags_ids = $this->prepare_tags_ids_from_string( $tags, $taxonomy );
                if ( ! empty( $tags_ids ) ) {
                    if ( $this->is_custom_post_type( $post_id ) ) {
                        $result = wp_set_object_terms( $post_id, $tags_ids, $taxonomy, false );
                    }
                    else {
                        $result = wp_set_post_terms( $post_id, $tags_ids, $taxonomy, false );
                    }
                    \Contently\Log::instance()->info( 'Set custom taxonomy', array(
                        'key'               => $key,
                        'value'             => $value,
                        'mapped_value'      => $tags,
                        'affected_value'    => $result
                    ) );
                }
            }
        }
    }

	/**
	 * Mapping wp and self custom fields
	 *
	 * @param $post_id
	 * @param $mapping_array
	 * @param $contently_story
	 */

	public function custom_fields_mapping( $post_id, $mapping_array, $contently_story ) {

		foreach ( $mapping_array['mapping_fields'] as $key => $value ) {
			$mapped_value = $name = '';
			if ( substr( $key, 0, 6 ) == 'wp_cf_' ) {
				$name = substr( $key, 6 );
			} elseif ( substr( $key, 0, 6 ) == 'cl_cf_' ) {
//                $name = $key;
				$name = substr( $key, 6 );
			}
			if ( ! empty( $name ) ) {
				$mapped_value = $this->get_mapped_value( $mapping_array['mapping_fields'][ $key ], $contently_story );
				$mapped_value = $this->find_replace_external_urls( $mapped_value );
			}
			if ( ! empty( $mapped_value ) ) {
				if ( ! update_post_meta( $post_id, $name, $mapped_value ) ) {
					add_post_meta( $post_id, $name, $mapped_value, true );
				}
//                add_post_meta($post_id, $name, $mapped_value, true) or update_post_meta($post_id, $name, $mapped_value);
			}
		}
	}

	/**
	 * Mapping plugin fields
	 *
	 * @param $mapping_array
	 * @param $post_id
	 * @param $contently_story
	 */

	public function plugins_fields_mapping( $post_id, $mapping_array, $contently_story ) {

		//mapping plugins fields
		$plugins = array( 'acf', 'yoast', 'aiosp' );

		foreach ( $plugins as $plugin ) {
			if ( isset( $mapping_array[ 'mapping_fields_' . $plugin ] ) && is_array( $mapping_array[ 'mapping_fields_' . $plugin ] ) && count( $mapping_array[ 'mapping_fields_' . $plugin ] ) > 0 ) {
				foreach ( $mapping_array[ 'mapping_fields_' . $plugin ] as $key => $value ) {
					$mapped_value = '';
					//if plugin acf and field type is image, get image
					if ( $plugin === 'acf' ) {
						$field  = function_exists('acf_get_field') ? acf_get_field( $key ) : $this->plugins_helper->get_acf_field_by_key( $key );
						$f_type = $field['type'];
                        switch ( $f_type ) {
                            case 'image':
                                $field_value = $this->get_mapped_value( $value, $contently_story, true );

                                \Contently\Log::instance()->info( 'Set image to a field value', array( 'key' => $key, 'field' => $value, 'value' => $field_value ) );

                                if ( ! empty( $field_value ) ) {
                                    $mapped_value = $this->get_image_byurl( $field_value );
                                    \Contently\Log::instance()->info( 'Get image by URL', array( 'mapped_value' => $mapped_value, 'field_value' => $field_value ) );
                                }
                                break;
                            case 'taxonomy':
                                $mapped_value = $this->get_mapped_value( $value, $contently_story, false );
                                if ( $mapped_value && isset($field['taxonomy']) ) {
                                    if ( $taxonomy = $field['taxonomy'] ) {
                                        $mapped_value = $this->prepare_tags_ids_from_string( $mapped_value, $taxonomy );
                                    }
                                }
                                break;
                            default:
                                $mapped_value = $this->get_mapped_value( $value, $contently_story, false );
                                break;
                        }
						//if plugin aiosp and field is checkbox, get checkbox value
					} elseif ( $plugin == 'aiosp' && $value == 'on' ) {
						$mapped_value = 'on';
					}

					\Contently\Log::instance()->info( 'Set mapped value', array(
						'plugin'       => $plugin,
						'mapped_value' => $mapped_value,
						'key'          => $key,
						'value'        => $value
					) );

					//if none of previous, get mapped value
					if ( empty( $mapped_value ) || $mapped_value === false ) {
						$mapped_value = $this->get_mapped_value( $mapping_array[ 'mapping_fields_' . $plugin ][ $key ], $contently_story );
					}
					//if mapped value exist, add post meta
					if ( ! empty( $mapped_value ) ) {
						if ( $plugin === 'acf' ) {
                            $this->plugins_helper->prepare_flexible_content_field($field, $post_id);
							if ( function_exists( 'acf_update_value' ) ) {
                                acf_update_value( $mapped_value, $post_id, $field );
                            }
                            else if ( has_action( 'acf/update_value' ) ) {
                                do_action( 'acf/update_value', $mapped_value, $post_id, $field );
                            }
						}
						else {
                            $updatePostMeta = ! update_post_meta( $post_id, $key, $mapped_value );
                            if ( $updatePostMeta ) {
                                add_post_meta( $post_id, $key, $mapped_value, true );
                            }
                        }
					}
				}
			}
		}
	}

    /**
     * Check if a post is a custom post type.
     *
     * @param  mixed $post Post object or ID
     *
     * @return boolean
     */

    function is_custom_post_type( $post = NULL )
    {
        $all_custom_post_types = get_post_types( array ( '_builtin' => false ) );

        // there are no custom post types
        if ( empty ( $all_custom_post_types ) )
            return false;

        $custom_types      = array_keys( $all_custom_post_types );
        $current_post_type = get_post_type( $post );

        // could not detect current type
        if ( ! $current_post_type )
            return false;

        return in_array( $current_post_type, $custom_types );
    }

	/**
	 * Check is post with story already exist
	 *
	 * @param $id
	 *
	 * @return array|null|object|void
	 */

	public function check_existing_story( $id ) {

		global $wpdb;
		$result = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_cl_story_id' AND meta_value = " . $id . " " );

		return $result;
	}

	/**
	 * Generate the post status regarding profile settings and story attributes
	 *
	 * @param $publishing_settings
	 * @param $contently_story
	 *
	 * @return string
	 */

	public function get_post_status( $publishing_settings, $contently_story ) {

		$post_status    = '';
		$is_planned     = $publishing_settings['is_planned'];
		$is_not_planned = $publishing_settings['is_not_planned'];
		$planned_date   = $contently_story->publish_at;
		$cur_date       = time();

		if ( ! empty( $contently_story->publish_at ) ) {
			if ( $is_planned == 1 ) {
				if ( $planned_date < $cur_date ) {
					$post_status = 'publish';
				} else {
					$post_status = 'future';
				}
			} elseif ( $is_planned == 0 ) {
				$post_status = 'draft';
			}
		} else {
			if ( $is_not_planned == 1 ) {
				$post_status = 'draft';
			} elseif ( $is_not_planned == 0 ) {
				$post_status = 'publish';
			}
		}

		return $post_status;
	}

	/**
	 * Check is user already exist. If not create a new one
	 *
	 * @param string $user_data_string
	 * @param array  $profile
	 *
	 * @return int $id
	 */

	function get_existing_or_add_new_user( $user_data_string, $profile ) {

		$data = explode( '@@', $user_data_string );
		if ( empty( $data ) || ! is_array( $data ) || ! isset( $data[2] ) ) {
			return 0;
		}
		$fName = $data[0];
		$lName = $data[1];
		$email = $data[2];

		//if in settings isset one author for all publications
		if ( ! $profile['author_settings']['is_blocked'] ) {
			if ( isset( $profile['author_settings']['uid'] ) ) {
				return $profile['author_settings']['uid'];
			} else {
				return 0;
			}
		}

		$chkUser = get_user_by( 'email', $email );
		if ( ! empty( $chkUser ) ) {
			$user_id = $chkUser->ID;
		} else {
			// generating name regarding name format
			$format = explode( '@', $profile['author_settings']['name_format'] );
			$result = '';
			foreach ( $format as $key ) {
				if ( trim( $key ) == 'f' ) {
					$result = $result . ucfirst( $fName );
				} elseif ( trim( $key ) == 'l' ) {
					$result = $result . ucfirst( $lName );
				} elseif ( trim( $key ) == 'l,' ) {
					$result = $result . ucfirst( $lName ) . ',';
				}
				$result = $result . ' ';
			}

			//prepare user data for creating new user

			$user_data = array(
				'user_login'           => $fName . $lName,
				'first_name'           => $fName,
				'last_name'            => $lName,
				//if author is blocked we add new user with random password, minimal role, and no pass to toolbar
				'user_pass'            => $profile['author_settings']['is_blocked'] ? substr( md5( rand( 0, 1000 ) ), 0, 7 ) : 'contently_' . $fName,
				'role'                 => $profile['author_settings']['is_blocked'] ? 'subscriber' : 'author',
				'show_admin_bar_front' => $profile['author_settings']['is_blocked'] ? 'false' : 'true',
				'user_email'           => $email,
				'user_nicename'        => $result,
				'display_name'         => $result,
			);
			$user_id   = wp_insert_user( $user_data );
		}

		return $user_id;
	}

	/**
	 * Get value from story content for mapping
	 *
	 * @param string     $variable
	 * @param array      $content
	 * @param bool|false $is_image
	 *
	 * @return string
	 */

	public function get_mapped_value( $variable = '', $content = array(), $is_image = false ) {
		$value          = '';
		if ( strpos( $variable, 'cm_' ) === 0 ) {
            $variable_break = substr( $variable, 3 );
			if ( $variable_break == 'creator' ) {
				$first_name = $content->creator->first_name;
				$last_name  = $content->creator->last_name;
				$email      = $content->creator->email;

				$value = $first_name . '@@' . $last_name . '@@' . $email;

			} elseif ( $variable_break == 'contributors' ) {
				/// getting the first contributor only
				$first_name = $content->contributors[0]->first_name;
				$last_name  = $content->contributors[0]->last_name;
				$email      = $content->contributors[0]->email;

				$value = $first_name . '@@' . $last_name . '@@' . $email;
			} else {
				$param_name = $variable_break;
				$value      = isset( $content->$param_name ) ? $content->$param_name : '';
			}

		} elseif ( strpos( $variable, 'cf_' ) === 0 ) {
            $variable_break = substr( $variable, 3 );
			foreach ( $content->custom_fields as $custom_field ) {
				if ( $custom_field->name === $variable_break ) {
					if ( $is_image ) {
						$value = $custom_field->asset_url;
					} else {
						$value = $custom_field->content;
					}
				}
			}
		} elseif ( strpos( $variable, 'attr_' ) === 0 ) {
            $variable_break = substr( $variable, 5 );
			foreach ( $content->tags as $tag ) {
				if ( $tag->name === $variable_break ) {
					foreach ( $tag->values as $tt ) {
						$value .= $tt->name . ",";
					}
				}
			}
		}

		return $value;
	}

	/**
	 * Get tags ids from tags string
	 *
	 * @param        $tags_string
	 * @param string $taxonomy
	 *
	 * @return array
	 */

	public function prepare_tags_ids_from_string( $tags_string, $taxonomy = 'category' ) {

		$result = array();

		$tags_names = explode( ',', $tags_string );
		foreach ( $tags_names as $name ) {
			if ( empty( $name ) || ! is_string( $name ) ) {
				continue;
			}
			$name = trim( $name );

			if ( ! $term = term_exists( $name, $taxonomy ) ) {
				$tag      = wp_insert_term( $name, $taxonomy );
				$result[] = (int) $tag['term_id'];
			} else {
				$result[] = (int) $term['term_id'];
			}
		}

		return $result;
	}

	/**
	 * Find and replace external urls in field content
	 *
	 * @param $content
	 *
	 * @return mixed
	 */

	public function find_replace_external_urls( $content ) {
        if ( ! is_string( $content ) ) {
            return 'Invalid Content format';
        }

        //iframes
        preg_match_all( '#<iframe[^>]*>[^<]*(</iframe>)?#i', $content, $iframes );
        if ( $iframes ) {
            foreach ( $iframes[0] as $iframe ) {
                preg_match( '#src\s*=\s*["\']([^"\']+)#i', $iframe, $data );
                if ( isset( $data[1] ) && ! empty( $data[1] ) ) {
                    $src = trim( $data[1] );
                    if ( filter_var( $src, FILTER_VALIDATE_URL ) === false ) {
                        continue;
                    }
                    $args = array_merge( array( 'width' => '', 'height' => '' ), wp_embed_defaults() );
                    preg_match( '#width\s*=\s*["\']([^"\']+)#i', $iframe, $data );
                    if ( isset( $data[1] ) && ! empty( $data[1] ) ) {
                        $args['width'] = trim( $data[1] );
                    }
                    preg_match( '#height\s*=\s*["\']([^"\']+)#i', $iframe, $data );
                    if ( isset( $data[1] ) && ! empty( $data[1] ) ) {
                        $args['height'] = trim( $data[1] );
                    }

                    $embed = "[embed width=\"{$args['width']}\" height=\"{$args['height']}\"]{$src}[/embed]";
                    $content = str_replace( $iframe, $embed, $content );
                }
            }
        }

        //images
        preg_match_all( '#<img[^>]*>#i', $content, $images );
        if ( $images ) {
            foreach ( $images[0] as $image ) {
                preg_match( '#src\s*=\s*["\']([^"\']+)#i', $image, $data );
                if ( isset( $data[1] ) && ! empty( $data[1] ) ) {
                    $src = trim( $data[1] );
                    $attachment_id  = $this->get_image_byurl( $src );
                    if ( $attachment_id ) {
                        $attachment_image = wp_get_attachment_image( $attachment_id, 'full', false, array(
                            'class' => 'wp-image-' . $attachment_id
                        ) );
                        if ( ! empty( $attachment_image ) ) {
                            $content = str_replace( $image, $attachment_image, $content );
                        }
                    }
                }
            }
        }

        return $content;
	}

	/**
	 * Get the image by url
	 *
	 * @param $inPath
	 *
	 * @return int
	 */

	function get_image_byurl( $inPath ) {

		if ( filter_var( $inPath, FILTER_VALIDATE_URL ) === false ) {
			return false;
		}

        $response = wp_remote_head( $inPath );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $headers        = wp_remote_retrieve_headers( $response );
        $content_type   = $headers['content-type'];
        $content_length = (int) $headers['content-length'];

        if ( empty( $content_type ) || strpos( $content_type, 'image' ) === false ) {
            return false;
        }

        $url        = wp_parse_url( $inPath );
        $filename   = sanitize_file_name( pathinfo( $url['path'], PATHINFO_FILENAME ) );
        $ext        = pathinfo( $url['path'], PATHINFO_EXTENSION );

        if ( empty( $ext ) ) {
            $mimes  = get_allowed_mime_types();
            $mime   = array_search( $content_type, $mimes );
            if ( empty( $mime ) ) {
                return false;
            }
            $mimes  = explode( '|', $mime );
            $ext    = $mimes[0];
        }

        $ext        = sanitize_mime_type( $ext );
        $upload_dir = wp_upload_dir();
        $outPath    = $upload_dir['path'] . '/' . $filename . '.' . $ext;

        if ( file_exists( $outPath ) ) {
            $attach_id      = $this->get_attachment_id_from_url( $upload_dir['subdir'] . '/' . $filename . '.' . $ext );
            $attach_path    = get_attached_file( $attach_id );
            if ( file_exists( $attach_path ) && strcmp( $outPath, $attach_path ) === 0 && filesize( $attach_path ) === $content_length ) {
                return $attach_id;
            }
        }

        $index = 0;
        while ( file_exists( $outPath ) ) {
            $index++;
            $outPath = $upload_dir['path'] . '/' . $filename . '-' . $index . '.' . $ext;
        }

        $in  = @fopen( $inPath, "rb" );
        $out = @fopen( $outPath, "wb" );

        if ( $in !== false && $out !== false ) {
            while ( $chunk = fread( $in, 8192 ) ) {
                fwrite( $out, $chunk, 8192 );
            }
            fclose( $in );
            fclose( $out );
        } else {
            return false;
        }

        $attachment = array(
            'guid' => $upload_dir['url'] . '/' . $filename . '.' . $ext,
            'post_mime_type' => $content_type,
            'post_title' => $filename,
            'post_content' => '',
            'post_status' => 'inherit'
        );

        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        $attach_id = wp_insert_attachment( $attachment, $outPath );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $outPath );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
	}

    /**
     * Find attachment by url
     *
     * @param $url
     *
     * @return mixed
     */

	function get_attachment_id_from_url ( $url ) {
        global $wpdb;
        return $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid LIKE %s ORDER BY ID DESC LIMIT 1;", '%' . $url ) );
    }
}
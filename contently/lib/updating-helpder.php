<?php

/**
 * Class Contently_Updating
 *
 * This is a library-helper for working with custom post types
 * Version: 1.0
 * Author: Contently
 * Author URI: http://www.contently.com
 * License: GPL2
 *
 */
Class Contently_Updating {

	public $default_profile;
	public $old_options = array(
		'contently_apiKey',
		'ct_p_type',
		'ct_p_taxonomy',
		'publishing_settings',
		'contently_author_attributes',
		'mapping_type',
		'story_post_mapping',
		'mapping_array',
		'mapping_array_post',
		'mapping_array_page',
		'mapping_array_acf_post',
		'mapping_array_acf_page',
		'ct_p_taxonomy_post',
		'ct_p_taxonomy_page',
		'ct_p_tags_post',
		'ct_p_tags_page',
		'acffieldgrouppost',
		'acffieldgrouppage',
		'cl_data',
		'ttttt',
	);

	public function get_old_options() {

		$old_params = array();
		foreach ( $this->old_options as $option ) {
			$old_params[ $option ] = get_option( $option );
		}

		return $old_params;
	}


	public function __construct( $default_profile ) {

		$this->default_profile = $default_profile;
	}

	public function run() {

		$params = $this->get_old_options();
		$this->set_new_options( $params );
//        $this->keep_deprecated_options($params);
		$this->delete_deprecated_options();
	}

//functions for plugin updating

	public function set_new_options( $old_params ) {

		$profiles = get_option( 'cl_profiles' );
		if ( ! is_array( $profiles ) ) {
			$profiles = array();
		}

		if ( is_string( $old_params['contently_apiKey'] ) ) {
			$api_key              = $old_params['contently_apiKey'];
			$profiles[ $api_key ] = array();
			$profile              = &$profiles[ $api_key ];
			$profile += $this->default_profile['api_key'];
		} else {
			return array(
				'error' => true,
				'text'  => 'no api key founded',
			);
		}

		$API = new \Contently\Connector( array( 'key' => $api_key ) );

		if ( ! \Contently\Connector::keyValidator( $api_key ) ) {
			$profile['authorized'] = false;
		}

		$result = $API->getTaxonomy();
		if ( ! isset( $result->error ) ) {
			$profile['author_attributes'] = is_object( $result ) ? json_decode( json_encode( $result ), true ) : $result;
			$profile['authorized']        = true;
		} else {
			$profile['authorized'] = false;
		}

		$result = $API->setWebHook( home_url('', Contently_Plugins::getProtocol()) );

		if ( ! isset( $result->error ) && isset( $result->webhook_url ) ) {
			$profile['webhook'] = $result->webhook_url;
			$profile['name']    = $result->name;
		} else {
			$profile['authorized'] = false;
		}

		if ( is_array( $old_params['publishing_settings'] ) ) {
			$profile['publishing_settings'] = $old_params['publishing_settings'];
		}
		if ( is_array( $old_params['story_post_mapping'] ) ) {
			$profile['story_post_mapping'] = $old_params['story_post_mapping'];
		}
		if ( is_string( $old_params['mapping_type'] ) ) {
			$profile['mapping_type'] = $old_params['mapping_type'];
		}
		if ( is_string( $old_params['ct_p_type'] ) ) {
			$profile['post_type'] = $old_params['ct_p_type'];
		}
		if ( is_array( $old_params['mapping_array'] ) ) {
			$profile['mapping_array'] = array();
			if ( $profile['mapping_type'] == 'all' ) {
				$profile['mapping_array']['all'] = $old_params['mapping_array'];
			}
		}
		if ( is_array( $old_params['mapping_array_post'] ) ) {
			$profile['mapping_array']['post'] = $this->collect_mapping_fields( $old_params, 'post' );
			if ( isset( $profile['story_post_mapping']['all'] ) && $profile['story_post_mapping']['all'] == 'post' ) {
				$profile['mapping_array']['all'] = $this->collect_mapping_fields( $old_params, 'post' );
			}
		}
		if ( is_array( $old_params['mapping_array_page'] ) ) {
			$profile['mapping_array']['page'] = $this->collect_mapping_fields( $old_params, 'page' );
			if ( isset( $profile['story_post_mapping']['all'] ) && $profile['story_post_mapping']['all'] == 'page' ) {
				$profile['mapping_array']['all'] = $this->collect_mapping_fields( $old_params, 'page' );
			}
		}

		update_option( 'cl_profiles', $profiles );

		return array(
			'error' => false,
			'text'  => 'success',
		);
	}

	public function collect_mapping_fields( $old_params, $type = 'post' ) {

		$mapping_fields = array();  //mapping_array_post/mapping_array_page
		if ( is_array( $old_params[ 'mapping_array_' . $type ] ) ) {
			$mapping_fields             = $old_params[ 'mapping_array_' . $type ] = array_map( array(
				$this,
				"change_names"
			), $old_params[ 'mapping_array_' . $type ] );
			$mapping_fields['post_tag'] = isset( $mapping_fields['ptags'] ) ? $mapping_fields['ptags'] : '';
		}

		$acf_field_group = '';  //acffieldgrouppost/acffieldgrouppage
		if ( is_string( $old_params[ 'acffieldgroup' . $type ] ) || is_integer( $old_params[ 'acffieldgroup' . $type ] ) ) {
			$acf_field_group = $old_params[ 'acffieldgroup' . $type ];
		}

		$mapping_fields_acf   = array();    //mapping_array_acf_post/mapping_array_acf_post unset[_yoast...]
		$mapping_fields_yoast = array();    //mapping_array_acf_post[_yoast...]
		if ( is_array( $old_params[ 'mapping_array_acf_' . $type ] ) ) {
			$old_params[ 'mapping_array_acf_' . $type ] = array_map( array(
				$this,
				"change_names"
			), $old_params[ 'mapping_array_acf_' . $type ] );;
			foreach ( $old_params[ 'mapping_array_acf_' . $type ] as $key => $value ) {
				if ( strpos( $key, '_yoast' ) !== 0 ) {
					$mapping_fields_acf += array( $key => $value );
				} else {
					$mapping_fields_yoast += array( $key => $value );
				}
			}
		}

		return array(
			'mapping_fields'       => $mapping_fields,
			'acf_field_group'      => $acf_field_group,
			'mapping_fields_acf'   => $mapping_fields_acf,
			'mapping_fields_yoast' => $mapping_fields_yoast,
		);
	}

	public function change_names( $v ) {

		return str_replace( "sf_", "cf_", $v );
	}

	//clean trash in wp db

	public function delete_deprecated_options() {

		foreach ( $this->old_options as $option ) {
			delete_option( $option );
		}
	}

	public function restore_deprecated_options() {

		$old_params = get_option( 'cl_deprecated_options' );
		if ( is_array( $old_params ) ) {
			foreach ( $old_params as $key => $value ) {
				if ( in_array( $key, $this->old_options ) ) {
					update_option( $key, $value );
				}
			}
		}
	}

	public function keep_deprecated_options( $old_params ) {

		update_option( 'cl_deprecated_options', $old_params );
	}

}
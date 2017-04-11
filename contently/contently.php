<?php

class WP_Contently {

	private $plugins_helper;
	private $custom_fields_helper;
    private $custom_taxonomies_helper;
	private $page;

	private static $default_profile = array(
		'api_key' => array(
			'post_type'           => 'post',
			'author_attributes'   => array(),
			'name'                => 'Publication',
			'authorized'          => false,
			'publishing_settings' => array(
				'is_planned'     => 0,
				'is_not_planned' => 1,
				'save_as_draft'  => 1
			),
			'author_settings'     => array(
				'name_format' => "@f @l",
				'is_blocked'  => "1",
				'uname'       => "",
				'uid'         => ""
			),
			'story_post_mapping'  => array(
				'all' => 'post'
			),
			'mapping_type'        => 'all',
			'mapping_array'       => array(
				'all' => array(
					'mapping_fields' => array(
						'title'        => 'cm_title',
						'body'         => 'cm_content',
//						'author' 			=> 'cm_creator',
						'author'       => 'cm_contributors',
						'excerpt'      => '',
						'category'     => '',
						'post_tag'     => '',
						'featured_img' => '',
					)
				)
			),
		)
	);

	private $default_fields = array(
		'title'        => 'Title',
		'body'         => 'Body',
		'category'     => 'Categories',
		'post_tag'     => 'Tags',
		'author'       => 'Author',
		'excerpt'      => 'Post excerpt',
		'featured_img' => 'Featured image',
	);

	private $meta_box_campaigns_data = array(
		'id'       => 'contently-link-meta-box',
		'title'    => 'Post Link on Contently ',
		'page'     => 'post',
		'context'  => 'normal',
		'priority' => 'high',
		'fields'   => array()
	);

	/**
	 * Static property to hold our singleton instance
	 *
	 */

	static $instance = false;

	private $API = false;

	/**
	 * Constructor
	 *
	 */
	private function __construct() {
		// back end
		add_action( 'admin_menu', array( $this, 'contently_admin_menu' ), 1 );
		add_action( 'admin_menu', array( $this, 'add_meta_box_to_cl_post' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_action( 'new_to_publish', array( $this, 'mark_published' ), 10, 1 );
		add_action( 'future_to_publish', array( $this, 'mark_published' ), 10, 1 );
		add_action( 'draft_to_publish', array( $this, 'mark_published' ), 10, 1 );
		add_action( 'wp_loaded', array( $this, 'get_contently_webhook_response' ), 1 );

		// ajax
		add_action( 'wp_ajax_set_api_keys', array( $this, 'set_api_keys' ) );
		add_action( 'wp_ajax_delete_api_key', array( $this, 'delete_api_key' ) );
		add_action( 'wp_ajax_get_profile_api_key', array( $this, 'get_profile_api_key' ) );
		add_action( 'wp_ajax_set_profile_api_key', array( $this, 'set_profile_api_key' ) );
		add_action( 'wp_ajax_get_profile_publishing_options', array( $this, 'get_profile_publishing_options' ) );
		add_action( 'wp_ajax_set_profile_publishing_options', array( $this, 'set_profile_publishing_options' ) );
		add_action( 'wp_ajax_get_profile_post_type_mapping', array( $this, 'get_profile_post_type_mapping' ) );
		add_action( 'wp_ajax_set_profile_post_type_mapping', array( $this, 'set_profile_post_type_mapping' ) );
		add_action( 'wp_ajax_get_profile_configure_mapping', array( $this, 'get_profile_configure_mapping' ) );
		add_action( 'wp_ajax_set_profile_configure_mapping', array( $this, 'set_profile_configure_mapping' ) );

		spl_autoload_register( array( $this, 'class_file_autoloader' ), true );

		$this->plugins_helper           = new Contently_Plugins;
		$this->custom_fields_helper     = new Contently_Custom_Fields;
        $this->custom_taxonomies_helper = new Contently_Custom_Taxonomies;
		$this->response_helper          = new Contently_Webhook_Response( $this->plugins_helper );

		add_action( 'wp_ajax_get_html_acf_fields', array( $this->plugins_helper, 'get_html_acf_fields' ) );

		if ( isset( $_REQUEST['api_key'] ) ) {
			$this->API = new \Contently\Connector( array( 'key' => $_REQUEST['api_key'] ) );
		}

		//core fixes
		if ( ! defined( 'WP_POST_REVISIONS' ) ) {
			define( 'WP_POST_REVISIONS', true );
		}

		if ( $GLOBALS['wp_rewrite'] == null ) {
			$GLOBALS['wp_rewrite'] = new WP_Rewrite();
		}

		//add multilanguage support
		$this->textdomain();

	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * returns it.
	 *
	 * @return WP_Contently
	 */

	public static function getInstance() {

		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Autoloads files when requested
	 *
	 * @param  string $class_name Name of the class being requested
	 */

	function class_file_autoloader( $class_name ) {

		if ( strpos( $class_name, 'Contently_' ) !== 0 ) {
			return;
		}
		$file_name = str_replace(
			array( 'Contently_', '_' ),
			array( '', '-' ),
			$class_name
		);
		$file_name = strtolower( $file_name );
		$file_name .= '-helper';
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $file_name . '.php';
		if ( file_exists( $file ) ) {
			require_once( $file );
		}
	}

	/**
	 * Add css and java-script to html header
	 *
	 */

	public function add_scripts() {

		//css
		wp_enqueue_style( 'contently-styles', plugins_url( '/css/style.css', __FILE__ ), false, '1.0', 'all' );
		wp_enqueue_style( 'contently-styles-toast', plugins_url( '/css/toast.css', __FILE__ ), false, '1.0', 'all' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		//js
		wp_enqueue_script( 'tags-box' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'contently-scripts-toast', plugins_url( '/js/toast.js', __FILE__ ), false, '1.0', 'all' );
		wp_enqueue_script( 'contently-scripts', plugins_url( '/js/contently.js', __FILE__ ), false, '1.4', 'all' );

		$blog_users = get_users( 'blog_id=1&orderby=nicename' );
		$users      = array();
		foreach ( $blog_users as $user ) {
			$users[] = array(
				'label' => $user->data->display_name,
				'value' => $user->ID
			);
		}

		wp_localize_script( 'contently-scripts', 'contently_data', array(
				'admin_url'  => admin_url( '', Contently_Plugins::getProtocol() ),
				'plugin_url' => plugins_url( '', __FILE__ ),
				'users'      => $users,
				'messages'   => array(
					'invalid_key'   => __( 'Invalid API Key', 'contently' ),
					'allowed_chars' => __( 'Allowed only numbers and letters', 'contently' ),
					'updating'      => __( 'Updating', 'contently' ),
				)
			)
		);
	}

	/**
	 * Add page to wp admin menu
	 *
	 */

	public function contently_admin_menu() {
		$options = get_option( 'cl_options', array(
			'debug_state' => true
		) );

		$this->page = add_menu_page(
			'Contently',
			'Contently',
			'edit_theme_options',
			'contently_setting',
			array(
				$this,
				'contently_settings_page'
			) );

        //Add settings panel
        add_submenu_page(
            'contently_setting',
            'Settings',
            'Settings',
            'edit_theme_options',
            'contently_setting',
            array( $this, 'contently_settings_page' )
        );

		if ( filter_var( $options['debug_state'], FILTER_VALIDATE_BOOLEAN ) ) {
			//Add debug panel
			add_submenu_page(
				'contently_setting',
				'Log',
				'Log',
				'edit_theme_options',
				'contently_debug',
				array( $this, 'contently_debug_page' )
			);
		}

        //Add backup panel
        $backup_page = add_submenu_page(
            'contently_setting',
            'Import/Export',
            'Import/Export',
            'edit_theme_options',
            'contently_backup',
            array( $this, 'contently_backup_page' )
        );

        add_action( "load-{$backup_page}", array( $this, 'contently_backup_load' ) );

	}

	/**
	 * load textdomain
	 *
	 */

	public function textdomain() {

		load_plugin_textdomain( 'contently', false, basename( dirname( __FILE__ ) ) . '/lang' );
	}

	/**
	 * Helper function, print template and message in json
	 *
	 * @param string $template_name
	 * @param array  $params with messages, popups, additional data
	 */

	public function make_view( $template_name = '', $params = array() ) {
		$logger = \Contently\Log::instance();
		$logger->info( 'Make view', array(
			'template'       => $template_name,
			'json!__request' => $_REQUEST,
			'json!__params'  => $params
		) );

		$result = array();
		//variables for view
		$profiles = get_option( 'cl_profiles' );
		$api_key  = isset( $_REQUEST['api_key'] ) ? $_REQUEST['api_key'] : 'api_key';
		$type     = isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : false;
		$popups   = isset( $params['popups'] ) ? $params['popups'] : false;
		$messages = isset( $params['messages'] ) ? $params['messages'] : array();
		$message  = isset( $params['message'] ) ? $params['message'] : array();

		$logger->info( 'init view variables', array(
			'json!__profiles' => $profiles,
			'api_key'         => $api_key,
			'type'            => $type,
			'popups'          => $popups,
			'messages'        => $messages,
			'message'         => $message,
		) );

		if ( isset( $params['api_key'] ) ) {
			$api_key = $params['api_key'];
		}

		if ( ! empty( $template_name ) ) {

			if ( empty( $api_key ) ) {
				$api_key = 'api_key';
				$profile = self::$default_profile['api_key'];
			} else {
				$profile = isset( $profiles[ $api_key ] ) ? $profiles[ $api_key ] : self::$default_profile['api_key'];
			}

			if ( ! isset( $profile['authorized'] ) || ! $profile['authorized'] ) {
				$logger->info( 'Put into message: Invalid API Key' );
				$template_name = 'profile_api_key';
				$message       = array(
					'type' => 'danger',
					'text' => __( 'Invalid API Key', 'contently' ),
				);
			}
			if ( empty( $api_key ) || $api_key == 'api_key' ) {
				$logger->info( 'Clear messages.' );
				$message = array();
//				$messages = array();
			}

			@ob_get_contents();
			@ob_end_clean();
			if ( ! @ob_start( "ob_gzhandler" ) ) {
				@ob_start();
			}

			if ( $api_key != 'api_key' ) {
				$logger->info( 'Start render profile_header.php' );
				require_once( plugin_dir_path( __FILE__ ) . '/templates/profile_header.php' );
				$logger->info( 'Complete render profile_header.php' );
			}
			$logger->info( 'Start render template', array( 'template' => $template_name ) );
			require_once( plugin_dir_path( __FILE__ ) . '/templates/' . $template_name . '.php' );
			$logger->info( 'Complete render template' );
			if ( $template_name != 'profile_configure_mapping' ) {
				$logger->info( 'Start render profile_footer.php' );
				require_once( plugin_dir_path( __FILE__ ) . '/templates/profile_footer.php' );
				$logger->info( 'Complete render profile_footer.php' );
			}
			$template = @ob_get_contents();
			@ob_end_clean();
			$result['template'] = $template;
		}

		if ( is_array( $popups ) ) {
			@ob_get_contents();
			@ob_end_clean();
			if ( ! @ob_start( "ob_gzhandler" ) ) {
				@ob_start();
			}

			foreach ( $popups as $popup_text ) {
				$this->popup_message( $popup_text );
			}
			$popups_html = @ob_get_contents();
			@ob_end_clean();
			$result['popups'] = $popups_html;
		}

		$result['message']  = $message;
		$result['messages'] = $messages;
		if ( isset( $params['publication_name'] ) ) {
			$result['publication_name'] = $params['publication_name'];
		}

		$response = json_encode( $result );
		$logger->info( 'Send response', array( 'json!__template' => $response ) );
		echo $response;
		exit;
	}

	/**
	 * Get page with contently settings
	 */

	public function contently_settings_page() {
		$profiles = get_option( 'cl_profiles' );

		require_once( plugin_dir_path( __FILE__ ) . '/templates/header.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/templates/main_settings.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/templates/footer.php' );
	}

	public function contently_testing_page() {
		require_once( plugin_dir_path( __FILE__ ) . '/templates/page_testing.php' );
	}

	public function contently_debug_page() {
		require_once( plugin_dir_path( __FILE__ ) . '/templates/debug_panel.php' );
	}

	public function contently_backup_page() {
	    require_once( plugin_dir_path( __FILE__ ) . '/templates/backup.php' );
    }

    public function contently_backup_load() {
        $params = $_REQUEST;

        if ( isset( $params['contently_nonce'] ) ) {
            switch ( true ) {
                case wp_verify_nonce($params['contently_nonce'], 'import'):
                    $this->contently_import_settings();
                    break;
                case wp_verify_nonce($params['contently_nonce'], 'export'):
                    $this->contently_export_settings();
                    break;
            }
        }
    }

    private function contently_import_settings() {
        if( ! isset( $_FILES['contently_import_file'] ) || empty( $_FILES['contently_import_file'] ) ) {
            add_settings_error(
                'contently_admin_notice',
                esc_attr( 'settings_error' ),
                __( 'No file selected', 'contently' ),
                'error'
            );
            return;
        }

        $file = $_FILES['contently_import_file'];

        if( $file['error'] ) {
            add_settings_error(
                'contently_admin_notice',
                esc_attr( 'settings_error' ),
                __( 'Error uploading file. Please try again', 'contently' ),
                'error'
            );
            return;
        }

        if( pathinfo( $file['name'], PATHINFO_EXTENSION ) !== 'json' ) {
            add_settings_error(
                'contently_admin_notice',
                esc_attr( 'settings_error' ),
                __( 'Incorrect file type', 'contently' ),
                'error'
            );
            return;
        }

        $json = @file_get_contents( $file['tmp_name'] );
        $json = json_decode( $json, true );

        if( empty( $json ) ) {
            add_settings_error(
                'contently_admin_notice',
                esc_attr( 'settings_error' ),
                __( 'Import file empty', 'contently' ),
                'error'
            );
            return;
        }

        \Contently\Log::instance()->info( 'Import Contently settings', array( 'json!__data' => $json ) );

        if ( isset( $json['cl_profiles'] ) && ! empty( $json['cl_profiles'] ) ) {
            update_option( 'cl_profiles', $json['cl_profiles'] );
        }

        if ( isset( $json['cl_custom_fields'] ) && ! empty( $json['cl_custom_fields'] ) ) {
            update_option( 'cl_custom_fields', $json['cl_custom_fields'] );
        }

        if ( isset( $json['cl_options'] ) && ! empty( $json['cl_options'] ) ) {
            update_option( 'cl_options', $json['cl_options'] );
        }

        add_settings_error(
            'contently_admin_notice',
            esc_attr( 'settings_updated' ),
            __( 'Contently settings have been successfully imported', 'contently' ),
            'updated'
        );
    }

    private function contently_export_settings() {
        $settings = array(
            'cl_profiles' => get_option( 'cl_profiles' ),
            'cl_custom_fields' => get_option( 'cl_custom_fields' ),
            'cl_options' => get_option( 'cl_options' )
        );

        header( 'Content-Description: File Transfer' );
        header( 'Content-Disposition: attachment; filename=contently.json' );

        wp_send_json($settings);
    }

	/**
	 * Get template with api key
	 *
	 */

	public function get_profile_api_key() {
		$this->make_view( 'profile_api_key' );
	}

	/**
	 * Get template with api key
	 *
	 */
	public function set_profile_api_key() {
		$profiles = get_option( 'cl_profiles' );
		$options  = get_option( 'cl_options', array() );

		$single_message = array();
		$messages       = array();
		$popups         = array();
		$name           = '';
		$api_key        = $this->API->getKey();
		$api_key_new    = $_REQUEST['api_key_new'];
		$debug_state    = filter_var( $_REQUEST['debug_state'], FILTER_VALIDATE_BOOLEAN );

		$options['debug_state'] = $debug_state;
		update_option( 'cl_options', $options );

		// If should replace a profile
		if ( ! empty( $api_key ) && $api_key != $api_key_new ) {
			$temp_profile = $profiles[ $api_key ];
			unset( $profiles[ $api_key ] );
			$api_key              = $api_key_new;
			$profiles[ $api_key ] = $temp_profile;
			unset( $temp_profile );
		}
		// If should create new profile
		if ( empty( $api_key ) && ! empty( $api_key_new ) ) {
			$api_key = $api_key_new;
		}
		$this->save_api_key( $profiles, $api_key, $name, $messages, $popups );

		//save profiles into db
		update_option( 'cl_profiles', $profiles );

		if ( count( $messages ) < 1 ) {
			$single_message = array(
				'type' => 'success',
				'text' => __( 'Updated Successfully', 'contently' )
			);
		}

		$this->make_view(
			'profile_api_key',
			array(
				'api_key'          => $api_key,
				'message'          => $single_message,
				'messages'         => $messages,
				'publication_name' => $name,
				'popups'           => $popups
			)
		);
	}

	/**
	 * Connect to api and save data into array $profiles
	 * if API Key incorrect create messages and popups with errors
	 *
	 * @param $profiles
	 * @param $api_key
	 * @param $name
	 * @param $messages
	 * @param $popups
	 *
	 * @return bool
	 */

	public function save_api_key( &$profiles, $api_key, &$name, &$messages, &$popups ) {
		// Validation key
		if ( empty( $api_key ) || $api_key == 'api_key' ) {
			$messages[] = array(
				'type' => 'info',
				'text' => __( 'The field is empty. Please, enter the API Key', 'contently' )
			);

			return false;
		}

		if ( ! isset( $profiles[ $api_key ] ) || ! is_array( $profiles[ $api_key ] ) ) {
			$profiles[ $api_key ] = array();
		}

		if ( ! \Contently\Connector::keyValidator( $api_key ) ) {
			$profiles[ $api_key ] = array(
				'authorized' => false
			);
			$messages[]           = array(
				'type' => 'danger',
				'text' => __( 'Invalid API Key validation', 'contently' ) . ' : ' . $api_key
			);

			return false;
		}

		$this->API->useKey( $api_key );
		$result = $this->API->getTaxonomy();

		if ( ! isset( $result->error ) ) {
			$profiles[ $api_key ]['author_attributes'] = is_object( $result ) ? json_decode( json_encode( $result ), true ) : $result;
			$profiles[ $api_key ]['authorized']        = true;
			$profiles[ $api_key ] += self::$default_profile['api_key'];

		} else {
			$profiles[ $api_key ] = array(
				'authorized' => false
			);
			$popups[]             = __( 'Invalid API Key', 'contently' ) . ' :<br> ' . $api_key . '<br>' . 'Error message :<br>' . $result->error;

			return false;
		}

		$result = $this->API->setWebHook( home_url( '', Contently_Plugins::getProtocol() ) );

		if ( ! isset( $result->error ) && isset( $result->webhook_url ) ) {
			$profiles[ $api_key ]['webhook'] = $result->webhook_url;
			$profiles[ $api_key ]['name']    = $result->name;
			$name                            = $result->name;
		} else {
			$profiles[ $api_key ] = array(
				'authorized' => false
			);
			$popups[]             = __( 'Webhook error', 'contently' ) . ' :<br> ' . $api_key . '<br>' . 'Error message :<br>' . $result->error;

			return false;
		}
	}

	public function delete_api_key() {
		$api_key  = $this->API->getKey();
		$profiles = get_option( 'cl_profiles' );
		if ( isset( $profiles[ $api_key ] ) ) {
			$message = array(
				'type' => 'danger',
				'text' => __( "API Key deleted :", 'contently' ) . "\n" . $api_key
			);
			unset( $profiles[ $api_key ] );
		} else {
			$message = array(
				'type' => 'danger',
				'text' => __( "API Key not exist :", 'contently' ) . "\n" . $api_key
			);
		}
		update_option( 'cl_profiles', $profiles );
		$this->make_view( '', array( 'message' => $message ) );

	}

	/**
	 * Get template with publishing options
	 *
	 */

	public function get_profile_publishing_options() {

		$this->make_view( 'profile_publishing_options' );
	}

	/**
	 * Save profile publishing options in to wpdb
	 *
	 */

	public function set_profile_publishing_options() {

		$params   = $_REQUEST;
		$profiles = get_option( 'cl_profiles' );
		$api_key  = $params['api_key'];

		$profiles[ $api_key ]['publishing_settings'] = $params['publishing_settings'];
		$profiles[ $api_key ]['author_settings']     = $params['author_settings'];

		//save profile data into db
		update_option( 'cl_profiles', $profiles );

		$this->make_view(
			'profile_publishing_options',
			array(
				'message' => array(
					'type' => 'success',
					'text' => __( 'Updated successfully', 'contently' )
				)
			)
		);
	}

	/**
	 * Get template with profile post type mapping
	 *
	 */

	public function get_profile_post_type_mapping() {

		$this->make_view( 'profile_post_type_mapping' );
	}

	/**
	 * Save profile profile post type mapping in to wpdb
	 *
	 */

	public function set_profile_post_type_mapping() {

		$params   = $_REQUEST;
		$profiles = get_option( 'cl_profiles' );
		$api_key  = $params['api_key'];

		$profiles[ $api_key ]['story_post_mapping'] = $params['story_post_mapping'];
		$profiles[ $api_key ]['mapping_type']       = $params['mapping_type'];
		//save profile data into db
		update_option( 'cl_profiles', $profiles );

		$this->make_view(
			'profile_post_type_mapping',
			array(
				'message' => array(
					'type' => 'success',
					'text' => __( 'Updated successfully', 'contently' )
				)
			)
		);
	}

	/**
	 * Get template profile mapping configurations
	 *
	 */

	public function get_profile_configure_mapping() {
		$logger = \Contently\Log::instance();
		$logger->info( 'START get_profile_configure_mapping' );

		$this->plugins_helper->rebuild_acf_mapping();

		$params   = $_REQUEST;
		$api_key  = $params['api_key'];
		$profiles = get_option( 'cl_profiles' );

		$logger->info( 'With parameters:', array( 'json!__parameters' => array( 'params' => $params, 'key' => $api_key, '__profiles' => $profiles ) ) );

		$client = new \Contently\Connector( array( 'key' => $api_key ) );
		$result = $client->getTaxonomy();

		$logger->info( 'Get taxonomy.' );

		if ( ! isset( $result->error ) ) {
			$logger->info( 'No Result Error.', array() );
			$profiles[ $api_key ]['author_attributes'] = is_object( $result ) ? json_decode( json_encode( $result ), true ) : $result;
			$profiles[ $api_key ]['authorized']        = true;
			$profiles[ $api_key ] += self::$default_profile['api_key'];

		} else {
			$logger->info( 'Result Error.', array( 'error' => $result->error ) );
			$profiles[ $api_key ] = array(
				'authorized' => false
			);
			$popups[]             = __( 'Invalid API Key', 'contently' ) . ' :<br> ' . $api_key . '<br>' . 'Error message :<br>' . $result->error;

			return false;
		}

		$logger->info( 'Before update option' );
		update_option( 'cl_profiles', $profiles );

		$logger->info( 'Before make_view' );
		$this->make_view( 'profile_configure_mapping' );
	}

	/**
	 * Save profile  profile mapping configurations in to wpdb
	 *
	 */

	public function set_profile_configure_mapping() {

		$params   = $_REQUEST;
		$profiles = get_option( 'cl_profiles' );
		$api_key  = $params['api_key'];
		$type     = $params['type'];

		$configuration = array(
			'mapping_fields' => $params['mapping_fields'],
		);

		if ( $this->plugins_helper->plugin_acf ) {
			$configuration += array(
				'mapping_fields_acf' => isset( $params['mapping_fields_acf'] ) ? $params['mapping_fields_acf'] : '',
				'acf_field_group'    => isset( $params['acf_field_group'] ) ? $params['acf_field_group'] : '',
			);
		}

		if ( $this->plugins_helper->plugin_yoast_seo ) {
			$configuration += array(
				'mapping_fields_yoast' => isset( $params['mapping_fields_yoast'] ) ? $params['mapping_fields_yoast'] : '',
			);
		}

		if ( $this->plugins_helper->plugin_all_in_one_seo ) {
			$configuration += array(
				'mapping_fields_aiosp' => isset( $params['mapping_fields_aiosp'] ) ? $params['mapping_fields_aiosp'] : '',
			);
		}

		$configuration = $this->custom_fields_helper->cl_check_custom_fields( $configuration, $type );

		//save profile data into db
		$this->set_fields_mapping( $profiles, $api_key, $type, $configuration );

		$this->make_view(
			'profile_configure_mapping',
			array(
				'message' => array(
					'type' => 'success',
					'text' => __( 'Updated successfully', 'contently' )
				)
			)
		);
	}

	/**
	 * Get array with fields mapping
	 *
	 * @param array  $profile
	 * @param string $type
	 * @param string $fields_key
	 *
	 * @return array
	 */

	public function get_fields_mapping( $profile, $type, $fields_key = 'mapping_fields' ) {

		if ( $profile['mapping_type'] == 'all' ) {
			$result = isset( $profile['mapping_array']['all'][ $fields_key ] ) ? $profile['mapping_array']['all'][ $fields_key ] : array();
		} else {
			$result = isset( $profile['mapping_array'][ $type ][ $fields_key ] ) ? $profile['mapping_array'][ $type ][ $fields_key ] : array();
		}

		return $result;
	}

	/**
	 * Set fields mapping configuration to db
	 *
	 * @param array  $profiles
	 * @param string $api_key
	 * @param string $type
	 * @param array  $configuration
	 */

	public function set_fields_mapping( $profiles, $api_key, $type, $configuration ) {

		if ( ! isset( $profiles[ $api_key ]['mapping_array'] ) ) {
			$profiles[ $api_key ]['mapping_array'] = array();
		}
		if ( $profiles[ $api_key ]['mapping_type'] == 'all' ) {
			$profiles[ $api_key ]['mapping_array']['all'] = $configuration;
		} else {
			$profiles[ $api_key ]['mapping_array'][ $type ] = $configuration;
		}
		update_option( 'cl_profiles', $profiles );
	}


	//html helper functions

	/**
	 * Get html. dropdown with categories
	 *
	 * @param $type
	 * @param $profile
	 *
	 * @return html
	 */

	public function get_dropdown_categories( $type, $profile ) {

		$mapping_array  = $this->get_fields_mapping( $profile, $type );
		$selected_value = isset( $mapping_array['wp_category'] ) ? $mapping_array['wp_category'] : '';

		$args = array(
			'show_option_all'   => '',
			'show_option_none'  => '',
			'option_none_value' => '-1',
			'orderby'           => 'ID',
			'order'             => 'ASC',
			'show_count'        => 0,
			'hide_empty'        => 0,
			'child_of'          => 0,
			'exclude'           => '',
			'echo'              => 1,
			'selected'          => $selected_value,
			'hierarchical'      => 0,
			'name'              => 'mapping_fields[wp_category]',
			'id'                => '',
			'class'             => 'cl-form-select',
			'depth'             => 0,
			'tab_index'         => 0,
			'taxonomy'          => 'category',
			'hide_if_empty'     => false,
			'value_field'       => 'term_id',
		);
		wp_dropdown_categories( $args );
	}

	/**
	 * Get html WP box for tags
	 *
	 * @param $type
	 * @param $profile
	 *
	 * @return html
	 */

	public function get_dropdown_tags( $type, $profile ) {

		$mapping_array   = $this->get_fields_mapping( $profile, $type );
		$selected_values = isset( $mapping_array['wp_post_tag'] ) ? $mapping_array['wp_post_tag'] : '';
		$taxonomy        = get_taxonomy( 'post_tag' );

		echo '
		<script  type="text/javascript" >window.tagBox && window.tagBox.init();</script>
		<div class="tagsdiv" id="post_tag">
			<div class="jaxtag">
			<input type="hidden" name="mapping_fields[wp_post_tag]" class="the-tags" value="' . $selected_values . '">
				<div class="ajaxtag hide-if-no-js">
					<label class="screen-reader-text" for="new-tag-post_tag">' . __( 'Tags', 'contently' ) . '</label>
					<p>
						<input type="text" id="new-tag-post_tag" name="newtag[post_tag]" class="newtag form-input-tip" size="16" autocomplete="off" value="" aria-describedby="new-tag-desc" />
						<button type="button" class="button tagadd">' . __( 'Add', 'contently' ) . '</button>
					</p>
				</div>
				<p class="howto" id="new-tag-desc">' . $taxonomy->labels->separate_items_with_commas . '</p>
			</div>
			<div class="tagchecklist"></div>
		</div>
		<button type="button" class="button-link tagcloud-link" id="link-post_tag">' . $taxonomy->labels->choose_from_most_used . '</button>
		';
	}


	/**
	 * Get html. dropdown list with contently fields
	 *
	 * @param $field_name
	 * @param $type
	 * @param $profile
	 *
	 * @return html
	 */

	public function get_dropdown_list( $field_name = '', $type, $profile ) {

		$array_constants = array(
			'title'        => 'Title',
			'content'      => 'Content',
			'creator'      => 'Creator',
			'contributors' => 'Contributors'
		);

		$story_fields     = $profile['author_attributes']['publication_custom_fields'];
		$story_attributes = $profile['author_attributes']['publication_tags'];

		$mapping_array = $this->get_fields_mapping( $profile, $type );

		$selected_value = isset( $mapping_array[ $field_name ] ) ? $mapping_array[ $field_name ] : '';
		if ( empty( $field_name ) ) {
			echo '<select class="cl-form-select">';
		} else {
			echo '<select class="cl-form-select" name="mapping_fields[' . $field_name . ']" >';
		}
		echo '<option value="">' . __( 'select', 'contently' ) . '</option>';
		foreach ( $array_constants as $key => $array_constant ) {
			$selected = $selected_value == "cm_" . $key ? 'selected="selected"' : '';
			echo '<option value="cm_' . $key . '" ' . $selected . '> ' . $array_constant . '</option>';
		}
		echo '<optgroup label="Custom Fields">';
		foreach ( $story_fields as $story_field ) {
			$selected = $selected_value == "cf_" . $story_field['name'] ? 'selected="selected"' : '';
			echo '<option value="cf_' . $story_field['name'] . '" ' . $selected . '> ' . $story_field['name'] . '</option>';
		}

		echo ' </optgroup>';
		echo ' <optgroup label="Tags">';

		foreach ( $story_attributes as $attributes_values ) {
			$selected = $selected_value == 'attr_' . $attributes_values['name'] ? 'selected="selected"' : '';
			echo '<option value="attr_' . $attributes_values['name'] . '" ' . $selected . '> ' . $attributes_values['name'] . '</option>';
		}
		echo '</optgroup>';
		echo ' </select>';
	}

	/**
	 * Get response from API, save/update new story
	 */

	public function get_contently_webhook_response() {
		global $wpdb;
		$logger = \Contently\Log::instance();

		/**
		 * @todo GET Debug data from contently
		 */
		if ( isset( $_GET['contently_pull'] ) ) {
			//$logger->info( 'New pull from Contently.' );
			$decoded_key = base64_decode( $_GET['contently_pull'] );
			$profiles    = get_option( 'cl_profiles' );
			$profile     = $this->check_profile( $decoded_key, $profiles );
			if ( ! $profile ) {
				$logger->alert( 'Pull profile not found.' );
				exit;
			}

			if ( ! $logger->getState() ) {
				echo "Logger disabled";
			} else {
				if ( isset( $_GET['action'] ) ) {
					if ( $_GET['action'] === 'clear_log' ) {
						$logger->remove();
					}
				}

				$logData = $logger->getData();

				$hash_key = $_GET['contently_pull'];

				if ( isset( $_GET['type'] ) ) {
					switch ( $_GET['type'] ) {
						case 'json':
							echo json_encode( $logData );
							break;
						case 'test':
							$testModule = new \Contently\Test();
							$action     = $_GET['action'];
							echo $testModule->$action();
					}
				} else {
					require_once( plugin_dir_path( __FILE__ ) . '/templates/debug_result.php' );
				}
			}
			exit;
		}

		if ( isset( $_GET['contently_push'] ) ) {
			$input_data      = file_get_contents( 'php://input' );
			$contently_story = json_decode( $input_data );
			$contently_push  = $_GET['contently_push'];
			$decoded_key     = base64_decode( $contently_push );
			$profiles        = get_option( 'cl_profiles' );

			$logger->info( 'New push from Contently.', array(
				'raw!__raw'   => $input_data,
				'json!__data' => $contently_story
			) );
			$profile = $this->check_profile( $decoded_key, $profiles );

			if ( ! $profile ) {
				$logger->alert( 'Profile not found.', array( 'key' => $decoded_key, 'profiles' => $profiles ) );
				exit;
			}

			if ( empty( $contently_story ) ) {
				$logger->alert( 'Invalid data into $contently_story' );
				exit;
			}

			$logger->info( 'Start parsing data.' );
			$this->response_helper->create_update_post_from_cl( $contently_story, $profile, $decoded_key );
			$logger->info( 'End parsing data.' );

			exit;
		}
	}

	/**
	 * Check profile settings. Add default settings if profile is not complete
	 *
	 * @param       $api_key
	 * @param array $profiles
	 *
	 * @return bool
	 */

	public function check_profile( $api_key, $profiles = array() ) {

		if ( count( $profiles ) < 1 ) {
			$profiles = get_option( 'cl_profiles' );
		}

		if ( isset( $profiles[ $api_key ] ) ) {
			$profile          = $profiles[ $api_key ];
			$required_options = array(
				'publishing_settings',
				'author_settings',
				'story_post_mapping',
				'mapping_type',
				'mapping_array',
			);
			$profile_invalid  = false;
			foreach ( $required_options as $option ) {
				if ( ! isset( $profile[ $option ] ) ) {
					//for debug
//					echo "profile needs update. the option is required:" . $option. "\n";
//					echo "the settings will be set by default \n";
					$profile[ $option ] = self::$default_profile['api_key'][ $option ];
//					$profile_invalid = true;
				}
			}
			if ( $profile_invalid ) {
				\Contently\Log::instance()->alert( 'Profile is not valid.' );

				return false;
			}

			return $profile;
		} else {
			\Contently\Log::instance()->alert( 'No such api key profile' );

			return false;
		}

	}

	/**
	 * Add metabox to story post page
	 */

	public function add_meta_box_to_cl_post() {

		$this->meta_box_campaigns_data;
		add_meta_box( $this->meta_box_campaigns_data['id'], $this->meta_box_campaigns_data['title'], array(
			$this,
			'cl_post_link_data'
		), 'post', $this->meta_box_campaigns_data['context'], $this->meta_box_campaigns_data['priority'] );
	}

	/**
	 * Get data for metabox on story post page
	 */

	public function cl_post_link_data() {

		global $post;
		$story_id = get_post_meta( $post->ID, '_cl_story_id', true );

		if ( ! empty( $story_id ) ) {
			echo '<div><a target="_blank" href="https://contently.com/stories/' . $story_id . '">https://contently.com/stories/' . $story_id . '</a></div>';
		} else {
			echo '<style>#contently-link-meta-box{display:none;}</style>';
		}
	}

	/**
	 * Set the story attribute publish and post url to API
	 *
	 * @param $post
	 */

	public function mark_published( $post ) {

		$post_id = $post->ID;
		if ( $post->post_status != 'publish' ) {
			return;
		}
		$published_url = get_permalink( $post_id );
		$story_id      = get_post_meta( $post_id, '_cl_story_id', true );
		$api_key       = get_post_meta( $post_id, '_cl_api_key', true );

		$client = new \Contently\Connector( array( 'key' => $api_key ) );
		$client->setPublished( $story_id, $published_url );

		$story_id_old = get_post_meta( $post_id, 'ct_story_id', true );
		if ( is_string( $story_id_old ) ) {
			$this->mark_published_old_story( $story_id_old, $published_url );

			return;
		}
	}

	public function mark_published_old_story( $story_id_old, $published_url ) {
		$profiles = get_option( 'cl_profiles' );
		if ( is_array( $profiles ) ) {
			//we don't know what api key was used with this story
			foreach ( $profiles as $api_key => $profile ) {
				$client = new \Contently\Connector( array( 'key' => $api_key ) );
				$client->setPublished( $story_id_old, $published_url );
			}
		}

	}

	/**
	 * Generate popup with message
	 *
	 * @param string $text
	 */

	public function popup_message( $text ) {

		if ( ! is_string( $text ) ) {
			return;
		}
		$random_id = 'dialog' . substr( str_shuffle( implode( '', range( 'a', 'z' ) ) ), 0, 10 );

		echo '
			<script>
			  jQuery(function() {
				jQuery(".dialog' . $random_id . '").dialog({
				  modal: true,
				  buttons: {
					Ok: function() {
					  jQuery( this ).dialog("close");
					}
				  }
				});
			  });
			</script>

			<div class=dialog' . $random_id . ' title="Some key problem">
				' . $text . '
			</div>

	';
	}

	/**
	 * If in database exist api_key, run update script
	 *
	 * @return bool
	 */
	public static function update_plugin_settings() {

		$check_api_key = get_option( 'contently_apiKey' );
		if ( ! is_string( $check_api_key ) ) {
			return true;
		}

		require_once( __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'updating-helpder.php' );
		$updating_helper = new Contently_Updating( self::$default_profile );
		$updating_helper->run();
	}

	//activation, deactivation. These methods are must be static and executed before other methods

	public static function on_activation() {

		return self::update_plugin_settings();
	}

	public static function on_deactivation() {

		return true;
	}

// end class
}
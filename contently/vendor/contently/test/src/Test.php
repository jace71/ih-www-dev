<?php

namespace Contently;

class Test {

	private function get_plugin_build() {
		$config = require_once plugin_dir_path( __FILE__ ) . '../../../../config.php';

		return isset( $config['BUILD_VERSION'] ) ? $config['BUILD_VERSION'] : 'no build version';
	}

	/**
	 * Should check the test init.
	 */
	public function get_test_contently_initial_test() {
		$result = array(
			'result' => true
		);

		$this->sendResponse( __FUNCTION__, $result );
	}

	public function get_contently_test_summary() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
		$plugins      = get_plugins();
		$used_plugins = array();
		foreach ( $plugins as $plugin ) {
			$used_plugins[] = array(
				'name'    => $plugin['Name'],
				'version' => $plugin['Version'],
				'url'     => $plugin['PluginURI'],
			);
		}

		$result = array(
			'result'            => true,
			'build_version'     => $this->get_plugin_build(),
			'php_version'       => phpversion(),
			'wordpress_version' => get_bloginfo( 'version' ),
			'used_plugins'      => $used_plugins
		);

		$this->sendResponse( __FUNCTION__, $result );
	}

	public function get_test_connector_stream() {
		$transport = new \Contently\Transports\Stream();
		$result    = array(
			'result' => $transport->isAvailable()
		);

		$this->sendResponse( __FUNCTION__, $result );
	}

	public function get_test_connector_curl() {
		$transport = new \Contently\Transports\Curl();
		$result    = array(
			'result' => $transport->isAvailable()
		);

		$this->sendResponse( __FUNCTION__, $result );
	}

	public function get_test_connector_current() {
		$result = array(
			'result'    => false,
			'transport' => 'no transport detected'
		);

		try {
			$connector           = new \Contently\Connector();
			$result['result']    = true;
			$result['transport'] = $connector->_getCurrentTransport();
		} catch ( \Exception $e ) {
			$result['transport'] = $e->getMessage();
		}

		$this->sendResponse( __FUNCTION__, $result );
	}

	public function get_test_filesystem_privileges() {
		$LoggerConfig = \Contently\Log::instance()->getConfig();

		$result = array(
			'result'                => true,
			'file'                  => $LoggerConfig['file'],
			'is_writable_file'      => is_writable( $LoggerConfig['file'] ),
			'directory'             => dirname( $LoggerConfig['file'] ),
			'is_writable_directory' => is_writable( dirname( $LoggerConfig['file'] ) ),
		);

		$this->sendResponse( __FUNCTION__, $result );
	}

	public function get_test_logger_filesystem() {
		$logger_driver = new \Contently\Log\Drivers\Filesystem( array(
			'file'  => plugin_dir_path( __FILE__ ) . '.log/contently.log',
			'state' => true
		) );
		$result        = array(
			'result' => $logger_driver->hasHandler()
		);

		$this->sendResponse( __FUNCTION__, $result );
	}

	public function get_test_database_privileges() {

		global $wpdb;
		$rows = $wpdb->get_results( "SHOW PRIVILEGES ", ARRAY_A );

		$result_privileges = array();
		$privileges        = array( 'Create', 'Drop', 'Insert', 'Select' );
		foreach ( $rows as $row ) {
			if ( in_array( $row['Privilege'], $privileges ) ) {
				$row['result']       = (bool) preg_match( '/Tables/i', $row['Context'] );
				$result_privileges[] = $row;
			}
		}

		$result = array(
			'result'     => true,
			'privileges' => $result_privileges
		);

		$this->sendResponse( __FUNCTION__, $result );
	}

	public function get_test_logger_database() {
		$logger_driver = new \Contently\Log\Drivers\Wp_database( array(
			'table_name' => 'contently_log',
			'state'      => true
		) );
		$result        = array(
			'result' => $logger_driver->hasHandler()
		);

		$this->sendResponse( __FUNCTION__, $result );
	}

	public function get_contently_test_buffer() {
		ob_get_contents();
		ob_end_clean();
		if ( ! ob_start( "ob_gzhandler" ) ) {
			ob_start();
		}
		require_once( plugin_dir_path( __FILE__ ) . '../../../../templates/profile_footer.php' );
		$template = ob_get_contents();
		ob_end_clean();
		$result = array(
			'result'  => true,
			'content' => $template
		);
		$this->sendResponse( __FUNCTION__, $result );
	}

	private function sendResponse( $function_name, $result ) {
//		$logger = \Contently\Log::instance();
//		$logger->log( 'TEST', 'Test event: ', array( 'test' => $function_name, 'json!__result' => $result ) );
		echo json_encode( $result );
		exit;
	}

}
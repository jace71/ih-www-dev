<?php

namespace Contently\Log\Drivers;

class Wp_database implements DriverInterface {

	private $DRIVER = 'database';

	private $config = array(
		'table_name' => 'contently_log',
		'state'      => false
	);

	private $handler = null;

	/**
	 * Wp_database constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {

		if ( $config ) {
			$this->config = array_merge( $this->config, $config );
		}

		if ( ! $this->config['state'] ) {
			return false;
		}

		global $wpdb;
		$this->config['table_name'] = $wpdb->prefix . $this->config['table_name'];
		$db_log_table               = $wpdb->get_var( "SHOW TABLES LIKE '{$this->config['table_name']}'" );
		if ( $db_log_table !== $this->config['table_name'] ) {
			$sql = "CREATE TABLE {$this->config['table_name']} (
  						`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  						`date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  						`text` text NOT NULL,
  						PRIMARY KEY (`id`)
					)";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			if ( $result = dbDelta( $sql ) ) {
				$this->handler = true;
				$this->log( 'INFO', 'Log table created.', $result );
			}
		}

		$this->handler = true;
	}

	public function getDriverDescription() {
		return $this->DRIVER;
	}

	public function hasHandler() {
		return $this->handler;
	}

	public function getData() {
		global $wpdb;
		$rows = $wpdb->get_results( "SELECT * FROM {$this->config['table_name']}" );

		$result = array();
		foreach ( $rows as $row ) {
			$result[] = json_decode( $row->text, JSON_OBJECT_AS_ARRAY );
		}

		return $result;
	}

	public function log( $data = '' ) {
		if ( $this->config['state'] && $this->handler ) {
			global $wpdb;

			return (bool) $wpdb->insert( $this->config['table_name'], array(
				'date' => date( 'Y-m-d H:i:s' ),
				'text' => $data
			) );
		} else {
			return false;
		}
	}

	public function remove() {
		global $wpdb;

		return $wpdb->get_row( "DELETE FROM {$this->config['table_name']}" );
	}

	public function drop() {
		global $wpdb;

		return $wpdb->query( "DROP TABLE IF EXISTS {$this->config['table_name']}" );
	}

}
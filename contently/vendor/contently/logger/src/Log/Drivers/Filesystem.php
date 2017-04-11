<?php

namespace Contently\Log\Drivers;

class Filesystem implements DriverInterface {

	private $DRIVER = 'local file';

	private $config = array(
		'state'     => false,
		'file'      => "",
		'overwrite' => false
	);

	private $handler = null;

	public function __construct( Array $config = array() ) {
		if ( $config ) {
			$this->config = array_merge( $this->config, $config );
		}

		if ( ! $this->config['state'] ) {
			return false;
		}

		if ( empty( $this->config['file'] ) ) {
			$this->config['file'] = dirname( __FILE__ ) . '/../.log/contently.log';
		}

		$file_name = $this->config['file'];
		if ( $file_name && ! file_exists( $file_name ) ) {
			@mkdir( dirname( $file_name ) );
		}
		$this->handler = @fopen( $file_name, 'a+' );
	}

	public function getDriverDescription() {
		return $this->DRIVER;
	}

	public function hasHandler() {
		return gettype( $this->handler ) === 'resource';
	}

	/**
	 * @param string $data
	 *
	 * @return bool
	 */
	public function log( $data = '' ) {
		if ( $this->config['state'] && $this->handler ) {

			return (bool) @fwrite( $this->handler, $data . PHP_EOL );
		}

		return false;
	}

	public function getData() {
		$data = @file_get_contents( $this->config['file'] );
		$data = explode( PHP_EOL, $data );

		$result = array();
		foreach ( $data as $row ) {
			$result[] = json_decode( $row, JSON_OBJECT_AS_ARRAY );
		}

		return $result;
	}

	public function remove() {
		$file_name = $this->config['file'];
		@unlink( $file_name );

		return @rmdir( dirname( $file_name ) );
	}

}
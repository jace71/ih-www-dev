<?php

namespace Contently;

class Log {

	static $_instance = null;

	private $id_instance;
	private $order_id = 0;

	private $driver = null;

	private $config = array(
		'file'      => '',
		'overwrite' => false,
		'state'     => true,
		'driver'    => 'Wp_database'
	);

	/**
	 * Log constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {
		$this->id_instance = uniqid();

		if ( $config ) {
			$this->config = array_merge( $this->config, $config );
		}

		if ( ! $this->config['state'] ) {
			return;
		}

		// Make Driver instance.
		switch ( true ) {
			case is_string( $this->config['driver'] ):
				$driverClassName = "\\Contently\\Log\\Drivers\\" . $this->config['driver'];
				$this->driver    = new $driverClassName( $this->config );
				break;
			case is_array( $this->config['driver'] ):
				foreach ( $this->config['driver'] as $driver ) {
					$driverClassName = "\\Contently\\Log\\Drivers\\" . $driver;
					$this->driver    = new $driverClassName( $this->config );
					if ( $this->driver->hasHandler() ) {
						break;
					}
				}
				break;
		}

		if ( $this->driver ) {
			if ( $this->config['overwrite'] ) {
				$this->driver->remove();
			}
		}
	}

	public function getConfig() {
		return $this->config;
	}

	/**
	 * @param array $config
	 *
	 * @return Log
	 */
	static function instance( $config = array() ) {
		if ( ! self::$_instance ) {
			self::$_instance = new self( $config );
		}

		return self::$_instance;
	}

	public function getUsedDriver() {
		if ( $this->driver ) {
			return $this->driver->getDriverDescription();
		} else {
			if ( $this->getState() ) {
				return 'Driver is not selected';
			} else {
				return 'Disabled';
			}
		}
	}

	/**
	 * @return bool
	 */
	public function hasHandler() {
		if ( $this->driver ) {
			return $this->driver->hasHandler();
		} else {
			return false;
		}
	}

	/**
	 * Get all data from the Log
	 * @return string
	 */
	public function getData() {
		if ( $this->driver ) {
			$data = $this->driver->getData();

			$data = array_reverse( $data );

			// Group by instance id
			$data_by_group = array();
			foreach ( $data as $key => $value ) {
				$id = $value['id'];
				if ( ! isset( $data_by_group[ $id ] ) ) {
					$data_by_group[ $id ] = array();
				}
				array_unshift( $data_by_group[ $id ], $value );
			}

			$data = array();
			foreach ( $data_by_group as $key => $values ) {
				if ( is_array( $values ) ) {
					foreach ( $values as $key_v => $value ) {
						$data[] = $value;
					}
				}
			}

			return $data;
		} else {
			if ( $this->getState() ) {
				return 'WARNING: Data not found. Driver is not available.';
			} else {
				return '';
			}
		}
	}

	/**
	 * @param bool $state
	 */
	public function setState( $state = false ) {
		$this->config['state'] = $state;
		$this->__construct( $this->config );

		return $this->config['state'];
	}

	/**
	 * @return mixed
	 */
	public function getState() {
		return $this->config['state'];
	}

	/**
	 * Put data to Log.
	 *
	 * @param       $type
	 * @param       $message
	 * @param array $context
	 *
	 * @return bool
	 */
	public function log( $type, $message, $context = array() ) {
		if ( $this->driver ) {

			$data = array(
				'id'        => $this->id_instance,
				'time'      => date( 'Y-m-d H:i:s' ),
				'timestamp' => time(),
				'order'     => $this->order_id,
				'type'      => $type,
				'message'   => $message,
				'context'   => $context
			);

			$this->order_id ++;

			return $this->driver->log( json_encode( $data, true ) );
		}

		return false;
	}

	/**
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool|int
	 */
	public function info( $message, $context = array() ) {
		$this->log( 'INFO', $message, $context );
	}

	public function step( $file, $class = 'is not a class', $function, $line, $message = 'stack state' ) {
		$this->log( 'STACK', $message, array(
			'file'     => $file,
			'class'    => $class,
			'function' => $function,
			'line'     => $line
		) );
	}

	/**
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool|int
	 */
	public function alert( $message, $context = array() ) {
		return $this->log( 'ALERT', $message, $context );
	}

	/**
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool|int
	 */
	public function error( $message, $context = array() ) {
		return $this->log( 'ERROR', $message, $context );
	}

	/**
	 * Remove the log file.
	 */
	public function remove() {
		if ( $this->driver ) {
			return $this->driver->remove();
		}

		return false;
	}

}
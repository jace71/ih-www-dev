<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 08.06.16
 * Time: 17:40
 */

namespace Contently\Transports;


class Transport {

	private $config = array();
	private $transport = false;
	private $transport_current_name = 'no detected';

	public function __construct( $config ) {
		$this->config = $config;

		// Get transport
		$transports = array( 'Stream', 'Curl' );
		foreach ( $transports as $transport ) {
			$transport_class = '\\Contently\\Transports\\' . $transport;
			$_transport      = new $transport_class( $this->config );
			if ( $_transport->isAvailable() ) {
				$this->transport_current_name = $transport;
				$this->transport              = $_transport;
				break;
			}
		}
	}

	/**
	 * @private
	 */
	public function _getTransportName() {
		return $this->transport_current_name;
	}

	public function setConfig( $config ) {
		$this->config = $config;
	}

	public function hasAvailable() {
		return $this->transport;
	}

	public function request( $handler, $data = null ) {
		$method = $handler['method'];
		$link   = $this->config['url'] . $handler['link'];

		\Contently\Log::instance()->log( 'NETWORK', '-> request to server', array(
			'link' => $link,
			'data' => $data
		) );

		$response = $this->transport->$method( $link, $this->config, $data );

		\Contently\Log::instance()->log( 'NETWORK', '<- response from server', array(
			'json!__data' => $response
		) );

		return (object) json_decode( $response, true );
	}

}
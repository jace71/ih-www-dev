<?php
namespace Contently;

class Connector {

	private $config = array(
		'url'       => 'https://api.contently.com/v1',
		'key'       => '',
		'userAgent' => 'Wordpress',
		'version'   => '1.1.1'
	);

	private $handlers = array(
		'SET_WEB_HOOK'        => array( 'method' => 'put', 'link' => '/set_webhook' ),
		'GET_TAXONOMY'        => array( 'method' => 'get', 'link' => '/taxonomy' ),
		'GET_STORY'           => array( 'method' => 'get', 'link' => '/stories/{ID}' ),
		'SET_STORY_PUBLISHED' => array( 'method' => 'put', 'link' => '/stories/{ID}/mark_published' )
	);

	private $transport = false;

	/**
	 * Connector constructor.
	 *
	 * @param array $config
	 *
	 * @throws
	 */
	public function __construct( $config = array() ) {
		$this->config = array_merge( $this->config, $config );

//		if ( ! $this->config['key'] ) {
//			throw new \Exception( 'Api key is not valid' );
//		}

		$this->transport = new Transports\Transport( $this->config );

		if ( ! $this->transport ) {
			throw new \Exception( 'There are no HTTP transports available which can complete the requested request.' );
		}
	}

	/**
	 * @private
	 * @return bool|Transports\Transport
	 */
	public function _getCurrentTransport() {
		return $this->transport->_getTransportName();
	}

	public static function keyValidator( $apiKey ) {
		if ( ! is_string( $apiKey ) || strlen( $apiKey ) != 32 || ! ctype_alnum( $apiKey ) ) {
			return false;
		}

		return true;
	}

	public function useKey( $key ) {
		$this->config['key'] = $key;
		$this->transport->setConfig( $this->config );

		return $this;
	}

	public function getKey() {
		return $this->config['key'];
	}

	/**
	 * Get Taxonomy of publications.
	 *
	 * @return array
	 */
	public function getTaxonomy() {
		return $this->transport->request( $this->handlers['GET_TAXONOMY'] );
	}

	/**
	 * Get Story by id
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function getStory( $id ) {
		$handler         = $this->handlers['GET_STORY'];
		$handler['link'] = str_replace( '{ID}', $id, $handler['link'] );

		return $this->transport->request( $handler );
	}

	/**
	 * Set a web-callback.
	 *
	 * @param $localUrl
	 *
	 * @return mixed
	 */
	public function setWebHook( $localUrl ) {
		$handler = $this->handlers['SET_WEB_HOOK'];
		$data    = 'webhook_url=' . $localUrl . '/?contently_push=' . base64_encode( $this->config['key'] );

		return $this->transport->request( $handler, $data );
	}

	public function setPublished( $id, $published_url ) {
		$handler         = $this->handlers['SET_STORY_PUBLISHED'];
		$handler['link'] = str_replace( '{ID}', $id, $handler['link'] );
		$data            = 'published_to_url=' . $published_url;

		return $this->transport->request( $handler, $data );
	}

}
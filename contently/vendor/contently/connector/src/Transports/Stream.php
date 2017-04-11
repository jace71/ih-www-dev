<?php

namespace Contently\Transports;


class Stream {

	public function isAvailable() {
		if ( ! function_exists( 'stream_socket_client' ) ) {
			return false;
		}

		if ( ! extension_loaded( 'openssl' ) ) {
			return false;
		}

		if ( ! function_exists( 'openssl_x509_parse' ) ) {
			return false;
		}

		return true;
	}

	public function get( $link, $config, $data = null ) {
		$request = array(
			'http' => array(
				'ignore_errors' => true,
				'method'        => 'GET',
				'header'        =>
					"User-Agent: Contently " . $config['userAgent'] . " " . $config['version'] . "\r\n" .
					"Contently-Api-Key: " . $config['key'] . "\r\n"
			)
		);
		$stream  = stream_context_create( $request );

		return @file_get_contents( $link, false, $stream );
	}

	public function put( $link, $config, $data ) {
		$request = array(
			'http' => array(
				'ignore_errors' => true,
				'method'  => 'PUT',
				'header'  =>
					"User-Agent: Contently " . $config['userAgent'] . " " . $config['version'] . "\r\n" .
					"Contently-Api-Key: " . $config['key'] . "\r\n" .
					"Content-type: application/x-www-form-urlencoded\r\n" .
					"Content-Length: " . strlen( $data ) . "\r\n",
				'content' => $data
			)
		);
		$stream  = stream_context_create( $request );

		return @file_get_contents( $link, false, $stream );
	}

}
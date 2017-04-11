<?php

namespace Contently\Transports;


class Curl {

	public function isAvailable() {

		if ( ! function_exists( 'curl_init' ) || ! function_exists( 'curl_version' ) || ! function_exists( 'curl_exec' ) ) {
			return false;
		}

		$disabled = explode( ',', ini_get( 'disable_functions' ) );

		if ( in_array( 'curl_exec', $disabled ) || in_array( 'curl_multi_exec', $disabled ) ) {
			return false;
		}

		$curl_version = curl_version();

		if ( ! ( CURL_VERSION_SSL & $curl_version['features'] ) ) {
			return false;
		}

		return true;
	}

	private function prepareCurl( $link, $config, $method ) {
		$curlHeader = array(
			"User-Agent: Contently " . $config['userAgent'] . " " . $config['version'],
			"Contently-Api-Key: " . $config['key']
		);

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $link );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $curlHeader );

		return $ch;
	}

	public function get( $link, $config ) {
		$curl   = $this->prepareCurl( $link, $config, 'GET' );
		$result = curl_exec( $curl );
		curl_close( $curl );

		return $result;
	}

	public function put( $link, $config, $data ) {
		$curl = $this->prepareCurl( $link, $config, 'PUT' );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
		$result = curl_exec( $curl );
		curl_close( $curl );

		return $result;
	}

}
<?php
namespace Contently\Log\Drivers;

interface DriverInterface {

	public function getDriverDescription();

	public function hasHandler();

	public function getData();

	public function log( $data );

}
<?php 
require_once __DIR__ . '/KdtApiClient.php';

final class Youzan {

	private $client;

	function __construct($appId, $appSecret) {
		$this->client = new KdtApiClient($appId, $appSecret);
	}

	function exec($method, $params) {
		return $this->client->post($method, $params);
	}
}
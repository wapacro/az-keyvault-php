<?php


namespace AzKeyVault;


use GuzzleHttp\Client;

class Identity {

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var array
	 */
	protected $accessTokenResponse;

	public function __construct() {
		$this->client = new Client();
	}

	public function getAccessToken($secret = null, $endpoint = null) {
		if (!isset($this->accessTokenResponse->access_token)) {
			$this->retrieveAccessToken($secret, $endpoint);
		}

		return $this->accessTokenResponse->access_token;
	}

	protected function retrieveAccessToken($secret = null, $endpoint = null) {
		$endpoint = $endpoint ?? getenv('MSI_ENDPOINT');

		$response = $this->client->get($endpoint, [
			'query' => [
				'api-version' => '2017-09-01',
				'resource' => 'https://vault.azure.net',
			],
			'headers' => [
				'secret' => $secret ?? getenv('MSI_SECRET'),
			],
		]);

		$this->accessTokenResponse = json_decode($response->getBody());
	}


}

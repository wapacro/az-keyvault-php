<?php


namespace AzKeyVault;


use Spatie\Url\Url;

class Client {

	const OAUTH_API_VERSION = '2019-08-01';
	const VAULT_API_VERSION = '7.0';

	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $client;
	/**
	 * @var void
	 */
	protected $accessToken;

	/**
	 * Client constructor
	 */
	public function __construct() {
		$this->client = new \GuzzleHttp\Client();
		$this->accessToken = $this->getAccessToken();
	}

	/**
	 * Get access token using managed identity
	 * @return string
	 */
	protected function getAccessToken() {
		$endpoint = getenv('IDENTITY_ENDPOINT');
		$idHeader = getenv('IDENTITY_HEADER');
		$resource = 'https://vault.azure.net';

		$endpoint = Url::fromString($endpoint)->withQueryParameter('resource', $resource);
		return 'Bearer ' . $this->get($endpoint, $idHeader, 'X-IDENTITY-HEADER', self::OAUTH_API_VERSION)->access_token;
	}

	/**
	 * Wrapper for HTTP GET requests
	 * @param $url
	 * @param string $accessToken
	 * @param string $accessTokenHeader
	 * @param string $apiVersion
	 * @return mixed
	 */
	public function get(string $url, string $accessToken = null, string $accessTokenHeader = 'Authorization', string $apiVersion = self::VAULT_API_VERSION) {
		$url = Url::fromString($url)->withQueryParameter('api-version', $apiVersion);
		return json_decode($this->client->get($url, [
			'headers' => [$accessTokenHeader => $accessToken ?? $this->accessToken],
		])->getBody());
	}

}

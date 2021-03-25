<?php


namespace AzKeyVault;

use Spatie\Url\Url;

class Client {
    public const OAUTH_API_VERSION = '2019-08-01';

    public const VAULT_API_VERSION = '7.0';

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var void */
    protected $accessToken;

    /**
     * Client constructor
     */
    public function __construct() {
        $this->client = new \GuzzleHttp\Client();
        $this->accessToken = $this->getAccessToken();
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

	/**
	 * Wrapper for HTTP Post requests
	 * @param string $url
	 * @param array $body
	 * @param string|null $accessToken
	 * @param string $accessTokenHeader
	 * @param string $apiVersion
	 * @return mixed
	 */
    public function post(string $url, array $body, string $accessToken = null, string $accessTokenHeader = 'Authorization', string $apiVersion = self::VAULT_API_VERSION) {
        $url = Url::fromString($url)->withQueryParameter('api-version', $apiVersion);
        return json_decode($this->client->post($url, [
            'headers' => [$accessTokenHeader => $accessToken ?? $this->accessToken],
			'body' => $body,
        ])->getBody());
    }

    /**
     * Get access token using managed identity
     * @return string
     */
    protected function getAccessToken() {
        // Get MSI endpoint & token from environment (App Service) or use hardcoded values in case of VM
        $endpoint = $this->env('IDENTITY_ENDPOINT', 'http://169.254.169.254/metadata/identity/oauth2/token');
        $idHeaderValue = $this->env('IDENTITY_HEADER', 'true');
        $idHeaderName = !empty($this->env('IDENTITY_HEADER')) ? 'X-IDENTITY-HEADER' : 'Metadata';
        $resource = 'https://vault.azure.net';

        $endpoint = Url::fromString($endpoint)->withQueryParameter('resource', $resource);
        return 'Bearer ' . $this->get($endpoint, $idHeaderValue, $idHeaderName, self::OAUTH_API_VERSION)->access_token;
    }

    /**
     * Returns the environment variable value if it
     * exists, otherwise the passed fallback value
     * @param string $name
     * @param string $fallback
     * @return array|string
     */
    private function env(string $name, string $fallback = '') {
        $value = getenv($name);
        return $value !== false ? $value : $fallback;
    }
}

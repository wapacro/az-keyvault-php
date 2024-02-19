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

    /** @var iterable */
    protected iterable $options;

    /**
     * Client constructor
     */
    public function __construct(iterable $options = []) {
        $this->options = $options;
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
            'headers' => [$accessTokenHeader => $accessToken ?? $this->accessToken, 'metadata' => 'true'],
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


    protected function getClientCredentialsToken(string $tenantId, string $clientId, string $clientSecret): string
    {
        $params = [
            'client_id' => $clientId
            , 'scope' => 'https://vault.azure.net/.default'
            , 'client_secret' => $clientSecret
            , 'grant_type' => 'client_credentials'
        ];

        $url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/v2.0/token';
        $bearer = json_decode($this->client->post($url, [
            'form_params' => $params,
        ])->getBody())->access_token;

        return 'Bearer ' .  $bearer;
    }

    protected function getManagedIdentityToken()
    {
        // Get MSI endpoint & token from environment (App Service) or use hardcoded values in case of VM
        $endpoint = $this->env('IDENTITY_ENDPOINT', 'http://169.254.169.254/metadata/identity/oauth2/token');
        $idHeaderValue = $this->env('IDENTITY_HEADER', 'true');
        $idHeaderName = !empty($this->env('IDENTITY_HEADER')) ? 'X-IDENTITY-HEADER' : 'Metadata';
        $resource = 'https://vault.azure.net';

        $endpoint = Url::fromString($endpoint)->withQueryParameter('resource', $resource);
        return 'Bearer ' . $this->get($endpoint, $idHeaderValue, $idHeaderName, self::OAUTH_API_VERSION)->access_token;
    }

    /**
     * Get access token using managed identity
     * @return string
     */
    protected function getAccessToken() {
        if (!empty($this->option("AZURE_CLIENT_ID"))) {
            return $this->getClientCredentialsToken($this->option('AZURE_TENANT_ID')
                                                  , $this->option('AZURE_CLIENT_ID')
                                                  , $this->option('AZURE_CLIENT_SECRET')
                                                );
        } else {
            return $this->getManagedIdentityToken();
        }
    }

        /**
     * Returns the option value if it exists
     * otherwise it search in the environment variable,
     * if it stills not exist, the passed fallback value
     * @param string $name
     * @param string $fallback
     * @return array|string
     */
    private function option(string $name, string $fallback = '') {
        return isset($this->options[$name]) ? $this->options[$name] : $this->env($name, $fallback);
    }

    /**
     * Returns the environment variable value if it
     * exists, otherwise the passed fallback value
     * @param string $name
     * @param string $fallback
     * @return array|string
     */
    private function env(string $name, string $fallback = '') {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $fallback;
    }
}

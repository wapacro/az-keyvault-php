<?php

namespace AzKeyVault;


use Spatie\Url\Url;

class ClientForVM extends Client {

	const MSI_ENDPOINT = "http://169.254.169.254/metadata/identity/oauth2/token";

		/**
         * Get access token using managed identity on an Azure VM.
         * @return string
         */
        protected function getAccessToken() {
            $metaHeader = 'true';

            $endpoint = Url::fromString(self::MSI_ENDPOINT)->withQueryParameter('resource', self::VAULT_RESOURCE);
            return 'Bearer ' . $this->get($endpoint, $metaHeader, 'Metadata', self::OAUTH_API_VERSION)->access_token;
        }

}

?>

# Azure Key Vault Library
This library allows easy integration of
[Azure Key Vault](https://docs.microsoft.com/en-us/azure/key-vault/about-keys-secrets-and-certificates)
in PHP applications.

### Highlights
- [Built-in managed identity support](https://docs.microsoft.com/en-us/azure/app-service/overview-managed-identity)<br>
  Setup managed identities for your apps and centralise all secrets,
  keys and certificates in Azure Key Vault. Get secure access directly
  from your code without worrying about credentials.
- Easy to use API<br>
  This library's API is simple and easy to understand. After some setup
  in Azure and a few lines of code you're good to go!
- Works with Windows & Linux based App Service Plans and Virtual Machines

## How to use
Get started in three simple steps!

1. [Add a system-assigned identity](https://docs.microsoft.com/en-us/azure/app-service/overview-managed-identity#add-a-system-assigned-identity)
   to your Azure App Service and assign permissions to your application
   to read & list secrets from Key Vault
2. Install this package in your project
   using Composer
   ```
   composer require wapacro/az-keyvault-php
   ````
3. Access your secrets & keys in Key Vault using the simple API:
   ```php
   <?php
   /**
    * Secrets
    */
   $secret = new AzKeyVault\Secret('https://my-keyvault-dns.vault.azure.net');

   // If you want to get all secrets (default max to 25):
   $secrets = $secret->getSecrets();
   // ... else get next page via nextLink
   $secrets = $secret->getSecrets($secrets->getNextLink());

   // If you want the latest secret
   $value = $secret->getSecret('mySecretName');

   // If you want a specific secret version:
   $value = $secret->getSecret('mySecretName', '9fe63d32-5eb0-47f2-8ef8-version-id');

   // ... otherwise get all versions of secret
   // with name "mySecretName" which are marked
   // as enabled and retrieve the first one
   $enabledSecretVersions = $secret->getSecretVersions('mySecretName')->enabled();
   $firstEnabledVersion = reset($enabledSecretVersions);
   $value = $secret->getSecret($firstEnabledVersion);

   echo $value->secret;
   // prints: my super secret message

   // If you want to set secret or update secret with newer version:
   $value = $secret->setSecret('mySecretName', 'mySecretValue');
   echo $value->secret;
   // prints: mySecretValue

   /**
    * Keys
    */
   $key = new AzKeyVault\Key('https://my-keyvault-dns.vault.azure.net');

   // Retrieve specific key version:
   $value = $key->getKey('myKeyName', 'j7d8rd32-5eb0-47f2-8ef8-version-id');

   // ... or get all versions of a key
   // and retrieve the first one which
   // is enabled, just like with the
   // secrets above
   $enabledKeyVersions = $key->getKeyVersions('myKeyName')->enabled();
   $firstEnabledVersion = reset($enabledKeyVersions);
   $value = $key->getKey($firstEnabledVersion);

   echo $value->type; // e.g. "RSA"
   echo $value->n;    // prints base64 encoded RSA modulus

   // This library also provides some key utilities
   // to make retrieved keys work with the OpenSSL extension
   $pem = (new AzKeyVault\KeyUtil($value))->toPEM();
   $keyDetails = openssl_pkey_get_details(openssl_pkey_get_private($pem));
   var_dump($keyDetails);
   ````

*Note:* `KeyUtil` supports RSA and EC keys

## Planned features
- Accessing certificates

# Azure Key Vault Library
This library allows easy integration of
[Azure Key Vault](https://docs.microsoft.com/en-us/azure/key-vault/about-keys-secrets-and-certificates)
in PHP applications.

### Highlights
- [Built-in managed identity support](https://docs.microsoft.com/en-us/azure/app-service/overview-managed-identity)  
  Setup managed identities for your apps and centralise all secrets,
  keys and certificates in Azure Key Vault. Get secure access directly
  from your code without worrying about credentials.
- Easy to use API  
  This library's API is simple and easy to understand. After some setup
  in Azure and a few lines of code you're good to go!
- Works with Windows & Linux based App Service Plans

## How to use
Get started in three simple steps!

1. [Add a system-assigned identity](https://docs.microsoft.com/en-us/azure/app-service/overview-managed-identity#add-a-system-assigned-identity)
   to your Azure App Service and assign permissions to your application
   to read & list secrets from Key Vault
2. Install this package in your project
   using Composer
   ```
   composer install citizen-of-planet-earth/az-keyvault-php
   ````
3. Access your secrets in Key Vault using the simple API:
   ```php
   <?php
   $secret = new AzKeyVault\Secret('https://my-keyvault-dns.vault.azure.net');
   
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
   ````

## Planned features
- Accessing certificates & keys

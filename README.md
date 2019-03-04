# Clay Hardware API PHP SDK

## Requirements
- PHP 7.2 or above
- JSON
- CURL
- OpenSSL


## Installation
- Require the library using `composer require claysolutions/clp-php-sdk:^1.0`.
### Laravel service provider
- If you're using Laravel 5.5 or above, the service is automatically registered for you.
- If not, add the `Clay\CLP\Providers\CLPServiceProfider::class` to the list of providers in your `app.php`.

## Basic usage
- This package will provide you with three clients:
    - `CLPClient`: this is the client you can use to interact with the **Hardware API**.
    - `IdentityServerClient`: this is the client you can use to **generate access tokens**.
    - `VaultClient`: this is the client you can use to sign certificates for **Mobile Key** generation.
- The clients require you to inject a **configuration repository** dependency.
    - If you're using Laravel, this is done automatically on the Service Provider, so all you need to do is request an instance of any of those clients, and they'll be provided already configured for you.
    - If you're not using Laravel, you must provide an instance of the `Illuminate\Contracts\Config\Repository` interface. If you're using environment variables or a DotEnv file, a `DotEnvConfigLoader` class that implements this interface is provided, so you can load your variables directly from your `.env` or environment vars.
        - You can find an example `.env` file in the `tests/` folder. The file must have the same variables, with the same name, to be correctly loaded.
    
### Examples
#### Using the provided clients and `.env` 
```
<?php
require("vendor/autoload.php");

// Create the Config repository via .env file.
$config = new DotEnvConfigLoader(Dotenv::create(__DIR__));

// Creates the identity server client.
$identityServer = new IdentityServerClient($config);

// Creates the CLP client.
$client = new CLPClient($config);

// Configures the CLP client to use the Identity Server as auth provider.
$client->setAuthorizationHeaderProvider(function () use ($identityServer) {
    return $identityServer->provideAccessToken()->generateAuthorizationHeader();
});

// Example: gets a list of accessors from the API.
// This will return a Collection object with the list of Accessor structs.
$accessors = $client->accessors()->getAccessors()->items();

// Example: create a new accessor.
// This will return an Accessor object upon succesfully registering the new accessor.
$newAccessor = new NewAccessor();
$accessor = $client->accessors()->createAccessor($newAccessor);

```

## Provided APIs
- `AccessGroupAPI`: lets you manage access groups; implements the endpoints within the `/access_group` namespace. 
- `AccessorAPI`: lets you manage accessors (end users), as well as their devices and keys; implements the endpoints within the `/accessor` namespace.
- `CollectionAPI`: lets you manage hardware collections; implements the endpoints within the `/collections` namespace.
- `IQAPI`: lets you manage IQs; implements the endpoints within the `/iqs` namespace.
- `LockAPI`: lets you manage locks; implements the endpoints within the `/lock` namespace.
- `TagAPI`: lets you manage tags; implements the endpoints within the `/tag` endpoint.

#### Namespaces still not implemented
- `AccessorSettings` (`/collections/{collection_id}/accessor_settings`)
- `CollectionSettings` (`/accessors/{accessor_id}/collection_settings`)
- `Entries` (`/entries`)
- `Incidents` (`/incidents`)
- `Updates` (`/updates`)

## Usage guide (TO-DO)
- [Generating and using tokens](/docs/generating_and_using_tokens.md)
- [Registering an IQ]() *(TO-DO)*
- [Attaching Locks to an IQ]() *(TO-DO)*
- [Using Repeaters and the HW Tree]() *(TO-DO)*
- [Registering Accessors and Tags]() *(TO-DO)*
- [Registering a Mobile Key]() *(TO-DO)*
- [Using Time Schedules for Locks]() *(TO-DO)*
- [Customizing access rules for Accessors and Access Groups]() *(TO-DO)*
- [Using the Tag Registration Mode]() *(TO-DO)*
- [Setting up Offline Access on a Lock]() *(TO-DO)*

## Glossary
- **IQ:** the internet-connected device that controls access to locks.
- **Lock:** any Clay-powered electronic lock. They get attached to a single IQ.
- **Repeater:** a device that repeats the IQ signal further. Locks can be attacked to a repeater instead an IQ.
- **Accessor:** a end user that can access zero or more locks. An accessor will have Keys, which may be Tags (regular tags) or Devices (mobile key).
- **Accessor Group:** a group of accessors, used to determine access rules in locks.
- **Collection:** a group of hardware devices (IQs, locks and repeaters).
- **Key:** an identification method for an accessor. A key can be a Tag or a Device (mobile key).
- **Tag:** the physical tag that enables accessors to access locks. They can be unregistered, or registered to a single accessor.
- **Time Schedule:** a schedule for which days of the week and at which times access is to be allowed.
- **IQ Hardware:** any and all hardware that is attached to an IQ
- **IQ Hardware Tree:** the tree of hardware devices connected to the IQ. It resembles a tree because locks can be attached to either IQs or Repeaters, and Repeaters to IQs.
- **Accessor Device:** a mobile device registered to an accessor, to be used as mobile key.
- **IQ Registration:** the process that registers a brand-new IQ into the API, via its Activation Code. After this process, the IQ gets an ID, and can have hardware attached to it. 
- **Tag Registration Mode:** a mode that Locks have in order to assign brand-new tags into the system. When tags are not previously registered, they are not recognized by the API and cannot be assigned to accessors. Setting a Lock to Tag Registration Mode lets you touch the lock with the fresh tags, and these tags get registered on the system.
- **Offline Mode / Offline Access:** when there is a communication problem (power outage, RF interference, etc) between locks and their IQs/Repeaters, the Locks enter Offline Mode. While in Offline Mode, they resort to an internal list of preset tags/mobile keys that are allowed access. While in Offline Mode, only the keys registered for Offline Access to that specific Lock can access it. Offline Mode does not support time schedules, and cannot be given directly to accessors, only to keys (tags & mobile keys). 
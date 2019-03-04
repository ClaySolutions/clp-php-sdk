# Registering an IQ

To register a brand-new IQ by its' activation code, you must first create a `NewIQRegistration` object:

```
<?php
$iqRegistration = new NewIQRegistration(
    $customerReference,
    $collectionId,
    $timeZone,
    $activationCode,
    $isSubscribed
);
```

The `NewIQRegistration` constructor takes the following parameters:

**`string $customerReference`:** A name, identifier or ID that identifies this IQ in your system. This is always returned when listing or getting the IQ by ID later, but is not used for anything else in the CLP. *Example:* `"Office IQ, second floor"`

**`string $collectionId`:** The ID for a collection, if you want to place this IQ in a collection from the get-go. You can pass null if you don't want to place the IQ in a collection. *Example*: `"b34369d2-43b4-4344-8c12-b7977afa2522"`

**`string $timeZone`:** A timezone identifier, designating at which timezone is this IQ located. *Example*: `"Europe/Amsterdam"`

**`string $activationCode`:** The activation code for the IQ. You can find this in the back of the IQ device. *Example*: `"03.E4.AD"`

**`bool $isSubscribed`:** Whether this IQ has an active Clay subscription. KS Connect partners can leave this as `false`. *Example*: `false`

Once the object is created, you can call the `registerIQ()` endpoint, giving it as a parameter:

```
<?php
$iqRegistration = new NewIQRegistration(
    "Office IQ, second floor",
    "b34369d2-43b4-4344-8c12-b7977afa2522",
    "Europe/Amsterdam",
    "03.E4.AD",
    false
);

$newIQ = $client->iqs()->registerIQ($iqRegistration);
```

The returned value will be an instance of the `IQ` struct, containing the created IQ details (including the ID).

If the registration fails, you will get an exception.

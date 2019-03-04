# Attaching locks to an IQ

**Note: this flow is not yet implemented in the SDK!**

First, we register the locks in the system:

```
<?php
$lockRegistration = new NewLockRegistration(
     $customerReference,
     $activationCode,
     $collectionId
);

$newLock = $client->locks()->registerLock($lockRegistration);
```

Once the lock is registered, it can be added to the IQ hardware tree, using the following:

```
<?php
$tree = $client->iqs()->getHardwareTree($iqID);
$tree->addHardware($newLock);
$client->iqs()->setHardwareTree($tree);
```


To detach the lock from the IQ, remove it from the IQ hardware tree, as follows:

```
<?php

$lock = $client->locks()->getLock($lockID);

$tree = $client->iqs()->getHardwareTree($iqID);
$tree->removeHardware($lock);

$client->iqs()->setHardwareTree($tree);
```


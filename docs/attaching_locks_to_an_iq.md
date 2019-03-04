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


To detach the lock from the IQ, you can call the `detachLock()` endpoint. You can pass an accessor ID as a second parameter, if you'd like to log the executor of the action.  

```
<?php

$lock = $client->locks()->getLock($lockID);
$client->locks()->detachFromIQ($lock->getID());

```


[ ![Codeship Status for StuartApp/stuart-client-php](https://app.codeship.com/projects/724b1210-3725-0135-3056-466529bde11a/status?branch=master)](https://app.codeship.com/projects/227364)

# Stuart PHP Client
For a complete documentation of all endpoints offered by the Stuart API, you can visit [Stuart API documentation](https://stuart.api-docs.io).

## Install
Via Composer:

``` bash
$ composer require stuartapp/stuart-client-php
```

## Usage

1. [Initialize Client](#initialize-client)
2. [Create a Job](#create-a-job)
    1. [Minimalist](#minimalist)
    2. [Complete](#complete)
        1. [With scheduling at pickup](#with-scheduling-at-pickup)
        2. [With stacking (multi-drop)](#with-stacking-multi-drop)
3. [Get a Job](#get-a-job)
4. [Custom requests](#custom-requests)

### Initialize client

```php
$environment = \Stuart\Infrastructure\Environment::SANDBOX;
$api_client_id = '65176d7a1f4e734f6723hd690825f166f8dadf69fb40af52fffdeac4593e4bc'; // can be found here: https://admin-sandbox.stuart.com/client/api
$api_client_secret = '681ae68635c7aadef5cd1jdng8ef357a808cd9dc794811296446f19268d48fcd'; // can be found here: https://admin-sandbox.stuart.com/client/api
$authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret);

$client = new \Stuart\Client($authenticator);
```

### Create a Job
**Important**: Even if you can create a Job with a minimal set of parameters, we **highly recommend** that you fill as many information as 
you can in order to ensure the delivery process goes well.


#### Minimalist
```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small');
    
$client->createJob($job);
```

#### Complete

```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris')
    ->setComment('Wait outside for an employee to come.')   
    ->setContactCompany('KFC Paris Barbès')                
    ->setContactFirstName('Martin')                         
    ->setContactLastName('Pont')                          
    ->setContactPhone('+33698348756');                     

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small')
    ->setComment('code: 3492B. 3e étage droite. Sonner à Durand.')
    ->setContactCompany('Durand associates.')
    ->setContactFirstName('Alex')
    ->setContactLastName('Durand')
    ->setContactPhone('+33634981209')
    ->setPackageDescription('Pizza box.')
    ->setClientReference('12345678ABCDE'); // Must be unique
    
$client->createJob($job);
```

##### With scheduling at pickup

For more information about job scheduling you should [check our API documentation](https://stuart.api-docs.io/v2/jobs/scheduling-a-job).

```php
$job = new \Stuart\Job();

$pickupAt = new \DateTime('now', new DateTimeZone('Europe/London'));
$pickupAt->add(new \DateInterval('PT2H'));

$job->addPickup('46 Boulevard Barbès, 75018 Paris')
    ->setPickupAt($pickupAt)
    ->setComment('Wait outside for an employee to come.')   
    ->setContactCompany('KFC Paris Barbès')                
    ->setContactFirstName('Martin')                         
    ->setContactLastName('Pont')                          
    ->setContactPhone('+33698348756');                     

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small')
    ->setComment('code: 3492B. 3e étage droite. Sonner à Durand.')
    ->setContactCompany('Durand associates.')
    ->setContactFirstName('Alex')
    ->setContactLastName('Durand')
    ->setContactPhone('+33634981209')
    ->setPackageDescription('Pizza box.')
    ->setClientReference('12345678ABCDE'); // Must be unique
    
$client->createJob($job);
```

##### With stacking (multi-drop)

```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris')
    ->setComment('Wait outside for an employee to come.')   
    ->setContactCompany('KFC Paris Barbès')                
    ->setContactFirstName('Martin')                         
    ->setContactLastName('Pont')                          
    ->setContactPhone('+33698348756');                     

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small')
    ->setComment('code: 3492B. 3e étage droite. Sonner à Durand.')
    ->setContactCompany('Durand associates.')
    ->setContactFirstName('Alex')
    ->setContactLastName('Durand')
    ->setContactPhone('+33634981209')
    ->setPackageDescription('Red packet.')
    ->setClientReference('12345678ABCDE'); // Must be unique;
    
$job->addDropOff('12 avenue claude vellefaux, 75010 Paris')
    ->setPackageType('small')
    ->setComment('code: 92A42. 2e étage gauche')
    ->setContactFirstName('Maximilien')
    ->setContactLastName('Lebluc')
    ->setContactPhone('+33632341209')
    ->setPackageDescription('Blue packet.')
    ->setClientReference('ABCDE213124'); // Must be unique
    
$client->createJob($job);
```

### Get a Job

Once you successfully created a Job you can retrieve it this way:

```php
$jobId = 126532;
$job = $client->getJob($jobId);
```

Or when you create a new Job:

```php
$job = new \Stuart\Job();

$job->addPickup('46 Boulevard Barbès, 75018 Paris');

$job->addDropOff('156 rue de Charonne, 75011 Paris')
    ->setPackageType('small');
    
$jobWithRoute = $client->createJob($job);

$jobWithRoute->getDeliveries();
```

The Stuart API determine the optimal route on your behalf, 
that's why the `getDeliveries()` method will return an empty 
array when the Job has not been created yet. The `getDeliveries()` 
method will return an array of `Delivery` as soon as the Job is created.

### Custom requests
You can also send requests on your own without relying on the `\Stuart\Client`.
It allows you to use endpoints that are not yet available on the `\Stuart\Client` and enjoy the `\Stuart\Authenticator`.

```php
$environment = \Stuart\Infrastructure\Environment::SANDBOX;
$api_client_id = '65176d7a1f4e734f6723hd690825f166f8dadf69fb40af52fffdeac4593e4bc'; // can be found here: https://admin-sandbox.stuart.com/client/api
$api_client_secret = '681ae68635c7aadef5cd1jdng8ef357a808cd9dc794811296446f19268d48fcd'; // can be found here: https://admin-sandbox.stuart.com/client/api
$authenticator = new \Stuart\Infrastructure\Authenticator($environment, $api_client_id, $api_client_secret);

$httpClient = new \Stuart\Infrastructure\HttpClient($this->authenticator);

$apiResponse = $httpClient->performGet('/v2/jobs?page=1');
$apiResponse->success();
$apiResponse->getBody();
```

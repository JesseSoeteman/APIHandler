# APIHandler
This class can be used to manage the status of an api request.

## Requirements
There are a couple things you will need in order to use this php class.
- php 8.0
- composer

## How to use APIHandler?

### Require APIHandler
If you have composer initialized you can run this command in you terminal to download APIHandler.
```console
$ composer require jessesoeteman/api-handler
```

### Require the autoloader and APIHandler
You will need to require the autoloader in your code, after that you can 'use' the APIReturn class.
```php
require __DIR__ . '/vendor/autoload.php';

use APIHandler\APIHandler;
```

### Basic usage

#### __Declare the class__
To start you will need to initialize the class.
The class needs one parameter that tells one of the following request types:
- get_request
- post_request

In this example we will use a GET request.
```php
$apiHandler = new APIHandler(get_request);
```
If the request type is not right the class will echo and exit: 
```json 
{
    "status": "error",
    "errors": [
        "Request method needs to be ...."
    ]
}
```

#### __Error Handling__
Let's say the an error occured, we can then add the error to the errors array.
The first parameter can be a string or an array, this contains the error you want to add.
The second parameter is a boolean (false on default), if the boolean is true, the script will echo a json object and exit.
```php
$apiHandler->addError("error 1");
$apiHandler->addError("error 2");

$apiHandler->addError("error 3", true); // <-- Exits here

$apiHandler->addError("error 4");
```
```php 
$apiHandler->addError("error 1");
$apiHandler->addError("error 2");
$apiHandler->addError("error 3");

$apiHandler->APIExitOnError(); // <-- Exits here

$apiHandler->addError("error 4");
$apiHandler->addData([]);
$apiHandler->APIExit();
```
```php
$apiHandler->addError("error 1");
$apiHandler->addError("error 2");
$apiHandler->addError("error 3");

$apiHandler->addData([]);

$apiHandler->APIExit(); // <-- Exits here
```
In the 3 examples above the class will echo the following and exit the script:
```json
{
    "status": "error",
    "errors": [
        "error 1",
        "error 2",
        "error 3",
    ]
}
```

#### __Succesfull request__
If the request was succesfull and no errors occured the class will echo that there was a succes:
```php
if (false) {
    $apiHandler->addError("error 1"); // Error wont be added.
}
$apiHandler->APIExitOnError(); // <-- Does not exit because there is no error.

$apiHandler->addData({});
$apiHandler->addData([]);
$apiHandler->addData("Hello World!");

$apiHandler->APIExit(); // <-- Exits here
```
This example will give the following output:
```json
{
    "status": "success",
    "data": [
        {},
        [],
        "Hello World!"
    ]
}
```

## License

This project is licensed under the [MIT](license)
 License - see the [LICENSE](license) file for
details

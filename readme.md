# APIReturn 
This class can be used to manage the status of an api request.

## Requirements
There are a couple things you will need in order to use this php class.
- php 8.0
- composer

## How to use APIReturn?

### Require APIReturn
If you have composer initialized you can run this command in you terminal to download APIReturn.
```console
$ composer require jessesoeteman/api-return
```

### Require the autoloader and APIReturn
You will need to require the autoloader in your code, after that you can 'use' the APIReturn class.
```php
require __DIR__ . '/vendor/autoload.php';

use APIReturn\APIReturn;
```

### Basic usage

#### __Declare the class__
To start you will need to initialize the class.
The class needs one parameter that tells one of the following request types:
- get_request
- post_request

In this example we will use a GET request.
```php
$apiReturn = new APIReturn(get_request);
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
$apiReturn->addError("error 1");
$apiReturn->addError("error 2");

$apiReturn->addError("error 3", true); // <-- Exits here

$apiReturn->addError("error 4");
```
```php 
$apiReturn->addError("error 1");
$apiReturn->addError("error 2");
$apiReturn->addError("error 3");

$apiReturn->APIExitOnError(); // <-- Exits here

$apiReturn->addError("error 4");
$apiReturn->addData([]);
$apiReturn->APIExit();
```
```php
$apiReturn->addError("error 1");
$apiReturn->addError("error 2");
$apiReturn->addError("error 3");

$apiReturn->addData([]);

$apiReturn->APIExit(); // <-- Exits here
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
    $apiReturn->addError("error 1"); // Error wont be added.
}
$apiReturn->APIExitOnError(); // <-- Does not exit because there is no error.

$apiReturn->addData({});
$apiReturn->addData([]);
$apiReturn->addData("Hello World!");

$apiReturn->APIExit(); // <-- Exits here
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

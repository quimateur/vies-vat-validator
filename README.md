vies-vat-validator
==================

Validates an Euopean VAT code against European Comission VIES Database

http://ec.europa.eu/taxation_customs/vies/faqvies.do#item_16

It's inspired by: http://isvat.appspot.com/

Installation
------------

Copy all php files to the same directory of your web server. You can delete vat.php
if you do not plan to allow access to the validator directly.

Examples
-------------

Call http://yourdomain.com/vat.php?country=ES&number=B63920920

Parameters are passed via GET and both are mandatory:

- country: A valid European ISO country code.
- number: Vat number to request.

You can also instantiate a VatValidator object to use it in your app: 

$validator = new VatValidator();
$validator->check('ES','1111111');

You'll need to require_once('VatValidator.php') or use an autoloader.

Response
-------

The output is a json object like:

{
	is_valid: true/false
}


Error logging
--------------

There is a minimal error reporting. Log messages are reported like:

{
	error_message: "XXXXX"
}

The errors logged are:

- Invalid country
- No vat / country specified.
- VAT Validation service not available
vies-vat-validator
==================

Validates a European VAT number against the European Commission VIES service

https://ec.europa.eu/taxation_customs/vies/

It's inspired by: http://isvat.appspot.com/

Installation
------------

Copy all php files to the same directory of your web server. You can delete vat.php
if you do not plan to allow access to the validator directly.

Examples
-------------

Call http://yourdomain.com/vat.php?country=IE&number=6388047V

Parameters are passed via GET and both are mandatory:

- country: A country code as VIES expects it (note: Greece is `EL`, Northern Ireland is `XI`).
- number: Vat number to request.

You can also instantiate a VatValidator object to use it in your app: 

$validator = new VatValidator();
$validator->checkVat('IE', '6388047V');

You'll need to require_once('VatValidator.php') or use an autoloader.

Response
-------

The output is a json object like:

{
	is_valid: true/false
}

When the number is valid and the country shares it, VIES also returns the company
name and address, and they are added to the response:

{
	is_valid: true,
	name: "GOOGLE IRELAND LIMITED",
	address: "3RD FLOOR, GORDON HOUSE, BARROW STREET\nDUBLIN 4"
}

Some countries (Spain, for example) don't share this data, so `name` and `address`
are only present when available. The address comes as a single text, as VIES gives it.


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
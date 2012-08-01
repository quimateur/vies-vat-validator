vies-vat-validator
==================

Validates an Euopean VAT code against European Comission VIES Database

http://ec.europa.eu/taxation_customs/vies/faqvies.do#item_16

It's inspired by: http://isvat.appspot.com/

Examples
-------------

http://wc.dev/vat.php?country=ES&number=B63920920

Parameters are passed via GET and both are mandatory:

country: A valid European ISO country code.

number: Vat number to request.

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
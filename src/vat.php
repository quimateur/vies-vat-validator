<?php

use Quimateur\ViesVatValidator\VatValidator;

require_once "VatValidator.php";

// API Call
$vies = new VatValidator();
$vies->checkVat(strtoupper($_GET['country']), $_GET['number']);
$response = json_encode($vies->response());
echo $response;

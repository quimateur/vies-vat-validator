<?php

use Quimateur\ViesVatValidator\VatValidator;

require_once "VatValidator.php";

// API Call
$vies = new VatValidator();
$country = isset($_GET['country']) ? strtoupper(trim($_GET['country'])) : '';
$number = isset($_GET['number']) ? trim($_GET['number']) : '';
$vies->checkVat($country, $number);
header('Content-Type: application/json');
echo json_encode($vies->response());

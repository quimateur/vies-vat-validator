<?php

namespace Quimateur\ViesVatValidator;

// The purpose of this interface is to make the class robust to unavailability of VIES service
interface SoapClientFactoryInterface
{
    public function getSoapClient($WSDL_url);
}
<?php

namespace Quimateur\ViesVatValidator;

require_once "SoapClientFactoryInterface.php";

class SoapClientFactory implements SoapClientFactoryInterface
{
    public function getSoapClient($WSDL_url)
    {
        return new \SoapClient($WSDL_url);
    }
}
<?php

namespace Quimateur\ViesVatValidator;

class SoapClientFactory implements SoapClientFactoryInterface
{
    public function getSoapClient($WSDL_url)
    {
        return new \SoapClient($WSDL_url);
    }
}
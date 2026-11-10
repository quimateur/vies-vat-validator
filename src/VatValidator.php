<?php

namespace Quimateur\ViesVatValidator;

require_once "SoapClientFactory.php";
require_once "SoapClientFactoryInterface.php";

/*
 * LICENSE
 *
 * 2012 Quim Blanch / 2016 Alvaro Maceda
 * threep@gmail.com / alvaro@alvaromaceda.es
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *	 http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
class VatValidator
{
    private $response;
    protected $soap;
    protected $SOAPFactory;

    public static $VIES_Service_WSDL_URL = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
    protected $WSDL_URL;

    // Country codes as VIES expects them: Greece is EL, not GR, and XI is Northern Ireland
    protected static $valid_european_country_ISO_codes = array(
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK',
        'EE', 'EL', 'ES', 'FI', 'FR', 'HR', 'HU',
        'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL',
        'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'XI'
    );

    protected function getSoapClient()
    {
        if ($this->soap) {
            return $this->soap;
        }

        try {
            $this->soap = $this->SOAPFactory->getSoapClient(self::$VIES_Service_WSDL_URL);
        } catch (\Exception $e) {
            return null;
        }
        return $this->soap;
    }

    // We need to use a factory for SOAP objects to allow testing
    public function __construct(?SoapClientFactoryInterface $SOAPFactory = null)
    {
        $this->SOAPFactory = $SOAPFactory ?: new SoapClientFactory();
    }

    public function checkVat($country, $number)
    {
        $error = $this->checkParameters($country, $number);
        if ($error!="") {
            return $this->generateErrorResponse($error);
        }

        return $this->launchSOAPRequest($country, $number);
    }

    public function response()
    {
        return $this->response;
    }

    private function checkParameters($country, $number)
    {
        $OK = "";

        if (empty($country) || empty($number)) {
            return "Both 'country' and 'number' params are mandatory";
        }

        if (!in_array($country, self::$valid_european_country_ISO_codes)) {
            return "Invalid country";
        }

        return $OK;
    }

    protected function launchSOAPRequest($country, $number)
    {
        $soap = $this->getSoapClient();
        if ($soap==null) {
            return $this->generateErrorResponse('VAT Validation service not available');
        }

        $vat = array(
            'vatNumber' => $number,
            'countryCode' => $country,
        );

        try {
            $result = $soap->checkVat($vat);
        } catch (\Exception $e) {
            return $this->generateErrorResponse($e->getMessage());
        }

        $this->response = array('is_valid' => (bool) $result->valid);

        // Some countries (e.g. Spain) don't share company data: VIES answers '---'
        if ($this->response['is_valid']) {
            foreach (array('name', 'address') as $field) {
                $value = isset($result->$field) ? trim($result->$field) : '';
                if ($value !== '' && $value !== '---') {
                    $this->response[$field] = $value;
                }
            }
        }

        return $this->response;
    }

    protected function generateErrorResponse($error)
    {
        $this->response = array('error_message' => $error);
        return $this->response;
    }
}

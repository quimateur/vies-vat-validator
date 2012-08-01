<?php
/**
 * LICENSE
 *
 * 2012 Quim Blanch
 * threep@gmail.com
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
	public $response;
	protected $soap;
	
	// WSDL VIES Url Service
	protected static $url_vies = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
	
	// Valid european coutries code.
	protected static $european_countries = array(
		'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'
	);
	
	public function __construct ()
	{
		$this->soap = new SoapClient( self::$url_vies );
	}

	/**
	 * Check if it's a valid vat number.
	 */
	public function checkVat ( $country, $number )
	{
		$response = array( 'is_valid' => false );
		$vat = $this->prepareVat( $country, $number );
		if ($vat)
		{
			$this->response = array( 'is_valid' =>$this->soap->checkVat( $vat )->valid );
		}
		return json_encode( $this->response );
	}

	/**
	* Checks that there are all needed params ( Code Country and number );
	*/
	protected function prepareVat( $country, $number )
	{
	try
		{
			if ( empty( $country ) || empty( $number ) )
			{
				throw new Exception( "Both 'country' and 'number' params are mandatory" );
			}
			
			if ( !in_array( $country, self::$european_countries ) )
			{
				throw new Exception( "Invalid country" );
			}
			
			$vat = array(
				'vatNumber'		=> $number,
				'countryCode'	=> $country,
				
			);
			return $vat;
		}

		catch( Exception $e )
		{
			$this->response = array( 'error_message' => $e->getMessage() );
			return false;
		}
	}
}

// API Call
$vies = new VatValidator();
$vies->checkVat( strtoupper( $_GET['country'] ), $_GET['number']);
$response = json_encode( $vies->response);
echo $response;
?>
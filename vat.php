<?php
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
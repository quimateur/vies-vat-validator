<?php
namespace Quimateur\ViesVatValidator\Tests;

use \Quimateur\ViesVatValidator\VatValidator;
use \Quimateur\ViesVatValidator\SoapClientFactoryInterface;
use SoapFault;

class FailingSoapFactory implements SoapClientFactoryInterface
{

    public function getSoapClient($url)
    {
        throw new \Exception('Unavailable');
    }
}

class MockSoapFactory implements SoapClientFactoryInterface
{

    private $mock;

    public function __construct($mock)
    {
        $this->mock = $mock;
    }

    public function getSoapClient($url)
    {
        return $this->mock;
    }
}

class MockResponse
{

    public $valid;

    public function __construct($response)
    {
        $this->valid = $response;
    }
}

class VatValidatorTest extends \PHPUnit_Framework_TestCase
{
    private function getSoapClientMock()
    {
        return $this->getMockBuilder('StdClass')
            ->setMethods(['checkVat'])
            ->getMock();
    }

    public function test_It_Returns_Error_When_Country_Or_VAT_Not_Informed()
    {
        $expectedError = "Both 'country' and 'number' params are mandatory";
        $validator = new VatValidator(new MockSoapFactory(null));

        $response = $validator->checkVat('DE', '');
        $this->assertEquals($response['error_message'], $expectedError);

        $response = $validator->checkVat('', 'NOT MATTERS');
        $this->assertEquals($response['error_message'], $expectedError);

        $response = $validator->checkVat('', '');
        $this->assertEquals($response['error_message'], $expectedError);
    }

    public function test_It_Returns_Valid_When_SOAP_Returns_Valid()
    {
        $SoapClientMock = $this->getSoapClientMock();

        $vat = array(
            'vatNumber' => 'A_VAT_NUMBER',
            'countryCode' => 'ES',
        );

        $SoapClientMock->expects($this->once())
            ->method('checkVat')
            ->with($vat)
            ->willReturn(new MockResponse(true));

        $validator = new VatValidator(new MockSoapFactory($SoapClientMock));
        $response = $validator->checkVat('ES', 'A_VAT_NUMBER');
        $this->assertTrue($response['is_valid']);
    }

    public function test_It_Returns_Not_Valid_When_SOAP_Returns_Not_Valid()
    {
        $SoapClientMock = $this->getSoapClientMock();

        $vat = array(
            'vatNumber' => 'A_VAT_NUMBER',
            'countryCode' => 'ES',
        );

        $SoapClientMock->expects($this->once())
            ->method('checkVat')
            ->with($vat)
            ->willReturn(new MockResponse(false));

        $validator = new VatValidator(new MockSoapFactory($SoapClientMock));
        $response = $validator->checkVat('ES', 'A_VAT_NUMBER');
        $this->assertFalse($response['is_valid']);
    }

    public function test_It_Works_When_Service_Unavailable()
    {
        $validator = new VatValidator(new FailingSoapFactory());
        $response = $validator->checkVat('DE', 'NOT MATTERS');
        $this->assertEquals($response['error_message'], 'VAT Validation service not available');
    }

    public function test_It_Works_When_Call_To_Service_Fails()
    {
        $errorMsg = 'Terrible Error In Service';
        $SoapClientMock = $this->getSoapClientMock();
        $SoapClientMock->method('checkVat')->willThrowException(new \Exception($errorMsg));
        $validator = new VatValidator(new MockSoapFactory($SoapClientMock));

        $response = $validator->checkVat('DE', 'NOT MATTERS');
        $this->assertEquals($response['error_message'], $errorMsg);
    }

}

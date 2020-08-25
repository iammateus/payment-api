<?php

use Faker\Factory as Faker;
use App\Services\PaymentService;

class ParseDefaultPaymentParamsTest extends TestCase 
{
	private PaymentService $paymentService;

	public function setUp(): void
	{
        parent::setUp();
        $this->paymentService = app(PaymentService::class);
	}

	/**
	 * Fake a valid options array to be parsed
	 */ 
	public function fakeOptions()
	{
		$faker = Faker::create('pt_BR');

		return [
			'sender' => [
				'name' => $faker->name(),
				'document' => [
					'value' => $faker->cpf(false)
				],
				'phone'=> [
					'areaCode' => $faker->areaCode(),
					'number' => $faker->phoneNumber()
				],
				'email' => $faker->email,
				'hash' => $faker->word(),
			],
			'shipping' => [
				'addressRequired' => false
			],
			'extraAmount' => $faker->randomFloat()
		];
	}

	public function testParseDefaultPaymentParamsTest()
	{
		$options = $this->fakeOptions();
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsArray($parsed);
	}

	public function testParseDefaultPaymentParamsSendingInvalidArgumentTest()
	{
		$this->expectException(TypeError::class);
		$invalidParam = 'A invalid param';
		$this->paymentService->parseDefaultPaymentParams($invalidParam);
	}

	public function testParseDefaultPaymentParamsParsingPaymentMode()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertNotNull($parsed['paymentMode']);
		$this->assertEquals('default', $parsed['paymentMode']);
	}
	
	public function testParseDefaultPaymentParamsParsingCurrency()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertNotNull($parsed['currency']);
		$this->assertEquals('BRL', $parsed['currency']);
	}
	
	public function testParseDefaultPaymentParamsParsingNotificationUrl()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertNotNull($parsed['notificationURL']);
		$this->assertEquals(env("PAGSEGURO_NOTIFICATION_URL"), $parsed['notificationURL']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderName()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertNotNull($parsed['senderName']);
		$this->assertEquals($options['sender']['name'], $parsed['senderName']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderCpf()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertNotNull($parsed['senderCPF']);
		$this->assertEquals($options['sender']['document']['value'], $parsed['senderCPF']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderAreaCode()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertNotNull($parsed['senderAreaCode']);
		$this->assertEquals($options['sender']['phone']['areaCode'], $parsed['senderAreaCode']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderPhone()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertNotNull($parsed['senderPhone']);
		$this->assertEquals($options['sender']['phone']['number'], $parsed['senderPhone']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderEmail()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertNotNull($parsed['senderEmail']);
		$this->assertEquals($options['sender']['email'], $parsed['senderEmail']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderHash()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertNotNull($parsed['senderHash']);
		$this->assertEquals($options['sender']['hash'], $parsed['senderHash']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderShippingAddressRequired()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertNotNull($parsed['shippingAddressRequired']);

		$addressRequired = $options['shipping']['addressRequired'] ? 'true' : 'false';
		$this->assertEquals($addressRequired, $parsed['shippingAddressRequired']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderExtraAmount()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		
		$this->assertNotNull($parsed['extraAmount']);

		$intendedExtraAmount = number_format($options['extraAmount'], 2, '.', '');
		$this->assertEquals($intendedExtraAmount, $parsed['extraAmount']);
	}
	
	public function testParseDefaultPaymentParamsParsingShippingAddressStreet()
	{
		$faker = Faker::create('pt_BR');

		$options = $this->fakeOptions();
		$options['shipping']['street'] = $faker->streetName;

		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		
		$this->assertNotNull($parsed['shippingAddressStreet']);
		$this->assertEquals($options['shipping']['street'], $parsed['shippingAddressStreet']);
	}
	
	public function testParseDefaultPaymentParamsParsingShippingAddressStreetNotSendindShippingAddressStreetExpectingNull()
	{
		$options = $this->fakeOptions();

		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		
		$this->assertArrayNotHasKey('shippingAddressStreet', $parsed);
	}
	
	public function testParseDefaultPaymentParamsParsingShippingAddressNumber()
	{
		$faker = Faker::create('pt_BR');

		$options = $this->fakeOptions();
		$options['shipping']['number'] = $faker->text(20);

		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		
		$this->assertNotNull($parsed['shippingAddressNumber']);
		$this->assertEquals($options['shipping']['number'], $parsed['shippingAddressNumber']);
	}
	
	public function testParseDefaultPaymentParamsParsingShippingAddressNumberNotSendindShippingAddressNumberExpectingNull()
	{
		$options = $this->fakeOptions();

		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		
		$this->assertArrayNotHasKey('shippingAddressNumber', $parsed);
	}
	
	public function testParseDefaultPaymentParamsParsingShippingAddressDistrict()
	{
		$faker = Faker::create('pt_BR');

		$options = $this->fakeOptions();
		$options['shipping']['district'] = $faker->text(20);

		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		
		$this->assertNotNull($parsed['shippingAddressDistrict']);
		$this->assertEquals($options['shipping']['district'], $parsed['shippingAddressDistrict']);
	}
	
	public function testParseDefaultPaymentParamsParsingShippingAddressDistrictNotSendindShippingDistrictExpectingNull()
	{
		$options = $this->fakeOptions();

		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		
		$this->assertArrayNotHasKey('shippingAddressDistrict', $parsed);
	}
}
<?php

use Faker\Factory as Faker;
use App\Http\Controllers\PaymentService;

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
			'mode' => 'default',
			'currency' => 'BRL',
			'notificationURL' => env("PAGSEGURO_NOTIFICATION_URL"),
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
			]
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
		$this->assertIsString($parsed['paymentMode']);
		$this->assertEquals($options['mode'], $parsed['paymentMode']);
	}
	
	public function testParseDefaultPaymentParamsParsingCurrency()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsString($parsed['currency']);
		$this->assertEquals($options['currency'], $parsed['currency']);
	}
	
	public function testParseDefaultPaymentParamsParsingNotificationUrl()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsString($parsed['notificationURL']);
		$this->assertEquals($options['notificationURL'], $parsed['notificationURL']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderName()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsString($parsed['senderName']);
		$this->assertEquals($options['sender']['name'], $parsed['senderName']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderCpf()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsString($parsed['senderCPF']);
		$this->assertEquals($options['sender']['document']['value'], $parsed['senderCPF']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderAreaCode()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsString($parsed['senderAreaCode']);
		$this->assertEquals($options['sender']['phone']['areaCode'], $parsed['senderAreaCode']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderPhone()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsString($parsed['senderPhone']);
		$this->assertEquals($options['sender']['phone']['number'], $parsed['senderPhone']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderEmail()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsString($parsed['senderEmail']);
		$this->assertEquals($options['sender']['email'], $parsed['senderEmail']);
	}
	
	public function testParseDefaultPaymentParamsParsingSenderHash()
	{
		$options = $this->fakeOptions();
		$this->paymentService->parseDefaultPaymentParams($options);
		$parsed = $this->paymentService->parseDefaultPaymentParams($options);
		$this->assertIsString($parsed['senderHash']);
		$this->assertEquals($options['sender']['hash'], $parsed['senderHash']);
	}
}
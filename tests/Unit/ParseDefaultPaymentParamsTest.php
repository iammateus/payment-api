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
                    'value' => $faker->cpf(false),
                    'type' => 'CPF'
                ],
                'phone' => [
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

    public function testParseDefaultPaymentParamsParsingSenderCpfWhenSendingDocumentOfTypeCpf()
    {
        $options = $this->fakeOptions();
        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);
        $this->assertNotNull($parsed['senderCPF']);
        $this->assertEquals($options['sender']['document']['value'], $parsed['senderCPF']);
    }

    public function testParseDefaultPaymentParamsParsingSenderCnpjWhenSendingDocumentOfTypeCnpj()
    {
        $faker = Faker::create('pt_BR');
        $options = $this->fakeOptions();
        $options['sender']['document'] = [
            'type' => 'CNPJ',
            'value' => $faker->cnpj(false)
        ];
        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);
        $this->assertNotNull($parsed['senderCNPJ']);
        $this->assertEquals($options['sender']['document']['value'], $parsed['senderCNPJ']);
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

    public function testParseDefaultPaymentParamsParsingShippingAddressCity()
    {
        $faker = Faker::create('pt_BR');

        $options = $this->fakeOptions();
        $options['shipping']['city'] = $faker->city;

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertNotNull($parsed['shippingAddressCity']);
        $this->assertEquals($options['shipping']['city'], $parsed['shippingAddressCity']);
    }

    public function testParseDefaultPaymentParamsParsingShippingAddressCityNotSendindShippingCityExpectingNull()
    {
        $options = $this->fakeOptions();

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertArrayNotHasKey('shippingAddressCity', $parsed);
    }

    public function testParseDefaultPaymentParamsParsingShippingAddressState()
    {
        $faker = Faker::create('pt_BR');

        $options = $this->fakeOptions();
        $options['shipping']['state'] = $faker->stateAbbr;

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertNotNull($parsed['shippingAddressState']);
        $this->assertEquals($options['shipping']['state'], $parsed['shippingAddressState']);
    }

    public function testParseDefaultPaymentParamsParsingShippingAddressStateNotSendindShippingStateExpectingNull()
    {
        $options = $this->fakeOptions();

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertArrayNotHasKey('shippingAddressState', $parsed);
    }

    public function testParseDefaultPaymentParamsParsingShippingAddressCountry()
    {
        $faker = Faker::create('pt_BR');

        $options = $this->fakeOptions();
        $options['shipping']['country'] = $faker->randomElement(['BRA']);

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertNotNull($parsed['shippingAddressCountry']);
        $this->assertEquals($options['shipping']['country'], $parsed['shippingAddressCountry']);
    }

    public function testParseDefaultPaymentParamsParsingShippingAddressCountryNotSendindShippingCountryExpectingNull()
    {
        $options = $this->fakeOptions();

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertArrayNotHasKey('shippingAddressCountry', $parsed);
    }

    public function testParseDefaultPaymentParamsParsingShippingAddressPostalCode()
    {
        $faker = Faker::create('pt_BR');

        $options = $this->fakeOptions();
        $options['shipping']['postalCode'] = $faker->numberBetween(10000000, 99999999);

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertNotNull($parsed['shippingAddressPostalCode']);
        $this->assertEquals($options['shipping']['postalCode'], $parsed['shippingAddressPostalCode']);
    }

    public function testParseDefaultPaymentParamsParsingShippingAddressPostalCodeNotSendindShippingPostalCodeExpectingNull()
    {
        $options = $this->fakeOptions();

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertArrayNotHasKey('shippingAddressPostalCode', $parsed);
    }

    public function testParseDefaultPaymentParamsParsingShippingCost()
    {
        $faker = Faker::create('pt_BR');

        $options = $this->fakeOptions();
        $options['shipping']['cost'] = $faker->randomFloat();

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertNotNull($parsed['shippingCost']);

        $intendedCost = number_format($options['shipping']['cost'], 2, '.', '');
        $this->assertEquals($intendedCost, $parsed['shippingCost']);
    }

    public function testParseDefaultPaymentParamsParsingShippingCostNotSendindShippingCostExpectingNull()
    {
        $options = $this->fakeOptions();

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertArrayNotHasKey('shippingCost', $parsed);
    }

    public function testParseDefaultPaymentParamsParsingShippingType()
    {
        $faker = Faker::create('pt_BR');

        $options = $this->fakeOptions();
        $options['shipping']['type'] = $faker->randomElement([1, 2, 3]);

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertNotNull($parsed['shippingType']);

        $this->assertEquals($options['shipping']['type'], $parsed['shippingType']);
    }

    public function testParseDefaultPaymentParamsParsingShippingTypeNotSendindShippingTypeExpectingNull()
    {
        $options = $this->fakeOptions();

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertArrayNotHasKey('shippingType', $parsed);
    }

    public function testParseDefaultPaymentParamsParsingReference()
    {
        $faker = Faker::create('pt_BR');

        $options = $this->fakeOptions();
        $options['reference'] = $faker->text(200);

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertNotNull($parsed['reference']);

        $this->assertEquals($options['reference'], $parsed['reference']);
    }

    public function testParseDefaultPaymentParamsParsingReferenceNotSendindReferenceExpectingNull()
    {
        $options = $this->fakeOptions();

        $this->paymentService->parseDefaultPaymentParams($options);
        $parsed = $this->paymentService->parseDefaultPaymentParams($options);

        $this->assertArrayNotHasKey('reference', $parsed);
    }
}

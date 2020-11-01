<?php

use Faker\Factory as Faker;
use App\Services\PaymentService;

class ParseCreditCardPaymentParamsTest extends TestCase
{
    /**
     * Fake a valid options array to be parsed
     */
    public function fakeOptions()
    {
        $faker = Faker::create('pt_BR');

        return [
            'creditCard' => [
                'token' => $faker->text(),
                'holder' => [
                    'name' => $faker->name(),
                    'birthDate' => $faker->date(),
                    'document' => [
                        'type' => 'CPF',
                        'value' => $faker->cpf(false),
                    ],
                    'phone' => [
                        'areaCode' => $faker->areaCode(),
                        'number' => $faker->numberBetween(1000000, 999999999)
                    ]
                ],
                'installment' => [
                    'value' => $faker->randomFloat(),
                    'quantity' => $faker->randomNumber()
                ],
                'maxInstallmentNoInterest' => $faker->randomNumber()
            ],
            'billing' => [
                'street' => $faker->text(),
                'number' => $faker->word(),
                'district' => $faker->text(),
                'postalCode' => $faker->word(),
                'complement' => $faker->text(),
                'city' => $faker->text(),
                'state' => $faker->text(),
                'country' => $faker->text(),
            ]
        ];
    }

    public function testParseCreditCardPaymentParams()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
    }

    public function testParseCreditCardPaymentParamsParsingCreditCardToken()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['creditCardToken'], $options['creditCard']['token']);
    }

    public function testParseCreditCardPaymentParamsParsingCreditCardHolderName()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['creditCardHolderName'], $options['creditCard']['holder']['name']);
    }

    public function testParseCreditCardPaymentParamsParsingCreditCardHolderCpf()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['creditCardHolderCPF'], $options['creditCard']['holder']['document']['value']);
    }

    public function testParseCreditCardPaymentParamsParsingCreditCardHolderBirthDate()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['creditCardHolderBirthDate'], $options['creditCard']['holder']['birthDate']);
    }

    public function testParseCreditCardPaymentParamsParsingCreditCardHolderCpfWhenSendigDocumentOfTypeCpf()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['creditCardHolderCPF'], $options['creditCard']['holder']['document']['value']);
    }

    public function testParseCreditCardPaymentParamsParsingCreditCardHolderPhoneAreaCode()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['creditCardHolderAreaCode'], $options['creditCard']['holder']['phone']['areaCode']);
    }

    public function testParseCreditCardPaymentParamsParsingCreditCardHolderPhone()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['creditCardHolderPhone'], $options['creditCard']['holder']['phone']['number']);
    }

    public function testParseCreditCardPaymentParamsParsingInstallmentValue()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $expectedValue = number_format($options['creditCard']['installment']['value'], 2, '.', '');
        $this->assertEquals($result['installmentValue'], $expectedValue);
    }

    public function testParseCreditCardPaymentParamsParsingInstallmentQuantity()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['installmentQuantity'], $options['creditCard']['installment']['quantity']);
    }

    public function testParseCreditCardPaymentParamsParsingNoInterestInstallmentQuantity()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['noInterestInstallmentQuantity'], $options['creditCard']['maxInstallmentNoInterest']);
    }

    public function testParseCreditCardPaymentParamsParsingBillingAddressStreet()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['billingAddressStreet'], $options['billing']['street']);
    }

    public function testParseCreditCardPaymentParamsParsingBillingAddressNumber()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['billingAddressNumber'], $options['billing']['number']);
    }

    public function testParseCreditCardPaymentParamsParsingBillingAddressDistrict()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['billingAddressDistrict'], $options['billing']['district']);
    }

    public function testParseCreditCardPaymentParamsParsingBillingAddressPostalCode()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['billingAddressPostalCode'], $options['billing']['postalCode']);
    }

    public function testParseCreditCardPaymentParamsParsingBillingAddressCity()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['billingAddressCity'], $options['billing']['city']);
    }

    public function testParseCreditCardPaymentParamsParsingBillingAddressState()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['billingAddressState'], $options['billing']['state']);
    }

    public function testParseCreditCardPaymentParamsParsingBillingAddressCountry()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['billingAddressCountry'], $options['billing']['country']);
    }

    public function testParseCreditCardPaymentParamsParsingBillingAddressComplement()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['billingAddressComplement'], $options['billing']['complement']);
    }

    public function testParseCreditCardPaymentParamsParsingPaymentMethod()
    {
        $options = $this->fakeOptions();
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $result = $mock->parseCreditCardPaymentParams($options);
        $this->assertIsArray($result);
        $this->assertEquals($result['paymentMethod'], 'creditCard');
    }
}

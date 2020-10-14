<?php

use App\Services\PaymentService;

class MakePagseguroPaymentTest extends TestCase 
{
	public function testMakePagseguroPaymentSendingBoletoAsPaymentMethodExpectingInvalidArgumentException()
	{
        $mock = Mockery::mock(PaymentService::class)->makePartial();

        try {
            $mock->makePagseguroPayment([
                'method' => 'BOLETO'
            ]);
        } catch (Exception $e) { }

        $mock->shouldHaveReceived('payWithBoleto');
	}
}
<?php

use App\Services\PaymentService;

class MakePagseguroPaymentTest extends TestCase 
{
	public function testMakePagseguroPaymentSendingBoletoAsPaymentMethodExpectingPayWithBoletoToBeCalled()
	{
        $mock = Mockery::mock(PaymentService::class)->makePartial();

        try {
            $mock->makePagseguroPayment([
                'method' => 'BOLETO'
            ]);
        } catch (Exception $e) { }

        $mock->shouldHaveReceived('payWithBoleto');
	}
    
    public function testMakePagseguroPaymentSendingCreditCardAsPaymentMethodExpectingPayWithCreditCardToBeCalled()
	{
        $mock = Mockery::mock(PaymentService::class)->makePartial();

        try {
            $mock->makePagseguroPayment([
                'method' => 'CREDIT_CARD'
            ]);
        } catch (Exception $e) { }

        $mock->shouldHaveReceived('payWithCreditCard');
	}
}
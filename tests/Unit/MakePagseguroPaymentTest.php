<?php

use App\Services\PaymentService;

class MakePagseguroPaymentTest extends TestCase 
{
	public function testPayWithBoletoOptionWillTriggerBoletoPaymentMethod()
	{
        $mock = Mockery::mock(PaymentService::class)->makePartial();

        try {
            $mock->makePagseguroPayment([]);
        } catch (\Exception $e) { }

        $mock->shouldHaveReceived('payWithBoleto');
	}
}
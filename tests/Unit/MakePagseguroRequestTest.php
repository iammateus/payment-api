<?php

use GuzzleHttp\Client;
use App\Services\PaymentService;

class MakePagseguroRequestTest extends TestCase
{
    public function testMakePagseguroRequest()
    {
        $mockedClient = Mockery::mock(Client::class)->makePartial();
        $mockedClient->shouldReceive('request')->andReturn(new stdClass);
        $this->app->instance('GuzzleHttp\Client', $mockedClient);

        $data = [];
        $paymentService = app(PaymentService::class);
        $result = $paymentService->makePagseguroRequest($data);
        $this->assertIsObject($result);

        $link = env('PAGSEGURO_URL') . '/transactions';
        $mockedClient->shouldHaveReceived('request', ['POST', $link, [
            'form_params' => $data,
            'query' => [
                'email' => env('PAGSEGURO_EMAIL'),
                'token' => env('PAGSEGURO_TOKEN'),
            ]
        ]]);
    }

    public function testMakePagseguroRequestWithoutSendingArrayParamExpectinTypeError()
    {
        $this->expectException(TypeError::class);
        $mock = Mockery::mock(PaymentService::class)->makePartial();
        $mock->makePagseguroRequest();
    }
}

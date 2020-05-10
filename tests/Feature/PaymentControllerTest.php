<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PaymentControllerTest extends TestCase
{
    public function testPayExpectingSuccess()
    {
        $this->post('/payment');
        $this->assertResponseOk();
    }
}
<?php

class EnvironmentTest extends TestCase
{
    public function testPagseguroEmailEnvironmentVariableIsSet()
    {
        $this->assertNotEmpty(env('PAGSEGURO_EMAIL'));
    }
    
    public function testPagseguroTokenEnvironmentVariableIsSet()
    {
        $this->assertNotEmpty(env('PAGSEGURO_TOKEN'));
    }
}
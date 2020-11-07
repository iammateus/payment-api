<?php

namespace App\Mocks;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Http\Response;
use GuzzleHttp\Psr7\Response as MockResponse;
use GuzzleHttp\Handler\MockHandler;

class PagseguroMocker
{
    public static function getMockedGuzzleInstanceWithPaymentWithBoletoResponse()
    {
        $xmlPagseguroResponse = '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>
                    <transaction>
                        <date>2020-07-26T12:42:56.000-03:00</date>
                        <code>02B62EF5-1824-4FCC-824A-9607348C6D08</code>
                        <type>1</type>
                        <status>1</status>
                        <lastEventDate>2020-07-26T12:42:59.000-03:00</lastEventDate>
                        <paymentMethod>
                            <type>2</type>
                            <code>202</code>
                        </paymentMethod>
                        <paymentLink>https://sandbox.pagseguro.uol.com.br/checkout/payment/booklet/print.jhtml?c=c0b4c231ab35affff4f36b511aa200813a220daec9db33d941d0005fd226f013f86940b74c2b5e55</paymentLink>
                        <grossAmount>114.00</grossAmount>
                        <discountAmount>0.00</discountAmount>
                        <feeAmount>6.09</feeAmount>
                        <netAmount>107.91</netAmount>
                        <extraAmount>10.00</extraAmount>
                        <installmentCount>1</installmentCount>
                        <itemCount>3</itemCount>
                        <items>
                            <item>
                                <id>1</id>
                                <description>Produto 1</description>
                                <quantity>2</quantity>
                                <amount>2.00</amount>
                            </item>
                            <item>
                                <id>2</id>
                                <description>Produto 2</description>
                                <quantity>1</quantity>
                                <amount>60.00</amount>
                            </item>
                            <item>
                                <id>3</id>
                                <description>Produto 3</description>
                                <quantity>2</quantity>
                                <amount>20.00</amount>
                            </item>
                        </items>
                        <sender>
                            <name>Mateus Soares</name>
                            <email>chagaswc89@sandbox.pagseguro.com.br</email>
                            <phone>
                                <areaCode>48</areaCode>
                                <number>991510980</number>
                            </phone>
                            <documents>
                                <document>
                                    <type>CPF</type>
                                    <value>71783955082</value>
                                </document>
                            </documents>
                        </sender>
                    </transaction>';

        $headers = [
            'Content-Type' => 'application/xml'
        ];

        $mock = new MockHandler([new MockResponse(Response::HTTP_OK, $headers, $xmlPagseguroResponse)]);
        $handlerStack = HandlerStack::create($mock);

        $client = new Client(['handler' => $handlerStack]);
        return $client;
    }

    public static function getMockedGuzzleInstanceWithPaymentWithCreditCardResponse()
    {
        $xmlPagseguroResponse = "<?xml version='1.0' encoding='ISO-8859-1' standalone='yes'?>
                                    <transaction>
                                        <date>2020-11-02T14:06:13.000-03:00</date>
                                        <code>5960A35C-2EBF-4D54-962E-3AF067D36E17</code>
                                        <reference>Teste Pagseguro React</reference>
                                        <type>1</type>
                                        <status>1</status>
                                        <lastEventDate>2020-11-02T14:06:13.000-03:00</lastEventDate>
                                        <paymentMethod>
                                            <type>1</type>
                                            <code>101</code>
                                        </paymentMethod>
                                        <grossAmount>114.00</grossAmount>
                                        <discountAmount>0.00</discountAmount>
                                        <feeAmount>6.09</feeAmount>
                                        <netAmount>107.91</netAmount>
                                        <extraAmount>10.00</extraAmount>
                                        <installmentCount>1</installmentCount>
                                        <itemCount>3</itemCount>
                                        <items>
                                            <item>
                                                <id>1</id>
                                                <description>Produto 1</description>
                                                <quantity>2</quantity>
                                                <amount>2.00</amount>
                                            </item>
                                            <item>
                                                <id>2</id>
                                                <description>Produto 2</description>
                                                <quantity>1</quantity>
                                                <amount>60.00</amount>
                                            </item>
                                            <item>
                                                <id>3</id>
                                                <description>Produto 3</description>
                                                <quantity>2</quantity>
                                                <amount>20.00</amount>
                                            </item>
                                        </items>
                                        <sender>
                                            <name>Mateus Soares</name>
                                            <email>chagaswc89@sandbox.pagseguro.com.br</email>
                                            <phone>
                                                <areaCode>48</areaCode>
                                                <number>991510980</number>
                                            </phone>
                                            <documents>
                                                <document>
                                                <type>CPF</type>
                                                <value>71783955082</value>
                                                </document>
                                            </documents>
                                        </sender>
                                        <shipping>
                                            <address>
                                                <street>Largo Eduardo Zamana</street>
                                                <number>Et facilis ut.</number>
                                                <complement></complement>
                                                <district>Eum impedit qui reiciendis est officia ex.</district>
                                                <city>Porto Micaela do Leste</city>
                                                <state>DF</state>
                                                <country>BRA</country>
                                                <postalCode>42737128</postalCode>
                                            </address>
                                            <type>3</type>
                                            <cost>0.00</cost>
                                        </shipping>
                                        <gatewaySystem>
                                            <type>cielo</type>
                                            <rawCode xsi:nil='true' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'/><rawMessage xsi:nil='true' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'/><normalizedCode xsi:nil='true' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'/>
                                            <normalizedMessage xsi:nil='true' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'/>
                                            <authorizationCode>0</authorizationCode>
                                            <nsu>0</nsu>
                                            <tid>0</tid>
                                            <establishmentCode>1056784170</establishmentCode>
                                            <acquirerName>CIELO</acquirerName>
                                        </gatewaySystem>
                                    </transaction>";

        $headers = [
            'Content-Type' => 'application/xml'
        ];

        $mock = new MockHandler([new MockResponse(Response::HTTP_OK, $headers, $xmlPagseguroResponse)]);
        $handlerStack = HandlerStack::create($mock);

        $client = new Client(['handler' => $handlerStack]);
        return $client;
    }

    public static function getMockedGuzzleInstanceWithPaymentWithOnlineDebitResponse()
    {
        $xmlPagseguroResponse = "<?xml version='1.0' encoding='UTF-8'?>
                                    <transaction>
                                    <date>2020-11-05T19:59:14.000-03:00</date>
                                    <code>0B7E653B-80A9-40BB-BBA9-3AD8EC0E0118</code>
                                    <reference>Teste Pagseguro React</reference>
                                    <recoveryCode>7df5a68dd88c50bbbfe3c08f8cf9dc9993093c62495f251b</recoveryCode>
                                    <type>1</type>
                                    <status>1</status>
                                    <lastEventDate>2020-11-05T19:59:15.000-03:00</lastEventDate>
                                    <paymentMethod>
                                        <type>3</type>
                                        <code>304</code>
                                    </paymentMethod>
                                    <paymentLink>https://sandbox.pagseguro.uol.com.br/checkout/payment/eft/print.jhtml?c=170ca80fa1369b8debfbb9d8c8871a321fc7725e512c7597815176a762f5d231cb365c8c8886292c</paymentLink>
                                    <grossAmount>114.00</grossAmount>
                                    <discountAmount>0.00</discountAmount>
                                    <feeAmount>6.09</feeAmount>
                                    <netAmount>107.91</netAmount>
                                    <extraAmount>10.00</extraAmount>
                                    <installmentCount>1</installmentCount>
                                    <itemCount>3</itemCount>
                                    <items>
                                        <item>
                                            <id>1</id>
                                            <description>Produto 1</description>
                                            <quantity>2</quantity>
                                            <amount>2.00</amount>
                                        </item>
                                        <item>
                                            <id>2</id>
                                            <description>Produto 2</description>
                                            <quantity>1</quantity>
                                            <amount>60.00</amount>
                                        </item>
                                        <item>
                                            <id>3</id>
                                            <description>Produto 3</description>
                                            <quantity>2</quantity>
                                            <amount>20.00</amount>
                                        </item>
                                    </items>
                                    <sender>
                                        <name>Mateus Soares</name>
                                        <email>chagaswc89@sandbox.pagseguro.com.br</email>
                                        <phone>
                                            <areaCode>48</areaCode>
                                            <number>991510980</number>
                                        </phone>
                                        <documents>
                                            <document>
                                                <type>CPF</type>
                                                <value>71783955082</value>
                                            </document>
                                        </documents>
                                    </sender>
                                    <shipping>
                                        <address>
                                            <street>Largo Eduardo Zamana</street>
                                            <number>Et facilis ut.</number>
                                            <complement />
                                            <district>Eum impedit qui reiciendis est officia ex.</district>
                                            <city>Porto Micaela do Leste</city>
                                            <state>DF</state>
                                            <country>BRA</country>
                                            <postalCode>42737128</postalCode>
                                        </address>
                                        <type>3</type>
                                        <cost>0.00</cost>
                                    </shipping>
                                    </transaction>";

        $headers = [
            'Content-Type' => 'application/xml'
        ];

        $mock = new MockHandler([new MockResponse(Response::HTTP_OK, $headers, $xmlPagseguroResponse)]);
        $handlerStack = HandlerStack::create($mock);

        $client = new Client(['handler' => $handlerStack]);
        return $client;
    }
}

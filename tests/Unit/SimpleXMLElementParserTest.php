<?php

use Faker\Factory as Faker;
use App\Helpers\SimpleXMLElementParser;

class SimpleXMLElementParserTest extends TestCase
{
    public function testParseToArray()
    {
        $faker = Faker::create();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                    <data>
                        <child1>' . $faker->text() . '</child1>
                        <child2>
                            <child3>' . $faker->text() . '</child3>
                            <child4>
                                <child5>' . $faker->text() . '</child5>
                            </child4>
                        </child2>
                    </data>';

        $simpleXMLElement =  new SimpleXMLElement( $xml );
        
        $parsed = SimpleXMLElementParser::parseToArray($simpleXMLElement);
        $this->assertIsArray($parsed);

        $child1 = $simpleXMLElement->child1;
        $child3 = $simpleXMLElement->child2->child3;
        $child5 = $simpleXMLElement->child2->child4->child5;
        $this->assertEquals($child1, $parsed['child1']);
        $this->assertEquals($child3, $parsed['child2']['child3']);
        $this->assertEquals($child5, $parsed['child2']['child4']['child5']);
    }

    public function testParseToArrayWithEmptyObject()
    {
        $faker = Faker::create();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                    <data>
                        <child></child>
                    </data>';

        $simpleXMLElement =  new SimpleXMLElement( $xml );

        $parsed = SimpleXMLElementParser::parseToArray($simpleXMLElement);
        
        $this->assertNull($parsed['child']);
    }
}
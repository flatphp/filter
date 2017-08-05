<?php
use Flatphp\Filter\Sanitizer;

class SanitizerTest extends PHPUnit_Framework_TestCase
{

    public function testSingle()
    {
        $value = Sanitizer::sanitize(' aa ', 'trim|upper');
        $this->assertEquals('AA', $value);
    }

    public function testMulti()
    {
        $data = array(
            'a' => ' aa ',
            'b' => '2bb'
        );
        $result = Sanitizer::sanitize($data, array(
            'a' => 'trim|upper',
            'b' => 'int'
        ));
        $this->assertEquals('AA', $result['a']);
        $this->assertEquals(2, $result['b']);
    }

}

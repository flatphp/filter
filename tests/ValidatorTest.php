<?php
use Flatphp\Filter\Validator;

class ValidatorTest extends PHPUnit_Framework_TestCase
{

    public function testSingle()
    {
        $res = Validator::validate('hello@163.com', 'length:10|email');
        $this->assertTrue($res);
    }

    public function testMulti()
    {
        $data = array(
            'aa' => 'hello',
            'bb' => 1
        );
        $rule = array(
            'aa' => 'required|string',
            'bb' => 'required|int|in:1,2,3'
        );
        $res = Validator::validate($data, $rule);
        $this->assertTrue($res);
    }

    public function testMultiFail()
    {
        $data = array(
            'aa' => 'hello',
            'bb' => 1
        );
        $rule = array(
            'aa' => 'required|string',
            'bb' => 'required|int|in:1,2,3',
            'cc' => 'required'
        );
        $messages = array(
            'aa.required' => 'aa is required',
            'cc.required' => 'cc is required'
        );
        $res = Validator::validate($data, $rule, $messages);
        $this->assertFalse($res);
        $this->assertEquals('cc', Validator::getKey());
        $this->assertEquals('cc.required', Validator::getMessageKey());
        $this->assertEquals('cc is required', Validator::getMessage());
    }

}

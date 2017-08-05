# filter
Validation and Sanitization etc.


# Installation
```
composer require "flatphp/filter"
```

# Validator
```
use Flatphp\Filter\Validator;

// simple use
$bool = Validator::isEmail('test@gmail.com');
$bool = Validator::isUrl('http://test.com');


// single value
Validator::validate('test', 'email|length:10,20', array(
    'email' => 'value is not a valid email'
))


// multiple value
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
if (!$res) {
    echo Validator::getMessage();
}

```

# Sanitizer
```
use Flatphp\Filter\Sanitizer;

// simple
$value = Sanitizer::stripChars('hello', 'e');

// single value
$value = Sanitizer::sanitize('test/', 'strtoupper|trim:/');

// multiple value
$data = ['aa' => 22.12, 'bb' => 34.1789, 'cc' => ' HELLO '];
$data = Sanitizer::sanitize($data, array(
    'aa' => 'intval',
    'bb' => 'float:2',
    'cc' => 'strtolower|trim|stripChars:e'
));
```

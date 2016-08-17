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
Validator::validate('test', 'required|email|length:10,20')

// single value with message
$message = '';
Validator::validate('test', [
    'required' => 'value is required',
    'email' => 'value must be a valid email'
    'length:10,20' => 'value length must in 10-20'
], $message)


// multiple value
$data = ['aa' => 'hello', 'bb' => 'world'];
Validator::validate($data, 'required | string');
Validator::validate($data, ['aa' => 'required | string', 'bb' => 'in:1,2,3']);

//multiple value with message
Validator::validate($data, array(
    'aa' => ['required' => 'aa is required', 'string' => 'aa must be a string'],
    'bb' => ['in:1,2,3' => 'bb must in 1,2,3']
), $message);

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

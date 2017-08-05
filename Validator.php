<?php namespace Flatphp\Filter;

class Validator
{
    protected static $key;
    protected static $msgkey;
    protected static $msg;

    /**
     * single value:
     * validate('test', 'required|email|length:10,20', array(
     *     'required' => 'value is required',
     *     'email' => 'value must be a valid email',
     *     'length' => 'value length must in 10-20'
     * ))
     *
     * multi values:
     * validate(['aa' => 'hello', 'bb' => 'world'], ['aa' => 'required | string', 'bb' => 'in:1,2,3'], array(
     *     'aa.required' => 'aa is required',
     *     'aa.string' => 'aa must be a string',
     *     'bb.in' => 'bb must in 1,2,3'
     * ))
     *
     * @param mixed $data
     * @param mixed $rule
     * @param array|null $messages
     * @return bool
     * @throws \Exception
     */
    public static function validate($data, $rules, $messages = null)
    {
        if (is_array($rules)) {
            foreach ($rules as $key => $rule) {
                $value = isset($data[$key]) ? $data[$key] : null;
                $res = self::validateOne($key, $value, $rule, $messages);
                if (!$res) {
                    return false;
                }
            }
        } else {
            return self::validateOne(null, $data, $rules, $messages);
        }
        return true;
    }

    protected static function validateOne($key, $value, $rule, $messages = null)
    {
        $rule = explode('|', $rule);
        foreach ($rule as $method) {
            if (strpos($method, ':')) {
                $method = explode(':', $method, 2);
                $param = $method[1];
                $method = trim($method[0]);
            } else {
                $param = null;
                $method = trim($method);
            }
            $self_method = 'is'. ucfirst($method);
            if (method_exists(__CLASS__, $self_method)) {
                $res = static::$self_method($value, $param);
            } elseif (function_exists($method)) {
                if (null === $param) {
                    $res = $method($value);
                } else {
                    $res = $method($value, $param);
                }
            } else {
                throw new \Exception('method '. $method .' not exists');
            }
            if (!$res) {
                self::$key = $key;
                self::$msgkey = ($key === null) ? $method : ($key .'.'. $method);
                if (isset($messages[self::$msgkey])) {
                    self::$msg = $messages[self::$msgkey];
                } else {
                    self::$msg = self::$msgkey;
                }
                return false;
            }
        }
        return true;
    }

    public static function getKey()
    {
        return self::$key;
    }

    public static function getMessageKey()
    {
        return self::$msgkey;
    }

    public static function getMessage()
    {
        return self::$msg;
    }


    /**
     * Not empty
     * @param mixed $value
     * @return bool
     */
    public static function isRequired($value)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif ((is_array($value) || $value instanceof \Countable) && count($value) < 1) {
            return false;
        }
        return true;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isDate($value)
    {
        if (strtotime($value) === false) {
            return false;
        }
        $date = date_parse($value);
        return checkdate($date['month'], $date['day'], $date['year']);
    }

    /**
     * @param string $value
     * @param string $format
     * @return bool
     */
    public static function isDatetime($value, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $value);
        return $d && $d->format($format) == $value;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isUrl($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * @param string $value
     * @param string $pattern
     * @return bool
     */
    public static function isMatch($value, $pattern)
    {
        return (bool)preg_match($pattern, $value);
    }

    /**
     * @param string $value
     * @param string|array $param
     * @return bool
     */
    public static function isLength($value, $param)
    {
        if (!is_array($param)) {
            $param = explode(',', $param);
        }
        $min = $param[0];
        $max = null;
        if (isset($param[1])) {
            $max = $param[1];
        }
        $len = mb_strlen($value, 'UTF-8');
        return ($len >= $min && (null === $max || $len <= $max));
    }

    /**
     * @param mixed $value
     * @param string|array $param
     * @return bool
     */
    public static function isRange($value, $param)
    {
        if (!is_array($param)) {
            $param = explode(',', $param);
        }
        $min = $param[0];
        $max = null;
        if (isset($param[1])) {
            $max = $param[1];
        }
        return ($value >= $min && (null === $max || $value <= $max));
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isBool($value)
    {
        return in_array($value, [true, false, 0, 1, '0', '1'], true);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isString($value)
    {
        return is_string($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isInt($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isNumeric($value)
    {
        return is_numeric($value);
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isArray($value)
    {
        return is_array($value);
    }

    /**
     * @param mixed $value
     * @param mixed $param
     * @return bool
     */
    public static function isEqual($value, $param)
    {
        return $value == $param;
    }

    /**
     * @param mixed $value
     * @param mixed $param
     * @return bool
     */
    public static function isSame($value, $param)
    {
        return $value === $param;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isIp($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * If is a json
     * @param $value
     * @return bool
     */
    public static function isJson($value)
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * @param mixed $value
     * @param array|string $param
     * @return bool
     */
    public static function isIn($value, $param)
    {
        if (!is_array($param)) {
            $param = explode(',', $param);
        }
        return in_array($value, $param);
    }

    /**
     * @param mixed $value
     * @param array|string $param
     * @return bool
     */
    public static function isNotin($value, $param)
    {
        return !static::isIn($value, $param);
    }
}
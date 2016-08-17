<?php namespace Flatphp\Filter;

class Sanitizer
{
    protected static $_funcs = array(
        'p1' => ['intval', 'strtolower', 'strtoupper', 'addslashes'],
        'p2' => ['trim', 'ltrim', 'rtrim', 'htmlspecialchars', 'strip_tags']
    );

    /**
     * single value:
     * sanitize('test', 'upper|trim:/')
     *
     * multi values:
     * sanitize(['aa' => 22.12, 'bb' => 34.1789], ['aa' => 'int', 'bb' => 'float:3'])
     *
     * @param mixed $value
     * @param mixed $rule
     * @return bool
     */
    public static function sanitize($value, $rule)
    {
        if (is_array($rule)) {
            foreach ($rule as $k=>$r) {
                if (isset($value[$k])) {
                    $value[$k] = self::_sanitizeOne($value[$k], $r);
                }
            }
        } else {
            $value = self::_sanitizeOne($value, $rule);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @param string $rule
     * @return mixed
     */
    protected static function _sanitizeOne($value, $rule)
    {
        // get all sanitizer methods
        static $methods = null;
        if (null === $methods) {
            $methods = get_class_methods(get_called_class());
        }
        $rule = explode('|', $rule);
        foreach ($rule as $method) {
            if (strpos($method, ':')) {
                $p = strpos($method, ':');
                $param = substr($method, $p + 1);
                $method = trim(substr($method, 0, $p));
            } else {
                $method = trim($method);
                $param = null;
            }
            if (in_array($method, $methods)) {
                $value = self::$method($value, $param);
            } elseif (in_array($method, self::$_funcs['p1'])) {
                $value = $method($value);
            } elseif (in_array($method, self::$_funcs['p2'])) {
                $value = $method($value, $param);
            }
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @param int $decimals
     * @return float|array
     */
    public static function float($value, $decimals = 2)
    {
        return number_format($value, $decimals, '.', '');
    }

    /**
     * @param string $value
     * @param string|array $chars
     * @return string
     */
    public static function stripChars($value, $chars)
    {
        if (!is_array($chars)) {
            $chars = str_split($chars);
        }
        return str_replace($chars, '', $value);
    }

    /**
     * strip ASCII value less than 32
     * @param string $value
     * @return string
     */
    public static function stripLow($value)
    {
        return filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
    }
}
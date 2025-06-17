<?php
namespace LogicExpression;

class LogicParserService
{
    /** @var self|null */
    protected static $instance = null;

    /** @var Logic */
    protected $logic;

    public function __construct($messages = [])
    {
        $this->logic = new Logic($messages);
    }

    public static function getInstance($messages = [])
    {
        if (!self::$instance) {
            self::$instance = new self($messages);
        }

        return self::$instance;
    }

    public function reset($messages = [])
    {
        $this->logic = new Logic($messages);
    }

    public function ifs_excel()
    {
        $args = func_get_args();
        $wrapped = [];

        for ($i = 0; $i < count($args); $i += 2) {
            $cond = $args[$i];
            $value = isset($args[$i + 1]) ? $args[$i + 1] : null;

            $wrapped[] = function () use ($cond) {
                return $cond;
            };
            $wrapped[] = is_callable($value) ? $value : function () use ($value) {
                return $value;
            };
        }

        return call_user_func_array([$this->logic, 'ifs_excel'], $wrapped);
    }

    public function if_excel($cond, $trueVal, $falseVal)
    {
        return function () use ($cond, $trueVal, $falseVal) {
            return $this->logic->if_excel($cond, $trueVal, $falseVal);
        };
    }

    public function and_excel()
    {
        $args = func_get_args();
        return function () use ($args) {
            return call_user_func_array([$this->logic, 'and_excel'], $args);
        };
    }

    public function or_excel()
    {
        $args = func_get_args();
        return function () use ($args) {
            return call_user_func_array([$this->logic, 'or_excel'], $args);
        };
    }

    public function not_excel($arg)
    {
        return function () use ($arg) {
            return $this->logic->not_excel($arg);
        };
    }

    public function parse($formula)
    {
        try {
            $php = $this->convertToPhpCallable($formula);
            $call = eval("return function() { return {$php}; };");
            return $call instanceof \Closure ? $call() : $call;
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'msg' => "[LogicParserService] Invalid expression: " . $e->getMessage() . $e->getTraceAsString()
            ];
        }
    }

    protected function convertToPhpCallable($expr)
    {
        $expr = trim($expr);
        $expr = preg_replace('/\bIFS\s*\(/i', 'self::getInstance()->ifs_excel(', $expr);
        $expr = preg_replace('/\bIF\s*\(/i', 'self::getInstance()->if_excel(', $expr);
        $expr = preg_replace('/\bAND\s*\(/i', 'self::getInstance()->and_excel(', $expr);
        $expr = preg_replace('/\bOR\s*\(/i', 'self::getInstance()->or_excel(', $expr);
        $expr = preg_replace('/\bNOT\s*\(/i', 'self::getInstance()->not_excel(', $expr);

        // Thay toán tử Excel: "<>" → "!="
        $expr = str_replace('<>', '!=', $expr);
        // Sửa "=" thành "==" nếu không phải >=, <=, !=, ===, ...
        $expr = preg_replace('/(?<![<>!=])=(?!=)/', '==', $expr);
        return $expr;
    }
}

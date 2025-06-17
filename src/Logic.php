<?php
namespace LogicExpression;

class Logic
{
    /** @var array */
    protected $messages = [];

    /**
     * @param array $messages
     */
    public function __construct($messages = [])
    {
        $this->messages = $messages;
    }

    public function if_excel($condition = null, $trueVal = null, $falseVal = null)
    {
        if (func_num_args() !== 3) {
            return $this->error('if_excel', 'Exactly 3 arguments required');
        }

        try {
            $condition = $this->resolve($condition);
            return $condition ? $this->resolve($trueVal) : $this->resolve($falseVal);
        } catch (\Throwable $e) {
            return $this->error('if_excel', $e->getMessage());
        }
    }

    public function ifs_excel()
    {
        $args = func_get_args();
        if (count($args) < 2 || count($args) % 2 !== 0) {
            return $this->error('ifs_excel', 'Even number of arguments required (condition/value pairs)');
        }

        try {
            for ($i = 0; $i < count($args); $i += 2) {
                $cond = $this->resolve($args[$i]);
                if ($cond) return $this->resolve($args[$i + 1]);
            }
            return null;
        } catch (\Throwable $e) {
            return $this->error('ifs_excel', $e->getMessage());
        }
    }

    public function and_excel()
    {
        $args = func_get_args();
        if (count($args) < 1) {
            return $this->error('and_excel', 'At least 1 argument required');
        }

        try {
            foreach ($args as $arg) {
                if (!$this->resolve($arg)) return false;
            }
            return true;
        } catch (\Throwable $e) {
            return $this->error('and_excel', $e->getMessage());
        }
    }

    public function or_excel()
    {
        $args = func_get_args();
        if (count($args) < 1) {
            return $this->error('or_excel', 'At least 1 argument required');
        }

        try {
            foreach ($args as $arg) {
                if ($this->resolve($arg)) return true;
            }
            return false;
        } catch (\Throwable $e) {
            return $this->error('or_excel', $e->getMessage());
        }
    }

    public function not_excel($arg = null)
    {
        if (func_num_args() !== 1) {
            return $this->error('not_excel', 'Exactly 1 argument required');
        }

        try {
            return !$this->resolve($arg);
        } catch (\Throwable $e) {
            return $this->error('not_excel', $e->getMessage());
        }
    }

    public function parserFromString($formula)
    {
        try {
            $php = $this->convertToPhpCallable($formula);
            $call = eval("return {$php};");
            return $call instanceof \Closure ? $call() : $call;
        } catch (\Throwable $e) {
            return $this->error('parserFromString', 'Invalid expression: ' . $e->getMessage());
        }
    }

    protected function convertToPhpCallable($expr)
    {
        $expr = trim($expr);
        $expr = preg_replace('/\bIFS\s*\(/i', '$this->ifs_excel(', $expr);
        $expr = preg_replace('/\bIF\s*\(/i', '$this->if_excel(', $expr);
        $expr = preg_replace('/\bAND\s*\(/i', '$this->and_excel(', $expr);
        $expr = preg_replace('/\bOR\s*\(/i', '$this->or_excel(', $expr);
        $expr = preg_replace('/\bNOT\s*\(/i', '$this->not_excel(', $expr);
        return $expr;
    }

    protected function resolve($val)
    {
        return is_callable($val) ? $val() : $val;
    }

    protected function error($method, $msg)
    {
        $context = isset($this->messages[strtolower($method)]) ? $this->messages[strtolower($method)] : '';
        $fullMsg = "[Logic::$method] $msg";
        if ($context) $fullMsg .= "\nContext: $context";

        if (function_exists('slack_msg')) {
            slack_msg($fullMsg, true);
        } else {
            error_log($fullMsg);
        }

        return ['status' => false, 'msg' => $context];
    }
}

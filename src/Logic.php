<?php
namespace LogicExpression;

class Logic
{
    /**
     * IF(condition, valueIfTrue, valueIfFalse)
     */
    public static function if_excel($condition = false, $valueTrue = null, $valueFalse = null)
    {
        return $condition ? $valueTrue : $valueFalse;
    }

    /**
     * IFS(condition1, value1, condition2, value2, ...)
     */
    public static function ifs_excel(...$args)
    {
        $args = self::safeIFSArgs($args);

        for ($i = 0; $i < count($args); $i += 2) {
            $condition = (bool) ($args[$i] ?? false);
            if ($condition) return ($args[$i + 1] ?? null);
        }

        return null;
    }

    /**
     * AND(...conditions): all must be true
     */
    public static function and_excel(...$args): bool
    {
        $args = self::safeArgs($args, [true]);
        foreach ($args as $cond) {
            if (!$cond) return false;
        }
        return true;
    }

    /**
     * OR(...conditions): at least one true
     */
    public static function or_excel(...$args): bool
    {
        $args = self::safeArgs($args, [false]);
        foreach ($args as $cond) {
            if ($cond) return true;
        }
        return false;
    }

    /**
     * XOR(...conditions): exactly one true
     */
    public static function xor_excel(...$args): bool
    {
        $args = self::safeArgs($args, [false]);
        $trueCount = 0;
        foreach ($args as $cond) {
            if ($cond) $trueCount++;
        }
        return $trueCount === 1;
    }

    /**
     * NOT(condition): logical negation
     */
    public static function not_excel($arg): bool
    {
        return !$arg;
    }

    /**
     * Helper: fallback when empty
     */
    protected static function safeArgs(array $args, array $default): array
    {
        return empty($args) ? $default : $args;
    }

    /**
     * Helper: validate IFS arguments
     */
    protected static function safeIFSArgs(array $args): array
    {
        $count = count($args);
        if ($count < 2 || $count % 2 !== 0) {
            throw new \InvalidArgumentException("IFS() requires even number of arguments (condition/value pairs).");
        }
        return $args;
    }
}

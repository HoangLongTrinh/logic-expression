<?php
namespace LogicExpression;

class Logic
{

    public static function if_excel($condition = false, $valueTrue = null, $valueFalse = null)
    {
        try {
            return $condition ? $valueTrue : $valueFalse;
        } catch (\Throwable $e) {
            self::logError('if_excel', $e);
            return null;
        }
    }

    public static function ifs_excel(...$args)
    {
        try {
            $args = self::safeIFSArgs($args);
            for ($i = 0; $i < count($args); $i += 2) {
                if ((bool) ($args[$i] ?? false)) return $args[$i + 1];
            }
            return null;
        } catch (\Throwable $e) {
            self::logError('ifs_excel', $e);
            return null;
        }
    }

    public static function and_excel(...$args): bool
    {
        try {
            $args = self::safeArgs($args, [true]);
            foreach ($args as $cond) {
                if (!$cond) return false;
            }
            return true;
        } catch (\Throwable $e) {
            self::logError('and_excel', $e);
            return false;
        }
    }

    public static function or_excel(...$args): bool
    {
        try {
            $args = self::safeArgs($args, [false]);
            foreach ($args as $cond) {
                if ($cond) return true;
            }
            return false;
        } catch (\Throwable $e) {
            self::logError('or_excel', $e);
            return false;
        }
    }

    public static function xor_excel(...$args): bool
    {
        try {
            $args = self::safeArgs($args, [false]);
            $trueCount = 0;
            foreach ($args as $cond) {
                if ($cond) $trueCount++;
            }
            return $trueCount === 1;
        } catch (\Throwable $e) {
            self::logError('xor_excel', $e);
            return false;
        }
    }

    public static function not_excel($arg): bool
    {
        try {
            return !$arg;
        } catch (\Throwable $e) {
            self::logError('not_excel', $e);
            return false;
        }
    }

    protected static function safeArgs(array $args, array $default): array
    {
        return empty($args) ? $default : $args;
    }

    protected static function safeIFSArgs(array $args): array
    {
        $count = count($args);
        if ($count < 2 || $count % 2 !== 0) {
            throw new \InvalidArgumentException("ifs_excel() requires even number of arguments.");
        }
        return $args;
    }

    protected static function logError(string $method, \Throwable $e): void
    {
        $message = "🚨 *Logic::$method()* Exception:\n"
            . "`" . $e->getMessage() . "`\n"
            . "*File:* `" . $e->getFile() . ":" . $e->getLine() . "`";

        if (function_exists('slack_msg')) {
            // Gửi lên Slack qua helper hệ thống nếu có
            slack_msg($message, true);
        } else {
            // Fallback: gửi thẳng webhook hoặc log file
            error_log($message);
        }
    }
}

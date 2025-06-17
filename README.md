# Logic Expression

A lightweight PHP library that mimics Excel's logical expressions.

## Supported functions

- `IF(condition, valueIfTrue, valueIfFalse)`
- `IFS(condition1, value1, ..., conditionN, valueN)`
- `AND(...)`
- `OR(...)`
- `XOR(...)`
- `NOT(...)`

## Installation

```bash
composer require hoanglongtrinh/logic-expression
```
## use

```
use LogicExpression\Logic as L;
echo L::IF(true, 'yes', 'no'); // yes
echo L::AND(true, true, false); // false

// helper
if (!function_exists('logic')) {
    function logic(): string
    {
        return \LogicExpression\Logic::class;
    }
}

logic()::IF(...);

```
# Logic Expression

A lightweight PHP library that mimics Excel's logical expressions.

## Supported functions

- `if_excel(condition, valueIfTrue, valueIfFalse)`
- `ifs_excel(condition1, value1, ..., conditionN, valueN)`
- `and_excel(...)`
- `or_excel(...)`
- `xor_excel(...)`
- `not_excel(...)`

## Installation

```bash
composer require hoanglongtrinh/logic-expression
```
## use

```
use LogicExpression\Logic as L;
echo L::if_excel(true, 'yes', 'no'); // yes
echo L::and_excel(true, true, false); // false

// helper
if (!function_exists('logic')) {
    function logic(): string
    {
        return \LogicExpression\Logic::class;
    }
}

logic()::ifs_excel(...);

```
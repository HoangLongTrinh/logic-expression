# Logic Expression

A lightweight PHP library that mimics Excel's logical expressions.

## Supported functions

- `if_excel(condition, valueIfTrue, valueIfFalse)`
- `ifs_excel(condition1, value1, ..., conditionN, valueN)`
- `and_excel(...)`
- `or_excel(...)`
- `not_excel(...)`

## Installation

```bash
composer require hoanglongtrinh/logic-expression
```
## use

```
use LogicExpression\Logic;

$logic = new Logic();

$result = $logic->IF(
    fn() => 1 == 1,
    fn() => 'YES',
    fn() => 'NO'
); // 👉 'YES'


use LogicExpression\LogicParserService;

$parser = LogicParserService::getInstance();

$result = $parser->IFS(
    1 == 2, 100,
    $parser->IF(3 == 3, 200, 300),
    1
); // 👉 200


use function parseAllLogicExpressions;
$messages = [
    'ifs_excel' => 'Lỗi logic phân loại khách hàng',
    'if_excel'  => 'Sai biểu thức điều kiện IF tại bước xác minh',
    'and_excel' => 'AND không hợp lệ khi kết hợp điều kiện doanh số',
    'or_excel'  => 'OR bị lỗi trong phân tích KPI',
    'not_excel' => 'NOT dùng sai trong lọc báo cáo'
];

$expr = "IFS(1=2, 4, IF(3=1, 4, 0), IF(IF(3=3,7,0)=0,3,5)=5, 2, 1)";
echo parseAllLogicExpressions($expr, $messages); // 👉 "2"


$input = "IFS(1=2, 4, IF(3=1, 4, 0), IF(IF(3=3,7,0)=0,3,5)=5, 2, 1) + AND(1=1, 2=2)";
$output = parseAllLogicExpressions($input, $messages); // 👉 "2 + 1"
eval("echo $output;"); // 👉 "3"
```
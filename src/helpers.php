<?php

if (!function_exists('parseAllLogicExpressions')) {
    /**
     * @param string $input
     * @param array $messages (tuỳ chọn): truyền message riêng cho từng loại hàm
     * @return string
     */
    function parseAllLogicExpressions($input, $messages = array())
    {
        $parser = \LogicExpression\LogicParserService::getInstance();
        $parser->reset($messages);

        $pattern = '/\b(IFS|IF|AND|OR|NOT)\s*\(((?>[^()]+|(?R))*)\)/i';

        while (preg_match_all($pattern, $input, $matches, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($matches[0]) as $match) {
                $expr = $match[0];
                $offset = $match[1];

                $result = $parser->parse($expr);

                if (is_array($result) && isset($result['status']) && $result['status'] === false) {
                    // Có lỗi
                    return json_encode($result);
                }

                $input = substr_replace($input, $result, $offset, strlen($expr));
            }
        }

        return $input;
    }
}


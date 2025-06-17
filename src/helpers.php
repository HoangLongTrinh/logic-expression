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
                    // 📢 Gửi lỗi lên Slack nếu có hàm slack_msg
                    if (function_exists('slack_msg')) {
                        $msg = "[LogicExpression] Error in expression:\n{$expr}\nDetails: {$result['msg']}";
                        slack_msg($msg, true);
                    }
                    // ❗ Thay thế lỗi thành 0
                    $input = substr_replace($input, '0', $offset, strlen($expr));
                    continue;
                }

                // ✅ Nếu result là Closure → chạy lấy giá trị
                if ($result instanceof \Closure) {
                    $result = $result(); // gọi thực thi
                }

                $input = substr_replace($input, $result, $offset, strlen($expr));
            }
        }

        return $input;
    }
}


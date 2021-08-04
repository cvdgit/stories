<?php

namespace common\components\unisender;

use omgdef\unisender\UniSenderWrapper;

class WikidsUniSenderWrapper extends UniSenderWrapper
{

    /**
     * @param string $methodName
     * @param array $params
     * @return array
     */
    protected function sendQuery($methodName, array $params = [])
    {
        if ($this->encoding != 'UTF8') {
            if (function_exists('iconv')) {
                array_walk_recursive($params, array($this, 'iconv'));
            } else if (function_exists('mb_convert_encoding')) {
                array_walk_recursive($params, array($this, 'mb_convert_encoding'));
            }
        }

        $params['api_key'] = $this->apiKey;
        $body = http_build_query($params);

        $getParams = http_build_query(
            [
                'format' => 'json'
            ]
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout ?: 10);

        $retryCount = 0;
        do {
            curl_setopt($ch, CURLOPT_URL, $this->getApiHost($retryCount) . $methodName . '?' . $getParams);
            $result = curl_exec($ch);
            $retryCount++;
        } while ($result === false && $retryCount < $this->retryCount);

        curl_close($ch);
        return $result !== false ? json_decode($result, true) : null;
    }
}

<?php

namespace Non0\Socket\Support;

use Non0\Socket\SocketException;

class Json
{
    /**
     * 将数据编码为json，用于请求平台服务器
     * @param $data
     * @return string
     */
    public static function encode($data)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $str = json_encode($data);
            $str = preg_replace_callback(
                '#\\\u([0-9a-f]{4})#i',
                function ($matchs) {
                    return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
                },
                $str
            );
            return str_replace('\/', '/', $str);
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); //php 5.4.0
    }

    /**
     * @param $jsonStr
     * @return mixed
     * @throws SocketException
     */
    public static function parseOrFail($jsonStr)
    {
        $arr = json_decode($jsonStr, true);

        if (isset($arr['errcode']) && 0 !== $arr['errcode']) {
            if (empty($arr['errmsg'])) {
                $arr['errmsg'] = 'Unknown';
            }

            throw new SocketException($arr['errmsg'], $arr['errcode']);
        }
        return $arr;
    }
}
<?php
/**
 * Copyright (c) 2018. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/6/11/0011
 * Time: 下午 2:51
 * APPLICATION:
 */

namespace non0\socket\support;


class read
{
    public $data;
    public $index = 0;
    public $maskingKey = [];

    /**
     * read constructor.
     * @param $string
     */
    function __construct($string)
    {
        $this->data = bin2hex($string);
    }

    function readByte()
    {
        $data = substr($this->data, $this->index, 2);
        $this->index += 2;
        return $data;
    }

    function readShort()
    {
        $data = substr($this->data, $this->index, 4);
        $this->index += 4;
        return (int)$data;
    }

    function readInt()
    {
        $data = substr($this->data, $this->index, 8);
        $this->index += 8;
        return (int)$data;
    }

    function readLen($len)
    {
        $data = substr($this->data, $this->index, $len);
        $this->index += $len;
        return (int)$data;
    }

    function readString($len)
    {
        $data = substr($this->data, $this->index, $len);
        $this->index += $len;
        return $data;
    }

    function readBytes($len)
    {
        $mask = [];
        for ($i = 0; $i < $len; $i++) {
            $mask[] = hexdec(substr($this->data, $this->index, 2));
            $this->index += 2;
        }
        return $mask;
    }

    function masking($string)
    {
        $data = '';
        $n = 0;
        for ($i = 0; $i <strlen($string); $i += 2) {
            $data .= dechex($this->maskingKey[$n % 4] ^ hexdec(substr($string, $i, 2)));
            $n++;
        }
        return $data;
    }

    function hexToUtf8($string)
    {
        $data = '';
        $n = 0;

        for ($i = 0; $i < strlen($string); $i += 2) {
            $data .= chr(hexdec(substr($string, $i, 2)));
            $n++;
        }
        return $data;
    }

    function hex($str)
    {
        $len = strlen($str) / 2;
        $re = '';
        for ($i = 0; $i < $len; $i++) {
            $pos = $i * 2;
            $re .= chr(hexdec(substr($str, $pos, 1)) << 4) | chr(hexdec(substr($str, $pos + 1, 1)));
        }
        return $re;
    }
}
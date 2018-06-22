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
 * Date: 2018/4/17 0017
 * Time: 下午 12:49
 * APPLICATION:
 */

namespace Non0\Socket;


class socket
{
    public $sockets; //socket的连接池，即client连接进来的socket标志
    public $users;  //所有client连接进来的信息，包括socket、client名字等
    public $master; //socket的resource，即前期初始化socket时返回的socket资源

    /**
     * 获取wsk
     * @param $req
     * @return null
     */
    public function getKey($req)
    {
        $key = null;
        if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $req, $match)) {
            $key = $match[1];
        }
        return $key;
    }

    /**
     * 握手签名
     * @param $req
     * @return string
     */
    public function encrypt($req)
    {
        $key = $this->getKey($req);
        $mask = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
        return base64_encode(sha1($key . $mask, true));
    }

    /**
     * 是否握手帧
     * @param $req
     * @return bool
     */
    public function isConnect($req)
    {
        $key = null;
        if (preg_match("/Connection: (.*)\r\n/", $req, $match)) {
            $key = $match[1];
        }
        if (strpos(strtolower($key), 'upgrade')>=0 && strpos(strtolower($key), 'upgrade')!==false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 握手
     * @param $socket
     * @param $req
     */
    public function handShake($socket, $req)
    {

        $acceptKey = $this->encrypt($req);
        $upgrade = "HTTP/1.1 101 Switching Protocols\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept: " . $acceptKey . "\r\n\r\n";
        socket_write($socket, $upgrade, strlen($upgrade . chr(0)));
    }

    function code($msg)
    {
        $frame = array();
        $frame[0] = '81';
        $len = strlen($msg);
        if ($len < 126) {
            $frame[1] = $len < 16 ? '0' . dechex($len) : dechex($len);
        } else if ($len < 65025) {
            $s = dechex($len);
            $frame[1] = '7e' . str_repeat('0', 4 - strlen($s)) . $s;
        } else {
            $s = dechex($len);
            $frame[1] = '7f' . str_repeat('0', 16 - strlen($s)) . $s;
        }
        $frame[2] = $this->ord_hex($msg);
        $data = implode('', $frame);
        return pack("H*", $data);
    }

    function ord_hex($data)
    {
        $msg = '';
        $l = strlen($data);
        for ($i = 0; $i < $l; $i++) {
            $msg .= dechex(ord($data{$i}));
        }
        return $msg;
    }

    public function hex_dump($data, $newline = "n")
    {
        static $from = '';
        static $to = '';

        static $width = 16; # number of bytes per line

        static $pad = '.'; # padding for non-visible characters

        if ($from === '') {
            for ($i = 0; $i <= 0xFF; $i++) {
                $from .= chr($i);
                $to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
            }
        }

        $hex = str_split(bin2hex($data), $width * 2);
        $chars = str_split(strtr($data, $from, $to), $width);

        $offset = 0;
        foreach ($hex as $i => $line) {
            echo sprintf('%6X', $offset) . ' : ' . implode(' ', str_split($line, 2)) . ' [' . $chars[$i] . ']' . $newline;
            $offset += $width;
        }
    }
}
<?php

/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/4/27/0027
 * Time: 上午 11:34
 * APPLICATION:
 */

namespace non0\socket;

use Symfony\Component\EventDispatcher\Event;
use non0\socket\socket;

class message extends Event
{
    protected $message;
    protected $socket;

    function __construct($data = '', $socket)
    {
        $this->message = $data;
        $this->socket = $socket;
    }

    function getMessage()
    {
        return $this->message;
    }

    function get($name)
    {
        return $this->message[$name];
    }

    function send($data = [])
    {
        $socket = new socket();
        socket_write($this->socket,$socket->code(json_encode($data)));
    }
}
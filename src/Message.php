<?php

/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/4/27/0027
 * Time: 上午 11:34
 * APPLICATION:
 */

namespace Non0\Socket;

use Symfony\Component\EventDispatcher\Event;

class message extends Event
{
    protected $message;
    protected $key;//表示当前的socket的数组下标

    function __construct($data, $key)
    {
        $this->message = $data;
        $this->key = $key;
    }

    /**
     * 获取消息
     * @return mixed
     */
    function getMessage()
    {
        return $this->message;
    }

    /**
     * 获取信息数据
     * @param $name
     * @return mixed
     */
    function get($name)
    {
        if (isset($this->message[$name]))
            return $this->message[$name];
        return false;
    }

    function getKey()
    {
        return $this->key;
    }

    /**
     * 单发消息给自己
     * @param array $data
     * @param $service
     */
    function send($data = [], $service)
    {
        socket_write($service->socket->sockets[$this->key], $service->socket->code(json_encode($data)));
    }

    /**
     * 群发消息
     * @param $data
     * @param $service
     * @param $self
     */
    function sendALL($data, $service, $self = true)
    {
        foreach ($service->socket->sockets as $key => $socket) {
            //如果是自身，并且self传递为否则不发送给自己
            if ($key == $this->key && !$self) {
                continue;
            }
            socket_write($socket, $service->socket->code(json_encode($data)));
        }
    }
}
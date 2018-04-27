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
        return $this->message[$name];
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
     */
    function sendALL($data, $service)
    {
        foreach ($service->socket->sockets as $key => $socket) {
            socket_write($socket, $service->socket->code(json_encode($data)));
        }
    }
}
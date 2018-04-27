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

    function __construct($data='')
    {
        $this->message = $data;
    }

    function getMessage()
    {
        return $this->message;
    }

    function get($name)
    {
        return $this->message[$name];
    }
}